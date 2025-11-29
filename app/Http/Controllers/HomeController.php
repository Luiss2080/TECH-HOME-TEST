<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\Libro;
use App\Models\Categoria;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception;

class HomeController extends Controller
{
    /**
     * Página de inicio
     */
    public function index()
    {
        try {
            // Si el usuario está autenticado, redirigir a su dashboard
            if (Auth::check()) {
                $user = Auth::user();
                
                if ($user->hasRole('administrador')) {
                    return redirect('/admin/dashboard');
                } elseif ($user->hasRole('docente')) {
                    return redirect('/docente/dashboard');
                } elseif ($user->hasRole('estudiante')) {
                    return redirect('/estudiante/dashboard');
                }
            }

            // Datos para página pública
            $cursosDestacados = Curso::with(['categoria', 'docente'])
                                   ->where('estado', 'activo')
                                   ->orderBy('creado_en', 'desc')
                                   ->limit(6)
                                   ->get();

            $librosRecientes = Libro::with('categoria')
                                  ->where('estado', 'activo')
                                  ->orderBy('creado_en', 'desc')
                                  ->limit(6)
                                  ->get();

            $categorias = Categoria::withCount(['cursos', 'libros'])
                                 ->orderBy('nombre')
                                 ->limit(8)
                                 ->get();

            $estadisticas = [
                'total_cursos' => Curso::where('estado', 'activo')->count(),
                'total_libros' => Libro::where('estado', 'activo')->count(),
                'total_estudiantes' => User::whereHas('roles', function($query) {
                    $query->where('nombre', 'estudiante');
                })->count(),
                'total_docentes' => User::whereHas('roles', function($query) {
                    $query->where('nombre', 'docente');
                })->count()
            ];

            return view('home.index', compact('cursosDestacados', 'librosRecientes', 'categorias', 'estadisticas'));
            
        } catch (Exception $e) {
            return view('home.index', [
                'cursosDestacados' => collect(),
                'librosRecientes' => collect(),
                'categorias' => collect(),
                'estadisticas' => []
            ])->withErrors(['error' => 'Error al cargar página de inicio: ' . $e->getMessage()]);
        }
    }

    /**
     * Página Acerca de
     */
    public function about()
    {
        return view('home.about');
    }

    /**
     * Página de Contacto
     */
    public function contact()
    {
        return view('home.contact');
    }

    /**
     * Procesar formulario de contacto
     */
    public function submitContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'asunto' => 'required|string|max:255',
            'mensaje' => 'required|string|max:1000'
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'Ingrese un email válido',
            'asunto.required' => 'El asunto es obligatorio',
            'mensaje.required' => 'El mensaje es obligatorio'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Aquí implementarías el envío de email o guardado en BD
            // Mail::to(config('mail.contact_email'))->send(new ContactMail($request->all()));

            $message = 'Tu mensaje ha sido enviado exitosamente. Te contactaremos pronto.';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return back()->with('success', $message);

        } catch (Exception $e) {
            $error = 'Error al enviar mensaje. Intenta nuevamente.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $error], 500);
            }
            return back()->withErrors(['error' => $error])->withInput();
        }
    }

    /**
     * Página de Términos y Condiciones
     */
    public function terms()
    {
        return view('home.terms');
    }

    /**
     * Página de Política de Privacidad
     */
    public function privacy()
    {
        return view('home.privacy');
    }
}
