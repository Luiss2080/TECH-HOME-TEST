<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Role;
use App\Models\ActivationToken;
use App\Models\CodigoOTP;

class AuthController extends Controller
{
    public function login(): View
    {
        return view('auth.login', ['title' => 'Bienvenido']);
    }

    /**
     * Mostrar formulario de registro
     */
    public function register(): View
    {
        return view('auth.register', ['title' => 'Crear Cuenta']);
    }

    /**
     * Procesar registro de usuario
     */
    public function registerForm(Request $request): RedirectResponse
    {
        // Validar datos del formulario
        $request->validate([
            'nombre' => 'required|string|min:2|max:50',
            'apellido' => 'required|string|min:2|max:50',
            'email' => 'required|email|max:150|unique:usuarios',
            'password' => 'required|min:8|max:50|confirmed',
            'telefono' => 'nullable|string|max:20',
            'fecha_nacimiento' => 'nullable|date'
        ]);

        try {
            DB::beginTransaction();
            
            // Crear el usuario
            $user = User::create([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'telefono' => $request->telefono,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'estado' => 0, // Usuario inactivo hasta que valide el token
                'fecha_creacion' => now(),
                'fecha_actualizacion' => now()
            ]);

            // Asegurar que existe el rol "Invitado"
            $this->ensureGuestRoleExists();

            // Asignar rol de Invitado por defecto
            $guestRole = Role::where('nombre', 'Invitado')->first();
            if ($guestRole) {
                $user->assignRole($guestRole->id);
            }
            
            DB::commit();

            // Crear token de activación
            $activationToken = ActivationToken::createToken($user->email);

            // Enviar email de bienvenida con token
            try {
                // TODO: Implementar envío de email
                // Mail::to($user->email)->send(new WelcomeEmail($user, $activationToken));
            } catch (\Exception $e) {
                Log::error('Error enviando email de bienvenida: ' . $e->getMessage());
                // No fallar el registro si hay error en el email
            }

            return redirect()->route('login')->with('success', 
                '¡Tu cuenta ha sido creada exitosamente! Te hemos enviado un email con un enlace para activar tu cuenta. Revisa tu bandeja de entrada.'
            );
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en registro de usuario: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error interno. Intenta de nuevo más tarde.')
                ->withInput($request->except(['password', 'password_confirmation']));
        }
    }

    /**
     * Asegurar que el rol "Invitado" existe
     */
    private function ensureGuestRoleExists(): void
    {
        $guestRole = Role::where('nombre', 'Invitado')->first();

        if (!$guestRole) {
            // Crear el rol Invitado si no existe
            $role = Role::create([
                'nombre' => 'Invitado',
                'descripcion' => 'Acceso temporal de 3 días a todo el material',
                'estado' => 1
            ]);

            // Asignar permisos básicos al rol Invitado
            $basicPermissions = [
                'login',
                'logout',
                'cursos.ver',
                'libros.ver',
                'libros.descargar',
                'materiales.ver',
                'laboratorios.ver',
                'api.verify_session'
            ];

            foreach ($basicPermissions as $permission) {
                try {
                    $role->givePermissionTo($permission);
                } catch (\Exception $e) {
                    Log::error("Error asignando permiso {$permission} al rol Invitado: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Procesar inicio de sesión
     */
    public function loginForm(Request $request): RedirectResponse|JsonResponse
    {
        // Validar datos del primer paso
        $request->validate([
            'email' => 'required|email|max:150',
            'password' => 'required|min:8|max:50'
        ]);

        $email = $request->email;
        $password = $request->password;

        // PASO 1: Verificar credenciales (email + password)
        $user = $this->attempt($email, $password);
        if (!$user) {
            // Verificar si el usuario existe para mostrar mensaje específico de bloqueo
            $existingUser = User::where('email', $email)->where('estado', 1)->first();
            if ($existingUser && $existingUser->isBlocked()) {
                $timeRemaining = $existingUser->getBlockTimeRemaining();
                return redirect()->back()
                    ->with('error', "Tu cuenta está bloqueada por intentos fallidos. Intenta nuevamente en {$timeRemaining} minutos.")
                    ->withInput($request->except('password'));
            } else {
                // Registrar intento fallido
                $this->logFailedAttempt($email, 'Invalid credentials');
                
                return redirect()->back()
                    ->with('error', 'Credenciales incorrectas')
                    ->withInput($request->except('password'));
            }
        }

        // Verificar si la cuenta está activa
        if ($user->estado == 0) {
            return redirect()->back()
                ->with('error', 'Tu cuenta no está activada. Revisa tu email para activar tu cuenta.')
                ->withInput($request->except('password'));
        }

        // PASO 2: Credenciales correctas - Iniciar proceso 2FA
        return $this->initiate2FA($user, $email);
    }

    /**
     * Iniciar proceso de autenticación 2FA
     */
    private function initiate2FA(User $user, string $email): RedirectResponse
    {
        try {
            // Verificar si el usuario puede generar un nuevo código
            $canGenerate = CodigoOTP::canGenerateNewCode($user->id);
            if (!$canGenerate['can_generate']) {
                if (isset($canGenerate['bloqueado'])) {
                    return redirect()->route('login')->with('error', $canGenerate['reason']);
                }

                // Si hay código activo, ir directo a verificación
                if (isset($canGenerate['codigo_existente'])) {
                    session([
                        '2fa_user_id' => $user->id,
                        '2fa_email' => $email,
                        '2fa_start_time' => time()
                    ]);
                    return redirect()->route('auth.otp.verify');
                }
            }

            // Generar nuevo código OTP
            $otpResult = CodigoOTP::generateOTP($user->id);
            if (!$otpResult['success']) {
                Log::error('Error generando OTP para usuario ' . $user->id . ': ' . $otpResult['error']);
                return redirect()->route('login')->with('error', 'Error interno generando código de verificación. Intenta de nuevo.');
            }

            // Enviar código por email
            try {
                // TODO: Implementar envío de OTP por email
                // Mail::to($email)->send(new OTPEmail($otpResult['codigo'], $user->nombre . ' ' . $user->apellido));
            } catch (\Exception $e) {
                Log::error('Error enviando email OTP a: ' . $email);
                return redirect()->route('login')->with('error', 'Error enviando código de verificación. Intenta de nuevo.');
            }

            // Guardar datos de sesión 2FA
            session([
                '2fa_user_id' => $user->id,
                '2fa_email' => $email,
                '2fa_start_time' => time(),
                '2fa_attempts' => 0
            ]);

            // Log del proceso 2FA iniciado
            $this->log2FAEvent($user->id, $email, '2FA_INITIATED', [
                'codigo_enviado' => true,
                'expira_en' => $otpResult['expira_en'],
                'ip' => request()->ip()
            ]);

            return redirect()->route('auth.otp.verify');
            
        } catch (\Exception $e) {
            Log::error('Error en initiate2FA: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Error interno. Intenta de nuevo más tarde.');
        }
    }

    /**
     * Mostrar vista de verificación OTP
     */
    public function showOTPVerification(): View|RedirectResponse
    {
        // Verificar que hay una sesión 2FA activa
        if (!session()->has('2fa_user_id') || !session()->has('2fa_email')) {
            return redirect()->route('login')->with('error', 'Sesión de verificación expirada. Inicia sesión nuevamente.');
        }

        // Verificar timeout de sesión 2FA (5 minutos máximo)
        $startTime = session('2fa_start_time', 0);
        if (time() - $startTime > 300) { // 5 minutos
            session()->forget(['2fa_user_id', '2fa_email', '2fa_start_time']);
            return redirect()->route('login')->with('error', 'Sesión de verificación expirada. Inicia sesión nuevamente.');
        }

        $email = session('2fa_email');

        return view('auth.otp-verification', [
            'title' => 'Verificación 2FA',
            'email' => $email,
            'timer_duration' => 60, // 60 segundos
            'attempts_left' => max(0, 3 - session('2fa_attempts', 0))
        ]);
    }

    /**
     * Verificar código OTP
     */
    public function verifyOTP(Request $request): JsonResponse|RedirectResponse
    {
        // Verificar sesión 2FA
        if (!session()->has('2fa_user_id') || !session()->has('2fa_email')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesión de verificación expirada.',
                    'redirect' => route('login')
                ], 401);
            }
            return redirect()->route('login')->with('error', 'Sesión de verificación expirada.');
        }

        $userId = session('2fa_user_id');
        $email = session('2fa_email');

        // Validar código OTP
        $request->validate([
            'otp_code' => 'required|string|min:6|max:6'
        ]);

        $otpCode = $request->otp_code;

        // Verificar código con el modelo
        $verificationResult = CodigoOTP::validateOTP($userId, $otpCode);

        if (!$verificationResult['success']) {
            return $this->handle2FAError($verificationResult['error'], $userId, $email, $request, $verificationResult);
        }

        // ¡CÓDIGO VÁLIDO! Completar inicio de sesión
        return $this->complete2FALogin($userId, $email, $request);
    }

    /**
     * Manejar error en verificación 2FA
     */
    private function handle2FAError(string $message, int $userId, string $email, Request $request, array $verificationResult = []): JsonResponse|RedirectResponse
    {
        // Incrementar contador de intentos
        $attempts = session('2fa_attempts', 0) + 1;
        session(['2fa_attempts' => $attempts]);

        // Log del intento fallido
        $this->log2FAEvent($userId, $email, '2FA_FAILED', [
            'attempt' => $attempts,
            'error' => $message,
            'locked' => $verificationResult['locked'] ?? false
        ]);

        // Verificar si se excedieron los intentos
        if ($attempts >= 3) {
            // Limpiar sesión 2FA
            $this->clear2FASession();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Se excedieron los intentos de verificación.',
                    'redirect' => route('login')
                ], 429);
            }

            return redirect()->route('login')->with('error', 'Se excedieron los intentos de verificación. Tu cuenta ha sido bloqueada temporalmente.');
        }

        // Si es una solicitud AJAX, responder con JSON
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'attempts_left' => 3 - $attempts,
                'locked' => $verificationResult['locked'] ?? false
            ], 400);
        }

        // Si es solicitud normal, redirigir con error
        return redirect()->route('auth.otp.verify')->with('error', $message);
    }

    /**
     * Completar inicio de sesión 2FA
     */
    private function complete2FALogin(int $userId, string $email, Request $request): JsonResponse|RedirectResponse
    {
        try {
            // Obtener usuario
            $user = User::find($userId);
            if (!$user) {
                throw new \Exception('Usuario no encontrado');
            }

            // Autenticar usuario en Laravel
            Auth::login($user);

            // Log del login exitoso
            $this->log2FAEvent($userId, $email, '2FA_SUCCESS', [
                'login_completed' => true,
                'session_created' => true
            ]);

            // Limpiar datos de 2FA
            $this->clear2FASession();

            // Determinar redirection
            $roles = $user->roles();
            $redirectRoute = route('home'); // fallback

            if (!empty($roles)) {
                $firstRole = strtolower($roles[0]['nombre']);
                switch ($firstRole) {
                    case 'administrador':
                        $redirectRoute = route('admin.dashboard');
                        break;
                    case 'estudiante':
                        $redirectRoute = route('estudiante.dashboard');
                        break;
                    case 'docente':
                        $redirectRoute = route('docente.dashboard');
                        break;
                    case 'invitado':
                        $redirectRoute = route('home');
                        break;
                }
            }

            // Si es AJAX, responder con JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => '¡Inicio de sesión exitoso!',
                    'redirect' => $redirectRoute
                ]);
            }

            return redirect($redirectRoute)->with('success', '¡Bienvenido! Has iniciado sesión correctamente.');
            
        } catch (\Exception $e) {
            Log::error('Error completando 2FA login: ' . $e->getMessage());

            // Limpiar sesión en caso de error
            $this->clear2FASession();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error completando el inicio de sesión.',
                    'redirect' => route('login')
                ], 500);
            }

            return redirect()->route('login')->with('error', 'Error completando el inicio de sesión. Intenta de nuevo.');
        }
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Has cerrado sesión correctamente.');
    }

    /**
     * Validar credenciales
     */
    private function attempt(string $email, string $password): ?User
    {
        $user = User::where('email', $email)->where('estado', 1)->first();
        
        if ($user && Hash::check($password, $user->password)) {
            // Resetear intentos fallidos si el login es exitoso
            $user->update([
                'intentos_fallidos' => 0,
                'bloqueado_hasta' => null
            ]);
            
            return $user;
        }

        // Incrementar intentos fallidos
        if ($user) {
            $user->increment('intentos_fallidos');
            
            // Bloquear cuenta si excede 5 intentos
            if ($user->intentos_fallidos >= 5) {
                $user->update([
                    'bloqueado_hasta' => now()->addMinutes(15)
                ]);
            }
        }

        return null;
    }

    /**
     * Registrar intento fallido
     */
    private function logFailedAttempt(string $email, string $reason): void
    {
        Log::warning('Failed login attempt', [
            'email' => $email,
            'reason' => $reason,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    /**
     * Registrar evento 2FA
     */
    private function log2FAEvent(int $userId, string $email, string $event, array $data = []): void
    {
        Log::info('2FA Event: ' . $event, [
            'user_id' => $userId,
            'email' => $email,
            'data' => $data,
            'ip' => request()->ip()
        ]);
    }

    /**
     * Limpiar sesión 2FA
     */
    private function clear2FASession(): void
    {
        session()->forget(['2fa_user_id', '2fa_email', '2fa_start_time', '2fa_attempts']);
    }
}