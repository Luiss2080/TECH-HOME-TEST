<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Models\Componente;
use App\Models\Categoria;
use App\Models\Curso;
use App\Models\Material;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class ComponenteController extends Controller
{
    /**
     * Panel de administración de componentes
     */
    public function index(Request $request): View
    {
        try {
            $query = Componente::with(['curso', 'categoria']);

            // Filtros
            if ($request->filled('curso_id')) {
                $query->where('curso_id', $request->curso_id);
            }

            if ($request->filled('categoria_id')) {
                $query->where('categoria_id', $request->categoria_id);
            }

            if ($request->filled('tipo')) {
                $query->where('tipo', $request->tipo);
            }

            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('titulo', 'LIKE', '%' . $request->search . '%')
                      ->orWhere('descripcion', 'LIKE', '%' . $request->search . '%');
                });
            }

            $componentes = $query->orderBy('curso_id')
                                ->orderBy('orden')
                                ->paginate(15);

            $cursos = Curso::orderBy('titulo')->get();
            $categorias = Categoria::where('tipo', 'componente')->orderBy('nombre')->get();

            $estadisticas = [
                'total_componentes' => Componente::count(),
                'por_tipo' => Componente::selectRaw('tipo, COUNT(*) as total')
                                      ->groupBy('tipo')
                                      ->pluck('total', 'tipo'),
                'activos' => Componente::where('estado', 'activo')->count()
            ];

            return view('admin.componentes.index', compact('componentes', 'cursos', 'categorias', 'estadisticas'));
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar componentes: ' . $e->getMessage()]);
        }
    }

    /**
     * Muestra el formulario para crear un componente
     */
    public function crear(): View
    {
        try {
            $cursos = Curso::orderBy('titulo')->get();
            $categorias = Categoria::where('tipo', 'componente')->orderBy('nombre')->get();

            return view('admin.componentes.crear', compact('cursos', 'categorias'));
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar formulario: ' . $e->getMessage()]);
        }
    }

    /**
     * Guarda un nuevo componente
     */
    public function guardar(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'contenido' => 'nullable|string',
            'curso_id' => 'required|exists:courses,id',
            'categoria_id' => 'nullable|exists:categories,id',
            'tipo' => 'required|in:leccion,video,practica,evaluacion,recurso',
            'orden' => 'nullable|integer|min:1',
            'estado' => 'required|in:activo,inactivo',
            'duracion_minutos' => 'nullable|integer|min:1',
            'recursos_adicionales' => 'nullable|array'
        ], [
            'titulo.required' => 'El título es obligatorio',
            'curso_id.required' => 'El curso es obligatorio',
            'tipo.required' => 'El tipo es obligatorio'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $componenteData = [
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'contenido' => $request->contenido,
                'curso_id' => $request->curso_id,
                'categoria_id' => $request->categoria_id,
                'tipo' => $request->tipo,
                'orden' => $request->orden ?? 1,
                'estado' => $request->estado,
                'duracion_minutos' => $request->duracion_minutos,
                'recursos_adicionales' => $request->recursos_adicionales ?? []
            ];

            $componente = Componente::create($componenteData);

            DB::commit();

            return redirect('/admin/componentes')
                           ->with('success', 'Componente creado exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear componente: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Muestra el formulario para editar un componente
     */
    public function editar(int $id): View|RedirectResponse
    {
        try {
            $componente = Componente::with(['curso', 'categoria'])->findOrFail($id);
            
            $cursos = Curso::orderBy('titulo')->get();
            $categorias = Categoria::where('tipo', 'componente')->orderBy('nombre')->get();

            return view('admin.componentes.editar', compact('componente', 'cursos', 'categorias'));
        } catch (Exception $e) {
            return redirect('/admin/componentes')
                           ->withErrors(['error' => 'Error al cargar componente: ' . $e->getMessage()]);
        }
    }

    /**
     * Actualiza un componente existente
     */
    public function actualizar(Request $request, int $id): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'contenido' => 'nullable|string',
            'curso_id' => 'required|exists:courses,id',
            'categoria_id' => 'nullable|exists:categories,id',
            'tipo' => 'required|in:leccion,video,practica,evaluacion,recurso',
            'orden' => 'nullable|integer|min:1',
            'estado' => 'required|in:activo,inactivo',
            'duracion_minutos' => 'nullable|integer|min:1',
            'recursos_adicionales' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $componente = Componente::findOrFail($id);

            DB::beginTransaction();

            $componenteData = [
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'contenido' => $request->contenido,
                'curso_id' => $request->curso_id,
                'categoria_id' => $request->categoria_id,
                'tipo' => $request->tipo,
                'orden' => $request->orden ?? 1,
                'estado' => $request->estado,
                'duracion_minutos' => $request->duracion_minutos,
                'recursos_adicionales' => $request->recursos_adicionales ?? []
            ];

            $componente->update($componenteData);

            DB::commit();

            return redirect('/admin/componentes')
                           ->with('success', 'Componente actualizado exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar componente: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Elimina un componente
     */
    public function eliminar(int $id): RedirectResponse
    {
        try {
            $componente = Componente::findOrFail($id);

            DB::beginTransaction();

            // Verificar si tiene materiales asociados
            $tieneMateriales = Material::where('componente_id', $componente->id)->exists();
            
            if ($tieneMateriales) {
                return redirect('/admin/componentes')
                               ->withErrors(['error' => 'No se puede eliminar el componente porque tiene materiales asociados.']);
            }

            $componente->delete();

            DB::commit();

            return redirect('/admin/componentes')
                           ->with('success', 'Componente eliminado exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect('/admin/componentes')
                           ->withErrors(['error' => 'Error al eliminar componente: ' . $e->getMessage()]);
        }
    }

    /**
     * Muestra los detalles de un componente
     */
    public function ver(int $id): View|RedirectResponse
    {
        try {
            $componente = Componente::with(['curso', 'categoria', 'materiales' => function($query) {
                                        $query->where('estado', 'activo')->orderBy('orden');
                                    }])
                                  ->findOrFail($id);

            if ($componente->estado !== 'activo') {
                return back()->withErrors(['error' => 'Este componente no está disponible.']);
            }

            return view('public.componente-detalle', compact('componente'));
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar componente: ' . $e->getMessage()]);
        }
    }

    /**
     * Obtiene componentes por curso (AJAX)
     */
    public function porCurso(Request $request, int $cursoId): JsonResponse
    {
        try {
            $componentes = Componente::with(['categoria'])
                                   ->where('curso_id', $cursoId)
                                   ->where('estado', 'activo')
                                   ->orderBy('orden')
                                   ->get();

            return response()->json([
                'success' => true,
                'data' => $componentes->map(function($componente) {
                    return [
                        'id' => $componente->id,
                        'titulo' => $componente->titulo,
                        'descripcion' => $componente->descripcion,
                        'tipo' => $componente->tipo,
                        'categoria' => $componente->categoria ? $componente->categoria->nombre : null,
                        'duracion' => $componente->duracion_formateada,
                        'orden' => $componente->orden,
                        'url' => '/componentes/' . $componente->id
                    ];
                })
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener componentes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reordenar componentes
     */
    public function reordenar(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'componentes' => 'required|array',
            'componentes.*.id' => 'required|exists:components,id',
            'componentes.*.orden' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            foreach ($request->componentes as $componenteData) {
                Componente::where('id', $componenteData['id'])
                         ->update(['orden' => $componenteData['orden']]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Orden actualizado exitosamente.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al reordenar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambiar estado de un componente
     */
    public function cambiarEstado(Request $request, int $id): JsonResponse
    {
        try {
            $componente = Componente::findOrFail($id);
            
            $nuevoEstado = $componente->estado === 'activo' ? 'inactivo' : 'activo';
            $componente->update(['estado' => $nuevoEstado]);

            return response()->json([
                'success' => true,
                'message' => 'Estado del componente actualizado exitosamente.',
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
     * Duplicar un componente
     */
    public function duplicar(int $id): RedirectResponse
    {
        try {
            $componenteOriginal = Componente::findOrFail($id);

            DB::beginTransaction();

            // Crear copia del componente
            $componenteData = $componenteOriginal->toArray();
            unset($componenteData['id'], $componenteData['created_at'], $componenteData['updated_at']);
            $componenteData['titulo'] = $componenteData['titulo'] . ' (Copia)';
            
            // Obtener el siguiente orden disponible
            $maxOrden = Componente::where('curso_id', $componenteOriginal->curso_id)->max('orden');
            $componenteData['orden'] = $maxOrden + 1;

            $nuevoComponente = Componente::create($componenteData);

            // Duplicar materiales asociados
            $materiales = Material::where('componente_id', $componenteOriginal->id)->get();
            foreach ($materiales as $material) {
                $materialData = $material->toArray();
                unset($materialData['id'], $materialData['created_at'], $materialData['updated_at']);
                $materialData['componente_id'] = $nuevoComponente->id;
                $materialData['titulo'] = $materialData['titulo'] . ' (Copia)';
                
                Material::create($materialData);
            }

            DB::commit();

            return redirect('/admin/componentes')
                           ->with('success', 'Componente duplicado exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect('/admin/componentes')
                           ->withErrors(['error' => 'Error al duplicar componente: ' . $e->getMessage()]);
        }
    }
}
