<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\Categoria;
use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Exception;

class CursoController extends Controller
{
    /**
     * Muestra la lista de cursos según el rol del usuario
     */
    public function cursos(): View
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return redirect()->route('login');
            }

            if ($user->hasRole('docente')) {
                // Los docentes solo ven sus cursos
                $cursos = Curso::where('docente_id', $user->id)
                             ->with(['categoria', 'docente'])
                             ->latest()
                             ->paginate(10);
            } else {
                // Los administradores ven todos los cursos
                $cursos = Curso::with(['categoria', 'docente'])
                             ->latest()
                             ->paginate(10);
            }

            $estadisticas = [
                'total_cursos' => Curso::count(),
                'cursos_activos' => Curso::where('estado', 'activo')->count(),
                'total_estudiantes' => Enrollment::distinct('user_id')->count(),
                'cursos_populares' => Curso::withCount('inscripciones')
                                         ->orderBy('inscripciones_count', 'desc')
                                         ->take(5)
                                         ->get()
            ];

            return view('admin.cursos.index', compact('cursos', 'estadisticas'));
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar cursos: ' . $e->getMessage()]);
        }
    }

    /**
     * Muestra el catálogo público de cursos
     */
    public function catalogo(Request $request): View
    {
        try {
            $query = Curso::with(['categoria', 'docente'])
                         ->where('estado', 'activo');

            // Filtros
            if ($request->filled('categoria')) {
                $query->where('categoria_id', $request->categoria);
            }

            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('titulo', 'LIKE', '%' . $request->search . '%')
                      ->orWhere('descripcion', 'LIKE', '%' . $request->search . '%');
                });
            }

            $cursos = $query->paginate(12);
            $categorias = Categoria::where('tipo', 'curso')
                                 ->orderBy('nombre')
                                 ->get();

            return view('public.catalogo', compact('cursos', 'categorias'));
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar catálogo: ' . $e->getMessage()]);
        }
    }

    /**
     * Muestra el formulario para crear un curso
     */
    public function crearCurso(): View
    {
        try {
            $categorias = Categoria::where('tipo', 'curso')
                                 ->orderBy('nombre')
                                 ->get();
            
            $docentes = User::whereHas('roles', function($query) {
                            $query->where('nombre', 'docente');
                        })
                        ->orderBy('nombre')
                        ->get();

            return view('admin.cursos.crear', compact('categorias', 'docentes'));
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar formulario: ' . $e->getMessage()]);
        }
    }

    /**
     * Guarda un nuevo curso
     */
    public function guardarCurso(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'categoria_id' => 'required|exists:categories,id',
            'docente_id' => 'required|exists:users,id',
            'nivel' => 'required|in:principiante,intermedio,avanzado',
            'duracion_horas' => 'required|numeric|min:1',
            'precio' => 'nullable|numeric|min:0',
            'video_url' => 'nullable|url',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'estado' => 'required|in:activo,inactivo'
        ], [
            'titulo.required' => 'El título es obligatorio',
            'descripcion.required' => 'La descripción es obligatoria',
            'categoria_id.required' => 'La categoría es obligatoria',
            'categoria_id.exists' => 'La categoría seleccionada no existe',
            'docente_id.required' => 'El docente es obligatorio',
            'docente_id.exists' => 'El docente seleccionado no existe'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Validar URL de YouTube si se proporciona
        if ($request->filled('video_url') && !$this->isValidYoutubeUrl($request->video_url)) {
            return back()->withErrors(['video_url' => 'La URL debe ser un enlace válido de YouTube.'])
                        ->withInput();
        }

        try {
            DB::beginTransaction();

            $cursoData = [
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'categoria_id' => $request->categoria_id,
                'docente_id' => $request->docente_id,
                'nivel' => $request->nivel,
                'duracion_horas' => $request->duracion_horas,
                'precio' => $request->precio ?? 0,
                'video_url' => $request->video_url,
                'estado' => $request->estado,
                'slug' => \Str::slug($request->titulo)
            ];

            // Manejar imagen
            if ($request->hasFile('imagen')) {
                $imagen = $request->file('imagen');
                $nombreImagen = time() . '_' . $imagen->getClientOriginalName();
                $imagen->move(public_path('storage/cursos'), $nombreImagen);
                $cursoData['imagen'] = 'storage/cursos/' . $nombreImagen;
            }

            $curso = Curso::create($cursoData);

            DB::commit();

            return redirect()->route('admin.cursos.index')
                           ->with('success', 'Curso creado exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear curso: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Muestra el formulario para editar un curso
     */
    public function editarCurso(Request $request, int $id): View|RedirectResponse
    {
        try {
            $curso = Curso::with(['categoria', 'docente'])->findOrFail($id);
            $user = Auth::user();

            // Verificar permisos
            if ($user->hasRole('docente') && $curso->docente_id !== $user->id) {
                return redirect()->route('admin.cursos.index')
                               ->withErrors(['error' => 'No tienes permisos para editar este curso.']);
            }

            $categorias = Categoria::where('tipo', 'curso')
                                 ->orderBy('nombre')
                                 ->get();
            
            $docentes = User::whereHas('roles', function($query) {
                            $query->where('nombre', 'docente');
                        })
                        ->orderBy('nombre')
                        ->get();

            return view('admin.cursos.editar', compact('curso', 'categorias', 'docentes'));
        } catch (Exception $e) {
            return redirect()->route('admin.cursos.index')
                           ->withErrors(['error' => 'Error al cargar curso: ' . $e->getMessage()]);
        }
    }

    /**
     * Actualiza un curso existente
     */
    public function actualizarCurso(Request $request, int $id): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'categoria_id' => 'required|exists:categories,id',
            'docente_id' => 'required|exists:users,id',
            'nivel' => 'required|in:principiante,intermedio,avanzado',
            'duracion_horas' => 'required|numeric|min:1',
            'precio' => 'nullable|numeric|min:0',
            'video_url' => 'nullable|url',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'estado' => 'required|in:activo,inactivo'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $curso = Curso::findOrFail($id);
            $user = Auth::user();

            // Verificar permisos
            if ($user->hasRole('docente') && $curso->docente_id !== $user->id) {
                return redirect()->route('admin.cursos.index')
                               ->withErrors(['error' => 'No tienes permisos para editar este curso.']);
            }

            // Validar URL de YouTube si se proporciona
            if ($request->filled('video_url') && !$this->isValidYoutubeUrl($request->video_url)) {
                return back()->withErrors(['video_url' => 'La URL debe ser un enlace válido de YouTube.'])
                            ->withInput();
            }

            DB::beginTransaction();

            $cursoData = [
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'categoria_id' => $request->categoria_id,
                'docente_id' => $request->docente_id,
                'nivel' => $request->nivel,
                'duracion_horas' => $request->duracion_horas,
                'precio' => $request->precio ?? 0,
                'video_url' => $request->video_url,
                'estado' => $request->estado,
                'slug' => \Str::slug($request->titulo)
            ];

            // Manejar imagen
            if ($request->hasFile('imagen')) {
                // Eliminar imagen anterior si existe
                if ($curso->imagen && file_exists(public_path($curso->imagen))) {
                    unlink(public_path($curso->imagen));
                }

                $imagen = $request->file('imagen');
                $nombreImagen = time() . '_' . $imagen->getClientOriginalName();
                $imagen->move(public_path('storage/cursos'), $nombreImagen);
                $cursoData['imagen'] = 'storage/cursos/' . $nombreImagen;
            }

            $curso->update($cursoData);

            DB::commit();

            return redirect()->route('admin.cursos.index')
                           ->with('success', 'Curso actualizado exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar curso: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Elimina un curso
     */
    public function eliminarCurso(Request $request, int $id): RedirectResponse
    {
        try {
            $curso = Curso::findOrFail($id);
            $user = Auth::user();

            // Verificar permisos
            if ($user->hasRole('docente') && $curso->docente_id !== $user->id) {
                return redirect()->route('admin.cursos.index')
                               ->withErrors(['error' => 'No tienes permisos para eliminar este curso.']);
            }

            DB::beginTransaction();

            // Verificar si hay inscripciones activas
            $inscripcionesActivas = $curso->inscripciones()->count();
            
            if ($inscripcionesActivas > 0) {
                return redirect()->route('admin.cursos.index')
                               ->withErrors(['error' => 'No se puede eliminar el curso porque tiene estudiantes inscritos.']);
            }

            // Eliminar imagen si existe
            if ($curso->imagen && file_exists(public_path($curso->imagen))) {
                unlink(public_path($curso->imagen));
            }

            $curso->delete();

            DB::commit();

            return redirect()->route('admin.cursos.index')
                           ->with('success', 'Curso eliminado exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.cursos.index')
                           ->withErrors(['error' => 'Error al eliminar curso: ' . $e->getMessage()]);
        }
    }

    /**
     * Muestra los detalles de un curso
     */
    public function verCurso(Request $request, int $id): View|RedirectResponse
    {
        try {
            $curso = Curso::with(['categoria', 'docente', 'componentes', 'materiales', 'inscripciones'])
                         ->findOrFail($id);

            $user = Auth::user();
            $estaInscrito = false;
            $progreso = 0;

            if ($user) {
                $inscripcion = $curso->inscripciones()
                                   ->where('user_id', $user->id)
                                   ->first();
                
                if ($inscripcion) {
                    $estaInscrito = true;
                    $progreso = $inscripcion->progreso ?? 0;
                }
            }

            // Cursos relacionados
            $cursosRelacionados = Curso::where('categoria_id', $curso->categoria_id)
                                     ->where('id', '!=', $curso->id)
                                     ->where('estado', 'activo')
                                     ->take(4)
                                     ->get();

            $estadisticas = [
                'total_estudiantes' => $curso->inscripciones->count(),
                'rating_promedio' => 4.5, // TODO: Implementar sistema de ratings
                'total_componentes' => $curso->componentes->count(),
                'total_materiales' => $curso->materiales->count()
            ];

            return view('public.curso-detalle', compact(
                'curso', 
                'estaInscrito', 
                'progreso', 
                'cursosRelacionados', 
                'estadisticas'
            ));
        } catch (Exception $e) {
            return redirect()->route('public.catalogo')
                           ->withErrors(['error' => 'Error al cargar curso: ' . $e->getMessage()]);
        }
    }

    /**
     * Cambia el estado de un curso
     */
    public function cambiarEstado(Request $request, int $id): JsonResponse
    {
        try {
            $curso = Curso::findOrFail($id);
            $user = Auth::user();

            // Verificar permisos
            if ($user->hasRole('docente') && $curso->docente_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para modificar este curso.'
                ], 403);
            }

            $nuevoEstado = $curso->estado === 'activo' ? 'inactivo' : 'activo';
            $curso->update(['estado' => $nuevoEstado]);

            return response()->json([
                'success' => true,
                'message' => 'Estado del curso actualizado exitosamente.',
                'nuevo_estado' => $nuevoEstado
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Inscribe a un usuario en un curso
     */
    public function inscribirCurso(Request $request, int $id): RedirectResponse|JsonResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Debes iniciar sesión para inscribirte.'
                    ], 401);
                }
                return redirect()->route('login');
            }

            $curso = Curso::findOrFail($id);

            if ($curso->estado !== 'activo') {
                $message = 'Este curso no está disponible para inscripciones.';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message]);
                }
                return back()->withErrors(['error' => $message]);
            }

            // Verificar si ya está inscrito
            $inscripcionExistente = Enrollment::where('user_id', $user->id)
                                            ->where('curso_id', $curso->id)
                                            ->first();

            if ($inscripcionExistente) {
                $message = 'Ya estás inscrito en este curso.';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message]);
                }
                return back()->withErrors(['error' => $message]);
            }

            DB::beginTransaction();

            Enrollment::create([
                'user_id' => $user->id,
                'curso_id' => $curso->id,
                'fecha_inscripcion' => now(),
                'estado' => 'activo',
                'progreso' => 0
            ]);

            DB::commit();

            $message = 'Te has inscrito exitosamente al curso.';
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => $message]);
            }
            
            return redirect()->route('curso.cursando', $curso->id)
                           ->with('success', $message);
        } catch (Exception $e) {
            DB::rollBack();
            $message = 'Error al inscribirse: ' . $e->getMessage();
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 500);
            }
            return back()->withErrors(['error' => $message]);
        }
    }

    /**
     * Actualiza el progreso de un estudiante en un curso
     */
    public function actualizarProgreso(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'progreso' => 'required|numeric|min:0|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de progreso inválidos.'
            ], 400);
        }

        try {
            $user = Auth::user();
            $curso = Curso::findOrFail($id);

            $inscripcion = Enrollment::where('user_id', $user->id)
                                   ->where('curso_id', $curso->id)
                                   ->first();

            if (!$inscripcion) {
                return response()->json([
                    'success' => false,
                    'message' => 'No estás inscrito en este curso.'
                ], 404);
            }

            $inscripcion->update([
                'progreso' => $request->progreso,
                'fecha_ultimo_acceso' => now()
            ]);

            // Si el progreso es 100%, marcar como completado
            if ($request->progreso >= 100) {
                $inscripcion->update([
                    'estado' => 'completado',
                    'fecha_completado' => now()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Progreso actualizado exitosamente.',
                'progreso' => $inscripcion->progreso
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar progreso: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra la confirmación para eliminar un curso
     */
    public function confirmarEliminar(Request $request, int $id): View|RedirectResponse
    {
        try {
            $curso = Curso::with(['inscripciones', 'componentes', 'materiales'])
                         ->findOrFail($id);
            
            $user = Auth::user();

            // Verificar permisos
            if ($user->hasRole('docente') && $curso->docente_id !== $user->id) {
                return redirect()->route('admin.cursos.index')
                               ->withErrors(['error' => 'No tienes permisos para eliminar este curso.']);
            }

            $estadisticas = [
                'estudiantes_inscritos' => $curso->inscripciones->count(),
                'componentes' => $curso->componentes->count(),
                'materiales' => $curso->materiales->count()
            ];

            return view('admin.cursos.confirmar-eliminar', compact('curso', 'estadisticas'));
        } catch (Exception $e) {
            return redirect()->route('admin.cursos.index')
                           ->withErrors(['error' => 'Error al cargar información del curso: ' . $e->getMessage()]);
        }
    }

    /**
     * Obtiene estadísticas para AJAX
     */
    public function ajaxEstadisticas(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $estadisticas = [
                'total_cursos' => Curso::count(),
                'cursos_activos' => Curso::where('estado', 'activo')->count(),
                'total_estudiantes' => Enrollment::distinct('user_id')->count(),
                'cursos_populares' => Curso::withCount('inscripciones')
                                         ->orderBy('inscripciones_count', 'desc')
                                         ->take(5)
                                         ->get()
                                         ->map(function($curso) {
                                             return [
                                                 'titulo' => $curso->titulo,
                                                 'estudiantes' => $curso->inscripciones_count
                                             ];
                                         })
            ];

            if ($user->hasRole('docente')) {
                $estadisticas['mis_cursos'] = Curso::where('docente_id', $user->id)->count();
                $estadisticas['mis_estudiantes'] = Enrollment::whereHas('curso', function($query) use ($user) {
                    $query->where('docente_id', $user->id);
                })->distinct('user_id')->count();
            }

            return response()->json(['success' => true, 'data' => $estadisticas]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Busca cursos por AJAX
     */
    public function buscarCursos(Request $request): JsonResponse
    {
        try {
            $query = Curso::with(['categoria', 'docente'])
                         ->where('estado', 'activo');

            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('titulo', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('descripcion', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhereHas('categoria', function($q2) use ($searchTerm) {
                          $q2->where('nombre', 'LIKE', '%' . $searchTerm . '%');
                      });
                });
            }

            if ($request->filled('categoria')) {
                $query->where('categoria_id', $request->categoria);
            }

            if ($request->filled('nivel')) {
                $query->where('nivel', $request->nivel);
            }

            $cursos = $query->take(10)->get();

            return response()->json([
                'success' => true,
                'data' => $cursos->map(function($curso) {
                    return [
                        'id' => $curso->id,
                        'titulo' => $curso->titulo,
                        'descripcion' => \Str::limit($curso->descripcion, 100),
                        'categoria' => $curso->categoria->nombre,
                        'docente' => $curso->docente->nombre,
                        'nivel' => ucfirst($curso->nivel),
                        'precio' => $curso->precio,
                        'imagen' => $curso->imagen,
                        'url' => route('curso.ver', $curso->id)
                    ];
                })
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Valida si una URL es de YouTube
     */
    private function isValidYoutubeUrl(string $url): bool
    {
        $pattern = '/^(https?:\/\/)?(www\.)?(youtube\.com\/(watch\?v=|embed\/)|youtu\.be\/)[\w\-]+(&\S*)?$/';
        return preg_match($pattern, $url);
    }

    /**
     * Muestra la interfaz de curso para estudiantes inscritos
     */
    public function cursando(Request $request, int $id): View|RedirectResponse
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return redirect()->route('login');
            }

            $curso = Curso::with([
                'categoria', 
                'docente', 
                'componentes' => function($query) {
                    $query->orderBy('orden');
                },
                'materiales' => function($query) {
                    $query->orderBy('orden');
                }
            ])->findOrFail($id);

            // Verificar inscripción
            $inscripcion = Enrollment::where('user_id', $user->id)
                                   ->where('curso_id', $curso->id)
                                   ->first();

            if (!$inscripcion) {
                return redirect()->route('curso.ver', $curso->id)
                               ->withErrors(['error' => 'Debes inscribirte en este curso para acceder al contenido.']);
            }

            // Actualizar fecha de último acceso
            $inscripcion->update(['fecha_ultimo_acceso' => now()]);

            return view('public.cursando', compact('curso', 'inscripcion'));
        } catch (Exception $e) {
            return redirect()->route('public.catalogo')
                           ->withErrors(['error' => 'Error al acceder al curso: ' . $e->getMessage()]);
        }
    }
}
