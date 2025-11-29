<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\Categoria;
use App\Models\Componente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;

class LaboratorioController extends Controller
{
    /**
     * Mostrar lista de laboratorios
     */
    public function index(): View
    {
        try {
            // Obtener estadísticas generales
            $estadisticas = [
                'total_laboratorios' => 0, // Implementar cuando tengas modelo Laboratorio
                'laboratorios_activos' => 0,
                'total_participantes' => 0,
                'componentes_utilizados' => Componente::where('stock', '>', 0)->count()
            ];

            $categorias = Categoria::orderBy('nombre')->get();
            $docentes = User::role('Docente')->orderBy('nombre')->get();

            return view('admin.laboratorios.index', [
                'title' => 'Gestión de Laboratorios',
                'laboratorios' => [], // Implementar cuando tengas modelo Laboratorio
                'categorias' => $categorias,
                'docentes' => $docentes,
                'estadisticas' => $estadisticas
            ]);
            
        } catch (Exception $e) {
            return view('admin.laboratorios.index', [
                'title' => 'Gestión de Laboratorios',
                'laboratorios' => [],
                'categorias' => [],
                'docentes' => [],
                'estadisticas' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Mostrar formulario de creación
     */
    public function create(): View
    {
        try {
            $categorias = Categoria::orderBy('nombre')->get();
            $docentes = User::role('Docente')->orderBy('nombre')->get();
            $componentes = Componente::where('stock', '>', 0)->orderBy('nombre')->get();

            return view('admin.laboratorios.create', [
                'title' => 'Crear Laboratorio',
                'categorias' => $categorias,
                'docentes' => $docentes,
                'componentes' => $componentes
            ]);
            
        } catch (Exception $e) {
            return redirect()->route('admin.laboratorios.index')
                ->with('error', 'Error al cargar el formulario: ' . $e->getMessage());
        }
    }

    /**
     * Procesar creación de laboratorio
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|min:5|max:200',
                'descripcion' => 'required|min:20|max:1000',
                'categoria_id' => 'required|exists:categorias,id',
                'docente_id' => 'required|exists:usuarios,id',
                'nivel' => 'required|in:Principiante,Intermedio,Avanzado',
                'duracion_estimada' => 'required|numeric|min:1',
                'capacidad_maxima' => 'required|numeric|min:1|max:50',
                'fecha_inicio' => 'nullable|date|after:today',
                'fecha_fin' => 'nullable|date|after:fecha_inicio',
                'componentes' => 'array',
                'componentes.*' => 'exists:componentes,id',
                'es_publico' => 'boolean',
                'es_destacado' => 'boolean'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Aquí implementarías la creación del laboratorio cuando tengas el modelo
            // $laboratorio = Laboratorio::create($request->all());

            return redirect()->route('admin.laboratorios.index')
                ->with('success', 'Laboratorio creado exitosamente.');
            
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al crear el laboratorio: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar detalle de laboratorio
     */
    public function show(int $id): View|RedirectResponse
    {
        try {
            // Implementar cuando tengas modelo Laboratorio
            // $laboratorio = Laboratorio::with(['categoria', 'docente', 'participantes', 'componentes'])->findOrFail($id);

            return view('admin.laboratorios.show', [
                'title' => 'Detalle del Laboratorio',
                'laboratorio' => null, // $laboratorio cuando esté implementado
                'estadisticas' => [
                    'total_participantes' => 0,
                    'progreso_promedio' => 0,
                    'componentes_utilizados' => 0,
                    'sesiones_completadas' => 0
                ]
            ]);
            
        } catch (Exception $e) {
            return redirect()->route('admin.laboratorios.index')
                ->with('error', 'Laboratorio no encontrado.');
        }
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(int $id): View|RedirectResponse
    {
        try {
            // Implementar cuando tengas modelo Laboratorio
            // $laboratorio = Laboratorio::findOrFail($id);

            $categorias = Categoria::orderBy('nombre')->get();
            $docentes = User::role('Docente')->orderBy('nombre')->get();
            $componentes = Componente::where('stock', '>', 0)->orderBy('nombre')->get();

            return view('admin.laboratorios.edit', [
                'title' => 'Editar Laboratorio',
                'laboratorio' => null, // $laboratorio cuando esté implementado
                'categorias' => $categorias,
                'docentes' => $docentes,
                'componentes' => $componentes
            ]);
            
        } catch (Exception $e) {
            return redirect()->route('admin.laboratorios.index')
                ->with('error', 'Laboratorio no encontrado.');
        }
    }

    /**
     * Procesar actualización de laboratorio
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|min:5|max:200',
                'descripcion' => 'required|min:20|max:1000',
                'categoria_id' => 'required|exists:categorias,id',
                'docente_id' => 'required|exists:usuarios,id',
                'nivel' => 'required|in:Principiante,Intermedio,Avanzado',
                'estado' => 'required|in:Programado,En_Curso,Completado,Cancelado',
                'es_publico' => 'boolean',
                'es_destacado' => 'boolean'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Implementar cuando tengas modelo Laboratorio
            // $laboratorio = Laboratorio::findOrFail($id);
            // $laboratorio->update($request->all());

            return redirect()->route('admin.laboratorios.show', $id)
                ->with('success', 'Laboratorio actualizado exitosamente.');
            
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar el laboratorio: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Eliminar laboratorio
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            // Implementar cuando tengas modelo Laboratorio
            // $laboratorio = Laboratorio::findOrFail($id);
            // 
            // // Verificar si tiene participantes activos
            // if ($laboratorio->participantes()->where('estado', 'activo')->count() > 0) {
            //     return redirect()->route('admin.laboratorios.index')
            //         ->with('error', 'No puedes eliminar un laboratorio con participantes activos.');
            // }
            // 
            // $laboratorio->delete();

            return redirect()->route('admin.laboratorios.index')
                ->with('success', 'Laboratorio eliminado exitosamente.');
            
        } catch (Exception $e) {
            return redirect()->route('admin.laboratorios.index')
                ->with('error', 'Error al eliminar el laboratorio: ' . $e->getMessage());
        }
    }

    /**
     * Buscar laboratorios (AJAX)
     */
    public function search(Request $request): JsonResponse
    {
        try {
            if (!$request->ajax()) {
                throw new Exception('Solo se permiten peticiones AJAX');
            }

            $filters = [
                'buscar' => $request->get('buscar', ''),
                'estado' => $request->get('estado', 'todos'),
                'nivel' => $request->get('nivel', 'todos'),
                'categoria' => $request->get('categoria', 'todas'),
                'docente' => $request->get('docente', 'todos'),
                'publico' => $request->get('publico', ''),
                'destacado' => $request->get('destacado', ''),
                'fecha_desde' => $request->get('fecha_desde', ''),
                'fecha_hasta' => $request->get('fecha_hasta', ''),
                'orden' => $request->get('orden', 'fecha_desc')
            ];

            // Implementar búsqueda cuando tengas modelo Laboratorio
            $laboratorios = []; // Resultado de la búsqueda

            return response()->json([
                'success' => true,
                'data' => $laboratorios,
                'total' => count($laboratorios)
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar laboratorios: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambiar estado del laboratorio (AJAX)
     */
    public function changeStatus(Request $request, int $id): JsonResponse
    {
        try {
            if (!$request->ajax()) {
                throw new Exception('Solo se permiten peticiones AJAX');
            }

            $validator = Validator::make($request->all(), [
                'estado' => 'required|in:Programado,En_Curso,Completado,Cancelado'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Estado inválido'
                ], 400);
            }

            // Implementar cuando tengas modelo Laboratorio
            // $laboratorio = Laboratorio::findOrFail($id);
            // $laboratorio->update(['estado' => $request->estado]);

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado correctamente'
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Agregar participante (AJAX)
     */
    public function addParticipante(Request $request, int $id): JsonResponse
    {
        try {
            if (!$request->ajax()) {
                throw new Exception('Solo se permiten peticiones AJAX');
            }

            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:usuarios,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario inválido'
                ], 400);
            }

            // Implementar cuando tengas modelo Laboratorio
            // $laboratorio = Laboratorio::findOrFail($id);
            // $result = $laboratorio->participantes()->attach($request->user_id);

            return response()->json([
                'success' => true,
                'message' => 'Participante agregado exitosamente'
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al agregar participante: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Remover participante (AJAX)
     */
    public function removeParticipante(Request $request, int $id): JsonResponse
    {
        try {
            if (!$request->ajax()) {
                throw new Exception('Solo se permiten peticiones AJAX');
            }

            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:usuarios,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario inválido'
                ], 400);
            }

            // Implementar cuando tengas modelo Laboratorio
            // $laboratorio = Laboratorio::findOrFail($id);
            // $result = $laboratorio->participantes()->detach($request->user_id);

            return response()->json([
                'success' => true,
                'message' => 'Participante removido exitosamente'
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al remover participante: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Dashboard de laboratorios para docentes
     */
    public function dashboard(): View
    {
        try {
            $docenteId = Auth::id();
            
            // Estadísticas del docente
            $estadisticas = [
                'laboratorios_asignados' => 0, // Implementar con modelo Laboratorio
                'laboratorios_activos' => 0,
                'total_participantes' => 0,
                'promedio_asistencia' => 0,
                'componentes_asignados' => 0,
                'sesiones_mes' => 0
            ];

            return view('docente.laboratorios.dashboard', [
                'title' => 'Dashboard de Laboratorios',
                'estadisticas' => $estadisticas,
                'laboratorios_recientes' => [], // Implementar
                'participantes_activos' => [], // Implementar
                'alertas' => [] // Implementar
            ]);
            
        } catch (Exception $e) {
            return view('docente.laboratorios.dashboard', [
                'title' => 'Dashboard de Laboratorios',
                'estadisticas' => [],
                'laboratorios_recientes' => [],
                'participantes_activos' => [],
                'alertas' => [],
                'error' => $e->getMessage()
            ]);
        }
    }
}