<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\Componente;
use App\Models\Material;
use App\Models\User;
use App\Models\Categoria;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;

class LaboratorioController extends Controller
{
    /**
     * Dashboard de laboratorios (vista general)
     */
    public function index()
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            
            // Laboratorios son cursos prácticos con componentes tipo "laboratorio"
            $query = Curso::with(['categoria', 'docente', 'componentes'])
                         ->where('estado', 'activo');

            // Filtrar por rol
            // if ($user->hasRole('docente')) {
                $query->where('docente_id', $user->id);
            }

            // Solo cursos que tienen componentes de laboratorio
            $laboratorios = $query->whereHas('componentes', function($q) {
                $q->where('tipo', 'laboratorio');
            })->orderBy('creado_en', 'desc')
              ->paginate(12);

            $estadisticas = [
                'total_laboratorios' => $laboratorios->total(),
                'laboratorios_activos' => Curso::where('estado', 'activo')
                                               ->whereHas('componentes', function($q) {
                                                   $q->where('tipo', 'laboratorio');
                                               })->count(),
                'total_estudiantes' => Enrollment::whereHas('curso.componentes', function($q) {
                    $q->where('tipo', 'laboratorio');
                })->distinct('usuario_id')->count()
            ];

            return view('admin.laboratorios.index', compact('laboratorios', 'estadisticas'));
            
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar laboratorios: ' . $e->getMessage()]);
        }
    }

    /**
     * Mostrar detalles de un laboratorio específico
     */
    public function show($id)
    {
        try {
            $user = Auth::user();
            
            $laboratorio = Curso::with(['categoria', 'docente', 'componentes' => function($query) {
                $query->where('tipo', 'laboratorio')->orderBy('orden');
            }, 'materiales'])
            ->whereHas('componentes', function($q) {
                $q->where('tipo', 'laboratorio');
            })->findOrFail($id);

            // Verificar permisos (comentado hasta implementar sistema de roles)
            // // if ($user->hasRole('docente') && $laboratorio->docente_id !== $user->id) {
            //     // return back()->withErrors(['error' => 'No tiene permisos para ver este laboratorio.']);
            // }

            // Estadísticas del laboratorio
            $estadisticas = [
                'total_componentes' => $laboratorio->componentes->count(),
                'estudiantes_inscritos' => $laboratorio->enrollments()->count(),
                'completados' => $laboratorio->enrollments()
                                            ->where('progreso', 100)
                                            ->count()
            ];

            return view('admin.laboratorios.show', compact('laboratorio', 'estadisticas'));
            
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar laboratorio: ' . $e->getMessage()]);
        }
    }

    /**
     * Crear nuevo laboratorio (curso con componentes prácticos)
     */
    public function create()
    {
        try {
            $categorias = Categoria::orderBy('nombre')->get();
            $docentes = User::whereHas('roles', function($query) {
                $query->where('nombre', 'docente');
            })->orderBy('nombre')->get();

            return view('admin.laboratorios.create', compact('categorias', 'docentes'));
            
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar formulario: ' . $e->getMessage()]);
        }
    }

    /**
     * Guardar nuevo laboratorio
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'categoria_id' => 'required|exists:categories,id',
            'docente_id' => 'required|exists:users,id',
            'duracion_horas' => 'required|integer|min:1',
            'nivel' => 'required|in:principiante,intermedio,avanzado',
            'precio' => 'required|numeric|min:0',
            'requisitos' => 'nullable|string',
            'objetivos' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Crear curso base
            $laboratorio = Curso::create([
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'categoria_id' => $request->categoria_id,
                'docente_id' => $request->docente_id,
                'duracion_horas' => $request->duracion_horas,
                'nivel' => $request->nivel,
                'precio' => $request->precio,
                'requisitos' => $request->requisitos,
                'objetivos' => $request->objetivos,
                'estado' => 'borrador',
                'slug' => \Illuminate\Support\Str::slug($request->titulo),
                'creado_en' => now()
            ]);

            // Crear componente inicial de laboratorio
            Componente::create([
                'curso_id' => $laboratorio->id,
                'titulo' => 'Introducción al Laboratorio',
                'descripcion' => 'Componente inicial del laboratorio',
                'tipo' => 'laboratorio',
                'orden' => 1,
                'estado' => 'activo',
                'creado_en' => now()
            ]);

            DB::commit();

            return redirect('/admin/laboratorios/' . $laboratorio->id)
                          ->with('success', 'Laboratorio creado exitosamente.');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear laboratorio: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Editar laboratorio
     */
    public function edit($id)
    {
        try {
            $user = Auth::user();
            
            $laboratorio = Curso::whereHas('componentes', function($q) {
                $q->where('tipo', 'laboratorio');
            })->findOrFail($id);

            // Verificar permisos
            // if ($user->hasRole('docente') && $laboratorio->docente_id !== $user->id) {
                // return back()->withErrors(['error' => 'No tiene permisos para editar este laboratorio.']);
            }

            $categorias = Categoria::orderBy('nombre')->get();
            $docentes = User::whereHas('roles', function($query) {
                $query->where('nombre', 'docente');
            })->orderBy('nombre')->get();

            return view('admin.laboratorios.edit', compact('laboratorio', 'categorias', 'docentes'));
            
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar laboratorio: ' . $e->getMessage()]);
        }
    }

    /**
     * Actualizar laboratorio
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'categoria_id' => 'required|exists:categories,id',
            'docente_id' => 'required|exists:users,id',
            'duracion_horas' => 'required|integer|min:1',
            'nivel' => 'required|in:principiante,intermedio,avanzado',
            'precio' => 'required|numeric|min:0',
            'estado' => 'required|in:borrador,activo,inactivo',
            'requisitos' => 'nullable|string',
            'objetivos' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $user = Auth::user();
            
            $laboratorio = Curso::whereHas('componentes', function($q) {
                $q->where('tipo', 'laboratorio');
            })->findOrFail($id);

            // Verificar permisos
            // if ($user->hasRole('docente') && $laboratorio->docente_id !== $user->id) {
                // return back()->withErrors(['error' => 'No tiene permisos para editar este laboratorio.']);
            }

            $laboratorio->update([
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'categoria_id' => $request->categoria_id,
                'docente_id' => $request->docente_id,
                'duracion_horas' => $request->duracion_horas,
                'nivel' => $request->nivel,
                'precio' => $request->precio,
                'estado' => $request->estado,
                'requisitos' => $request->requisitos,
                'objetivos' => $request->objetivos,
                'slug' => \Illuminate\Support\Str::slug($request->titulo),
                'actualizado_en' => now()
            ]);

            return back()->with('success', 'Laboratorio actualizado exitosamente.');

        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al actualizar laboratorio: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Eliminar laboratorio
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = Auth::user();
            
            $laboratorio = Curso::whereHas('componentes', function($q) {
                $q->where('tipo', 'laboratorio');
            })->findOrFail($id);

            // Verificar permisos
            // if ($user->hasRole('docente') && $laboratorio->docente_id !== $user->id) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'No tiene permisos para eliminar este laboratorio.'], 403);
                }
                // return back()->withErrors(['error' => 'No tiene permisos para eliminar este laboratorio.']);
            }

            // Verificar si tiene estudiantes inscritos
            if ($laboratorio->enrollments()->count() > 0) {
                $error = 'No se puede eliminar el laboratorio porque tiene estudiantes inscritos.';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $error], 400);
                }
                return back()->withErrors(['error' => $error]);
            }

            $laboratorio->delete();

            $message = 'Laboratorio eliminado exitosamente.';
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => $message]);
            }

            return redirect('/admin/laboratorios')
                          ->with('success', $message);

        } catch (Exception $e) {
            $error = 'Error al eliminar laboratorio: ' . $e->getMessage();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $error], 500);
            }
            return back()->withErrors(['error' => $error]);
        }
    }

    /**
     * Gestionar componentes de laboratorio
     */
    public function components($id)
    {
        try {
            $user = Auth::user();
            
            $laboratorio = Curso::with(['componentes' => function($query) {
                $query->where('tipo', 'laboratorio')->orderBy('orden');
            }])->whereHas('componentes', function($q) {
                $q->where('tipo', 'laboratorio');
            })->findOrFail($id);

            // Verificar permisos
            // if ($user->hasRole('docente') && $laboratorio->docente_id !== $user->id) {
                // return back()->withErrors(['error' => 'No tiene permisos para gestionar este laboratorio.']);
            }

            return view('admin.laboratorios.components', compact('laboratorio'));
            
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar componentes: ' . $e->getMessage()]);
        }
    }

    /**
     * Reportes de laboratorio
     */
    public function reports($id)
    {
        try {
            $user = Auth::user();
            
            $laboratorio = Curso::whereHas('componentes', function($q) {
                $q->where('tipo', 'laboratorio');
            })->findOrFail($id);

            // Verificar permisos
            // if ($user->hasRole('docente') && $laboratorio->docente_id !== $user->id) {
                // return back()->withErrors(['error' => 'No tiene permisos para ver los reportes de este laboratorio.']);
            }

            // Estadísticas detalladas
            $inscripciones = $laboratorio->enrollments()
                                       ->with(['usuario'])
                                       ->orderBy('progreso', 'desc')
                                       ->get();

            $estadisticas = [
                'total_inscritos' => $inscripciones->count(),
                'completados' => $inscripciones->where('progreso', 100)->count(),
                'en_progreso' => $inscripciones->where('progreso', '>', 0)->where('progreso', '<', 100)->count(),
                'sin_iniciar' => $inscripciones->where('progreso', 0)->count(),
                'progreso_promedio' => $inscripciones->avg('progreso') ?? 0
            ];

            return view('admin.laboratorios.reports', compact('laboratorio', 'inscripciones', 'estadisticas'));
            
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar reportes: ' . $e->getMessage()]);
        }
    }
}
