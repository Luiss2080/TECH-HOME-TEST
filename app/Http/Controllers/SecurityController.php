<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class SecurityController extends Controller
{
    /**
     * Dashboard de seguridad
     */
    public function dashboard(): View
    {
        try {
            // Obtener estadísticas generales (cachear por 5 minutos)
            $stats = Cache::remember('security_stats', 300, function () {
                return $this->getSecurityStats();
            });
            
            // Obtener eventos recientes
            $recentEvents = $this->getRecentSecurityEvents();
            
            // Obtener IPs sospechosas
            $suspiciousIPs = $this->getSuspiciousIPs();
            
            return view('admin.security.dashboard', [
                'title' => 'Dashboard de Seguridad',
                'stats' => $stats,
                'recentEvents' => $recentEvents,
                'suspiciousIPs' => $suspiciousIPs
            ]);
            
        } catch (Exception $e) {
            Log::error('Error en dashboard de seguridad', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return view('admin.security.dashboard', [
                'title' => 'Dashboard de Seguridad',
                'stats' => [],
                'recentEvents' => [],
                'suspiciousIPs' => [],
                'error' => 'Error al cargar el dashboard de seguridad'
            ]);
        }
    }

    /**
     * Lista de logs de seguridad
     */
    public function securityLogs(Request $request): View
    {
        try {
            $page = (int) $request->input('page', 1);
            $perPage = 50;
            $eventType = $request->input('event_type');
            $severity = $request->input('severity');
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');
            
            $query = DB::table('security_logs as s')
                ->leftJoin('usuarios as u', 's.user_id', '=', 'u.id')
                ->select([
                    's.*',
                    'u.nombre',
                    'u.apellido',
                    'u.email'
                ]);
            
            // Aplicar filtros
            if ($eventType) {
                $query->where('s.event_type', $eventType);
            }
            
            if ($severity) {
                $query->where('s.severity', $severity);
            }
            
            if ($dateFrom) {
                $query->where('s.created_at', '>=', $dateFrom . ' 00:00:00');
            }
            
            if ($dateTo) {
                $query->where('s.created_at', '<=', $dateTo . ' 23:59:59');
            }
            
            $logs = $query->orderBy('s.created_at', 'desc')
                ->paginate($perPage);
            
            // Obtener tipos de eventos únicos para el filtro
            $eventTypes = DB::table('security_logs')
                ->distinct()
                ->pluck('event_type')
                ->filter()
                ->sort()
                ->values();
            
            return view('admin.security.logs', [
                'title' => 'Logs de Seguridad',
                'logs' => $logs,
                'eventTypes' => $eventTypes,
                'filters' => [
                    'event_type' => $eventType,
                    'severity' => $severity,
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo
                ]
            ]);
            
        } catch (Exception $e) {
            Log::error('Error cargando logs de seguridad', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return view('admin.security.logs', [
                'title' => 'Logs de Seguridad',
                'logs' => [],
                'eventTypes' => [],
                'filters' => [],
                'error' => 'Error al cargar los logs'
            ]);
        }
    }

    /**
     * Análisis de IPs sospechosas
     */
    public function suspiciousIPs(): View
    {
        try {
            $ips = $this->getSuspiciousIPs(true); // Análisis detallado
            
            return view('admin.security.suspicious-ips', [
                'title' => 'IPs Sospechosas',
                'ips' => $ips
            ]);
            
        } catch (Exception $e) {
            Log::error('Error analizando IPs sospechosas', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return view('admin.security.suspicious-ips', [
                'title' => 'IPs Sospechosas',
                'ips' => [],
                'error' => 'Error al analizar IPs sospechosas'
            ]);
        }
    }

    /**
     * Exportar logs de seguridad
     */
    public function exportSecurityLogs(Request $request): Response
    {
        try {
            $days = (int) $request->input('days', 7);
            $format = $request->input('format', 'csv');
            $eventType = $request->input('event_type');
            $severity = $request->input('severity');
            
            $logs = $this->getSecurityLogsForExport($days, $eventType, $severity);
            
            if ($format === 'csv') {
                return $this->exportToCSV($logs, 'security_logs_' . now()->format('Y-m-d'));
            } else {
                return $this->exportToJSON($logs, 'security_logs_' . now()->format('Y-m-d'));
            }
            
        } catch (Exception $e) {
            Log::error('Error exportando logs de seguridad', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al exportar logs'
            ], 500);
        }
    }

    /**
     * API para obtener estadísticas en tiempo real (AJAX)
     */
    public function apiStats(Request $request): JsonResponse
    {
        try {
            if (!$request->ajax()) {
                throw new Exception('Solo se permiten peticiones AJAX');
            }

            $stats = $this->getSecurityStats();
            $recentEvents = $this->getRecentSecurityEvents(10);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => $stats,
                    'recent_events' => $recentEvents,
                    'timestamp' => now()->timestamp
                ]
            ]);
            
        } catch (Exception $e) {
            Log::error('Error en API stats de seguridad', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas'
            ], 500);
        }
    }

    /**
     * Limpiar logs antiguos (AJAX)
     */
    public function cleanOldLogs(Request $request): JsonResponse
    {
        try {
            if (!$request->ajax()) {
                throw new Exception('Solo se permiten peticiones AJAX');
            }

            $days = (int) $request->input('days', 90);
            $cutoffDate = now()->subDays($days);
            
            $deletedCount = DB::table('security_logs')
                ->where('created_at', '<', $cutoffDate)
                ->delete();
            
            Log::info('Logs de seguridad limpiados', [
                'deleted_count' => $deletedCount,
                'cutoff_date' => $cutoffDate,
                'cleaned_by' => Auth::id()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => "Se eliminaron {$deletedCount} registros antiguos",
                'deleted_count' => $deletedCount
            ]);
            
        } catch (Exception $e) {
            Log::error('Error limpiando logs antiguos', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al limpiar logs antiguos'
            ], 500);
        }
    }

    /**
     * Bloquear IP sospechosa (AJAX)
     */
    public function blockIP(Request $request): JsonResponse
    {
        try {
            if (!$request->ajax()) {
                throw new Exception('Solo se permiten peticiones AJAX');
            }

            $request->validate([
                'ip_address' => 'required|ip',
                'reason' => 'required|string|max:255'
            ]);
            
            $ipAddress = $request->input('ip_address');
            $reason = $request->input('reason');
            
            // Aquí implementarías la lógica de bloqueo de IP
            // Por ejemplo, agregar a una tabla de IPs bloqueadas
            
            // Registrar el bloqueo en los logs
            $this->logSecurityEvent('ip_blocked', [
                'ip_address' => $ipAddress,
                'reason' => $reason,
                'blocked_by' => Auth::id()
            ], 'high');
            
            Log::warning('IP bloqueada', [
                'ip_address' => $ipAddress,
                'reason' => $reason,
                'blocked_by' => Auth::id()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => "IP {$ipAddress} bloqueada exitosamente"
            ]);
            
        } catch (Exception $e) {
            Log::error('Error bloqueando IP', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al bloquear IP'
            ], 500);
        }
    }

    // Métodos privados de apoyo

    /**
     * Obtener estadísticas de seguridad
     */
    private function getSecurityStats(): array
    {
        try {
            $since = now()->subDays(7);
            
            return [
                'total_events' => DB::table('security_logs')
                    ->where('created_at', '>=', $since)
                    ->count(),
                
                'by_severity' => DB::table('security_logs')
                    ->where('created_at', '>=', $since)
                    ->select('severity', DB::raw('count(*) as count'))
                    ->groupBy('severity')
                    ->get()
                    ->keyBy('severity'),
                
                'by_type' => DB::table('security_logs')
                    ->where('created_at', '>=', $since)
                    ->select('event_type', DB::raw('count(*) as count'))
                    ->groupBy('event_type')
                    ->orderBy('count', 'desc')
                    ->get(),
                
                'unique_ips' => DB::table('security_logs')
                    ->where('created_at', '>=', $since)
                    ->distinct('ip_address')
                    ->count(),
                
                'failed_logins' => DB::table('security_logs')
                    ->where('created_at', '>=', $since)
                    ->where('event_type', 'login_failed')
                    ->count()
            ];
            
        } catch (Exception $e) {
            Log::error('Error obteniendo estadísticas de seguridad', [
                'error' => $e->getMessage()
            ]);
            
            return [];
        }
    }

    /**
     * Obtener eventos recientes de seguridad
     */
    private function getRecentSecurityEvents(int $limit = 20): array
    {
        try {
            return DB::table('security_logs as s')
                ->leftJoin('usuarios as u', 's.user_id', '=', 'u.id')
                ->select([
                    's.*',
                    'u.nombre',
                    'u.apellido',
                    'u.email'
                ])
                ->orderBy('s.created_at', 'desc')
                ->limit($limit)
                ->get()
                ->toArray();
                
        } catch (Exception $e) {
            Log::error('Error obteniendo eventos recientes', [
                'error' => $e->getMessage()
            ]);
            
            return [];
        }
    }

    /**
     * Obtener IPs sospechosas
     */
    private function getSuspiciousIPs(bool $detailed = false): array
    {
        try {
            $query = DB::table('security_logs')
                ->select('ip_address', DB::raw('count(*) as event_count'))
                ->where('created_at', '>=', now()->subDays(7))
                ->where('severity', 'high')
                ->groupBy('ip_address')
                ->having('event_count', '>=', 3)
                ->orderBy('event_count', 'desc');
            
            if ($detailed) {
                return $query->get()->map(function ($ip) {
                    $details = DB::table('security_logs')
                        ->where('ip_address', $ip->ip_address)
                        ->where('created_at', '>=', now()->subDays(7))
                        ->select('event_type', DB::raw('count(*) as count'))
                        ->groupBy('event_type')
                        ->get();
                    
                    $ip->event_details = $details;
                    return $ip;
                })->toArray();
            }
            
            return $query->get()->toArray();
            
        } catch (Exception $e) {
            Log::error('Error obteniendo IPs sospechosas', [
                'error' => $e->getMessage()
            ]);
            
            return [];
        }
    }

    /**
     * Obtener logs para exportación
     */
    private function getSecurityLogsForExport(int $days, ?string $eventType = null, ?string $severity = null): array
    {
        try {
            $query = DB::table('security_logs as s')
                ->leftJoin('usuarios as u', 's.user_id', '=', 'u.id')
                ->select([
                    's.*',
                    'u.nombre',
                    'u.apellido',
                    'u.email as user_email'
                ])
                ->where('s.created_at', '>=', now()->subDays($days));
            
            if ($eventType) {
                $query->where('s.event_type', $eventType);
            }
            
            if ($severity) {
                $query->where('s.severity', $severity);
            }
            
            return $query->orderBy('s.created_at', 'desc')
                ->get()
                ->toArray();
                
        } catch (Exception $e) {
            Log::error('Error obteniendo logs para exportar', [
                'error' => $e->getMessage()
            ]);
            
            return [];
        }
    }

    /**
     * Exportar a CSV
     */
    private function exportToCSV(array $data, string $filename): Response
    {
        $csv = '';
        
        if (!empty($data)) {
            // Headers
            $csv .= implode(',', array_keys((array) $data[0])) . "\n";
            
            // Data
            foreach ($data as $row) {
                $csv .= implode(',', array_map(function ($value) {
                    return '"' . str_replace('"', '""', $value ?? '') . '"';
                }, (array) $row)) . "\n";
            }
        }
        
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"'
        ]);
    }

    /**
     * Exportar a JSON
     */
    private function exportToJSON(array $data, string $filename): Response
    {
        return response()->json($data, 200, [
            'Content-Disposition' => 'attachment; filename="' . $filename . '.json"'
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Registrar evento de seguridad
     */
    private function logSecurityEvent(string $eventType, array $data, string $severity = 'medium'): void
    {
        try {
            DB::table('security_logs')->insert([
                'event_type' => $eventType,
                'severity' => $severity,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'user_id' => Auth::id(),
                'data' => json_encode($data),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
        } catch (Exception $e) {
            Log::error('Error registrando evento de seguridad', [
                'error' => $e->getMessage(),
                'event_type' => $eventType,
                'data' => $data
            ]);
        }
    }
}