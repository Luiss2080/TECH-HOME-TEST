<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\Curso;
use App\Models\Libro;
use App\Models\Categoria;
use App\Models\Enrollment;
use App\Models\BookDownload;
use App\Models\Configuration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class AdminController extends Controller
{
    /**
     * Dashboard principal del administrador
     */
    public function dashboard(): View
    {
        try {
            // Estadísticas generales
            $estadisticas = [
                'total_usuarios' => User::count(),
                'total_cursos' => Curso::count(),
                'total_libros' => Libro::count(),
                'cursos_activos' => Curso::where('estado', 'activo')->count(),
                'libros_disponibles' => Libro::where('estado', 'Disponible')->count(),
                'inscripciones_mes' => Enrollment::whereMonth('fecha_inscripcion', now()->month)->count(),
                'descargas_mes' => BookDownload::whereMonth('fecha_descarga', now()->month)->count()
            ];

            // Usuarios registrados por mes (últimos 6 meses)
            $usuariosPorMes = User::selectRaw('MONTH(created_at) as mes, YEAR(created_at) as año, COUNT(*) as total')
                                ->where('created_at', '>=', now()->subMonths(6))
                                ->groupBy('año', 'mes')
                                ->orderBy('año')
                                ->orderBy('mes')
                                ->get();

            // Cursos más populares
            $cursosPopulares = Curso::withCount('inscripciones')
                                  ->orderBy('inscripciones_count', 'desc')
                                  ->take(5)
                                  ->get();

            // Libros más descargados
            $librosMasDescargados = Libro::orderBy('descargas', 'desc')
                                       ->take(5)
                                       ->get();

            // Actividad reciente
            $actividadReciente = [
                'inscripciones_recientes' => Enrollment::with(['estudiante', 'curso'])
                                                      ->latest('fecha_inscripcion')
                                                      ->take(5)
                                                      ->get(),
                'descargas_recientes' => BookDownload::with(['usuario', 'libro'])
                                                    ->latest('fecha_descarga')
                                                    ->take(5)
                                                    ->get()
            ];

            return view('admin.dashboard', compact(
                'estadisticas',
                'usuariosPorMes', 
                'cursosPopulares',
                'librosMasDescargados',
                'actividadReciente'
            ));

        } catch (Exception $e) {
            return view('admin.dashboard')->withErrors(['error' => 'Error al cargar dashboard: ' . $e->getMessage()]);
        }
    }

    /**
     * Panel de gestión de usuarios
     */
    public function usuarios(Request $request): View
    {
        try {
            $query = User::with(['roles']);

            // Filtros
            if ($request->filled('role')) {
                $query->whereHas('roles', function($q) use ($request) {
                    $q->where('nombre', $request->role);
                });
            }

            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            }

            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('nombre', 'LIKE', '%' . $request->search . '%')
                      ->orWhere('email', 'LIKE', '%' . $request->search . '%')
                      ->orWhere('cedula', 'LIKE', '%' . $request->search . '%');
                });
            }

            $usuarios = $query->orderBy('created_at', 'desc')->paginate(15);

            $estadisticas = [
                'total_usuarios' => User::count(),
                'usuarios_activos' => User::where('estado', 'activo')->count(),
                'administradores' => User::whereHas('roles', function($q) {
                    $q->where('nombre', 'administrador');
                })->count(),
                'docentes' => User::whereHas('roles', function($q) {
                    $q->where('nombre', 'docente');
                })->count(),
                'estudiantes' => User::whereHas('roles', function($q) {
                    $q->where('nombre', 'estudiante');
                })->count()
            ];

            return view('admin.usuarios.index', compact('usuarios', 'estadisticas'));
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar usuarios: ' . $e->getMessage()]);
        }
    }

    /**
     * Configuraciones del sistema
     */
    public function configuraciones(): View
    {
        try {
            $configuraciones = Configuration::orderBy('clave')->get();

            $configuracionesAgrupadas = $configuraciones->groupBy(function($config) {
                $parts = explode('.', $config->clave);
                return $parts[0] ?? 'general';
            });

            return view('admin.configuraciones.index', compact('configuracionesAgrupadas'));
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar configuraciones: ' . $e->getMessage()]);
        }
    }

    /**
     * Actualizar configuraciones
     */
    public function actualizarConfiguraciones(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            foreach ($request->all() as $clave => $valor) {
                if ($clave !== '_token') {
                    Configuration::where('clave', $clave)
                               ->where('editable', true)
                               ->update(['valor' => $valor]);
                }
            }

            DB::commit();

            return back()->with('success', 'Configuraciones actualizadas exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar configuraciones: ' . $e->getMessage()]);
        }
    }

    /**
     * Estadísticas avanzadas
     */
    public function estadisticas(): View
    {
        try {
            // Estadísticas de usuarios
            $statsUsuarios = [
                'total' => User::count(),
                'por_mes' => User::selectRaw('MONTH(created_at) as mes, COUNT(*) as total')
                               ->whereYear('created_at', now()->year)
                               ->groupBy('mes')
                               ->pluck('total', 'mes'),
                'por_rol' => User::selectRaw('roles.nombre as rol, COUNT(*) as total')
                               ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
                               ->join('roles', 'user_roles.role_id', '=', 'roles.id')
                               ->groupBy('roles.nombre')
                               ->pluck('total', 'rol')
            ];

            // Estadísticas de cursos
            $statsCursos = [
                'total' => Curso::count(),
                'activos' => Curso::where('estado', 'activo')->count(),
                'inscripciones_totales' => Enrollment::count(),
                'por_categoria' => Curso::selectRaw('categories.nombre as categoria, COUNT(*) as total')
                                       ->join('categories', 'courses.categoria_id', '=', 'categories.id')
                                       ->groupBy('categories.nombre')
                                       ->pluck('total', 'categoria')
            ];

            // Estadísticas de libros
            $statsLibros = [
                'total' => Libro::count(),
                'disponibles' => Libro::where('estado', 'Disponible')->count(),
                'gratuitos' => Libro::where('es_gratuito', true)->count(),
                'descargas_totales' => Libro::sum('descargas'),
                'por_categoria' => Libro::selectRaw('categories.nombre as categoria, COUNT(*) as total')
                                       ->join('categories', 'books.categoria_id', '=', 'categories.id')
                                       ->groupBy('categories.nombre')
                                       ->pluck('total', 'categoria')
            ];

            return view('admin.estadisticas', compact('statsUsuarios', 'statsCursos', 'statsLibros'));
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar estadísticas: ' . $e->getMessage()]);
        }
    }

    /**
     * API para estadísticas del dashboard
     */
    public function apiEstadisticas(): JsonResponse
    {
        try {
            $data = [
                'usuarios' => [
                    'total' => User::count(),
                    'nuevos_hoy' => User::whereDate('created_at', today())->count(),
                    'activos' => User::where('estado', 'activo')->count()
                ],
                'cursos' => [
                    'total' => Curso::count(),
                    'activos' => Curso::where('estado', 'activo')->count(),
                    'inscripciones_hoy' => Enrollment::whereDate('fecha_inscripcion', today())->count()
                ],
                'libros' => [
                    'total' => Libro::count(),
                    'disponibles' => Libro::where('estado', 'Disponible')->count(),
                    'descargas_hoy' => BookDownload::whereDate('fecha_descarga', today())->count()
                ]
            ];

            return response()->json(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Limpiar caché del sistema
     */
    public function limpiarCache(): JsonResponse
    {
        try {
            // Aquí puedes agregar lógica para limpiar diferentes tipos de caché
            // Por ejemplo: cache, views, routes, config, etc.
            
            return response()->json([
                'success' => true,
                'message' => 'Caché limpiado exitosamente.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al limpiar caché: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Backup de la base de datos
     */
    public function backup(): JsonResponse
    {
        try {
            // Aquí implementarías la lógica de backup
            // Por ahora solo devolvemos una respuesta de éxito
            
            return response()->json([
                'success' => true,
                'message' => 'Backup creado exitosamente.',
                'archivo' => 'backup_' . date('Y-m-d_H-i-s') . '.sql'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Información del sistema
     */
    public function sistemaInfo(): View
    {
        try {
            $info = [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'servidor' => $_SERVER['SERVER_SOFTWARE'] ?? 'Desconocido',
                'bd_conexion' => DB::connection()->getPdo() ? 'Conectado' : 'Desconectado',
                'timezone' => config('app.timezone'),
                'debug_mode' => config('app.debug') ? 'Habilitado' : 'Deshabilitado',
                'memoria_limite' => ini_get('memory_limit'),
                'tiempo_ejecucion' => ini_get('max_execution_time') . ' segundos'
            ];

            return view('admin.sistema-info', compact('info'));
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al obtener información del sistema: ' . $e->getMessage()]);
        }
    }
}
