<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Página principal del sitio
     * Redirige a los usuarios autenticados a su dashboard correspondiente
     */
    public function index(): View|RedirectResponse
    {
        // Si el usuario está autenticado, redirigir según su rol
        if (Auth::check()) {
            $user = Auth::user();
            
            if ($user->hasRole('Administrador')) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->hasRole('Docente')) {
                return redirect()->route('docente.dashboard');
            } elseif ($user->hasRole('Estudiante')) {
                return redirect()->route('estudiante.dashboard');
            }
        }

        // Para usuarios no autenticados, mostrar página de inicio pública
        return view('home.index', [
            'title' => 'Tech-Home - Inicio'
        ]);
    }

    /**
     * Página "Acerca de"
     */
    public function about(): View
    {
        return view('home.about', [
            'title' => 'Acerca de - Tech Home Bolivia'
        ]);
    }

    /**
     * Página de contacto
     */
    public function contact(): View
    {
        return view('home.contact', [
            'title' => 'Contacto - Tech Home Bolivia'
        ]);
    }

    /**
     * Página de cursos públicos
     */
    public function cursos(): View
    {
        return view('home.cursos', [
            'title' => 'Cursos - Tech Home Bolivia'
        ]);
    }

    /**
     * Página de términos y condiciones
     */
    public function terms(): View
    {
        return view('home.terms', [
            'title' => 'Términos y Condiciones - Tech Home Bolivia'
        ]);
    }

    /**
     * Página de política de privacidad
     */
    public function privacy(): View
    {
        return view('home.privacy', [
            'title' => 'Política de Privacidad - Tech Home Bolivia'
        ]);
    }
}