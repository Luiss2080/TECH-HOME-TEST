<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Libro;
use App\Models\Categoria;
use App\Models\User;
use App\Services\LibroService;
use Exception;

class LibroController extends Controller
{
    private LibroService $libroService;

    public function __construct()
    {
        $this->libroService = new LibroService();
    }

    // ==========================================
    // MÉTODOS PÚBLICOS - VISUALIZACIÓN
    // ==========================================

    /**
     * Mostrar listado público de libros
     */
    public function index(Request $request): View
    {
        try {
            $filtros = [
                'categoria' => $request->get('categoria'),
                'autor' => $request->get('autor'),
                'editorial' => $request->get('editorial'),
                'tipo' => $request->get('tipo'), // gratuito|pago
                'buscar' => $request->get('buscar'),
                'orden' => $request->get('orden', 'titulo')
            ];

            $page = max(1, (int)$request->get('page', 1));
            $perPage = 12;

            $resultado = $this->libroService->getLibrosFiltrados($filtros, $page, $perPage);
            $categorias = $this->libroService->getCategorias();
            
            return view('libros.index', [
                'libros' => $resultado['libros'],
                'total' => $resultado['total'],
                'page' => $page,
                'perPage' => $perPage,
                'filtros' => $filtros,
                'categorias' => $categorias,
                'title' => 'Catálogo de Libros'
            ]);
        } catch (Exception $e) {
            Log::error('Error al cargar libros: ' . $e->getMessage());
            return view('errors.500', [
                'error' => 'Error al cargar los libros: ' . $e->getMessage(),
                'title' => 'Error'
            ]);
        }
    }

    /**
     * Mostrar detalles de un libro
     */
    public function show(int $id): View
    {
        try {
            $libro = $this->libroService->obtenerLibro($id);
            
            if (!$libro || $libro['estado'] != 1) {
                return view('errors.404', [
                    'message' => 'El libro solicitado no existe o no está disponible.',
                    'title' => 'Libro no encontrado'
                ]);
            }

            // Libros relacionados (misma categoría)
            $librosRelacionados = $this->libroService->getLibrosRelacionados($id, $libro['categoria_id']);
            
            return view('libros.show', [
                'libro' => $libro,
                'librosRelacionados' => $librosRelacionados,
                'title' => $libro['titulo']
            ]);
        } catch (Exception $e) {
            Log::error('Error al cargar libro: ' . $e->getMessage());
            return view('errors.500', [
                'error' => 'Error al cargar el libro: ' . $e->getMessage(),
                'title' => 'Error'
            ]);
        }
    }

    // ==========================================
    // MÉTODOS DE GESTIÓN (ADMIN/DOCENTE)
    // ==========================================

    /**
     * Mostrar panel de gestión de libros
     */
    public function manage(Request $request): View
    {
        try {
            $user = Auth::user();
            $isDocente = $user && $user->hasRole('docente');
            
            $filtros = [
                'categoria' => $request->get('categoria'),
                'estado' => $request->get('estado'),
                'autor' => $request->get('autor'),
                'buscar' => $request->get('buscar')
            ];

            // Si es docente, solo mostrar sus libros
            if ($isDocente && !$user->hasRole('administrador')) {
                $filtros['autor_id'] = $user->id;
            }

            $page = max(1, (int)$request->get('page', 1));
            $perPage = 20;

            $resultado = $this->libroService->getLibrosFiltrados($filtros, $page, $perPage);
            $categorias = $this->libroService->getCategorias();
            $estadisticas = $this->libroService->getEstadisticas();
            
            return view('libros.manage', [
                'libros' => $resultado['libros'],
                'total' => $resultado['total'],
                'page' => $page,
                'perPage' => $perPage,
                'filtros' => $filtros,
                'categorias' => $categorias,
                'estadisticas' => $estadisticas,
                'isDocente' => $isDocente,
                'title' => 'Gestión de Libros'
            ]);
        } catch (Exception $e) {
            Log::error('Error al cargar gestión de libros: ' . $e->getMessage());
            return view('libros.manage', [
                'libros' => [],
                'total' => 0,
                'page' => 1,
                'perPage' => 20,
                'filtros' => [],
                'categorias' => [],
                'estadisticas' => [],
                'isDocente' => false,
                'title' => 'Gestión de Libros'
            ])->with('error', 'Error al cargar libros: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de creación de libro
     */
    public function create(): View|RedirectResponse
    {
        try {
            $categorias = $this->libroService->getCategorias();
            
            return view('libros.create', [
                'categorias' => $categorias,
                'title' => 'Crear Nuevo Libro'
            ]);
        } catch (Exception $e) {
            Log::error('Error al cargar formulario de creación: ' . $e->getMessage());
            return redirect()->route('libros.manage')
                ->with('error', 'Error al cargar formulario: ' . $e->getMessage());
        }
    }

    /**
     * Guardar nuevo libro
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'titulo' => 'required|string|min:5|max:200',
                'descripcion' => 'required|string|min:20',
                'autor' => 'required|string|max:200',
                'editorial' => 'nullable|string|max:150',
                'isbn' => 'nullable|string|max:20',
                'categoria_id' => 'required|integer|exists:categorias,id',
                'archivo_pdf' => 'nullable|file|mimes:pdf|max:50240', // 50MB
                'archivo_url' => 'nullable|url',
                'imagen_portada' => 'nullable|url',
                'precio' => 'nullable|numeric|min:0',
                'estado' => 'required|in:0,1'
            ]);

            $user = Auth::user();
            
            $libroData = [
                'titulo' => trim($request->titulo),
                'descripcion' => trim($request->descripcion),
                'autor' => trim($request->autor),
                'editorial' => $request->editorial ? trim($request->editorial) : null,
                'isbn' => $request->isbn ? trim($request->isbn) : null,
                'categoria_id' => $request->categoria_id,
                'imagen_portada' => $request->imagen_portada,
                'precio' => $request->precio ?: 0,
                'es_gratuito' => $request->precio > 0 ? 0 : 1,
                'estado' => $request->estado,
                'autor_id' => $user->id,
                'fecha_publicacion' => now()
            ];

            // Manejar archivo PDF
            if ($request->hasFile('archivo_pdf')) {
                $path = $request->file('archivo_pdf')->store('libros', 'public');
                $libroData['archivo_url'] = Storage::url($path);
            } elseif ($request->archivo_url) {
                $libroData['archivo_url'] = $request->archivo_url;
            }

            $libroId = $this->libroService->crearLibro($libroData);

            return redirect()->route('libros.manage')
                ->with('success', 'Libro creado exitosamente.');
                
        } catch (Exception $e) {
            Log::error('Error al crear libro: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al crear libro: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar formulario de edición de libro
     */
    public function edit($id): View|RedirectResponse
    {
        try {
            $libro = $this->libroService->obtenerLibro($id);
            
            if (!$libro) {
                return redirect()->route('libros.manage')
                    ->with('error', 'Libro no encontrado.');
            }

            // Verificar permisos si es docente
            $user = Auth::user();
            if ($user && $user->hasRole('docente') && !$user->hasRole('administrador')) {
                if ($libro['autor_id'] != $user->id) {
                    return redirect()->route('libros.manage')
                        ->with('error', 'No tienes permisos para editar este libro.');
                }
            }

            $categorias = $this->libroService->getCategorias();
            
            return view('libros.edit', [
                'libro' => $libro,
                'categorias' => $categorias,
                'title' => 'Editar Libro - ' . $libro['titulo']
            ]);
        } catch (Exception $e) {
            Log::error('Error al cargar libro para edición: ' . $e->getMessage());
            return redirect()->route('libros.manage')
                ->with('error', 'Error al cargar libro: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar libro
     */
    public function update(Request $request, $id): RedirectResponse
    {
        try {
            $libro = $this->libroService->obtenerLibro($id);
            
            if (!$libro) {
                return redirect()->route('libros.manage')
                    ->with('error', 'Libro no encontrado.');
            }

            // Verificar permisos si es docente
            $user = Auth::user();
            if ($user && $user->hasRole('docente') && !$user->hasRole('administrador')) {
                if ($libro['autor_id'] != $user->id) {
                    return redirect()->route('libros.manage')
                        ->with('error', 'No tienes permisos para editar este libro.');
                }
            }

            $request->validate([
                'titulo' => 'required|string|min:5|max:200',
                'descripcion' => 'required|string|min:20',
                'autor' => 'required|string|max:200',
                'editorial' => 'nullable|string|max:150',
                'isbn' => 'nullable|string|max:20',
                'categoria_id' => 'required|integer|exists:categorias,id',
                'archivo_pdf' => 'nullable|file|mimes:pdf|max:50240', // 50MB
                'archivo_url' => 'nullable|url',
                'imagen_portada' => 'nullable|url',
                'precio' => 'nullable|numeric|min:0',
                'estado' => 'required|in:0,1'
            ]);

            $libroData = [
                'titulo' => trim($request->titulo),
                'descripcion' => trim($request->descripcion),
                'autor' => trim($request->autor),
                'editorial' => $request->editorial ? trim($request->editorial) : null,
                'isbn' => $request->isbn ? trim($request->isbn) : null,
                'categoria_id' => $request->categoria_id,
                'imagen_portada' => $request->imagen_portada,
                'precio' => $request->precio ?: 0,
                'es_gratuito' => $request->precio > 0 ? 0 : 1,
                'estado' => $request->estado,
                'fecha_actualizacion' => now()
            ];

            // Manejar archivo PDF
            if ($request->hasFile('archivo_pdf')) {
                // Eliminar archivo anterior si existe
                if ($libro['archivo_url'] && str_contains($libro['archivo_url'], '/storage/')) {
                    $oldPath = str_replace('/storage/', '', $libro['archivo_url']);
                    Storage::disk('public')->delete($oldPath);
                }
                
                $path = $request->file('archivo_pdf')->store('libros', 'public');
                $libroData['archivo_url'] = Storage::url($path);
            } elseif ($request->archivo_url && $request->archivo_url != $libro['archivo_url']) {
                $libroData['archivo_url'] = $request->archivo_url;
            }

            $this->libroService->actualizarLibro($id, $libroData);

            return redirect()->route('libros.manage')
                ->with('success', 'Libro actualizado exitosamente.');
                
        } catch (Exception $e) {
            Log::error('Error al actualizar libro: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al actualizar libro: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Eliminar libro
     */
    public function destroy($id): RedirectResponse
    {
        try {
            $libro = $this->libroService->obtenerLibro($id);
            
            if (!$libro) {
                return redirect()->route('libros.manage')
                    ->with('error', 'Libro no encontrado.');
            }

            // Verificar permisos si es docente
            $user = Auth::user();
            if ($user && $user->hasRole('docente') && !$user->hasRole('administrador')) {
                if ($libro['autor_id'] != $user->id) {
                    return redirect()->route('libros.manage')
                        ->with('error', 'No tienes permisos para eliminar este libro.');
                }
            }

            $this->libroService->eliminarLibro($id);

            return redirect()->route('libros.manage')
                ->with('success', 'Libro eliminado exitosamente.');
                
        } catch (Exception $e) {
            Log::error('Error al eliminar libro: ' . $e->getMessage());
            return redirect()->route('libros.manage')
                ->with('error', 'Error al eliminar libro: ' . $e->getMessage());
        }
    }

    /**
     * Descargar libro (requiere autenticación)
     */
    public function download($id): \Symfony\Component\HttpFoundation\BinaryFileResponse|RedirectResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return redirect()->route('login')
                    ->with('error', 'Debes iniciar sesión para descargar libros.');
            }

            $libro = $this->libroService->obtenerLibro($id);
            
            if (!$libro || $libro['estado'] != 1) {
                return redirect()->back()
                    ->with('error', 'Libro no disponible para descarga.');
            }

            // Verificar si es un libro de pago y el usuario tiene acceso
            if (!$libro['es_gratuito']) {
                $hasAccess = $this->libroService->userHasAccessToBook($user->id, $id);
                if (!$hasAccess && !$user->hasRole(['administrador', 'docente'])) {
                    return redirect()->back()
                        ->with('error', 'No tienes acceso a este libro. Debes comprarlo primero.');
                }
            }

            // Registrar descarga
            $this->libroService->registrarDescarga($user->id, $id);

            // Si es URL externa, redireccionar
            if (str_starts_with($libro['archivo_url'], 'http')) {
                return redirect($libro['archivo_url']);
            }

            // Si es archivo local, descargarlo
            $filePath = str_replace('/storage/', '', $libro['archivo_url']);
            
            if (!Storage::disk('public')->exists($filePath)) {
                return redirect()->back()
                    ->with('error', 'Archivo no encontrado.');
            }

            return Storage::disk('public')->download($filePath, $libro['titulo'] . '.pdf');
            
        } catch (Exception $e) {
            Log::error('Error al descargar libro: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al descargar libro: ' . $e->getMessage());
        }
    }

    /**
     * API: Buscar libros
     */
    public function apiSearch(Request $request): JsonResponse
    {
        try {
            $query = $request->get('q', '');
            
            if (strlen($query) < 2) {
                return response()->json(['libros' => []]);
            }

            $libros = $this->libroService->buscarLibros($query);
            
            return response()->json(['libros' => $libros]);
            
        } catch (Exception $e) {
            Log::error('Error en búsqueda de libros: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error en la búsqueda',
                'libros' => []
            ], 500);
        }
    }
}