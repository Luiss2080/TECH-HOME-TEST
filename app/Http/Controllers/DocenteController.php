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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class DocenteController extends Controller
{
    /**
     * Dashboard principal del docente
     */
    public function dashboard()
    {
        try {
            $docente = Auth::user();
            
            if (!$docente->hasRole('docente')) {
                $request->session()->flash('error', 'Acceso no autorizado.');
                return redirect('/login');
            }

            // Estadísticas del docente
            $estadisticas = [
                'total_cursos' => Curso::where('docente_id', $docente->id)->count(),
                'cursos_activos' => Curso::where('docente_id', $docente->id)
                                       ->where('estado', 'activo')
                                       ->count(),
                'total_estudiantes' => Enrollment::whereHas('curso', function($query) use ($docente) {
                                         $query->where('docente_id', $docente->id);
                                       })->distinct('usuario_id')->count(),
                'cursos_recientes' => Curso::where('docente_id', $docente->id)
                                          ->orderBy('creado_en', 'desc')
                                          ->limit(5)
                                          ->get()
            ];

            return view('docente.dashboard', compact('docente', 'estadisticas'));
            
        } catch (Exception $e) {
            return view('docente.dashboard')->withErrors(['error' => 'Error al cargar dashboard: ' . $e->getMessage()]);
        }
    }

    /**
     * Listar cursos del docente
     */
    public function cursos(Request $request)
    {
        try {
            $docente = Auth::user();
            
            $query = Curso::where('docente_id', $docente->id)
                         ->with(['categoria', 'enrollments']);

            // Filtros
            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            }

            if ($request->filled('categoria_id')) {
                $query->where('categoria_id', $request->categoria_id);
            }

            if ($request->filled('buscar')) {
                $buscar = $request->buscar;
                $query->where(function($q) use ($buscar) {
                    $q->where('titulo', 'like', "%{$buscar}%")
                      ->orWhere('descripcion', 'like', "%{$buscar}%");
                });
            }

            $cursos = $query->orderBy('creado_en', 'desc')
                           ->paginate(12);

            $categorias = Categoria::orderBy('nombre')->get();

            return view('docente.cursos.index', compact('cursos', 'categorias'));

        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar cursos: ' . $e->getMessage()]);
        }
    }

    /**
     * Mostrar formulario para crear curso
     */
    public function crearCurso()
    {
        try {
            $categorias = Categoria::orderBy('nombre')->get();
            return view('docente.cursos.crear', compact('categorias'));
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar formulario: ' . $e->getMessage()]);
        }
    }

    /**
     * Guardar nuevo curso
     */
    public function guardarCurso(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'categoria_id' => 'required|exists:categories,id',
            'duracion_horas' => 'required|integer|min:1',
            'nivel' => 'required|in:principiante,intermedio,avanzado',
            'precio' => 'required|numeric|min:0',
            'imagen' => 'nullable|image|max:2048',
            'requisitos' => 'nullable|string',
            'objetivos' => 'nullable|string'
        ], [
            'titulo.required' => 'El título es obligatorio',
            'descripcion.required' => 'La descripción es obligatoria',
            'categoria_id.required' => 'La categoría es obligatoria',
            'duracion_horas.required' => 'La duración es obligatoria',
            'duracion_horas.min' => 'La duración debe ser al menos 1 hora',
            'nivel.required' => 'El nivel es obligatorio',
            'precio.required' => 'El precio es obligatorio'
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

            $docente = Auth::user();
            
            // Preparar datos del curso
            $cursoData = [
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'categoria_id' => $request->categoria_id,
                'docente_id' => $docente->id,
                'duracion_horas' => $request->duracion_horas,
                'nivel' => $request->nivel,
                'precio' => $request->precio,
                'requisitos' => $request->requisitos,
                'objetivos' => $request->objetivos,
                'estado' => 'borrador',
                'slug' => Str::slug($request->titulo),
                'creado_en' => now()
            ];

            // Manejar imagen
            if ($request->hasFile('imagen')) {
                $imagen = $request->file('imagen');
                $nombreImagen = time() . '_' . Str::slug($request->titulo) . '.' . $imagen->getClientOriginalExtension();
                
                $path = $imagen->storeAs('cursos/imagenes', $nombreImagen, 'public');
                $cursoData['imagen'] = $path;
            }

            $curso = Curso::create($cursoData);

            DB::commit();

            $message = 'Curso creado exitosamente.';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'curso_id' => $curso->id
                ]);
            }

            return redirect('/docente/cursos/' . $curso->id . '/edit')
                          ->with('success', $message);

        } catch (Exception $e) {
            DB::rollBack();
            
            $error = 'Error al crear el curso: ' . $e->getMessage();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $error], 500);
            }
            return back()->withErrors(['error' => $error])->withInput();
        }
    }

    /**
     * Mostrar curso específico
     */
    public function verCurso($id)
    {
        try {
            $docente = Auth::user();
            
            $curso = Curso::with(['categoria', 'componentes' => function($query) {
                $query->orderBy('orden');
            }, 'materiales'])
            ->where('id', $id)
            ->where('docente_id', $docente->id)
            ->firstOrFail();

            $estadisticas = [
                'total_estudiantes' => $curso->enrollments()->count(),
                'total_componentes' => $curso->componentes()->count(),
                'total_materiales' => $curso->materiales()->count(),
                'progreso_promedio' => $curso->enrollments()
                                           ->avg('progreso') ?? 0
            ];

            return view('docente.cursos.ver', compact('curso', 'estadisticas'));

        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar curso: ' . $e->getMessage()]);
        }
    }

    /**
     * Mostrar formulario para editar curso
     */
    public function editarCurso($id)
    {
        try {
            $docente = Auth::user();
            
            $curso = Curso::where('id', $id)
                         ->where('docente_id', $docente->id)
                         ->firstOrFail();

            $categorias = Categoria::orderBy('nombre')->get();

            return view('docente.cursos.editar', compact('curso', 'categorias'));

        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar curso: ' . $e->getMessage()]);
        }
    }

    /**
     * Actualizar curso
     */
    public function actualizarCurso(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'categoria_id' => 'required|exists:categories,id',
            'duracion_horas' => 'required|integer|min:1',
            'nivel' => 'required|in:principiante,intermedio,avanzado',
            'precio' => 'required|numeric|min:0',
            'imagen' => 'nullable|image|max:2048',
            'requisitos' => 'nullable|string',
            'objetivos' => 'nullable|string',
            'estado' => 'required|in:borrador,activo,inactivo'
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

            $docente = Auth::user();
            
            $curso = Curso::where('id', $id)
                         ->where('docente_id', $docente->id)
                         ->firstOrFail();

            // Preparar datos actualizados
            $cursoData = [
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'categoria_id' => $request->categoria_id,
                'duracion_horas' => $request->duracion_horas,
                'nivel' => $request->nivel,
                'precio' => $request->precio,
                'requisitos' => $request->requisitos,
                'objetivos' => $request->objetivos,
                'estado' => $request->estado,
                'slug' => Str::slug($request->titulo),
                'actualizado_en' => now()
            ];

            // Manejar nueva imagen
            if ($request->hasFile('imagen')) {
                // Eliminar imagen anterior si existe
                if ($curso->imagen && Storage::disk('public')->exists($curso->imagen)) {
                    Storage::disk('public')->delete($curso->imagen);
                }

                $imagen = $request->file('imagen');
                $nombreImagen = time() . '_' . Str::slug($request->titulo) . '.' . $imagen->getClientOriginalExtension();
                
                $path = $imagen->storeAs('cursos/imagenes', $nombreImagen, 'public');
                $cursoData['imagen'] = $path;
            }

            $curso->update($cursoData);

            DB::commit();

            $message = 'Curso actualizado exitosamente.';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return back()->with('success', $message);

        } catch (Exception $e) {
            DB::rollBack();
            
            $error = 'Error al actualizar el curso: ' . $e->getMessage();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $error], 500);
            }
            return back()->withErrors(['error' => $error])->withInput();
        }
    }

    /**
     * Eliminar curso
     */
    public function eliminarCurso(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $docente = Auth::user();
            
            $curso = Curso::where('id', $id)
                         ->where('docente_id', $docente->id)
                         ->firstOrFail();

            // Verificar si tiene estudiantes inscritos
            if ($curso->enrollments()->count() > 0) {
                $error = 'No se puede eliminar el curso porque tiene estudiantes inscritos.';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $error], 400);
                }
                return back()->withErrors(['error' => $error]);
            }

            // Eliminar imagen si existe
            if ($curso->imagen && Storage::disk('public')->exists($curso->imagen)) {
                Storage::disk('public')->delete($curso->imagen);
            }

            // Eliminar materiales asociados
            foreach ($curso->materiales as $material) {
                if ($material->archivo && Storage::disk('public')->exists($material->archivo)) {
                    Storage::disk('public')->delete($material->archivo);
                }
            }

            $curso->delete();

            DB::commit();

            $message = 'Curso eliminado exitosamente.';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return redirect('/docente/cursos')->with('success', $message);

        } catch (Exception $e) {
            DB::rollBack();
            
            $error = 'Error al eliminar el curso: ' . $e->getMessage();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $error], 500);
            }
            return back()->withErrors(['error' => $error]);
        }
    }

    /**
     * Gestionar componentes del curso
     */
    public function componentes($cursoId)
    {
        try {
            $docente = Auth::user();
            
            $curso = Curso::where('id', $cursoId)
                         ->where('docente_id', $docente->id)
                         ->firstOrFail();

            $componentes = Componente::where('curso_id', $cursoId)
                                   ->orderBy('orden')
                                   ->get();

            return view('docente.cursos.componentes', compact('curso', 'componentes'));

        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar componentes: ' . $e->getMessage()]);
        }
    }

    /**
     * Gestionar estudiantes inscritos
     */
    public function estudiantes($cursoId)
    {
        try {
            $docente = Auth::user();
            
            $curso = Curso::where('id', $cursoId)
                         ->where('docente_id', $docente->id)
                         ->firstOrFail();

            $inscripciones = Enrollment::with(['usuario'])
                                     ->where('curso_id', $cursoId)
                                     ->orderBy('fecha_inscripcion', 'desc')
                                     ->paginate(20);

            return view('docente.cursos.estudiantes', compact('curso', 'inscripciones'));

        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar estudiantes: ' . $e->getMessage()]);
        }
    }

    /**
     * Ver progreso de estudiante específico
     */
    public function progresoEstudiante($cursoId, $usuarioId)
    {
        try {
            $docente = Auth::user();
            
            $curso = Curso::where('id', $cursoId)
                         ->where('docente_id', $docente->id)
                         ->firstOrFail();

            $inscripcion = Enrollment::with(['usuario'])
                                   ->where('curso_id', $cursoId)
                                   ->where('usuario_id', $usuarioId)
                                   ->firstOrFail();

            // Obtener progreso detallado por componentes
            $componentes = Componente::where('curso_id', $cursoId)
                                   ->orderBy('orden')
                                   ->get();

            return view('docente.estudiantes.progreso', compact('curso', 'inscripcion', 'componentes'));

        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar progreso: ' . $e->getMessage()]);
        }
    }
}