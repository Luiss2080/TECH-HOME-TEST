<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;

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

// Páginas estáticas
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'submitContact'])->name('contact.submit');

// Newsletter
Route::post('/newsletter/subscribe', [HomeController::class, 'subscribeNewsletter'])->name('newsletter.subscribe');

// ============================================
// RUTAS DE AUTENTICACIÓN
// ============================================
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
});

// Ruta de logout también accesible por GET para compatibilidad
Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');

// ============================================
// RUTAS PROTEGIDAS (REQUIEREN AUTENTICACIÓN)
// ============================================
Route::middleware(['auth'])->group(function () {
    
    // Dashboard principal (redirige según el rol)
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    
    // ============================================
    // RUTAS DE ADMINISTRADOR
    // ============================================
    Route::prefix('admin')->name('admin.')->middleware(['role:administrador'])->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');
        
        // Gestión de usuarios
        Route::resource('usuarios', App\Http\Controllers\UserController::class);
        
        // Gestión de roles y permisos
        Route::resource('roles', App\Http\Controllers\RoleController::class);
        Route::resource('permisos', App\Http\Controllers\PermissionController::class);
        
        // Configuración del sistema
        Route::get('/configuracion', [App\Http\Controllers\AdminController::class, 'configuracion'])->name('configuracion');
        Route::post('/configuracion', [App\Http\Controllers\AdminController::class, 'guardarConfiguracion'])->name('configuracion.guardar');
        
        // Reportes
        Route::get('/reportes', [App\Http\Controllers\AdminController::class, 'reportes'])->name('reportes');
    });
    
    // ============================================
    // RUTAS DE DOCENTE
    // ============================================
    Route::prefix('docente')->name('docente.')->middleware(['role:docente'])->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\DocenteController::class, 'dashboard'])->name('dashboard');
        
        // Gestión de cursos
        Route::resource('cursos', App\Http\Controllers\CursoController::class);
        
        // Gestión de materiales
        Route::resource('materiales', App\Http\Controllers\MaterialController::class);
        
        // Gestión de estudiantes
        Route::get('/estudiantes', [App\Http\Controllers\DocenteController::class, 'estudiantes'])->name('estudiantes');
    });
    
    // ============================================
    // RUTAS DE ESTUDIANTE
    // ============================================
    Route::prefix('estudiante')->name('estudiante.')->middleware(['role:estudiante'])->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\EstudianteController::class, 'dashboard'])->name('dashboard');
        
        // Mis cursos
        Route::get('/mis-cursos', [App\Http\Controllers\EstudianteController::class, 'misCursos'])->name('mis-cursos');
        Route::get('/curso/{id}', [App\Http\Controllers\EstudianteController::class, 'verCurso'])->name('ver-curso');
        
        // Biblioteca personal
        Route::get('/biblioteca', [App\Http\Controllers\EstudianteController::class, 'biblioteca'])->name('biblioteca');
        Route::get('/libro/{id}', [App\Http\Controllers\EstudianteController::class, 'verLibro'])->name('ver-libro');
        
        // Perfil
        Route::get('/perfil', [App\Http\Controllers\EstudianteController::class, 'perfil'])->name('perfil');
        Route::post('/perfil', [App\Http\Controllers\EstudianteController::class, 'actualizarPerfil'])->name('perfil.actualizar');
    });
    
    // ============================================
    // RUTAS COMPARTIDAS (TODOS LOS USUARIOS AUTENTICADOS)
    // ============================================
    
    // Cursos (visualización)
    Route::get('/cursos', [App\Http\Controllers\CursoController::class, 'index'])->name('cursos');
    Route::get('/cursos/{id}', [App\Http\Controllers\CursoController::class, 'show'])->name('cursos.show');
    
    // Libros (visualización y descarga)
    Route::get('/libros', [App\Http\Controllers\LibroController::class, 'index'])->name('libros');
    Route::get('/libros/{id}', [App\Http\Controllers\LibroController::class, 'show'])->name('libros.show');
    Route::get('/libros/{id}/download', [App\Http\Controllers\LibroController::class, 'download'])->name('libros.download');
    
    // Categorías
    Route::get('/categoria/{id}', [App\Http\Controllers\HomeController::class, 'categoria'])->name('categoria');
    
    // Laboratorios virtuales
    Route::get('/laboratorios', [App\Http\Controllers\LaboratorioController::class, 'index'])->name('laboratorios');
    Route::get('/laboratorio/{id}', [App\Http\Controllers\LaboratorioController::class, 'show'])->name('laboratorio.show');
    
    // Notificaciones
    Route::get('/notificaciones', [App\Http\Controllers\HomeController::class, 'notificaciones'])->name('notificaciones');
    Route::post('/notificaciones/{id}/marcar-leida', [App\Http\Controllers\HomeController::class, 'marcarNotificacionLeida'])->name('notificacion.marcar-leida');
});
