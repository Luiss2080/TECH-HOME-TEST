<?php

namespace App\Http\Controllers;

use App\Services\MaterialService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class MaterialController extends Controller
{
    private $materialService;

    public function __construct()
    {
        $this->materialService = new MaterialService();
    }

    /**
     * Mostrar lista de materiales
     */
    public function index(Request $request): View
    {
        try {
            $filtros = [
                'busqueda' => $request->get('busqueda'),
                'categoria_id' => $request->get('categoria_id'),
                'tipo' => $request->get('tipo'),
                'curso_id' => $request->get('curso_id'),
                'estado' => $request->get('estado')
            ];

            $page = max(1, (int)$request->get('page', 1));
            $perPage = 20;

            $resultado = $this->materialService->getMaterialesFiltrados($filtros, $page, $perPage);
            $categorias = $this->materialService->getAllCategories();
            $cursos = $this->materialService->getAllCursos();
            
            return view('materiales.index', [
                'title' => 'Gestión de Materiales',
                'materiales' => $resultado['materiales'],
                'total' => $resultado['total'],
                'page' => $page,
                'perPage' => $perPage,
                'filtros' => $filtros,
                'categorias' => $categorias,
                'cursos' => $cursos
            ]);
        } catch (Exception $e) {
            Log::error('Error al cargar materiales: ' . $e->getMessage());
            return view('materiales.index', [
                'title' => 'Gestión de Materiales',
                'materiales' => [],
                'total' => 0,
                'page' => 1,
                'perPage' => 20,
                'filtros' => [],
                'categorias' => [],
                'cursos' => []
            ])->with('error', 'Error al cargar materiales: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de creación de material
     */
    public function create(): View|RedirectResponse
    {
        try {
            $categorias = $this->materialService->getAllCategories();
            $cursos = $this->materialService->getAllCursos();
            $docentes = $this->materialService->getAllDocentes();
            
            return view('materiales.create', [
                'title' => 'Crear Material',
                'categorias' => $categorias,
                'cursos' => $cursos,
                'docentes' => $docentes
            ]);
        } catch (Exception $e) {
            Log::error('Error al cargar formulario de creación: ' . $e->getMessage());
            return redirect()->route('materiales.index')
                ->with('error', 'Error al cargar formulario: ' . $e->getMessage());
        }
    }

    /**
     * Guardar nuevo material
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            // Validaciones base
            $rules = [
                'titulo' => 'required|min:3|max:200',
                'descripcion' => 'nullable|max:1000',
                'tipo' => 'required|in:video,documento,presentacion,audio,enlace,ejercicio,imagen',
                'categoria_id' => 'required|integer|exists:categorias,id',
                'curso_id' => 'nullable|integer|exists:cursos,id',
                'componente_id' => 'nullable|integer|exists:componentes,id',
                'orden' => 'nullable|integer|min:0',
                'es_descargable' => 'boolean',
                'estado' => 'required|in:0,1'
            ];

            // Validación condicional para archivo o enlace
            if ($request->tipo === 'enlace' || $request->enlace_externo) {
                $rules['enlace_externo'] = 'required|url|max:500';
            } else {
                $rules['archivo_upload'] = 'required|file|max:102400'; // 100MB
            }

            $request->validate($rules);

            // Preparar datos del material
            $materialData = [
                'titulo' => trim($request->titulo),
                'descripcion' => trim($request->descripcion),
                'tipo' => $request->tipo,
                'categoria_id' => $request->categoria_id,
                'curso_id' => $request->curso_id,
                'componente_id' => $request->componente_id,
                'orden' => $request->orden ?: $this->getNextOrder($request->curso_id, $request->componente_id),
                'es_descargable' => $request->boolean('es_descargable', true),
                'estado' => $request->estado,
                'fecha_creacion' => now(),
                'fecha_actualizacion' => now()
            ];

            // Manejar archivo o enlace externo
            if ($request->tipo === 'enlace' || $request->enlace_externo) {
                $materialData['archivo_url'] = $request->enlace_externo;
            } else {
                // Subir archivo
                $file = $request->file('archivo_upload');
                $path = $file->store('materiales', 'public');
                $materialData['archivo_url'] = Storage::url($path);
                $materialData['tamaño_archivo'] = $file->getSize();
                $materialData['formato'] = $file->getClientOriginalExtension();
            }

            $materialId = $this->materialService->crearMaterial($materialData);

            return redirect()->route('materiales.index')
                ->with('success', 'Material creado exitosamente.');
                
        } catch (Exception $e) {
            Log::error('Error al crear material: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al crear material: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar detalles de un material
     */
    public function show($id): View|RedirectResponse
    {
        try {
            $material = $this->materialService->getMaterialById($id);
            
            if (!$material) {
                return redirect()->route('materiales.index')
                    ->with('error', 'Material no encontrado.');
            }

            return view('materiales.show', [
                'title' => 'Detalles del Material - ' . $material['titulo'],
                'material' => $material
            ]);
        } catch (Exception $e) {
            Log::error('Error al cargar material: ' . $e->getMessage());
            return redirect()->route('materiales.index')
                ->with('error', 'Error al cargar material: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de edición de material
     */
    public function edit($id): View|RedirectResponse
    {
        try {
            $material = $this->materialService->getMaterialById($id);
            
            if (!$material) {
                return redirect()->route('materiales.index')
                    ->with('error', 'Material no encontrado.');
            }

            $categorias = $this->materialService->getAllCategories();
            $cursos = $this->materialService->getAllCursos();
            $docentes = $this->materialService->getAllDocentes();
            
            return view('materiales.edit', [
                'title' => 'Editar Material - ' . $material['titulo'],
                'material' => $material,
                'categorias' => $categorias,
                'cursos' => $cursos,
                'docentes' => $docentes
            ]);
        } catch (Exception $e) {
            Log::error('Error al cargar formulario de edición: ' . $e->getMessage());
            return redirect()->route('materiales.index')
                ->with('error', 'Error al cargar material: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar material
     */
    public function update(Request $request, $id): RedirectResponse
    {
        try {
            $material = $this->materialService->getMaterialById($id);
            
            if (!$material) {
                return redirect()->route('materiales.index')
                    ->with('error', 'Material no encontrado.');
            }

            // Validaciones
            $rules = [
                'titulo' => 'required|min:3|max:200',
                'descripcion' => 'nullable|max:1000',
                'tipo' => 'required|in:video,documento,presentacion,audio,enlace,ejercicio,imagen',
                'categoria_id' => 'required|integer|exists:categorias,id',
                'curso_id' => 'nullable|integer|exists:cursos,id',
                'componente_id' => 'nullable|integer|exists:componentes,id',
                'orden' => 'nullable|integer|min:0',
                'es_descargable' => 'boolean',
                'estado' => 'required|in:0,1'
            ];

            // Validación condicional
            if ($request->tipo === 'enlace' && $request->enlace_externo) {
                $rules['enlace_externo'] = 'required|url|max:500';
            } elseif ($request->hasFile('archivo_upload')) {
                $rules['archivo_upload'] = 'file|max:102400'; // 100MB
            }

            $request->validate($rules);

            // Preparar datos de actualización
            $materialData = [
                'titulo' => trim($request->titulo),
                'descripcion' => trim($request->descripcion),
                'tipo' => $request->tipo,
                'categoria_id' => $request->categoria_id,
                'curso_id' => $request->curso_id,
                'componente_id' => $request->componente_id,
                'orden' => $request->orden ?: $material['orden'],
                'es_descargable' => $request->boolean('es_descargable', true),
                'estado' => $request->estado,
                'fecha_actualizacion' => now()
            ];

            // Manejar cambio de archivo o enlace
            if ($request->tipo === 'enlace' && $request->enlace_externo) {
                // Eliminar archivo anterior si cambiamos a enlace
                if ($material['archivo_url'] && str_contains($material['archivo_url'], '/storage/')) {
                    $oldPath = str_replace('/storage/', '', $material['archivo_url']);
                    Storage::disk('public')->delete($oldPath);
                }
                
                $materialData['archivo_url'] = $request->enlace_externo;
                $materialData['tamaño_archivo'] = null;
                $materialData['formato'] = null;
                
            } elseif ($request->hasFile('archivo_upload')) {
                // Eliminar archivo anterior
                if ($material['archivo_url'] && str_contains($material['archivo_url'], '/storage/')) {
                    $oldPath = str_replace('/storage/', '', $material['archivo_url']);
                    Storage::disk('public')->delete($oldPath);
                }
                
                // Subir nuevo archivo
                $file = $request->file('archivo_upload');
                $path = $file->store('materiales', 'public');
                $materialData['archivo_url'] = Storage::url($path);
                $materialData['tamaño_archivo'] = $file->getSize();
                $materialData['formato'] = $file->getClientOriginalExtension();
            }

            $this->materialService->actualizarMaterial($id, $materialData);

            return redirect()->route('materiales.index')
                ->with('success', 'Material actualizado exitosamente.');
                
        } catch (Exception $e) {
            Log::error('Error al actualizar material: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al actualizar material: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Eliminar material
     */
    public function destroy($id): RedirectResponse
    {
        try {
            $material = $this->materialService->getMaterialById($id);
            
            if (!$material) {
                return redirect()->route('materiales.index')
                    ->with('error', 'Material no encontrado.');
            }

            // Eliminar archivo asociado si es local
            if ($material['archivo_url'] && str_contains($material['archivo_url'], '/storage/')) {
                $filePath = str_replace('/storage/', '', $material['archivo_url']);
                Storage::disk('public')->delete($filePath);
            }

            $this->materialService->eliminarMaterial($id);

            return redirect()->route('materiales.index')
                ->with('success', 'Material eliminado exitosamente.');
                
        } catch (Exception $e) {
            Log::error('Error al eliminar material: ' . $e->getMessage());
            return redirect()->route('materiales.index')
                ->with('error', 'Error al eliminar material: ' . $e->getMessage());
        }
    }

    /**
     * Previsualizar material
     */
    public function preview($id): View|RedirectResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return redirect()->route('login')
                    ->with('error', 'Debes iniciar sesión para ver este contenido.');
            }

            $material = $this->materialService->getMaterialById($id);
            
            if (!$material || $material['estado'] != 1) {
                return redirect()->back()
                    ->with('error', 'Material no disponible.');
            }

            // Verificar acceso al curso si aplica
            if ($material['curso_id'] && !$this->materialService->userHasAccessToCourse($user->id, $material['curso_id'])) {
                return redirect()->back()
                    ->with('error', 'No tienes acceso a este material.');
            }

            return view('materiales.preview', [
                'title' => $material['titulo'],
                'material' => $material
            ]);
        } catch (Exception $e) {
            Log::error('Error al previsualizar material: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al cargar material: ' . $e->getMessage());
        }
    }

    /**
     * Descargar material
     */
    public function download($id): \Symfony\Component\HttpFoundation\BinaryFileResponse|RedirectResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return redirect()->route('login')
                    ->with('error', 'Debes iniciar sesión para descargar este contenido.');
            }

            $material = $this->materialService->getMaterialById($id);
            
            if (!$material || $material['estado'] != 1) {
                return redirect()->back()
                    ->with('error', 'Material no disponible.');
            }

            if (!$material['es_descargable']) {
                return redirect()->back()
                    ->with('error', 'Este material no está disponible para descarga.');
            }

            // Verificar acceso al curso si aplica
            if ($material['curso_id'] && !$this->materialService->userHasAccessToCourse($user->id, $material['curso_id'])) {
                return redirect()->back()
                    ->with('error', 'No tienes acceso a este material.');
            }

            // Si es URL externa, redireccionar
            if (str_starts_with($material['archivo_url'], 'http')) {
                return redirect($material['archivo_url']);
            }

            // Si es archivo local, descargarlo
            $filePath = str_replace('/storage/', '', $material['archivo_url']);
            
            if (!Storage::disk('public')->exists($filePath)) {
                return redirect()->back()
                    ->with('error', 'Archivo no encontrado.');
            }

            return Storage::disk('public')->download($filePath, $material['titulo'] . '.' . $material['formato']);
            
        } catch (Exception $e) {
            Log::error('Error al descargar material: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al descargar material: ' . $e->getMessage());
        }
    }

    /**
     * API: Obtener materiales por curso
     */
    public function apiGetByCourse(Request $request, $cursoId): JsonResponse
    {
        try {
            $materiales = $this->materialService->getMaterialesByCurso($cursoId);
            
            return response()->json([
                'success' => true,
                'materiales' => $materiales
            ]);
            
        } catch (Exception $e) {
            Log::error('Error al obtener materiales por curso: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al cargar materiales'
            ], 500);
        }
    }

    /**
     * API: Obtener materiales por componente
     */
    public function apiGetByComponent(Request $request, $componenteId): JsonResponse
    {
        try {
            $materiales = $this->materialService->getMaterialesByComponente($componenteId);
            
            return response()->json([
                'success' => true,
                'materiales' => $materiales
            ]);
            
        } catch (Exception $e) {
            Log::error('Error al obtener materiales por componente: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al cargar materiales'
            ], 500);
        }
    }

    // ==========================================
    // MÉTODOS AUXILIARES PRIVADOS
    // ==========================================

    /**
     * Obtener el siguiente orden para curso/componente
     */
    private function getNextOrder($cursoId, $componenteId = null): int
    {
        return $this->materialService->getNextOrder($cursoId, $componenteId);
    }
}