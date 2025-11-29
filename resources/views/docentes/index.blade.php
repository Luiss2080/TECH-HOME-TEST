@extends('layouts.app')

@section('title', 'Docentes | TECH HOME')

@section('content')
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h1>Nuestro Cuerpo Docente</h1>
                <p>Profesionales especializados en robótica y tecnología</p>
            </div>
        </div>
        
        <div class="row">
            @foreach($docentes as $docente)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $docente->name }}</h5>
                            <p class="card-text">Especialidad: <strong>{{ $docente->especialidad }}</strong></p>
                            <button class="btn btn-primary">Ver Perfil</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection