<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\Curso;
use App\Models\Categoria;
use App\Models\Material;
use App\Models\Inscripcion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;

class DocenteController extends Controller
{
    /**
     * Obtener ID del docente autenticado con validación
     */
    private function getDocenteId(): int
    {
        $user = Auth::user();
        
        if (!$user) {
            throw new Exception('Usuario no autenticado o sesión expirada');
        }
        
        if (!$user->hasRole('Docente')) {
            throw new Exception('No tienes permisos de docente');
        }
        
        return $user->id;
    }

    /**
     * Dashboard principal del docente
     */
    public function dashboard(): View
    {
        try {
            $docenteId = $this->getDocenteId();
            
            // Obtener métricas del docente
            $metricas = [
                'estudiantes_totales' => Inscripcion::whereHas('curso', function($query) use ($docenteId) {
                    $query->where('docente_id', $docenteId);
                })->distinct('estudiante_id')->count(),
                'estudiantes_activos' => Inscripcion::whereHas('curso', function($query) use ($docenteId) {
                    $query->where('docente_id', $docenteId);
                })->where('estado', 'activa')->distinct('estudiante_id')->count(),
                'cursos_creados' => Curso::where('docente_id', $docenteId)->count(),
                'cursos_activos' => Curso::where('docente_id', $docenteId)->where('estado', 'Publicado')->count(),
                'materiales_subidos' => Material::where('docente_id', $docenteId)->count(),
                'materiales_mes' => Material::where('docente_id', $docenteId)
                    ->whereMonth(Material::CREATED_AT, now()->month)->count(),
                'progreso_promedio' => Inscripcion::whereHas('curso', function($query) use ($docenteId) {
                    $query->where('docente_id', $docenteId);
                })->avg('progreso') ?? 0,
            ];

            // Cursos recientes
            $cursosRecientes = Curso::where('docente_id', $docenteId)
                ->orderBy(Curso::CREATED_AT, 'desc')
                ->limit(5)
                ->get();

            // Estudiantes recientes
            $estudiantesRecientes = Inscripcion::with(['estudiante', 'curso'])
                ->whereHas('curso', function($query) use ($docenteId) {
                    $query->where('docente_id', $docenteId);
                })
                ->orderBy(Inscripcion::CREATED_AT, 'desc')
                ->limit(10)
                ->get();

            // Materiales recientes
            $materialesRecientes = Material::where('docente_id', $docenteId)
                ->orderBy(Material::CREATED_AT, 'desc')
                ->limit(5)
                ->get();

            return view('docente.dashboard', compact(
                'metricas',
                'cursosRecientes',
                'estudiantesRecientes', 
                'materialesRecientes'
            ));
            
        } catch (Exception $e) {
            return view('docente.dashboard', [
                'error' => $e->getMessage(),
                'metricas' => [],
                'cursosRecientes' => [],
                'estudiantesRecientes' => [],
                'materialesRecientes' => []
            ]);
        }
    }

    /**
     * AJAX para obtener métricas actualizadas
     */
    public function ajaxMetricas(Request $request): JsonResponse
    {
        try {
            if (!$request->ajax()) {
                throw new Exception('Solo se permiten peticiones AJAX');
            }

            $docenteId = $this->getDocenteId();
            $tipo = $request->get('tipo', 'general');
            
            $data = [];
            
            switch ($tipo) {
                case 'estudiantes':
                    $data = [
                        'total' => Inscripcion::whereHas('curso', function($query) use ($docenteId) {
                            $query->where('docente_id', $docenteId);
                        })->distinct('estudiante_id')->count(),
                        'activos' => Inscripcion::whereHas('curso', function($query) use ($docenteId) {
                            $query->where('docente_id', $docenteId);
                        })->where('estado', 'activa')->distinct('estudiante_id')->count(),
                    ];
                    break;
                case 'cursos':
                    $data = [
                        'total' => Curso::where('docente_id', $docenteId)->count(),
                        'publicados' => Curso::where('docente_id', $docenteId)->where('estado', 'Publicado')->count(),
                        'borradores' => Curso::where('docente_id', $docenteId)->where('estado', 'Borrador')->count(),
                    ];
                    break;
                default:
                    $data = [
                        'estudiantes' => Inscripcion::whereHas('curso', function($query) use ($docenteId) {
                            $query->where('docente_id', $docenteId);
                        })->distinct('estudiante_id')->count(),
                        'cursos' => Curso::where('docente_id', $docenteId)->count(),
                        'materiales' => Material::where('docente_id', $docenteId)->count(),
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

    // =========================================
    // GESTIÓN DE CURSOS
    // =========================================

    /**
     * Lista de cursos del docente
     */
    public function cursos(): View
    {
        try {
            $docenteId = $this->getDocenteId();
            
            $cursos = Curso::with('categoria')
                ->where('docente_id', $docenteId)
                ->orderBy(Curso::CREATED_AT, 'desc')
                ->paginate(10);
            
            return view('docente.cursos.index', compact('cursos'));
            
        } catch (Exception $e) {
            return view('docente.cursos.index', [
                'cursos' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Formulario para crear nuevo curso
     */
    public function crearCurso(): View
    {
        try {
            $categorias = Categoria::orderBy('nombre')->get();
            
            return view('docente.cursos.crear', compact('categorias'));
            
        } catch (Exception $e) {
            return redirect()->route('docente.cursos')
                ->with('error', 'Error al cargar formulario: ' . $e->getMessage());
        }
    }

    /**
     * Guardar nuevo curso
     */
    public function guardarCurso(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'titulo' => 'required|min:5|max:200',
                'descripcion' => 'required|min:20|max:1000',
                'categoria_id' => 'required|exists:categorias,id',
                'nivel' => 'required|in:Principiante,Intermedio,Avanzado',
                'duracion_estimada' => 'required|numeric|min:1',
                'precio' => 'nullable|numeric|min:0',
                'es_gratuito' => 'boolean',
                'video_url' => 'nullable|url'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $docenteId = $this->getDocenteId();
            
            Curso::create(array_merge($request->all(), [
                'docente_id' => $docenteId,
                'estado' => 'Borrador'
            ]));

            return redirect()->route('docente.cursos')
                ->with('success', 'Curso creado exitosamente.');
            
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al crear curso: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Ver detalles de un curso específico del docente
     */
    public function verCurso(int $id): View|RedirectResponse
    {
        try {
            $docenteId = $this->getDocenteId();
            
            $curso = Curso::with(['categoria', 'materiales', 'inscripciones.estudiante'])
                ->where('id', $id)
                ->where('docente_id', $docenteId)
                ->firstOrFail();
            
            return view('docente.cursos.ver', compact('curso'));
            
        } catch (Exception $e) {
            return redirect()->route('docente.cursos')
                ->with('error', 'Curso no encontrado o no tienes permisos para verlo.');
        }
    }

    /**
     * Mostrar formulario de edición de curso
     */
    public function editarCurso(int $id): View|RedirectResponse
    {
        try {
            $docenteId = $this->getDocenteId();
            
            $curso = Curso::where('id', $id)
                ->where('docente_id', $docenteId)
                ->firstOrFail();
            
            $categorias = Categoria::orderBy('nombre')->get();
            
            return view('docente.cursos.editar', compact('curso', 'categorias'));
            
        } catch (Exception $e) {
            return redirect()->route('docente.cursos')
                ->with('error', 'Curso no encontrado o no tienes permisos para editarlo.');
        }
    }

    /**
     * Actualizar curso del docente
     */
    public function actualizarCurso(Request $request, int $id): RedirectResponse
    {
        try {
            $docenteId = $this->getDocenteId();
            
            $curso = Curso::where('id', $id)
                ->where('docente_id', $docenteId)
                ->firstOrFail();
            
            $validator = Validator::make($request->all(), [
                'titulo' => 'required|min:5|max:200',
                'descripcion' => 'required|min:20|max:1000',
                'categoria_id' => 'required|exists:categorias,id',
                'nivel' => 'required|in:Principiante,Intermedio,Avanzado',
                'estado' => 'required|in:Borrador,Publicado,Archivado',
                'video_url' => 'nullable|url'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $curso->update($request->all());
            
            return redirect()->route('docente.cursos.ver', $id)
                ->with('success', 'Curso actualizado exitosamente.');
            
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar curso: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Eliminar curso del docente
     */
    public function eliminarCurso(int $id): RedirectResponse
    {
        try {
            $docenteId = $this->getDocenteId();
            
            $curso = Curso::where('id', $id)
                ->where('docente_id', $docenteId)
                ->firstOrFail();
            
            // Verificar si tiene inscripciones activas
            $inscripcionesActivas = $curso->inscripciones()->where('estado', 'activa')->count();
            
            if ($inscripcionesActivas > 0) {
                return redirect()->route('docente.cursos')
                    ->with('error', 'No puedes eliminar un curso con inscripciones activas.');
            }
            
            $curso->delete();
            
            return redirect()->route('docente.cursos')
                ->with('success', 'Curso eliminado exitosamente.');
            
        } catch (Exception $e) {
            return redirect()->route('docente.cursos')
                ->with('error', 'Error al eliminar curso: ' . $e->getMessage());
        }
    }

    // =========================================
    // GESTIÓN DE ESTUDIANTES
    // =========================================

    /**
     * Lista de estudiantes en cursos del docente
     */
    public function estudiantes(): View
    {
        try {
            $docenteId = $this->getDocenteId();
            
            $estudiantes = Inscripcion::with(['estudiante', 'curso'])
                ->whereHas('curso', function($query) use ($docenteId) {
                    $query->where('docente_id', $docenteId);
                })
                ->orderBy(Inscripcion::CREATED_AT, 'desc')
                ->paginate(15);
            
            return view('docente.estudiantes.index', compact('estudiantes'));
            
        } catch (Exception $e) {
            return view('docente.estudiantes.index', [
                'estudiantes' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    // =========================================
    // GESTIÓN DE MATERIALES
    // =========================================

    /**
     * Lista de materiales del docente
     */
    public function materiales(): View
    {
        try {
            $docenteId = $this->getDocenteId();
            
            $materiales = Material::with('curso')
                ->where('docente_id', $docenteId)
                ->orderBy(Material::CREATED_AT, 'desc')
                ->paginate(10);
            
            return view('docente.materiales.index', compact('materiales'));
            
        } catch (Exception $e) {
            return view('docente.materiales.index', [
                'materiales' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Subir nuevo material educativo
     */
    public function subirMaterial(): View
    {
        try {
            $docenteId = $this->getDocenteId();
            
            $cursos = Curso::where('docente_id', $docenteId)->get();
            $categorias = Categoria::orderBy('nombre')->get();
            
            return view('docente.materiales.subir', compact('cursos', 'categorias'));
            
        } catch (Exception $e) {
            return redirect()->route('docente.materiales')
                ->with('error', 'Error al cargar formulario: ' . $e->getMessage());
        }
    }

    /**
     * Guardar nuevo material
     */
    public function guardarMaterial(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'titulo' => 'required|min:5|max:200',
                'descripcion' => 'required|min:10|max:500',
                'tipo' => 'required|in:pdf,video,codigo,guia,dataset',
                'curso_id' => 'required|exists:cursos,id',
                'archivo' => 'required|file|max:10240', // 10MB
                'es_publico' => 'boolean'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $docenteId = $this->getDocenteId();
            
            // Verificar que el curso pertenece al docente
            $curso = Curso::where('id', $request->curso_id)
                ->where('docente_id', $docenteId)
                ->firstOrFail();
            
            $archivo = null;
            $tamaño = 0;
            
            if ($request->hasFile('archivo')) {
                $file = $request->file('archivo');
                $archivo = $file->store('materiales', 'public');
                $tamaño = $file->getSize();
            }
            
            Material::create(array_merge($request->all(), [
                'docente_id' => $docenteId,
                'archivo' => $archivo,
                'tamaño_archivo' => $tamaño
            ]));

            return redirect()->route('docente.materiales')
                ->with('success', 'Material subido exitosamente.');
            
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al subir material: ' . $e->getMessage())
                ->withInput();
        }
    }

    // =========================================
    // ESTADÍSTICAS Y REPORTES
    // =========================================

    /**
     * Estadísticas detalladas
     */
    public function estadisticas(): View
    {
        try {
            $docenteId = $this->getDocenteId();
            
            $estadisticas = [
                'cursos_por_categoria' => DB::table('cursos')
                    ->join('categorias', 'cursos.categoria_id', '=', 'categorias.id')
                    ->where('cursos.docente_id', $docenteId)
                    ->select('categorias.nombre as categoria', DB::raw('count(*) as total'))
                    ->groupBy('categorias.nombre')
                    ->get(),
                
                'inscripciones_mes' => Inscripcion::whereHas('curso', function($query) use ($docenteId) {
                        $query->where('docente_id', $docenteId);
                    })
                    ->whereMonth(Inscripcion::CREATED_AT, now()->month)
                    ->count(),
                
                'progreso_promedio' => Inscripcion::whereHas('curso', function($query) use ($docenteId) {
                        $query->where('docente_id', $docenteId);
                    })
                    ->avg('progreso') ?? 0,
            ];
            
            return view('docente.reportes.estadisticas', compact('estadisticas'));
            
        } catch (Exception $e) {
            return view('docente.reportes.estadisticas', [
                'estadisticas' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Progreso de estudiantes
     */
    public function progreso(): View
    {
        try {
            $docenteId = $this->getDocenteId();
            
            $progreso = Inscripcion::with(['estudiante', 'curso'])
                ->whereHas('curso', function($query) use ($docenteId) {
                    $query->where('docente_id', $docenteId);
                })
                ->orderBy('progreso', 'desc')
                ->paginate(20);
            
            return view('docente.reportes.progreso', compact('progreso'));
            
        } catch (Exception $e) {
            return view('docente.reportes.progreso', [
                'progreso' => [],
                'error' => $e->getMessage()
            ]);
        }
    }
}