<?php

namespace App\Http\Controllers;

use App\Services\ComponenteService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Exception;

class ComponenteController extends Controller
{
    private $componenteService;

    public function __construct()
    {
        $this->componenteService = new ComponenteService();
    }

    /**
     * Listado principal de componentes
     */
    public function index(Request $request): View
    {
        try {
            // Datos predeterminados para evitar errores
            $componentes = [];
            $categorias = [];
            $marcas = [];
            $estadisticas = [
                'total_componentes' => 0,
                'agotados' => 0,
                'stock_bajo' => 0,
                'valor_inventario' => 0
            ];
            
            $filtros = [
                'busqueda' => $request->get('busqueda', ''),
                'categoria_id' => $request->get('categoria_id', ''),
                'estado' => $request->get('estado', ''),
                'marca' => $request->get('marca', ''),
                'stock_bajo' => $request->boolean('stock_bajo'),
                'pagina' => (int)$request->get('pagina', 1),
                'por_pagina' => (int)$request->get('por_pagina', 20)
            ];
            
            // Intentar cargar datos reales
            try {
                $data = $this->componenteService->listarComponentes($filtros);
                
                $componentes = $data['componentes'] ?? [];
                $categorias = $data['categorias'] ?? [];
                $marcas = $data['marcas'] ?? [];
                $estadisticas = $data['estadisticas'] ?? $estadisticas;
                
            } catch (Exception $serviceError) {
                Log::error('Error en servicio de componentes: ' . $serviceError->getMessage());
                session()->flash('error', 'Error en el servicio: ' . $serviceError->getMessage());
            }
            
            return view('componentes.index', compact(
                'componentes', 'categorias', 'marcas', 'filtros', 'estadisticas'
            ))->with('title', 'Gestión de Componentes');
            
        } catch (Exception $e) {
            Log::error('Error crítico al cargar componentes: ' . $e->getMessage());
            
            return view('componentes.index', [
                'title' => 'Gestión de Componentes',
                'componentes' => [],
                'categorias' => [],
                'marcas' => [],
                'filtros' => [],
                'estadisticas' => [
                    'total_componentes' => 0,
                    'agotados' => 0,
                    'stock_bajo' => 0,
                    'valor_inventario' => 0
                ]
            ])->with('error', 'Error crítico al cargar componentes: ' . $e->getMessage());
        }
    }

    /**
     * Formulario para crear nuevo componente
     */
    public function create(): View|RedirectResponse
    {
        try {
            $categorias = $this->componenteService->getCategorias();
            
            return view('componentes.create', [
                'title' => 'Crear Nuevo Componente',
                'categorias' => $categorias
            ]);
        } catch (Exception $e) {
            Log::error('Error al cargar formulario de creación: ' . $e->getMessage());
            return redirect()->route('componentes.index')
                ->with('error', 'Error al cargar formulario: ' . $e->getMessage());
        }
    }

    /**
     * Guardar nuevo componente
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'nombre' => 'required|string|min:3|max:200',
                'descripcion' => 'required|string|min:10',
                'categoria_id' => 'required|integer|exists:categorias,id',
                'precio' => 'required|numeric|min:0',
                'stock_actual' => 'required|integer|min:0',
                'stock_minimo' => 'required|integer|min:0',
                'marca' => 'nullable|string|max:100',
                'modelo' => 'nullable|string|max:100',
                'especificaciones' => 'nullable|string',
                'imagen_url' => 'nullable|url',
                'estado' => 'required|in:Activo,Inactivo,Descontinuado'
            ]);

            $componenteData = [
                'nombre' => trim($request->nombre),
                'descripcion' => trim($request->descripcion),
                'categoria_id' => $request->categoria_id,
                'precio' => $request->precio,
                'stock_actual' => $request->stock_actual,
                'stock_minimo' => $request->stock_minimo,
                'marca' => $request->marca ? trim($request->marca) : null,
                'modelo' => $request->modelo ? trim($request->modelo) : null,
                'especificaciones' => $request->especificaciones ? trim($request->especificaciones) : null,
                'imagen_url' => $request->imagen_url,
                'estado' => $request->estado,
                'fecha_creacion' => now(),
                'fecha_actualizacion' => now()
            ];

            $componenteId = $this->componenteService->crearComponente($componenteData);

            return redirect()->route('componentes.index')
                ->with('success', 'Componente creado exitosamente.');
                
        } catch (Exception $e) {
            Log::error('Error al crear componente: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al crear componente: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar detalles de un componente
     */
    public function show($id): View|RedirectResponse
    {
        try {
            $componente = $this->componenteService->getComponenteById($id);
            
            if (!$componente) {
                return redirect()->route('componentes.index')
                    ->with('error', 'Componente no encontrado.');
            }

            $historialStock = $this->componenteService->getHistorialStock($id);
            
            return view('componentes.show', [
                'title' => 'Detalles del Componente - ' . $componente['nombre'],
                'componente' => $componente,
                'historial' => $historialStock
            ]);
        } catch (Exception $e) {
            Log::error('Error al cargar componente: ' . $e->getMessage());
            return redirect()->route('componentes.index')
                ->with('error', 'Error al cargar componente: ' . $e->getMessage());
        }
    }

    /**
     * Formulario para editar componente
     */
    public function edit($id): View|RedirectResponse
    {
        try {
            $componente = $this->componenteService->getComponenteById($id);
            
            if (!$componente) {
                return redirect()->route('componentes.index')
                    ->with('error', 'Componente no encontrado.');
            }

            $categorias = $this->componenteService->getCategorias();
            
            return view('componentes.edit', [
                'title' => 'Editar Componente - ' . $componente['nombre'],
                'componente' => $componente,
                'categorias' => $categorias
            ]);
        } catch (Exception $e) {
            Log::error('Error al cargar formulario de edición: ' . $e->getMessage());
            return redirect()->route('componentes.index')
                ->with('error', 'Error al cargar componente: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar componente
     */
    public function update(Request $request, $id): RedirectResponse
    {
        try {
            $componente = $this->componenteService->getComponenteById($id);
            
            if (!$componente) {
                return redirect()->route('componentes.index')
                    ->with('error', 'Componente no encontrado.');
            }

            $request->validate([
                'nombre' => 'required|string|min:3|max:200',
                'descripcion' => 'required|string|min:10',
                'categoria_id' => 'required|integer|exists:categorias,id',
                'precio' => 'required|numeric|min:0',
                'stock_actual' => 'required|integer|min:0',
                'stock_minimo' => 'required|integer|min:0',
                'marca' => 'nullable|string|max:100',
                'modelo' => 'nullable|string|max:100',
                'especificaciones' => 'nullable|string',
                'imagen_url' => 'nullable|url',
                'estado' => 'required|in:Activo,Inactivo,Descontinuado'
            ]);

            $componenteData = [
                'nombre' => trim($request->nombre),
                'descripcion' => trim($request->descripcion),
                'categoria_id' => $request->categoria_id,
                'precio' => $request->precio,
                'stock_actual' => $request->stock_actual,
                'stock_minimo' => $request->stock_minimo,
                'marca' => $request->marca ? trim($request->marca) : null,
                'modelo' => $request->modelo ? trim($request->modelo) : null,
                'especificaciones' => $request->especificaciones ? trim($request->especificaciones) : null,
                'imagen_url' => $request->imagen_url,
                'estado' => $request->estado,
                'fecha_actualizacion' => now()
            ];

            $this->componenteService->actualizarComponente($id, $componenteData);

            return redirect()->route('componentes.index')
                ->with('success', 'Componente actualizado exitosamente.');
                
        } catch (Exception $e) {
            Log::error('Error al actualizar componente: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al actualizar componente: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Eliminar componente
     */
    public function destroy($id): RedirectResponse
    {
        try {
            $componente = $this->componenteService->getComponenteById($id);
            
            if (!$componente) {
                return redirect()->route('componentes.index')
                    ->with('error', 'Componente no encontrado.');
            }

            $this->componenteService->eliminarComponente($id);

            return redirect()->route('componentes.index')
                ->with('success', 'Componente eliminado exitosamente.');
                
        } catch (Exception $e) {
            Log::error('Error al eliminar componente: ' . $e->getMessage());
            return redirect()->route('componentes.index')
                ->with('error', 'Error al eliminar componente: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar stock de un componente
     */
    public function updateStock(Request $request, $id): JsonResponse|RedirectResponse
    {
        try {
            $request->validate([
                'tipo_movimiento' => 'required|in:entrada,salida',
                'cantidad' => 'required|integer|min:1',
                'motivo' => 'required|string|max:200',
                'precio_unitario' => 'nullable|numeric|min:0'
            ]);

            $movimientoData = [
                'componente_id' => $id,
                'tipo_movimiento' => $request->tipo_movimiento,
                'cantidad' => $request->cantidad,
                'motivo' => $request->motivo,
                'precio_unitario' => $request->precio_unitario,
                'fecha' => now()
            ];

            $resultado = $this->componenteService->actualizarStock($movimientoData);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Stock actualizado exitosamente',
                    'nuevo_stock' => $resultado['nuevo_stock']
                ]);
            }

            return redirect()->back()
                ->with('success', 'Stock actualizado exitosamente');
                
        } catch (Exception $e) {
            Log::error('Error al actualizar stock: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar stock: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error al actualizar stock: ' . $e->getMessage());
        }
    }

    /**
     * Exportar componentes a CSV
     */
    public function export(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        try {
            $filtros = [
                'busqueda' => $request->get('busqueda', ''),
                'categoria_id' => $request->get('categoria_id', ''),
                'estado' => $request->get('estado', ''),
                'marca' => $request->get('marca', ''),
                'stock_bajo' => $request->boolean('stock_bajo')
            ];

            return $this->componenteService->exportarCSV($filtros);
            
        } catch (Exception $e) {
            Log::error('Error al exportar componentes: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al exportar: ' . $e->getMessage());
        }
    }

    /**
     * API: Buscar componentes
     */
    public function apiSearch(Request $request): JsonResponse
    {
        try {
            $query = $request->get('q', '');
            
            if (strlen($query) < 2) {
                return response()->json(['componentes' => []]);
            }

            $componentes = $this->componenteService->buscarComponentes($query);
            
            return response()->json(['componentes' => $componentes]);
            
        } catch (Exception $e) {
            Log::error('Error en búsqueda de componentes: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error en la búsqueda',
                'componentes' => []
            ], 500);
        }
    }
}