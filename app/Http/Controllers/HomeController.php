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

    /**
     * Mostrar categoría específica
     */
    public function categoria($id)
    {
        try {
            $categoria = Categoria::findOrFail($id);
            
            $cursos = Curso::with(['docente'])
                          ->where('categoria_id', $id)
                          ->where('estado', 'activo')
                          ->orderBy('creado_en', 'desc')
                          ->paginate(12);

            $libros = Libro::where('categoria_id', $id)
                          ->where('estado', 'activo')
                          ->orderBy('creado_en', 'desc')
                          ->paginate(12);

            return view('categoria.show', compact('categoria', 'cursos', 'libros'));

        } catch (Exception $e) {
            return redirect()->route('home')->with('error', 'Categoría no encontrada');
        }
    }

    /**
     * Mostrar notificaciones del usuario
     */
    public function notificaciones()
    {
        try {
            $user = Auth::user();
            
            // Simular notificaciones hasta implementar el sistema completo
            $notificaciones = collect([
                (object) [
                    'id' => 1,
                    'titulo' => 'Nuevo curso disponible',
                    'mensaje' => 'Se ha publicado el curso de Laravel Avanzado',
                    'tipo' => 'info',
                    'leido' => false,
                    'created_at' => now()->subMinutes(30)
                ],
                (object) [
                    'id' => 2,
                    'titulo' => 'Recordatorio',
                    'mensaje' => 'Tienes una clase programada mañana a las 10:00 AM',
                    'tipo' => 'warning',
                    'leido' => false,
                    'created_at' => now()->subHours(2)
                ]
            ]);

            return view('notificaciones.index', compact('notificaciones'));

        } catch (Exception $e) {
            return view('notificaciones.index', ['notificaciones' => collect()]);
        }
    }

    /**
     * Marcar notificación como leída
     */
    public function marcarNotificacionLeida(Request $request, $id)
    {
        try {
            // Aquí implementarías la lógica real cuando tengas el modelo de notificaciones
            // $notificacion = Notificacion::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
            // $notificacion->update(['leido' => true]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notificación marcada como leída'
                ]);
            }

            return back()->with('success', 'Notificación marcada como leída');

        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al marcar notificación'
                ], 500);
            }

            return back()->with('error', 'Error al marcar notificación');
        }
    }

    /**
     * Suscribir a newsletter
     */
    public function subscribeNewsletter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255'
        ], [
            'email.required' => 'El email es obligatorio',
            'email.email' => 'Ingrese un email válido'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Aquí implementarías la lógica real de suscripción
            // NewsletterSubscription::create(['email' => $request->email]);

            return response()->json([
                'success' => true,
                'message' => '¡Te has suscrito exitosamente al newsletter!'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la suscripción'
            ], 500);
        }
    }
}
