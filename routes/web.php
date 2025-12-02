<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\LibroController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\LaboratorioController;
use App\Http\Controllers\PermissionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aquí se registran las rutas web para la aplicación. Estas rutas
| son cargadas por RouteServiceProvider y están asignadas al middleware "web".
|
*/

// ============================================
// RUTAS PÚBLICAS
// ============================================

// Página principal
Route::get('/', [HomeController::class, 'index'])->name('home');

// Newsletter
Route::post('/newsletter/subscribe', function() {
    return response()->json(['success' => true, 'message' => 'Suscripción exitosa']);
})->name('newsletter.subscribe');

// ============================================
// RUTAS DE AUTENTICACIÓN
// ============================================
Route::redirect('/login', '/auth/login');

Route::prefix('auth')->name('auth.')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    
    // Register
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
    
    // Forgot Password
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('forgot-password');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password.submit');
    
    // Reset Password
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('reset-password');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset-password.submit');
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Verify OTP
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verify.otp');
});

// Ruta de logout también accesible por GET para compatibilidad
Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');

// RUTAS PROTEGIDAS (REQUIEREN AUTENTICACIÓN)
// ============================================
Route::middleware(['auth'])->group(function () {
    
    // Dashboard principal (redirige según el rol)
    Route::get('/dashboard', function() {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        // Por ahora redirigir al home hasta que se implementen roles
        return redirect()->route('home');
        
        if ($role) {
            switch ($role->nombre) {
                case 'administrador':
                    return redirect()->route('admin.dashboard');
                case 'docente':
                    return redirect()->route('docente.dashboard');
                case 'estudiante':
                    return redirect()->route('estudiante.dashboard');
                default:
                    return view('dashboard.general', compact('user'));
            }
        }
        
        return view('dashboard.general', compact('user'));
    })->name('dashboard');
    
    // ============================================
    // RUTAS DE ADMINISTRADOR
    // ============================================
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // Gestión de permisos
        Route::resource('permisos', PermissionController::class);
        
        // Configuración del sistema
        Route::get('/configuracion', [AdminController::class, 'configuracion'])->name('configuracion');
        Route::post('/configuracion', [AdminController::class, 'guardarConfiguracion'])->name('configuracion.guardar');
        
        // Reportes
        Route::get('/reportes', [AdminController::class, 'reportes'])->name('reportes');
    });
    
    // ============================================
    // RUTAS DE DOCENTE
    // ============================================
    Route::prefix('docente')->name('docente.')->group(function () {
        Route::get('/dashboard', [DocenteController::class, 'dashboard'])->name('dashboard');
        
        // Gestión de cursos
        Route::resource('cursos', CursoController::class);
        
        // Gestión de materiales
        Route::resource('materiales', MaterialController::class);
        
        // Gestión de laboratorios
        Route::resource('laboratorios', LaboratorioController::class);
        
        // Gestión de estudiantes
        Route::get('/estudiantes', [DocenteController::class, 'estudiantes'])->name('estudiantes');
    });
    
    // ============================================
    // RUTAS DE ESTUDIANTE
    // ============================================
    Route::prefix('estudiante')->name('estudiante.')->group(function () {
        Route::get('/dashboard', [EstudianteController::class, 'dashboard'])->name('dashboard');
        
        // Mis cursos
        Route::get('/mis-cursos', [EstudianteController::class, 'misCursos'])->name('mis-cursos');
        Route::get('/curso/{id}', [EstudianteController::class, 'verCurso'])->name('ver-curso');
        
        // Biblioteca personal
        Route::get('/biblioteca', [EstudianteController::class, 'biblioteca'])->name('biblioteca');
        Route::get('/libro/{id}', [EstudianteController::class, 'verLibro'])->name('ver-libro');
        
        // Perfil
        Route::get('/perfil', [EstudianteController::class, 'perfil'])->name('perfil');
        Route::post('/perfil', [EstudianteController::class, 'actualizarPerfil'])->name('perfil.actualizar');
    });
    
    // ============================================
    // RUTAS COMPARTIDAS (TODOS LOS USUARIOS AUTENTICADOS)
    // ============================================
    
    // Cursos (visualización)
    Route::get('/cursos', [CursoController::class, 'index'])->name('cursos');
    Route::get('/cursos/{id}', [CursoController::class, 'show'])->name('cursos.show');
    
    // Libros (visualización y descarga)
    Route::get('/libros', [LibroController::class, 'index'])->name('libros');
    Route::get('/libros/{id}', [LibroController::class, 'show'])->name('libros.show');
    Route::get('/libros/{id}/download', [LibroController::class, 'download'])->name('libros.download');
    
    // Laboratorios (visualización)
    Route::get('/laboratorios', [LaboratorioController::class, 'index'])->name('laboratorios');
    Route::get('/laboratorios/{id}', [LaboratorioController::class, 'show'])->name('laboratorios.show');
    
    // Categorías
    Route::get('/categoria/{id}', [HomeController::class, 'categoria'])->name('categoria');
    
    // Estudiantes (visualización general)
    Route::get('/estudiantes', [HomeController::class, 'estudiantes'])->name('estudiantes');
    Route::get('/estudiantes/{id}', [HomeController::class, 'estudianteShow'])->name('estudiantes.show');
    
    // Laboratorios virtuales
    Route::get('/laboratorios', [HomeController::class, 'laboratorios'])->name('laboratorios');
    Route::get('/laboratorio/{id}', [HomeController::class, 'laboratorioShow'])->name('laboratorio.show');
    
    // Materiales (visualización)
    Route::get('/materiales', [HomeController::class, 'materiales'])->name('materiales');
    Route::get('/materiales/{id}', [HomeController::class, 'materialShow'])->name('materiales.show');
    
    // Gestión de usuarios (acceso temporal)
    Route::get('/usuarios', [HomeController::class, 'usuarios'])->name('usuarios');
    Route::get('/usuarios/{id}', [HomeController::class, 'usuarioShow'])->name('usuarios.show');
    
    // Componentes (visualización)
    Route::get('/componentes', [HomeController::class, 'componentes'])->name('componentes');
    
    // Docentes (visualización)
    Route::get('/docentes', [HomeController::class, 'docentes'])->name('docentes');
    
    // Notificaciones
    Route::get('/notificaciones', [HomeController::class, 'notificaciones'])->name('notificaciones');
    Route::post('/notificaciones/{id}/marcar-leida', [HomeController::class, 'marcarNotificacionLeida'])->name('notificacion.marcar-leida');
    
    // Perfil general (para todos los usuarios)
    Route::get('/perfil', function() {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        return view('perfil.index', compact('user'));
    })->name('perfil');
    
    Route::post('/perfil', function() {
        // Lógica de actualización de perfil
        return back()->with('success', 'Perfil actualizado correctamente');
    })->name('perfil.actualizar');
});

// ============================================
// RUTAS DE API (para AJAX)
// ============================================
Route::prefix('api')->name('api.')->middleware(['auth'])->group(function () {
    // Obtener notificaciones
    Route::get('/notificaciones', function() {
        return response()->json([
            'success' => true,
            'notificaciones' => []
        ]);
    });
    
    // Marcar notificación como leída
    Route::post('/notificaciones/{id}/leer', function($id) {
        return response()->json([
            'success' => true,
            'message' => 'Notificación marcada como leída'
        ]);
    });
    
    // Cambiar tema
    Route::post('/tema', function() {
        $tema = request('tema', 'light');
        session(['tema' => $tema]);
        
        return response()->json([
            'success' => true,
            'tema' => $tema
        ]);
    });
});

// ============================================
// RUTAS DE FALLBACK
// ============================================

// Manejar rutas no encontradas
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});