<?php

namespace App\Http\Controllers;

use App\Services\CursoService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class CursoController extends Controller
{
    private $cursoService;

    public function __construct()
    {
        $this->cursoService = new CursoService();
    }

    // ==========================================
    // MÉTODOS PRINCIPALES DE GESTIÓN
    // ==========================================

    /**
     * Mostrar listado de cursos
     */
    public function index(): View
    {
        try {
            $user = Auth::user();
            $isDocente = $user && $user->hasRole('docente');
            
            // Si es docente, mostrar solo sus cursos
            if ($isDocente) {
                $cursos = $this->cursoService->getCursosByDocente($user->id);
            } else {
                $cursos = $this->cursoService->getAllCursos();
            }
            
            $estadisticas = $this->cursoService->getEstadisticasCursos();
            
            return view('cursos.index', [
                'title' => 'Gestión de Cursos',
                'cursos' => $cursos,
                'estadisticas' => $estadisticas,
                'isDocente' => $isDocente
            ]);
        } catch (Exception $e) {
            Log::error('Error al cargar cursos: ' . $e->getMessage());
            return view('cursos.index', [
                'title' => 'Gestión de Cursos',
                'cursos' => [],
                'estadisticas' => [],
                'isDocente' => false
            ])->with('error', 'Error al cargar cursos: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar catálogo de cursos (vista pública)
     */
    public function catalogo(): View
    {
        try {
            $cursos = $this->cursoService->getAllCursos();
            $categorias = $this->cursoService->getAllCategoriasCursos();
            
            return view('cursos.catalogo', [
                'title' => 'Catálogo de Cursos',
                'cursos' => $cursos,
                'categorias' => $categorias
            ]);
        } catch (Exception $e) {
            Log::error('Error al cargar catálogo: ' . $e->getMessage());
            return view('cursos.catalogo', [
                'title' => 'Catálogo de Cursos',
                'cursos' => [],
                'categorias' => []
            ])->with('error', 'Error al cargar catálogo: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de creación de curso
     */
    public function create(): View|RedirectResponse
    {
        try {
            $categorias = $this->cursoService->getAllCategoriasCursos();
            $docentes = $this->cursoService->getAllDocentes();
            
            return view('cursos.create', [
                'title' => 'Crear Nuevo Curso',
                'categorias' => $categorias,
                'docentes' => $docentes
            ]);
        } catch (Exception $e) {
            Log::error('Error al cargar formulario: ' . $e->getMessage());
            return redirect()->route('cursos.index')->with('error', 'Error al cargar formulario: ' . $e->getMessage());
        }
    }

    /**
     * Procesar creación de curso
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            // Validaciones para cursos con videos de YouTube
            $request->validate([
                'titulo' => 'required|min:5|max:200',
                'descripcion' => 'required|min:10',
                'video_url' => 'required|url',
                'docente_id' => 'required|numeric|exists:usuarios,id',
                'categoria_id' => 'required|numeric|exists:categorias,id',
                'nivel' => 'required|in:Principiante,Intermedio,Avanzado',
                'estado' => 'required|in:Borrador,Publicado,Archivado',
                'imagen_portada' => 'nullable|url'
            ]);

            // Verificar que sea una URL de YouTube válida
            if (!$this->isValidYoutubeUrl($request->video_url)) {
                return redirect()->back()
                    ->with('error', 'La URL debe ser un enlace válido de YouTube.')
                    ->withInput();
            }

            // Preparar datos del curso
            $cursoData = [
                'titulo' => trim($request->titulo),
                'descripcion' => trim($request->descripcion),
                'video_url' => trim($request->video_url),
                'docente_id' => (int)$request->docente_id,
                'categoria_id' => (int)$request->categoria_id,
                'imagen_portada' => $request->imagen_portada,
                'nivel' => $request->nivel,
                'estado' => $request->estado ?: 'Borrador',
                'es_gratuito' => $request->boolean('es_gratuito')
            ];

            // Si es docente, solo puede crear cursos asignados a sí mismo
            $user = Auth::user();
            if ($user && $user->hasRole('docente') && !$user->hasRole('administrador')) {
                $cursoData['docente_id'] = $user->id;
            }

            $cursoId = $this->cursoService->createCurso($cursoData);

            return redirect()->route('cursos.index')->with('success', 'Curso creado exitosamente.');
            
        } catch (Exception $e) {
            Log::error('Error al crear curso: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al crear curso: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar formulario de edición de curso
     */
    public function edit($id): View|RedirectResponse
    {
        try {
            $curso = $this->cursoService->getCursoById($id);
            if (!$curso) {
                return redirect()->route('cursos.index')->with('error', 'Curso no encontrado.');
            }

            // Verificar permisos si es docente
            $user = Auth::user();
            if ($user && $user->hasRole('docente') && !$user->hasRole('administrador')) {
                if ($curso['docente_id'] != $user->id) {
                    return redirect()->route('cursos.index')
                        ->with('error', 'No tienes permisos para editar este curso.');
                }
            }

            $categorias = $this->cursoService->getAllCategoriasCursos();
            $docentes = $this->cursoService->getAllDocentes();

            return view('cursos.edit', [
                'title' => 'Editar Curso - ' . $curso['titulo'],
                'curso' => $curso,
                'categorias' => $categorias,
                'docentes' => $docentes
            ]);
        } catch (Exception $e) {
            Log::error('Error al cargar curso: ' . $e->getMessage());
            return redirect()->route('cursos.index')->with('error', 'Error al cargar curso: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar curso
     */
    public function update(Request $request, $id): RedirectResponse
    {
        try {
            $curso = $this->cursoService->getCursoById($id);
            if (!$curso) {
                return redirect()->route('cursos.index')->with('error', 'Curso no encontrado.');
            }

            // Verificar permisos si es docente
            $user = Auth::user();
            if ($user && $user->hasRole('docente') && !$user->hasRole('administrador')) {
                if ($curso['docente_id'] != $user->id) {
                    return redirect()->route('cursos.index')
                        ->with('error', 'No tienes permisos para editar este curso.');
                }
            }

            // Validaciones
            $request->validate([
                'titulo' => 'required|min:5|max:200',
                'descripcion' => 'required|min:10',
                'video_url' => 'required|url',
                'docente_id' => 'required|numeric|exists:usuarios,id',
                'categoria_id' => 'required|numeric|exists:categorias,id',
                'nivel' => 'required|in:Principiante,Intermedio,Avanzado',
                'estado' => 'required|in:Borrador,Publicado,Archivado',
                'imagen_portada' => 'nullable|url'
            ]);

            // Verificar que sea una URL de YouTube válida
            if (!$this->isValidYoutubeUrl($request->video_url)) {
                return redirect()->back()
                    ->with('error', 'La URL debe ser un enlace válido de YouTube.')
                    ->withInput();
            }

            // Preparar datos del curso
            $cursoData = [
                'titulo' => trim($request->titulo),
                'descripcion' => trim($request->descripcion),
                'video_url' => trim($request->video_url),
                'categoria_id' => (int)$request->categoria_id,
                'imagen_portada' => $request->imagen_portada,
                'nivel' => $request->nivel,
                'estado' => $request->estado,
                'es_gratuito' => $request->boolean('es_gratuito')
            ];

            // Solo admin puede cambiar el docente asignado
            if (!($user && $user->hasRole('docente') && !$user->hasRole('administrador'))) {
                $cursoData['docente_id'] = (int)$request->docente_id;
            }

            $this->cursoService->updateCurso($id, $cursoData);

            return redirect()->route('cursos.index')->with('success', 'Curso actualizado exitosamente.');
            
        } catch (Exception $e) {
            Log::error('Error al actualizar curso: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error al actualizar curso: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar detalles de un curso
     */
    public function show($id): View|RedirectResponse
    {
        try {
            $curso = $this->cursoService->getCursoById($id);
            if (!$curso) {
                return redirect()->route('cursos.index')->with('error', 'Curso no encontrado.');
            }

            $componentes = $this->cursoService->getComponentesByCurso($id);
            $user = Auth::user();
            $isEnrolled = $user ? $this->cursoService->isUserEnrolled($user->id, $id) : false;

            return view('cursos.show', [
                'title' => $curso['titulo'],
                'curso' => $curso,
                'componentes' => $componentes,
                'isEnrolled' => $isEnrolled
            ]);
        } catch (Exception $e) {
            Log::error('Error al cargar curso: ' . $e->getMessage());
            return redirect()->route('cursos.catalogo')->with('error', 'Error al cargar curso: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar curso
     */
    public function destroy($id): RedirectResponse
    {
        try {
            $curso = $this->cursoService->getCursoById($id);
            if (!$curso) {
                return redirect()->route('cursos.index')->with('error', 'Curso no encontrado.');
            }

            // Verificar permisos
            $user = Auth::user();
            if ($user && $user->hasRole('docente') && !$user->hasRole('administrador')) {
                if ($curso['docente_id'] != $user->id) {
                    return redirect()->route('cursos.index')
                        ->with('error', 'No tienes permisos para eliminar este curso.');
                }
            }

            $this->cursoService->deleteCurso($id);

            return redirect()->route('cursos.index')->with('success', 'Curso eliminado exitosamente.');
            
        } catch (Exception $e) {
            Log::error('Error al eliminar curso: ' . $e->getMessage());
            return redirect()->route('cursos.index')->with('error', 'Error al eliminar curso: ' . $e->getMessage());
        }
    }

    /**
     * Inscribir usuario a un curso
     */
    public function inscribir($id): RedirectResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return redirect()->route('login')->with('error', 'Debes iniciar sesión para inscribirte.');
            }

            $curso = $this->cursoService->getCursoById($id);
            if (!$curso) {
                return redirect()->route('cursos.catalogo')->with('error', 'Curso no encontrado.');
            }

            if ($this->cursoService->isUserEnrolled($user->id, $id)) {
                return redirect()->back()->with('info', 'Ya estás inscrito en este curso.');
            }

            $this->cursoService->enrollUser($user->id, $id);

            return redirect()->back()->with('success', '¡Te has inscrito exitosamente al curso!');
            
        } catch (Exception $e) {
            Log::error('Error al inscribir usuario: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al inscribirse: ' . $e->getMessage());
        }
    }

    // ==========================================
    // MÉTODOS AUXILIARES
    // ==========================================

    /**
     * Validar si es una URL de YouTube válida
     */
    private function isValidYoutubeUrl(string $url): bool
    {
        $pattern = '/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/.+/';
        return preg_match($pattern, $url);
    }
}