<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Models\Material;
use App\Models\Categoria;
use App\Models\Curso;
use App\Models\Componente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class MaterialController extends Controller
{
    /**
     * Panel de administración de materiales
     */
    public function index(Request $request): View
    {
        try {
            $query = Material::with(['curso', 'categoria', 'componente']);

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

            $materiales = $query->orderBy('curso_id')
                              ->orderBy('orden')
                              ->paginate(15);

            $cursos = Curso::orderBy('titulo')->get();
            $categorias = Categoria::where('tipo', 'material')->orderBy('nombre')->get();

            $estadisticas = [
                'total_materiales' => Material::count(),
                'por_tipo' => Material::selectRaw('tipo, COUNT(*) as total')
                                    ->groupBy('tipo')
                                    ->pluck('total', 'tipo'),
                'descargables' => Material::where('es_descargable', true)->count()
            ];

            return view('admin.materiales.index', compact('materiales', 'cursos', 'categorias', 'estadisticas'));
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar materiales: ' . $e->getMessage()]);
        }
    }

    /**
     * Muestra el formulario para crear un material
     */
    public function crear(Request $request): View
    {
        try {
            $cursos = Curso::orderBy('titulo')->get();
            $categorias = Categoria::where('tipo', 'material')->orderBy('nombre')->get();
            
            $componentes = collect();
            if ($request->filled('curso_id')) {
                $componentes = Componente::where('curso_id', $request->curso_id)
                                       ->orderBy('orden')
                                       ->get();
            }

            return view('admin.materiales.crear', compact('cursos', 'categorias', 'componentes'));
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar formulario: ' . $e->getMessage()]);
        }
    }

    /**
     * Guarda un nuevo material
     */
    public function guardar(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:documento,video,imagen,enlace,ejercicio',
            'curso_id' => 'required|exists:courses,id',
            'categoria_id' => 'nullable|exists:categories,id',
            'componente_id' => 'nullable|exists:components,id',
            'orden' => 'nullable|integer|min:1',
            'estado' => 'required|in:activo,inactivo',
            'es_descargable' => 'required|boolean',
            'archivo' => 'nullable|file|max:50000',
            'url_externa' => 'nullable|url'
        ], [
            'titulo.required' => 'El título es obligatorio',
            'tipo.required' => 'El tipo es obligatorio',
            'curso_id.required' => 'El curso es obligatorio'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Validar que al menos haya archivo o URL externa
        if (!$request->hasFile('archivo') && !$request->filled('url_externa')) {
            return back()->withErrors(['archivo' => 'Debe proporcionar un archivo o una URL externa.'])
                        ->withInput();
        }

        try {
            DB::beginTransaction();

            $materialData = [
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'tipo' => $request->tipo,
                'curso_id' => $request->curso_id,
                'categoria_id' => $request->categoria_id,
                'componente_id' => $request->componente_id,
                'orden' => $request->orden ?? 1,
                'estado' => $request->estado,
                'es_descargable' => $request->boolean('es_descargable'),
                'url_externa' => $request->url_externa
            ];

            // Manejar archivo
            if ($request->hasFile('archivo')) {
                $archivo = $request->file('archivo');
                $extension = $archivo->getClientOriginalExtension();
                $nombreArchivo = time() . '_' . Str::slug($request->titulo) . '.' . $extension;
                $ruta = $archivo->storeAs('materiales', $nombreArchivo, 'public');
                
                $materialData['ruta_archivo'] = $ruta;
                $materialData['extension'] = $extension;
                $materialData['tamaño_archivo'] = $archivo->getSize();
            }

            $material = Material::create($materialData);

            DB::commit();

            return redirect('/admin/materiales')
                           ->with('success', 'Material creado exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear material: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Muestra el formulario para editar un material
     */
    public function editar(int $id): View|RedirectResponse
    {
        try {
            $material = Material::with(['curso', 'categoria', 'componente'])->findOrFail($id);
            
            $cursos = Curso::orderBy('titulo')->get();
            $categorias = Categoria::where('tipo', 'material')->orderBy('nombre')->get();
            $componentes = Componente::where('curso_id', $material->curso_id)
                                   ->orderBy('orden')
                                   ->get();

            return view('admin.materiales.editar', compact('material', 'cursos', 'categorias', 'componentes'));
        } catch (Exception $e) {
            return redirect('/admin/materiales')
                           ->withErrors(['error' => 'Error al cargar material: ' . $e->getMessage()]);
        }
    }

    /**
     * Actualiza un material existente
     */
    public function actualizar(Request $request, int $id): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:documento,video,imagen,enlace,ejercicio',
            'curso_id' => 'required|exists:courses,id',
            'categoria_id' => 'nullable|exists:categories,id',
            'componente_id' => 'nullable|exists:components,id',
            'orden' => 'nullable|integer|min:1',
            'estado' => 'required|in:activo,inactivo',
            'es_descargable' => 'required|boolean',
            'archivo' => 'nullable|file|max:50000',
            'url_externa' => 'nullable|url'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $material = Material::findOrFail($id);

            DB::beginTransaction();

            $materialData = [
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'tipo' => $request->tipo,
                'curso_id' => $request->curso_id,
                'categoria_id' => $request->categoria_id,
                'componente_id' => $request->componente_id,
                'orden' => $request->orden ?? 1,
                'estado' => $request->estado,
                'es_descargable' => $request->boolean('es_descargable'),
                'url_externa' => $request->url_externa
            ];

            // Manejar archivo
            if ($request->hasFile('archivo')) {
                // Eliminar archivo anterior
                if ($material->ruta_archivo && Storage::disk('public')->exists($material->ruta_archivo)) {
                    Storage::disk('public')->delete($material->ruta_archivo);
                }

                $archivo = $request->file('archivo');
                $extension = $archivo->getClientOriginalExtension();
                $nombreArchivo = time() . '_' . Str::slug($request->titulo) . '.' . $extension;
                $ruta = $archivo->storeAs('materiales', $nombreArchivo, 'public');
                
                $materialData['ruta_archivo'] = $ruta;
                $materialData['extension'] = $extension;
                $materialData['tamaño_archivo'] = $archivo->getSize();
            }

            $material->update($materialData);

            DB::commit();

            return redirect('/admin/materiales')
                           ->with('success', 'Material actualizado exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar material: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Elimina un material
     */
    public function eliminar(int $id): RedirectResponse
    {
        try {
            $material = Material::findOrFail($id);

            DB::beginTransaction();

            // Eliminar archivo asociado
            if ($material->ruta_archivo && Storage::disk('public')->exists($material->ruta_archivo)) {
                Storage::disk('public')->delete($material->ruta_archivo);
            }

            $material->delete();

            DB::commit();

            return redirect('/admin/materiales')
                           ->with('success', 'Material eliminado exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect('/admin/materiales')
                           ->withErrors(['error' => 'Error al eliminar material: ' . $e->getMessage()]);
        }
    }

    /**
     * Descarga un material
     */
    public function descargar(Request $request, int $id): RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        try {
            $material = Material::findOrFail($id);

            if (!$material->es_descargable) {
                return back()->withErrors(['error' => 'Este material no está disponible para descarga.']);
            }

            if ($material->estado !== 'activo') {
                return back()->withErrors(['error' => 'Este material no está disponible.']);
            }

            // Si es enlace externo
            if ($material->url_externa) {
                return redirect($material->url_externa);
            }

            // Si tiene archivo
            if ($material->ruta_archivo && Storage::disk('public')->exists($material->ruta_archivo)) {
                return Storage::disk('public')->download(
                    $material->ruta_archivo, 
                    $material->titulo . '.' . $material->extension
                );
            }

            return back()->withErrors(['error' => 'No hay archivo disponible para descargar.']);

        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al descargar material: ' . $e->getMessage()]);
        }
    }

    /**
     * Obtiene materiales por curso (AJAX)
     */
    public function porCurso(Request $request, int $cursoId): JsonResponse
    {
        try {
            $materiales = Material::with(['categoria', 'componente'])
                                ->where('curso_id', $cursoId)
                                ->where('estado', 'activo')
                                ->orderBy('orden')
                                ->get();

            return response()->json([
                'success' => true,
                'data' => $materiales->map(function($material) {
                    return [
                        'id' => $material->id,
                        'titulo' => $material->titulo,
                        'descripcion' => $material->descripcion,
                        'tipo' => $material->tipo,
                        'categoria' => $material->categoria ? $material->categoria->nombre : null,
                        'componente' => $material->componente ? $material->componente->titulo : null,
                        'es_descargable' => $material->es_descargable,
                        'tiene_archivo' => !empty($material->ruta_archivo),
                        'tiene_url' => !empty($material->url_externa),
                        'url_descarga' => $material->es_descargable ? '/materiales/' . $material->id . '/descargar' : null
                    ];
                })
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener materiales: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene componentes por curso (AJAX)
     */
    public function componentesPorCurso(Request $request, int $cursoId): JsonResponse
    {
        try {
            $componentes = Componente::where('curso_id', $cursoId)
                                   ->where('estado', 'activo')
                                   ->orderBy('orden')
                                   ->get(['id', 'titulo']);

            return response()->json([
                'success' => true,
                'data' => $componentes
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener componentes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reordenar materiales
     */
    public function reordenar(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'materiales' => 'required|array',
            'materiales.*.id' => 'required|exists:materials,id',
            'materiales.*.orden' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            foreach ($request->materiales as $materialData) {
                Material::where('id', $materialData['id'])
                       ->update(['orden' => $materialData['orden']]);
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
}
