<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Laboratorio;
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
            
            // Obtener laboratorios activos
            $query = Laboratorio::where('estado', 'activo');

            // Filtrar por rol (comentado hasta implementar sistema de roles)
            // if ($user->hasRole('docente')) {
            //     $query->where('responsable', $user->nombre);
            // }

            $laboratorios = $query->orderBy('nombre', 'asc')
              ->paginate(12);

            $estadisticas = [
                'total_laboratorios' => $laboratorios->total(),
                'laboratorios_activos' => Laboratorio::where('estado', 'activo')->count(),
                'laboratorios_disponibles' => Laboratorio::disponibles()->count(),
                'capacidad_total' => Laboratorio::sum('capacidad'),
            ];

            return view('laboratorios.index', compact('laboratorios', 'estadisticas'));
            
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
            /** @var \App\Models\User $user */
            $user = Auth::user();
            
            $laboratorio = Laboratorio::findOrFail($id);

            // Verificar permisos (comentado hasta implementar sistema de roles)
            // // if ($user->hasRole('docente') && $laboratorio->docente_id !== $user->id) {
            //     // return back()->withErrors(['error' => 'No tiene permisos para ver este laboratorio.']);
            // }

            // Estadísticas del laboratorio
            $estadisticas = [
                'capacidad' => $laboratorio->capacidad,
                'estado' => $laboratorio->estado,
                'disponibilidad' => $laboratorio->disponibilidad,
                'equipamiento_count' => is_array($laboratorio->equipamiento) ? count($laboratorio->equipamiento) : 0
            ];

            return view('laboratorios.ver', compact('laboratorio', 'estadisticas'));
            
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
            return view('laboratorios.crear');
            
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
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'categoria_id' => 'required|exists:categories,id',
            'responsable_id' => 'required|exists:users,id',
            'capacidad' => 'required|integer|min:1',
            'ubicacion' => 'required|string|max:255',
            'equipamiento' => 'nullable|array',
            'horario_disponible' => 'nullable|string',
            'activo' => 'boolean'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Crear laboratorio
            $laboratorio = Laboratorio::create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'categoria_id' => $request->categoria_id,
                'responsable_id' => $request->responsable_id,
                'capacidad' => $request->capacidad,
                'ubicacion' => $request->ubicacion,
                'equipamiento' => $request->equipamiento ?? [],
                'horario_disponible' => $request->horario_disponible,
                'activo' => $request->activo ?? true
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
            
            $laboratorio = Laboratorio::findOrFail($id);

            // Verificar permisos
            // if ($user->hasRole('docente') && $laboratorio->responsable_id !== $user->id) {
                // return back()->withErrors(['error' => 'No tiene permisos para editar este laboratorio.']);
            // }

            $categorias = Categoria::orderBy('nombre')->get();
            $responsables = User::orderBy('nombre')->get();

            return view('admin.laboratorios.edit', compact('laboratorio', 'categorias', 'responsables'));
            
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
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'categoria_id' => 'required|exists:categories,id',
            'responsable_id' => 'required|exists:users,id',
            'capacidad' => 'required|integer|min:1',
            'ubicacion' => 'required|string|max:255',
            'equipamiento' => 'nullable|array',
            'horario_disponible' => 'nullable|string',
            'activo' => 'boolean'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $user = Auth::user();
            
            $laboratorio = Laboratorio::findOrFail($id);

            // Verificar permisos
            // if ($user->hasRole('docente') && $laboratorio->responsable_id !== $user->id) {
                // return back()->withErrors(['error' => 'No tiene permisos para editar este laboratorio.']);
            // }

            $laboratorio->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'categoria_id' => $request->categoria_id,
                'responsable_id' => $request->responsable_id,
                'capacidad' => $request->capacidad,
                'ubicacion' => $request->ubicacion,
                'equipamiento' => $request->equipamiento ?? [],
                'horario_disponible' => $request->horario_disponible,
                'activo' => $request->activo ?? true
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
            
            $laboratorio = Laboratorio::findOrFail($id);

            // Verificar permisos
            // if ($user->hasRole('docente') && $laboratorio->responsable_id !== $user->id) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'No tiene permisos para eliminar este laboratorio.'], 403);
                }
                // return back()->withErrors(['error' => 'No tiene permisos para eliminar este laboratorio.']);
            // }

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
     * Gestionar equipamiento de laboratorio
     */
    public function components($id)
    {
        try {
            $user = Auth::user();
            
            $laboratorio = Laboratorio::findOrFail($id);

            // Verificar permisos
            // if ($user->hasRole('admin') && $laboratorio->responsable_id !== $user->id) {
                // return back()->withErrors(['error' => 'No tiene permisos para gestionar este laboratorio.']);
            // }

            return view('admin.laboratorios.components', compact('laboratorio'));
            
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar laboratorio: ' . $e->getMessage()]);
        }
    }

    /**
     * Reportes de laboratorio
     */
    public function reports($id)
    {
        try {
            $user = Auth::user();
            
            $laboratorio = Laboratorio::findOrFail($id);

            // Verificar permisos
            // if ($user->hasRole('admin') && $laboratorio->responsable_id !== $user->id) {
                // return back()->withErrors(['error' => 'No tiene permisos para ver los reportes de este laboratorio.']);
            // }

            // Estadísticas básicas del laboratorio
            $estadisticas = [
                'capacidad' => $laboratorio->capacidad,
                'ubicacion' => $laboratorio->ubicacion,
                'equipamiento_count' => count($laboratorio->equipamiento ?? []),
                'responsable' => $laboratorio->responsable->nombre ?? 'No asignado',
                'categoria' => $laboratorio->categoria->nombre ?? 'Sin categoría',
                'activo' => $laboratorio->activo
            ];

            return view('admin.laboratorios.reports', compact('laboratorio', 'estadisticas'));
            
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar reportes: ' . $e->getMessage()]);
        }
    }
}
