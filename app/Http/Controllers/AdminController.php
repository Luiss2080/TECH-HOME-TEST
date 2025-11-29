<?php

namespace App\Http\Controllers;

use App\Services\AdminService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class AdminController extends Controller
{
    private $adminService;

    public function __construct()
    {
        $this->adminService = new AdminService();
    }

    /**
     * Dashboard principal del administrador
     */
    public function index(): View
    {
        try {
            // Obtener datos del dashboard usando el servicio
            $data = $this->adminService->showDashboard();
            return view('admin.dashboard', array_merge($data, ['title' => 'Dashboard - Panel de Administración']));
        } catch (Exception $e) {
            Log::error('Error en dashboard admin: ' . $e->getMessage());
            return view('admin.dashboard', [
                'title' => 'Dashboard - Panel de Administración',
                'estadisticas' => [],
                'resumen_sistema' => [],
                'actividad_reciente' => []
            ])->with('error', 'Error al cargar el dashboard: ' . $e->getMessage());
        }
    }

    /**
     * API: Obtener estadísticas para AJAX
     */
    public function ajaxStats(Request $request): JsonResponse
    {
        try {
            $type = $request->get('tipo', 'general');
            $data = $this->adminService->getStatsForAjax($type);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (Exception $e) {
            Log::error('Error al obtener stats AJAX: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al cargar estadísticas'
            ], 500);
        }
    }

    /**
     * API: Refrescar métricas del dashboard
     */
    public function refreshMetrics(Request $request): JsonResponse
    {
        try {
            if (!$request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Solo se permiten peticiones AJAX'
                ], 400);
            }

            $stats = $this->adminService->showDashboard();

            return response()->json([
                'success' => true,
                'estadisticas' => $stats['estadisticas'],
                'resumen_sistema' => $stats['resumen_sistema']
            ]);
        } catch (Exception $e) {
            Log::error('Error al refrescar métricas: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error al refrescar datos'
            ], 500);
        }
    }

    /**
     * Mostrar página de reportes
     */
    public function reportes(): View
    {
        try {
            $reportes = $this->adminService->getAvailableReports();
            
            return view('admin.reportes', [
                'title' => 'Reportes - Panel de Administración',
                'reportes' => $reportes
            ]);
        } catch (Exception $e) {
            Log::error('Error al cargar reportes: ' . $e->getMessage());
            return view('admin.reportes', [
                'title' => 'Reportes - Panel de Administración',
                'reportes' => []
            ])->with('error', 'Error al cargar reportes');
        }
    }

    /**
     * Mostrar página de configuración
     */
    public function configuracion(): View
    {
        try {
            $configuraciones = $this->adminService->getSystemConfigurations();
            
            return view('admin.configuracion', [
                'title' => 'Configuración - Panel de Administración',
                'configuraciones' => $configuraciones
            ]);
        } catch (Exception $e) {
            Log::error('Error al cargar configuraciones: ' . $e->getMessage());
            return view('admin.configuracion', [
                'title' => 'Configuración - Panel de Administración',
                'configuraciones' => []
            ])->with('error', 'Error al cargar configuraciones');
        }
    }

    // ==========================================
    // MÉTODOS PARA GESTIÓN DE USUARIOS
    // ==========================================

    /**
     * Listado de usuarios
     */
    public function usuarios(Request $request): View
    {
        try {
            $filtros = [
                'busqueda' => $request->get('busqueda'),
                'rol' => $request->get('rol'),
                'estado' => $request->get('estado'),
                'fecha_desde' => $request->get('fecha_desde'),
                'fecha_hasta' => $request->get('fecha_hasta')
            ];

            $page = max(1, (int)$request->get('page', 1));
            $perPage = 25;

            $resultado = $this->adminService->getUsuariosFiltrados($filtros, $page, $perPage);
            $roles = $this->adminService->getAllRoles();
            
            return view('admin.usuarios.index', [
                'title' => 'Gestión de Usuarios - Panel de Administración',
                'usuarios' => $resultado['usuarios'],
                'total' => $resultado['total'],
                'page' => $page,
                'perPage' => $perPage,
                'filtros' => $filtros,
                'roles' => $roles
            ]);
        } catch (Exception $e) {
            Log::error('Error al cargar usuarios: ' . $e->getMessage());
            return view('admin.usuarios.index', [
                'title' => 'Gestión de Usuarios - Panel de Administración',
                'usuarios' => [],
                'total' => 0,
                'page' => 1,
                'perPage' => 25,
                'filtros' => [],
                'roles' => []
            ])->with('error', 'Error al cargar usuarios: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar detalles de un usuario
     */
    public function mostrarUsuario($id): View|RedirectResponse
    {
        try {
            $usuario = $this->adminService->getUsuarioById($id);
            
            if (!$usuario) {
                return redirect()->route('admin.usuarios')
                    ->with('error', 'Usuario no encontrado.');
            }

            $historialActividad = $this->adminService->getHistorialActividadUsuario($id);
            
            return view('admin.usuarios.show', [
                'title' => 'Detalles de Usuario - ' . $usuario['nombre_completo'],
                'usuario' => $usuario,
                'historial' => $historialActividad
            ]);
        } catch (Exception $e) {
            Log::error('Error al cargar usuario: ' . $e->getMessage());
            return redirect()->route('admin.usuarios')
                ->with('error', 'Error al cargar usuario: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar estado de un usuario
     */
    public function cambiarEstadoUsuario(Request $request, $id): JsonResponse|RedirectResponse
    {
        try {
            $request->validate([
                'estado' => 'required|in:0,1',
                'motivo' => 'nullable|string|max:500'
            ]);

            $resultado = $this->adminService->cambiarEstadoUsuario($id, $request->estado, $request->motivo);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Estado actualizado correctamente'
                ]);
            }

            return redirect()->back()
                ->with('success', 'Estado del usuario actualizado correctamente.');
                
        } catch (Exception $e) {
            Log::error('Error al cambiar estado de usuario: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Error al actualizar estado'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error al actualizar estado: ' . $e->getMessage());
        }
    }

    /**
     * Asignar rol a usuario
     */
    public function asignarRol(Request $request, $id): JsonResponse|RedirectResponse
    {
        try {
            $request->validate([
                'rol_id' => 'required|integer|exists:roles,id'
            ]);

            $this->adminService->asignarRolUsuario($id, $request->rol_id);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Rol asignado correctamente'
                ]);
            }

            return redirect()->back()
                ->with('success', 'Rol asignado correctamente.');
                
        } catch (Exception $e) {
            Log::error('Error al asignar rol: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Error al asignar rol'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error al asignar rol: ' . $e->getMessage());
        }
    }

    // ==========================================
    // MÉTODOS DE SISTEMA Y MANTENIMIENTO
    // ==========================================

    /**
     * Limpiar caché del sistema
     */
    public function limpiarCache(Request $request): JsonResponse|RedirectResponse
    {
        try {
            $this->adminService->limpiarCache();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Caché limpiado correctamente'
                ]);
            }

            return redirect()->back()
                ->with('success', 'Caché del sistema limpiado correctamente.');
                
        } catch (Exception $e) {
            Log::error('Error al limpiar caché: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Error al limpiar caché'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error al limpiar caché: ' . $e->getMessage());
        }
    }

    /**
     * Generar reporte
     */
    public function generarReporte(Request $request): JsonResponse|\Illuminate\Http\Response
    {
        try {
            $request->validate([
                'tipo' => 'required|string|in:usuarios,cursos,materiales,actividad,sistema',
                'formato' => 'required|string|in:pdf,excel,csv',
                'fecha_desde' => 'nullable|date',
                'fecha_hasta' => 'nullable|date|after_or_equal:fecha_desde'
            ]);

            $reporte = $this->adminService->generarReporte(
                $request->tipo,
                $request->formato,
                $request->fecha_desde,
                $request->fecha_hasta
            );

            return response($reporte['contenido'], 200, [
                'Content-Type' => $reporte['mime_type'],
                'Content-Disposition' => 'attachment; filename="' . $reporte['nombre_archivo'] . '"'
            ]);
            
        } catch (Exception $e) {
            Log::error('Error al generar reporte: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Error al generar reporte'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error al generar reporte: ' . $e->getMessage());
        }
    }

    /**
     * Información del sistema
     */
    public function infoSistema(): View
    {
        try {
            $info = $this->adminService->getSystemInfo();
            
            return view('admin.sistema-info', [
                'title' => 'Información del Sistema',
                'info' => $info
            ]);
        } catch (Exception $e) {
            Log::error('Error al cargar info del sistema: ' . $e->getMessage());
            return view('admin.sistema-info', [
                'title' => 'Información del Sistema',
                'info' => []
            ])->with('error', 'Error al cargar información del sistema');
        }
    }
}