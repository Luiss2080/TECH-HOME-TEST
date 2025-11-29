<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Curso;
use App\Models\Categoria;
use App\Models\Enrollment;
use App\Models\Componente;
use App\Models\Material;
use App\Models\BookDownload;
use App\Models\Libro;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;

class EstudianteController extends Controller
{
    /**
     * Dashboard principal del estudiante
     */
    public function dashboard()
    {
        try {
            $estudiante = Auth::user();
            
            if (!$estudiante->hasRole('estudiante')) {
                $request->session()->flash('error', 'Acceso no autorizado.');
                return redirect('/login');
            }

            // Estadísticas del estudiante
            $estadisticas = [
                'cursos_inscritos' => $estudiante->inscripciones()->count(),
                'cursos_completados' => $estudiante->inscripciones()
                                                 ->where('progreso', 100)
                                                 ->count(),
                'libros_descargados' => BookDownload::where('usuario_id', $estudiante->id)
                                                  ->count(),
                'cursos_recientes' => $estudiante->inscripciones()
                                                ->with(['curso.categoria'])
                                                ->orderBy('fecha_inscripcion', 'desc')
                                                ->limit(5)
                                                ->get()
            ];

            return view('estudiante.dashboard', compact('estudiante', 'estadisticas'));
            
        } catch (Exception $e) {
            return view('estudiante.dashboard')->withErrors(['error' => 'Error al cargar dashboard: ' . $e->getMessage()]);
        }
    }

    /**
     * Mis cursos inscritos
     */
    public function misCursos(Request $request)
    {
        try {
            $estudiante = Auth::user();
            
            $query = $estudiante->inscripciones()
                               ->with(['curso.categoria', 'curso.docente']);

            // Filtros
            if ($request->filled('estado')) {
                if ($request->estado === 'completado') {
                    $query->where('progreso', 100);
                } elseif ($request->estado === 'en_progreso') {
                    $query->where('progreso', '>', 0)
                          ->where('progreso', '<', 100);
                } elseif ($request->estado === 'sin_iniciar') {
                    $query->where('progreso', 0);
                }
            }

            if ($request->filled('categoria_id')) {
                $query->whereHas('curso', function($q) use ($request) {
                    $q->where('categoria_id', $request->categoria_id);
                });
            }

            if ($request->filled('buscar')) {
                $buscar = $request->buscar;
                $query->whereHas('curso', function($q) use ($buscar) {
                    $q->where('titulo', 'like', "%{$buscar}%")
                      ->orWhere('descripcion', 'like', "%{$buscar}%");
                });
            }

            $inscripciones = $query->orderBy('fecha_inscripcion', 'desc')
                                 ->paginate(12);

            $categorias = Categoria::orderBy('nombre')->get();

            return view('estudiante.cursos.index', compact('inscripciones', 'categorias'));

        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar cursos: ' . $e->getMessage()]);
        }
    }

    /**
     * Ver curso específico
     */
    public function verCurso($id)
    {
        try {
            $estudiante = Auth::user();
            
            // Verificar inscripción
            $inscripcion = Enrollment::with(['curso.categoria', 'curso.docente'])
                                   ->where('curso_id', $id)
                                   ->where('usuario_id', $estudiante->id)
                                   ->firstOrFail();

            $curso = $inscripcion->curso;

            // Obtener componentes del curso en orden
            $componentes = Componente::where('curso_id', $id)
                                   ->orderBy('orden')
                                   ->get();

            // Materiales del curso
            $materiales = Material::where('curso_id', $id)
                                ->orderBy('nombre')
                                ->get();

            return view('estudiante.cursos.ver', compact('inscripcion', 'curso', 'componentes', 'materiales'));

        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar curso: ' . $e->getMessage()]);
        }
    }

    /**
     * Actualizar progreso en el curso
     */
    public function actualizarProgreso(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'componente_id' => 'required|exists:components,id',
            'completado' => 'required|boolean'
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
            $estudiante = Auth::user();
            
            // Verificar inscripción
            $inscripcion = Enrollment::where('curso_id', $id)
                                   ->where('usuario_id', $estudiante->id)
                                   ->firstOrFail();

            $componenteId = $request->componente_id;
            $completado = $request->boolean('completado');

            // Obtener progreso actual
            $progresoActual = json_decode($inscripcion->progreso_detallado ?? '[]', true);

            if ($completado) {
                $progresoActual[$componenteId] = true;
            } else {
                unset($progresoActual[$componenteId]);
            }

            // Calcular progreso general
            $totalComponentes = Componente::where('curso_id', $id)->count();
            $componentesCompletados = count($progresoActual);
            $porcentajeProgreso = $totalComponentes > 0 ? ($componentesCompletados / $totalComponentes) * 100 : 0;

            // Actualizar inscripción
            $inscripcion->update([
                'progreso_detallado' => json_encode($progresoActual),
                'progreso' => round($porcentajeProgreso, 2),
                'fecha_ultimo_acceso' => now()
            ]);

            $message = 'Progreso actualizado exitosamente.';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'progreso' => round($porcentajeProgreso, 2)
                ]);
            }

            return back()->with('success', $message);

        } catch (Exception $e) {
            $error = 'Error al actualizar progreso: ' . $e->getMessage();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $error], 500);
            }
            return back()->withErrors(['error' => $error]);
        }
    }

    /**
     * Catalogo de cursos disponibles
     */
    public function catalogo(Request $request)
    {
        try {
            $estudiante = Auth::user();
            
            $query = Curso::with(['categoria', 'docente'])
                         ->where('estado', 'activo');

            // Excluir cursos ya inscritos
            $cursosInscritos = $estudiante->inscripciones()->pluck('curso_id')->toArray();
            if (!empty($cursosInscritos)) {
                $query->whereNotIn('id', $cursosInscritos);
            }

            // Filtros
            if ($request->filled('categoria_id')) {
                $query->where('categoria_id', $request->categoria_id);
            }

            if ($request->filled('nivel')) {
                $query->where('nivel', $request->nivel);
            }

            if ($request->filled('precio_min')) {
                $query->where('precio', '>=', $request->precio_min);
            }

            if ($request->filled('precio_max')) {
                $query->where('precio', '<=', $request->precio_max);
            }

            if ($request->filled('buscar')) {
                $buscar = $request->buscar;
                $query->where(function($q) use ($buscar) {
                    $q->where('titulo', 'like', "%{$buscar}%")
                      ->orWhere('descripcion', 'like', "%{$buscar}%");
                });
            }

            // Ordenamiento
            $ordenar = $request->get('ordenar', 'reciente');
            switch ($ordenar) {
                case 'precio_asc':
                    $query->orderBy('precio', 'asc');
                    break;
                case 'precio_desc':
                    $query->orderBy('precio', 'desc');
                    break;
                case 'titulo':
                    $query->orderBy('titulo', 'asc');
                    break;
                default:
                    $query->orderBy('creado_en', 'desc');
            }

            $cursos = $query->paginate(12);

            $categorias = Categoria::orderBy('nombre')->get();

            return view('estudiante.catalogo', compact('cursos', 'categorias'));

        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar catálogo: ' . $e->getMessage()]);
        }
    }

    /**
     * Ver detalles de curso para inscribirse
     */
    public function detalleCurso($id)
    {
        try {
            $estudiante = Auth::user();
            
            $curso = Curso::with(['categoria', 'docente', 'componentes' => function($query) {
                $query->orderBy('orden');
            }])->where('estado', 'activo')
              ->findOrFail($id);

            // Verificar si ya está inscrito
            $yaInscrito = $estudiante->inscripciones()
                                   ->where('curso_id', $id)
                                   ->exists();

            return view('estudiante.cursos.detalle', compact('curso', 'yaInscrito'));

        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar detalles del curso: ' . $e->getMessage()]);
        }
    }

    /**
     * Inscribirse en un curso
     */
    public function inscribirse(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $estudiante = Auth::user();
            
            $curso = Curso::where('estado', 'activo')->findOrFail($id);

            // Verificar si ya está inscrito
            $yaInscrito = $estudiante->inscripciones()
                                   ->where('curso_id', $id)
                                   ->exists();

            if ($yaInscrito) {
                $error = 'Ya estás inscrito en este curso.';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $error], 400);
                }
                return back()->withErrors(['error' => $error]);
            }

            // Crear inscripción
            Enrollment::create([
                'usuario_id' => $estudiante->id,
                'curso_id' => $curso->id,
                'fecha_inscripcion' => now(),
                'progreso' => 0,
                'estado' => 'activo'
            ]);

            DB::commit();

            $message = 'Te has inscrito exitosamente en el curso.';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return redirect('/estudiante/cursos/' . $id)
                          ->with('success', $message);

        } catch (Exception $e) {
            DB::rollBack();
            
            $error = 'Error al inscribirse en el curso: ' . $e->getMessage();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $error], 500);
            }
            return back()->withErrors(['error' => $error]);
        }
    }

    /**
     * Mi biblioteca de libros
     */
    public function biblioteca(Request $request)
    {
        try {
            $estudiante = Auth::user();
            
            $query = BookDownload::with(['libro.categoria'])
                                ->where('usuario_id', $estudiante->id);

            // Filtros
            if ($request->filled('categoria_id')) {
                $query->whereHas('libro', function($q) use ($request) {
                    $q->where('categoria_id', $request->categoria_id);
                });
            }

            if ($request->filled('buscar')) {
                $buscar = $request->buscar;
                $query->whereHas('libro', function($q) use ($buscar) {
                    $q->where('titulo', 'like', "%{$buscar}%")
                      ->orWhere('autor', 'like', "%{$buscar}%");
                });
            }

            $descargas = $query->orderBy('fecha_descarga', 'desc')
                             ->paginate(12);

            $categorias = Categoria::orderBy('nombre')->get();

            return view('estudiante.biblioteca', compact('descargas', 'categorias'));

        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar biblioteca: ' . $e->getMessage()]);
        }
    }

    /**
     * Perfil del estudiante
     */
    public function perfil()
    {
        try {
            $estudiante = Auth::user();
            
            // Estadísticas del perfil
            $estadisticas = [
                'cursos_completados' => $estudiante->inscripciones()
                                                 ->where('progreso', 100)
                                                 ->count(),
                'horas_estudiadas' => $estudiante->inscripciones()
                                                ->with('curso')
                                                ->get()
                                                ->sum('curso.duracion_horas'),
                'certificados' => 0, // Por implementar
                'puntos' => 0 // Por implementar
            ];

            return view('estudiante.perfil', compact('estudiante', 'estadisticas'));

        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar perfil: ' . $e->getMessage()]);
        }
    }

    /**
     * Actualizar perfil
     */
    public function actualizarPerfil(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500'
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
            $estudiante = Auth::user();
            
            $estudiante->update([
                'nombre' => $request->nombre,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion
            ]);

            $message = 'Perfil actualizado exitosamente.';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return back()->with('success', $message);

        } catch (Exception $e) {
            $error = 'Error al actualizar perfil: ' . $e->getMessage();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $error], 500);
            }
            return back()->withErrors(['error' => $error])->withInput();
        }
    }
}