<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\OtpCode;
use App\Models\ActivationToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Exception;

class AuthController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Procesar login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
            'remember' => 'nullable|boolean'
        ], [
            'email.required' => 'El email es obligatorio',
            'email.email' => 'Ingrese un email válido',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput($request->only('email'));
        }

        try {
            // Verificar si el usuario existe
            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                $error = 'Credenciales incorrectas.';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $error], 401);
                }
                $request->session()->flash('error', $error);
                return redirect(back());
            }

            // Verificar si el usuario está activo
            if ($user->estado !== 'activo') {
                $error = 'Su cuenta está inactiva. Contacte al administrador.';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $error], 401);
                }
                $request->session()->flash('error', $error);
                return redirect(back());
            }

            // Intentar autenticar
            $credentials = $request->only('email', 'password');
            $remember = $request->boolean('remember');

            if (Auth::attempt($credentials, $remember)) {
                $request->session()->regenerate();
                
                // Actualizar último acceso
                $user->update([
                    'ultimo_acceso' => now(),
                    'ip_ultimo_acceso' => $request->ip()
                ]);

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Inicio de sesión exitoso',
                        'redirect_url' => $this->getRedirectUrl($user)
                    ]);
                }

return redirect($this->getRedirectUrl($user));
            }

            $error = 'Credenciales incorrectas.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $error], 401);
            }
            $request->session()->flash('error', $error);
            return redirect(back());

        } catch (Exception $e) {
            $error = 'Error en el servidor. Intente nuevamente.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $error], 500);
            }
            $request->session()->flash('error', $error);
            return \Core\Response::back();
        }
    }

    /**
     * Mostrar formulario de registro
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Procesar registro
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'cedula' => 'required|string|max:20|unique:users',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'terminos' => 'required|accepted'
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'email.required' => 'El email es obligatorio',
            'email.unique' => 'Este email ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'cedula.required' => 'La cédula es obligatoria',
            'cedula.unique' => 'Esta cédula ya está registrada',
            'terminos.accepted' => 'Debe aceptar los términos y condiciones'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Crear usuario
            $user = User::create([
                'nombre' => $request->nombre,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'cedula' => $request->cedula,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
                'estado' => 'pendiente', // Requiere activación
                'email_verificado' => false
            ]);

            // Asignar rol de estudiante por defecto
            $rolEstudiante = Role::where('nombre', 'estudiante')->first();
            if ($rolEstudiante) {
                $user->roles()->attach($rolEstudiante->id);
            }

            // Crear token de activación
            $token = ActivationToken::createToken($user->email);

            // Enviar email de activación (aquí implementarías el envío de email)
            // Mail::to($user->email)->send(new ActivationMail($user, $token));

            DB::commit();

            $message = 'Registro exitoso. Revise su email para activar su cuenta.';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return redirect('/login')->with('success', $message);

        } catch (Exception $e) {
            DB::rollBack();
            $error = 'Error al crear la cuenta. Intente nuevamente.';
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $error], 500);
            }
            return back()->withErrors(['error' => $error])->withInput();
        }
    }

    /**
     * Activar cuenta
     */
    public function activateAccount(Request $request, string $token)
    {
        try {
            $activationToken = ActivationToken::validateToken($token);

            if (!$activationToken) {
                $request->session()->flash('error', 'Token de activación inválido o expirado.');
                return redirect('/login');
            }

            // Obtener usuario por email
            $user = User::where('email', $activationToken->email)->first();
            
            if (!$user) {
                $request->session()->flash('error', 'Usuario no encontrado.');
                return redirect('/login');
            }

            DB::beginTransaction();

            $user->update([
                'estado' => 'activo',
                'email_verificado' => true,
                'email_verified_at' => now()
            ]);

            ActivationToken::markAsUsed($token);

            DB::commit();

            $request->session()->flash('success', 'Cuenta activada exitosamente. Ya puede iniciar sesión.');
            return redirect('/login');

        } catch (Exception $e) {
            DB::rollBack();
            $request->session()->flash('error', 'Error al activar la cuenta. Contacte al administrador.');
            return redirect('/login');
        }
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Sesión cerrada exitosamente.'
            ]);
        }

        return redirect('/login');
    }

    /**
     * Mostrar formulario de recuperación de contraseña
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Enviar código OTP para recuperación
     */
    public function sendPasswordReset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => 'El email es obligatorio',
            'email.exists' => 'No existe una cuenta con este email'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            $user = User::where('email', $request->email)->first();
            
            // Generar código OTP (usar el campo correcto usuario_id)
            $otpCode = OtpCode::generarCodigo($user->id, 15); // 15 minutos de vida

            // Enviar email con código (aquí implementarías el envío)
            // Mail::to($user->email)->send(new PasswordResetMail($user, $otpCode->codigo));

            $message = 'Se ha enviado un código de verificación a su email.';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return redirect('/password/reset/' . $user->id)
                          ->with('success', $message);

        } catch (Exception $e) {
            $error = 'Error al enviar el código. Intente nuevamente.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $error], 500);
            }
            return back()->withErrors(['error' => $error])->withInput();
        }
    }

    /**
     * Mostrar formulario de reseteo con código OTP
     */
    public function showResetPassword(int $userId)
    {
        $user = User::findOrFail($userId);
        return view('auth.reset-password', compact('user'));
    }

    /**
     * Resetear contraseña con código OTP
     */
    public function resetPassword(Request $request, int $userId)
    {
        $validator = Validator::make($request->all(), [
            'codigo' => 'required|string|size:6',
            'password' => 'required|string|min:6|confirmed'
        ], [
            'codigo.required' => 'El código de verificación es obligatorio',
            'codigo.size' => 'El código debe tener 6 dígitos',
            'password.required' => 'La nueva contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            $user = User::findOrFail($userId);
            
            // Verificar código OTP (usar el campo correcto usuario_id)
            $otpCode = OtpCode::verificarCodigo($user->id, $request->codigo);
            
            if (!$otpCode) {
                $error = 'Código de verificación inválido o expirado.';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $error], 400);
                }
                return back()->withErrors(['codigo' => $error])->withInput();
            }

            // Actualizar contraseña
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            $message = 'Contraseña actualizada exitosamente. Ya puede iniciar sesión.';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return redirect('/login')->with('success', $message);

        } catch (Exception $e) {
            $error = 'Error al resetear la contraseña. Intente nuevamente.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $error], 500);
            }
            return back()->withErrors(['error' => $error])->withInput();
        }
    }

    /**
     * Obtener URL de redirección según el rol del usuario
     */
    private function getRedirectUrl(User $user): string
    {
        if ($user->hasRole('administrador')) {
            return '/admin/dashboard';
        } elseif ($user->hasRole('docente')) {
            return '/docente/dashboard';
        } else {
            return '/estudiante/dashboard';
        }
    }
}
