<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\Curso;
use App\Models\Libro;
use App\Models\Inscripcion;
use App\Models\BookDownload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Exception;

class EstudianteController extends Controller
{
    /**
     * Obtener ID del estudiante autenticado con validación
     */
    private function getEstudianteId(): int
    {
        $user = Auth::user();
        
        if (!$user) {
            throw new Exception('Usuario no autenticado o sesión expirada');
        }
        
        if (!$user->hasRole('Estudiante')) {
            throw new Exception('No tienes permisos de estudiante');
        }
        
        return $user->id;
    }

    /**
     * Dashboard principal del estudiante
     */
    public function dashboard(): View
    {
        try {
            $estudianteId = $this->getEstudianteId();

            // Obtener métricas del estudiante
            $metricas = [
                'cursos_inscritos' => Inscripcion::where('estudiante_id', $estudianteId)->count(),
                'cursos_activos' => Inscripcion::where('estudiante_id', $estudianteId)
                    ->where('estado', 'activa')->count(),
                'progreso_general' => Inscripcion::where('estudiante_id', $estudianteId)
                    ->avg('progreso') ?? 0,
                'certificados_obtenidos' => Inscripcion::where('estudiante_id', $estudianteId)
                    ->where('progreso', '>=', 80)->count(),
                'libros_descargados' => BookDownload::where('usuario_id', $estudianteId)->count(),
            ];

            // Cursos actuales (inscritos y activos)
            $cursosActuales = Inscripcion::with(['curso.categoria'])
                ->where('estudiante_id', $estudianteId)
                ->where('estado', 'activa')
                ->orderBy('progreso', 'desc')
                ->limit(6)
                ->get();

            // Libros disponibles
            $librosDisponibles = Libro::where('es_publico', true)
                ->orderBy(Libro::CREATED_AT, 'desc')
                ->limit(8)
                ->get();

            // Actividad reciente
            $actividadReciente = Inscripcion::with('curso')
                ->where('estudiante_id', $estudianteId)
                ->orderBy(Inscripcion::UPDATED_AT, 'desc')
                ->limit(10)
                ->get();

            return view('estudiante.dashboard', compact(
                'metricas',
                'cursosActuales',
                'librosDisponibles',
                'actividadReciente'
            ));
            
        } catch (Exception $e) {
            return view('estudiante.dashboard', [
                'error' => $e->getMessage(),
                'metricas' => [],
                'cursosActuales' => [],
                'librosDisponibles' => [],
                'actividadReciente' => []
            ]);
        }
    }

    /**
     * Vista index para gestión de estudiantes (para administradores)
     */
    public function index(): View
    {
        try {
            $estudiantes = User::role('Estudiante')
                ->with(['inscripciones', 'bookDownloads'])
                ->orderBy(User::CREATED_AT, 'desc')
                ->paginate(20);
            
            return view('estudiantes.index', compact('estudiantes'));
            
        } catch (Exception $e) {
            return view('estudiantes.index', [
                'estudiantes' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * AJAX - Métricas actualizadas
     */
    public function ajaxMetricas(Request $request): JsonResponse
    {
        try {
            if (!$request->ajax()) {
                throw new Exception('Solo se permiten peticiones AJAX');
            }
            
            $estudianteId = $this->getEstudianteId();
            $tipo = $request->get('tipo', 'general');
            
            $data = [];
            
            switch ($tipo) {
                case 'cursos':
                    $data = [
                        'inscritos' => Inscripcion::where('estudiante_id', $estudianteId)->count(),
                        'activos' => Inscripcion::where('estudiante_id', $estudianteId)
                            ->where('estado', 'activa')->count(),
                        'completados' => Inscripcion::where('estudiante_id', $estudianteId)
                            ->where('progreso', '>=', 100)->count(),
                    ];
                    break;
                case 'progreso':
                    $data = [
                        'general' => Inscripcion::where('estudiante_id', $estudianteId)
                            ->avg('progreso') ?? 0,
                        'certificados' => Inscripcion::where('estudiante_id', $estudianteId)
                            ->where('progreso', '>=', 80)->count(),
                    ];
                    break;
                default:
                    $data = [
                        'cursos' => Inscripcion::where('estudiante_id', $estudianteId)->count(),
                        'progreso' => Inscripcion::where('estudiante_id', $estudianteId)
                            ->avg('progreso') ?? 0,
                        'libros' => BookDownload::where('usuario_id', $estudianteId)->count(),
                    ];
            }
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false, 
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lista de cursos inscritos
     */
    public function misCursos(): View
    {
        try {
            $estudianteId = $this->getEstudianteId();
            
            $cursos = Inscripcion::with(['curso.categoria', 'curso.docente'])
                ->where('estudiante_id', $estudianteId)
                ->orderBy('progreso', 'desc')
                ->paginate(12);
            
            return view('estudiante.cursos', compact('cursos'));
            
        } catch (Exception $e) {
            return view('estudiante.cursos', [
                'cursos' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Ver detalles de un curso
     */
    public function verCurso(int $id): View|RedirectResponse
    {
        try {
            $estudianteId = $this->getEstudianteId();
            
            // Verificar inscripción
            $inscripcion = Inscripcion::with(['curso.categoria', 'curso.docente', 'curso.materiales'])
                ->where('estudiante_id', $estudianteId)
                ->where('curso_id', $id)
                ->first();
            
            if (!$inscripcion) {
                return redirect()->route('estudiante.cursos')
                    ->with('error', 'No tienes acceso a este curso');
            }
            
            return view('estudiante.curso-detalle', compact('inscripcion'));
            
        } catch (Exception $e) {
            return redirect()->route('estudiante.cursos')
                ->with('error', 'Error al cargar curso: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar progreso de curso (AJAX)
     */
    public function actualizarProgreso(Request $request, int $id): JsonResponse
    {
        try {
            if (!$request->ajax()) {
                throw new Exception('Solo se permiten peticiones AJAX');
            }
            
            $estudianteId = $this->getEstudianteId();
            $progreso = (float) $request->input('progreso');
            
            if ($progreso < 0 || $progreso > 100) {
                throw new Exception('Progreso inválido. Debe estar entre 0 y 100');
            }
            
            $inscripcion = Inscripcion::where('estudiante_id', $estudianteId)
                ->where('curso_id', $id)
                ->first();
            
            if (!$inscripcion) {
                throw new Exception('No tienes acceso a este curso');
            }
            
            $inscripcion->update([
                'progreso' => $progreso,
                'ultima_actividad' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'mensaje' => 'Progreso actualizado correctamente',
                'progreso' => $progreso
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false, 
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Biblioteca de libros
     */
    public function libros(): View
    {
        try {
            $estudianteId = Auth::id();
            
            $libros = Libro::with('categoria')
                ->where('es_publico', true)
                ->orderBy(Libro::CREATED_AT, 'desc')
                ->paginate(12);
            
            // Obtener libros ya descargados por el usuario
            $librosDescargados = [];
            if ($estudianteId) {
                $librosDescargados = BookDownload::where('usuario_id', $estudianteId)
                    ->pluck('libro_id')
                    ->toArray();
            }
            
            return view('estudiante.libros', compact('libros', 'librosDescargados'));
            
        } catch (Exception $e) {
            return view('estudiante.libros', [
                'libros' => [],
                'librosDescargados' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Descargar libro
     */
    public function descargarLibro(int $id): RedirectResponse
    {
        try {
            $estudianteId = $this->getEstudianteId();
            
            $libro = Libro::findOrFail($id);
            
            if (!$libro->es_publico) {
                return redirect()->route('estudiante.libros')
                    ->with('error', 'Este libro no está disponible públicamente');
            }
            
            // Verificar si ya descargó el libro
            $yaDescargado = BookDownload::where('usuario_id', $estudianteId)
                ->where('libro_id', $id)
                ->exists();
            
            if (!$yaDescargado) {
                // Registrar descarga
                BookDownload::create([
                    'usuario_id' => $estudianteId,
                    'libro_id' => $id,
                    'ip_address' => request()->ip()
                ]);
            }
            
            // En una implementación real, aquí se devolvería el archivo
            return redirect()->route('estudiante.libros')
                ->with('success', 'Descarga iniciada correctamente');
            
        } catch (Exception $e) {
            return redirect()->route('estudiante.libros')
                ->with('error', 'Error en descarga: ' . $e->getMessage());
        }
    }

    /**
     * Ver progreso detallado
     */
    public function miProgreso(): View
    {
        try {
            $estudianteId = $this->getEstudianteId();
            
            $progreso = Inscripcion::with(['curso.categoria'])
                ->where('estudiante_id', $estudianteId)
                ->orderBy('progreso', 'desc')
                ->get();
            
            $metricas = [
                'progreso_general' => $progreso->avg('progreso') ?? 0,
                'cursos_completados' => $progreso->where('progreso', '>=', 100)->count(),
                'cursos_en_progreso' => $progreso->where('progreso', '>', 0)->where('progreso', '<', 100)->count(),
                'certificados' => $progreso->where('progreso', '>=', 80)->count(),
            ];
            
            return view('estudiante.progreso', compact('progreso', 'metricas'));
            
        } catch (Exception $e) {
            return view('estudiante.progreso', [
                'progreso' => [],
                'metricas' => [
                    'progreso_general' => 0,
                    'cursos_completados' => 0,
                    'cursos_en_progreso' => 0,
                    'certificados' => 0
                ],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Ver/editar perfil
     */
    public function perfil(): View
    {
        try {
            $usuario = Auth::user();
            
            // Estadísticas adicionales del perfil
            $estadisticas = [
                'total_inscripciones' => Inscripcion::where('estudiante_id', $usuario->id)->count(),
                'fecha_registro' => $usuario->{User::CREATED_AT}->format('d/m/Y'),
                'ultima_actividad' => Inscripcion::where('estudiante_id', $usuario->id)
                    ->max(Inscripcion::UPDATED_AT),
                'libros_descargados' => BookDownload::where('usuario_id', $usuario->id)->count(),
            ];
            
            return view('estudiante.perfil', compact('usuario', 'estadisticas'));
            
        } catch (Exception $e) {
            return view('estudiante.perfil', [
                'usuario' => Auth::user(),
                'estadisticas' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Actualizar perfil
     */
    public function actualizarPerfil(Request $request): RedirectResponse
    {
        try {
            $user = Auth::user();
            
            $request->validate([
                'nombre' => 'required|string|max:255',
                'apellido' => 'required|string|max:255',
                'telefono' => 'nullable|string|max:20',
            ]);
            
            $user->update($request->only(['nombre', 'apellido', 'telefono']));
            
            return redirect()->route('estudiante.perfil')
                ->with('success', 'Perfil actualizado correctamente');
            
        } catch (Exception $e) {
            return redirect()->route('estudiante.perfil')
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * AJAX - Obtener estadísticas detalladas
     */
    public function ajaxEstadisticas(Request $request): JsonResponse
    {
        try {
            if (!$request->ajax()) {
                throw new Exception('Solo se permiten peticiones AJAX');
            }
            
            $estudianteId = $this->getEstudianteId();
            
            $estadisticas = [
                'cursos_por_categoria' => DB::table('inscripciones')
                    ->join('cursos', 'inscripciones.curso_id', '=', 'cursos.id')
                    ->join('categorias', 'cursos.categoria_id', '=', 'categorias.id')
                    ->where('inscripciones.estudiante_id', $estudianteId)
                    ->select('categorias.nombre as categoria', DB::raw('count(*) as total'))
                    ->groupBy('categorias.nombre')
                    ->get(),
                
                'progreso_mensual' => Inscripcion::where('estudiante_id', $estudianteId)
                    ->selectRaw('MONTH(' . Inscripcion::UPDATED_AT . ') as mes, AVG(progreso) as promedio')
                    ->groupBy('mes')
                    ->orderBy('mes')
                    ->get(),
                
                'tiempo_estudio' => Inscripcion::where('estudiante_id', $estudianteId)
                    ->sum('tiempo_estudio') ?? 0,
            ];
            
            $alertas = [
                'cursos_sin_actividad' => Inscripcion::where('estudiante_id', $estudianteId)
                    ->where('ultima_actividad', '<', now()->subWeeks(2))
                    ->where('progreso', '<', 100)
                    ->count(),
            ];
            
            return response()->json([
                'success' => true,
                'data' => $estadisticas,
                'alertas' => $alertas
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false, 
                'error' => $e->getMessage()
            ], 500);
        }
    }
}