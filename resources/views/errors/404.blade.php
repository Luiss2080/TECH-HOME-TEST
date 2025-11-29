@extends('layouts.app')

@section('title', '404 - Página no encontrada | TECH HOME')

@push('styles')
    <style>
        .error-container {
            min-height: 80vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 2rem;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }

        .error-icon {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #dc2626, #ef4444);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
            color: white;
            font-size: 3rem;
            box-shadow: 0 20px 40px rgba(220, 38, 38, 0.3);
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .error-title {
            font-size: 4rem;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .error-subtitle {
            font-size: 1.5rem;
            color: #6b7280;
            margin-bottom: 0.5rem;
        }

        .error-message {
            font-size: 1.1rem;
            color: #9ca3af;
            margin-bottom: 3rem;
            max-width: 500px;
        }

        .error-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn-home {
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: white;
            padding: 1rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(220, 38, 38, 0.3);
        }

        .btn-home:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(220, 38, 38, 0.4);
        }

        .btn-back {
            background: rgba(107, 114, 128, 0.1);
            color: #6b7280;
            padding: 1rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            border: 2px solid #e5e7eb;
        }

        .btn-back:hover {
            background: #f9fafb;
            color: #374151;
            transform: translateY(-2px);
        }

        .robot-animation {
            margin-top: 3rem;
            color: #dc2626;
            font-size: 2rem;
            animation: rotate 4s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        body.ithr-dark-mode .error-container {
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
        }

        body.ithr-dark-mode .error-title {
            color: #f9fafb;
        }

        body.ithr-dark-mode .error-subtitle {
            color: #d1d5db;
        }

        body.ithr-dark-mode .error-message {
            color: #9ca3af;
        }

        @media (max-width: 768px) {
            .error-title {
                font-size: 3rem;
            }

            .error-subtitle {
                font-size: 1.3rem;
            }

            .error-actions {
                flex-direction: column;
                align-items: center;
            }

            .btn-home,
            .btn-back {
                width: 100%;
                max-width: 250px;
                justify-content: center;
            }
        }
    </style>
@endpush

@section('content')
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-robot"></i>
        </div>
        
        <h1 class="error-title">404</h1>
        <h2 class="error-subtitle">¡Oops! Página no encontrada</h2>
        <p class="error-message">
            Parece que el robot explorador se perdió en el ciberespacio. 
            La página que buscas no existe o ha sido movida a otra dimensión tecnológica.
        </p>
        
        <div class="error-actions">
            <a href="{{ route('home') }}" class="btn-home">
                <i class="fas fa-home"></i>
                Volver al Inicio
            </a>
            <a href="javascript:history.back()" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                Regresar
            </a>
        </div>
        
        <div class="robot-animation">
            <i class="fas fa-cog"></i>
        </div>
    </div>
@endsection