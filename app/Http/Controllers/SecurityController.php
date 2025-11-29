<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Exception;

class SecurityController extends Controller
{
    /**
     * Dashboard de seguridad
     */
    public function dashboard()
    {
        try {
            $user = Auth::user();
            
            if (!$user->hasRole('administrador')) {
                return back()->withErrors(['error' => 'No tiene permisos para acceder a esta sección.']);
            }

            // Estadísticas de seguridad
            $estadisticas = [
                'total_usuarios' => User::count(),
                'usuarios_activos' => User::where('estado', 'activo')->count(),
                'usuarios_bloqueados' => User::where('estado', 'bloqueado')->count(),
                'intentos_fallidos_hoy' => $this->getFailedAttemptsToday(),
                'usuarios_online' => $this->getUsersOnline(),
                'registros_recientes' => User::whereDate('created_at', today())->count()
            ];

            // Actividad reciente
            $actividadReciente = $this->getRecentActivity();
            
            // IPs sospechosas (simulado)
            $ipsSospechosas = $this->getSuspiciousIPs();

            return view('admin.security.dashboard', compact('estadisticas', 'actividadReciente', 'ipsSospechosas'));
            
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar dashboard de seguridad: ' . $e->getMessage()]);
        }
    }

    /**
     * Gestión de usuarios
     */
    public function usuarios(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user->hasRole('administrador')) {
                return back()->withErrors(['error' => 'No tiene permisos para gestionar usuarios.']);
            }

            $query = User::with(['roles']);

            // Filtros
            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            }

            if ($request->filled('rol')) {
                $query->whereHas('roles', function($q) use ($request) {
                    $q->where('nombre', $request->rol);
                });
            }

            if ($request->filled('buscar')) {
                $buscar = $request->buscar;
                $query->where(function($q) use ($buscar) {
                    $q->where('nombre', 'like', "%{$buscar}%")
                      ->orWhere('email', 'like', "%{$buscar}%")
                      ->orWhere('cedula', 'like', "%{$buscar}%");
                });
            }

            $usuarios = $query->orderBy('created_at', 'desc')->paginate(20);

            return view('admin.security.usuarios', compact('usuarios'));
            
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar usuarios: ' . $e->getMessage()]);
        }
    }

    /**
     * Cambiar estado de usuario
     */
    public function cambiarEstadoUsuario(Request $request, $userId)
    {
        $validator = Validator::make($request->all(), [
            'estado' => 'required|in:activo,inactivo,bloqueado,pendiente'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator);
        }

        try {
            $currentUser = Auth::user();
            
            if (!$currentUser->hasRole('administrador')) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Acceso denegado'], 403);
                }
                return back()->withErrors(['error' => 'No tiene permisos para realizar esta acción.']);
            }

            $usuario = User::findOrFail($userId);
            
            // No permitir cambiar estado del propio usuario
            if ($usuario->id === $currentUser->id) {
                $error = 'No puede modificar su propio estado.';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $error], 400);
                }
                return back()->withErrors(['error' => $error]);
            }

            $estadoAnterior = $usuario->estado;
            $usuario->update([
                'estado' => $request->estado,
                'fecha_cambio_estado' => now()
            ]);

            // Log de seguridad
            Log::info('Estado de usuario cambiado', [
                'user_id' => $usuario->id,
                'email' => $usuario->email,
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => $request->estado,
                'changed_by' => $currentUser->id
            ]);

            $message = 'Estado del usuario actualizado exitosamente.';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return back()->with('success', $message);

        } catch (Exception $e) {
            $error = 'Error al cambiar estado: ' . $e->getMessage();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $error], 500);
            }
            return back()->withErrors(['error' => $error]);
        }
    }

    /**
     * Resetear contraseña de usuario
     */
    public function resetearPassword(Request $request, $userId)
    {
        $validator = Validator::make($request->all(), [
            'nueva_password' => 'required|string|min:6|confirmed'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator);
        }

        try {
            $currentUser = Auth::user();
            
            if (!$currentUser->hasRole('administrador')) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Acceso denegado'], 403);
                }
                return back()->withErrors(['error' => 'No tiene permisos para resetear contraseñas.']);
            }

            $usuario = User::findOrFail($userId);

            $usuario->update([
                'password' => Hash::make($request->nueva_password),
                'password_changed_at' => now()
            ]);

            // Log de seguridad
            Log::info('Contraseña reseteada por administrador', [
                'user_id' => $usuario->id,
                'email' => $usuario->email,
                'reset_by' => $currentUser->id
            ]);

            $message = 'Contraseña reseteada exitosamente.';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return back()->with('success', $message);

        } catch (Exception $e) {
            $error = 'Error al resetear contraseña: ' . $e->getMessage();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $error], 500);
            }
            return back()->withErrors(['error' => $error]);
        }
    }

    /**
     * Logs de actividad del sistema
     */
    public function logs(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user->hasRole('administrador')) {
                return back()->withErrors(['error' => 'No tiene permisos para ver los logs.']);
            }

            // Obtener logs desde Laravel log files (simplificado)
            $logs = $this->getSystemLogs($request);

            return view('admin.security.logs', compact('logs'));
            
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar logs: ' . $e->getMessage()]);
        }
    }

    /**
     * Configuración de seguridad
     */
    public function configuracion()
    {
        try {
            $user = Auth::user();
            
            if (!$user->hasRole('administrador')) {
                return back()->withErrors(['error' => 'No tiene permisos para configurar seguridad.']);
            }

            // Configuraciones de seguridad (pueden venir de config o BD)
            $configuraciones = [
                'intentos_login_max' => config('auth.max_login_attempts', 5),
                'tiempo_bloqueo_minutos' => config('auth.lockout_duration', 15),
                'sesion_duracion_minutos' => config('session.lifetime', 120),
                'password_min_length' => 6,
                'password_require_special' => false,
                'two_factor_enabled' => false
            ];

            return view('admin.security.configuracion', compact('configuraciones'));
            
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar configuración: ' . $e->getMessage()]);
        }
    }

    /**
     * Actualizar configuración de seguridad
     */
    public function actualizarConfiguracion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'intentos_login_max' => 'required|integer|min:3|max:10',
            'tiempo_bloqueo_minutos' => 'required|integer|min:5|max:60',
            'sesion_duracion_minutos' => 'required|integer|min:30|max:480',
            'password_min_length' => 'required|integer|min:6|max:20',
            'password_require_special' => 'boolean',
            'two_factor_enabled' => 'boolean'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $user = Auth::user();
            
            if (!$user->hasRole('administrador')) {
                return back()->withErrors(['error' => 'No tiene permisos para actualizar configuración.']);
            }

            // Aquí actualizarías las configuraciones en BD o archivo de config
            // Por ahora solo simulamos
            
            Log::info('Configuración de seguridad actualizada', [
                'updated_by' => $user->id,
                'config_data' => $request->all()
            ]);

            return back()->with('success', 'Configuración de seguridad actualizada exitosamente.');

        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al actualizar configuración: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Obtener intentos fallidos de hoy
     */
    private function getFailedAttemptsToday()
    {
        // Simulado - en implementación real usarías tabla de logs o Laravel Telescope
        return rand(5, 25);
    }

    /**
     * Obtener usuarios online
     */
    private function getUsersOnline()
    {
        // Simulado - en implementación real verificarías sesiones activas
        return rand(10, 50);
    }

    /**
     * Obtener actividad reciente
     */
    private function getRecentActivity()
    {
        return collect([
            [
                'evento' => 'Inicio de sesión exitoso',
                'usuario' => 'juan@ejemplo.com',
                'ip' => '192.168.1.100',
                'fecha' => now()->subMinutes(15)->format('d/m/Y H:i:s')
            ],
            [
                'evento' => 'Cambio de contraseña',
                'usuario' => 'maria@ejemplo.com', 
                'ip' => '192.168.1.101',
                'fecha' => now()->subMinutes(30)->format('d/m/Y H:i:s')
            ],
            [
                'evento' => 'Intento de acceso fallido',
                'usuario' => 'admin@ejemplo.com',
                'ip' => '10.0.0.5',
                'fecha' => now()->subHour()->format('d/m/Y H:i:s')
            ]
        ]);
    }

    /**
     * Obtener IPs sospechosas
     */
    private function getSuspiciousIPs()
    {
        return collect([
            [
                'ip' => '203.0.113.1',
                'intentos' => 15,
                'ultimo_intento' => now()->subMinutes(5)->format('d/m/Y H:i:s')
            ],
            [
                'ip' => '198.51.100.2',
                'intentos' => 8,
                'ultimo_intento' => now()->subMinutes(20)->format('d/m/Y H:i:s')
            ]
        ]);
    }

    /**
     * Obtener logs del sistema
     */
    private function getSystemLogs($request)
    {
        // Simulado - en implementación real leerías archivos de log
        return collect([
            [
                'nivel' => 'INFO',
                'mensaje' => 'Usuario autenticado correctamente',
                'fecha' => now()->subMinutes(10)->format('d/m/Y H:i:s'),
                'contexto' => ['user_id' => 1, 'ip' => '192.168.1.1']
            ],
            [
                'nivel' => 'WARNING',
                'mensaje' => 'Intento de acceso con credenciales incorrectas',
                'fecha' => now()->subMinutes(15)->format('d/m/Y H:i:s'),
                'contexto' => ['email' => 'test@test.com', 'ip' => '10.0.0.1']
            ],
            [
                'nivel' => 'ERROR',
                'mensaje' => 'Error en conexión a base de datos',
                'fecha' => now()->subMinutes(25)->format('d/m/Y H:i:s'),
                'contexto' => ['error' => 'Connection timeout']
            ]
        ]);
    }
}
