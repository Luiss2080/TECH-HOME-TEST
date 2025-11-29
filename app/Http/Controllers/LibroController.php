<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Models\Libro;
use App\Models\Categoria;
use App\Models\User;
use App\Models\BookDownload;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class LibroController extends Controller
{
    /**
     * Muestra el catálogo público de libros
     */
    public function catalogo(Request $request): View
    {
        try {
            $query = Libro::with(['categoria'])
                         ->where('estado', 'Disponible');

            // Filtros
            if ($request->filled('categoria')) {
                $query->where('categoria_id', $request->categoria);
            }

            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('titulo', 'LIKE', '%' . $request->search . '%')
                      ->orWhere('autor', 'LIKE', '%' . $request->search . '%')
                      ->orWhere('descripcion', 'LIKE', '%' . $request->search . '%');
                });
            }

            if ($request->filled('es_gratuito')) {
                $query->where('es_gratuito', $request->es_gratuito);
            }

            $libros = $query->orderBy('titulo')->paginate(12);
            
            $categorias = Categoria::where('tipo', 'libro')
                                 ->where('estado', true)
                                 ->orderBy('nombre')
                                 ->get();

            return view('public.libros.catalogo', compact('libros', 'categorias'));
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar catálogo: ' . $e->getMessage()]);
        }
    }

    /**
     * Muestra los detalles de un libro
     */
    public function ver(Request $request, int $id): View|RedirectResponse
    {
        try {
            $libro = Libro::with(['categoria'])->findOrFail($id);

            // Libros relacionados
            $librosRelacionados = Libro::where('categoria_id', $libro->categoria_id)
                                     ->where('id', '!=', $libro->id)
                                     ->where('estado', 'Disponible')
                                     ->take(4)
                                     ->get();

            return view('public.libros.detalle', compact('libro', 'librosRelacionados'));
        } catch (Exception $e) {
            return redirect('/libros')
                          ->withErrors(['error' => 'Error al cargar libro: ' . $e->getMessage()]);
        }
    }

    /**
     * Panel de administración de libros
     */
    public function admin(Request $request): View
    {
        try {
            $query = Libro::with(['categoria']);

            // Filtros
            if ($request->filled('categoria')) {
                $query->where('categoria_id', $request->categoria);
            }

            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            }

            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('titulo', 'LIKE', '%' . $request->search . '%')
                      ->orWhere('autor', 'LIKE', '%' . $request->search . '%')
                      ->orWhere('isbn', 'LIKE', '%' . $request->search . '%');
                });
            }

            $libros = $query->orderBy('titulo')->paginate(15);
            
            $categorias = Categoria::where('tipo', 'libro')
                                 ->orderBy('nombre')
                                 ->get();

            $estadisticas = [
                'total_libros' => Libro::count(),
                'libros_disponibles' => Libro::where('estado', 'Disponible')->count(),
                'libros_gratuitos' => Libro::where('es_gratuito', true)->count(),
                'total_descargas' => Libro::sum('descargas'),
                'libros_mas_descargados' => Libro::orderBy('descargas', 'desc')->take(5)->get()
            ];

            return view('admin.libros.index', compact('libros', 'categorias', 'estadisticas'));
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar panel de administración: ' . $e->getMessage()]);
        }
    }

    /**
     * Muestra el formulario para crear un libro
     */
    public function crear(): View
    {
        try {
            $categorias = Categoria::where('tipo', 'libro')
                                 ->where('estado', true)
                                 ->orderBy('nombre')
                                 ->get();

            return view('admin.libros.crear', compact('categorias'));
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al cargar formulario: ' . $e->getMessage()]);
        }
    }

    /**
     * Guarda un nuevo libro
     */
    public function guardar(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:200',
            'autor' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'required|exists:categories,id',
            'isbn' => 'nullable|string|max:20|unique:books,isbn',
            'paginas' => 'nullable|integer|min:1',
            'editorial' => 'nullable|string|max:100',
            'año_publicacion' => 'nullable|integer|min:1900|max:' . date('Y'),
            'precio' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'es_gratuito' => 'required|boolean',
            'estado' => 'required|in:Disponible,Agotado,Descontinuado',
            'imagen_portada' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'archivo_pdf' => 'nullable|mimes:pdf|max:50000',
            'enlace_externo' => 'nullable|url'
        ], [
            'titulo.required' => 'El título es obligatorio',
            'autor.required' => 'El autor es obligatorio',
            'categoria_id.required' => 'La categoría es obligatoria',
            'isbn.unique' => 'Este ISBN ya existe en el sistema'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $libroData = [
                'titulo' => $request->titulo,
                'autor' => $request->autor,
                'descripcion' => $request->descripcion,
                'categoria_id' => $request->categoria_id,
                'isbn' => $request->isbn,
                'paginas' => $request->paginas ?? 0,
                'editorial' => $request->editorial,
                'año_publicacion' => $request->año_publicacion,
                'precio' => $request->precio ?? 0,
                'stock' => $request->stock ?? 0,
                'stock_minimo' => $request->stock_minimo ?? 5,
                'es_gratuito' => $request->boolean('es_gratuito'),
                'estado' => $request->estado,
                'enlace_externo' => $request->enlace_externo
            ];

            // Manejar imagen de portada
            if ($request->hasFile('imagen_portada')) {
                $imagen = $request->file('imagen_portada');
                $nombreImagen = time() . '_' . Str::slug($request->titulo) . '.' . $imagen->getClientOriginalExtension();
                $rutaImagen = $imagen->storeAs('libros/portadas', $nombreImagen, 'public');
                $libroData['imagen_portada'] = $rutaImagen;
            }

            // Manejar archivo PDF
            if ($request->hasFile('archivo_pdf')) {
                $pdf = $request->file('archivo_pdf');
                $nombrePdf = time() . '_' . Str::slug($request->titulo) . '.pdf';
                $rutaPdf = $pdf->storeAs('libros/archivos', $nombrePdf, 'public');
                $libroData['archivo_pdf'] = $rutaPdf;
                $libroData['tamaño_archivo'] = $pdf->getSize();
            }

            $libro = Libro::create($libroData);

            DB::commit();

            return redirect('/admin/libros')
                           ->with('success', 'Libro creado exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear libro: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Muestra el formulario para editar un libro
     */
    public function editar(int $id): View|RedirectResponse
    {
        try {
            $libro = Libro::with(['categoria'])->findOrFail($id);
            
            $categorias = Categoria::where('tipo', 'libro')
                                 ->where('estado', true)
                                 ->orderBy('nombre')
                                 ->get();

            return view('admin.libros.editar', compact('libro', 'categorias'));
        } catch (Exception $e) {
            return redirect('/admin/libros')
                           ->withErrors(['error' => 'Error al cargar libro: ' . $e->getMessage()]);
        }
    }

    /**
     * Actualiza un libro existente
     */
    public function actualizar(Request $request, int $id): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:200',
            'autor' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'required|exists:categories,id',
            'isbn' => 'nullable|string|max:20|unique:books,isbn,' . $id,
            'paginas' => 'nullable|integer|min:1',
            'editorial' => 'nullable|string|max:100',
            'año_publicacion' => 'nullable|integer|min:1900|max:' . date('Y'),
            'precio' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'es_gratuito' => 'required|boolean',
            'estado' => 'required|in:Disponible,Agotado,Descontinuado',
            'imagen_portada' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'archivo_pdf' => 'nullable|mimes:pdf|max:50000',
            'enlace_externo' => 'nullable|url'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $libro = Libro::findOrFail($id);

            DB::beginTransaction();

            $libroData = [
                'titulo' => $request->titulo,
                'autor' => $request->autor,
                'descripcion' => $request->descripcion,
                'categoria_id' => $request->categoria_id,
                'isbn' => $request->isbn,
                'paginas' => $request->paginas ?? 0,
                'editorial' => $request->editorial,
                'año_publicacion' => $request->año_publicacion,
                'precio' => $request->precio ?? 0,
                'stock' => $request->stock ?? 0,
                'stock_minimo' => $request->stock_minimo ?? 5,
                'es_gratuito' => $request->boolean('es_gratuito'),
                'estado' => $request->estado,
                'enlace_externo' => $request->enlace_externo
            ];

            // Manejar imagen de portada
            if ($request->hasFile('imagen_portada')) {
                // Eliminar imagen anterior
                if ($libro->imagen_portada && Storage::disk('public')->exists($libro->imagen_portada)) {
                    Storage::disk('public')->delete($libro->imagen_portada);
                }

                $imagen = $request->file('imagen_portada');
                $nombreImagen = time() . '_' . Str::slug($request->titulo) . '.' . $imagen->getClientOriginalExtension();
                $rutaImagen = $imagen->storeAs('libros/portadas', $nombreImagen, 'public');
                $libroData['imagen_portada'] = $rutaImagen;
            }

            // Manejar archivo PDF
            if ($request->hasFile('archivo_pdf')) {
                // Eliminar archivo anterior
                if ($libro->archivo_pdf && Storage::disk('public')->exists($libro->archivo_pdf)) {
                    Storage::disk('public')->delete($libro->archivo_pdf);
                }

                $pdf = $request->file('archivo_pdf');
                $nombrePdf = time() . '_' . Str::slug($request->titulo) . '.pdf';
                $rutaPdf = $pdf->storeAs('libros/archivos', $nombrePdf, 'public');
                $libroData['archivo_pdf'] = $rutaPdf;
                $libroData['tamaño_archivo'] = $pdf->getSize();
            }

            $libro->update($libroData);

            DB::commit();

            return redirect('/admin/libros')
                           ->with('success', 'Libro actualizado exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar libro: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Elimina un libro
     */
    public function eliminar(int $id): RedirectResponse
    {
        try {
            $libro = Libro::findOrFail($id);

            DB::beginTransaction();

            // Verificar si hay descargas registradas
            $tieneDescargas = BookDownload::where('libro_id', $libro->id)->exists();
            
            if ($tieneDescargas) {
                return redirect('/admin/libros')
                               ->withErrors(['error' => 'No se puede eliminar el libro porque tiene descargas registradas.']);
            }

            // Eliminar archivos asociados
            if ($libro->imagen_portada && Storage::disk('public')->exists($libro->imagen_portada)) {
                Storage::disk('public')->delete($libro->imagen_portada);
            }

            if ($libro->archivo_pdf && Storage::disk('public')->exists($libro->archivo_pdf)) {
                Storage::disk('public')->delete($libro->archivo_pdf);
            }

            $libro->delete();

            DB::commit();

            return redirect('/admin/libros')
                           ->with('success', 'Libro eliminado exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect('/admin/libros')
                           ->withErrors(['error' => 'Error al eliminar libro: ' . $e->getMessage()]);
        }
    }

    /**
     * Descarga o accede a un libro
     */
    public function descargar(Request $request, int $id): RedirectResponse|JsonResponse
    {
        try {
            $libro = Libro::findOrFail($id);

            if ($libro->estado !== 'Disponible') {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Este libro no está disponible.'
                    ], 404);
                }
                return back()->withErrors(['error' => 'Este libro no está disponible.']);
            }

            // Registrar descarga
            BookDownload::create([
                'libro_id' => $libro->id,
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'fecha_descarga' => now()
            ]);

            // Incrementar contador
            $libro->increment('descargas');

            // Si es enlace externo, redirigir
            if ($libro->enlace_externo) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'redirect_url' => $libro->enlace_externo
                    ]);
                }
                return redirect($libro->enlace_externo);
            }

            // Si tiene archivo PDF, servir descarga
            if ($libro->archivo_pdf && Storage::disk('public')->exists($libro->archivo_pdf)) {
                return Storage::disk('public')->download($libro->archivo_pdf, $libro->titulo . '.pdf');
            }

            // Si no hay archivo disponible
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay archivo disponible para descargar.'
                ], 404);
            }
            return back()->withErrors(['error' => 'No hay archivo disponible para descargar.']);

        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al procesar descarga: ' . $e->getMessage()
                ], 500);
            }
            return back()->withErrors(['error' => 'Error al procesar descarga: ' . $e->getMessage()]);
        }
    }

    /**
     * Búsqueda AJAX de libros
     */
    public function buscar(Request $request): JsonResponse
    {
        try {
            $query = Libro::with(['categoria'])
                         ->where('estado', 'Disponible');

            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('titulo', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('autor', 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere('descripcion', 'LIKE', '%' . $searchTerm . '%');
                });
            }

            if ($request->filled('categoria')) {
                $query->where('categoria_id', $request->categoria);
            }

            $libros = $query->take(10)->get();

            return response()->json([
                'success' => true,
                'data' => $libros->map(function($libro) {
                    return [
                        'id' => $libro->id,
                        'titulo' => $libro->titulo,
                        'autor' => $libro->autor,
                        'descripcion' => Str::limit($libro->descripcion, 100),
                        'categoria' => $libro->categoria->nombre,
                        'precio' => $libro->precio,
                        'es_gratuito' => $libro->es_gratuito,
                        'imagen_portada' => $libro->imagen_portada ? asset('storage/' . $libro->imagen_portada) : null,
                        'url' => '/libros/' . $libro->id
                    ];
                })
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambiar estado de un libro
     */
    public function cambiarEstado(Request $request, int $id): JsonResponse
    {
        try {
            $libro = Libro::findOrFail($id);
            
            $validator = Validator::make($request->all(), [
                'estado' => 'required|in:Disponible,Agotado,Descontinuado'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Estado inválido.'
                ], 400);
            }

            $libro->update(['estado' => $request->estado]);

            return response()->json([
                'success' => true,
                'message' => 'Estado del libro actualizado exitosamente.',
                'nuevo_estado' => $libro->estado
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar estado: ' . $e->getMessage()
            ], 500);
        }
    }
}
