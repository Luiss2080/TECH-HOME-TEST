@extends('layouts.app')

@section('title', 'Laboratorios | TECH HOME')

@section('content')
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h1>Laboratorios Virtuales</h1>
                <p>Acceso a laboratorios de simulación y práctica</p>
            </div>
        </div>
        
        <div class="row">
            @foreach($laboratorios as $laboratorio)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $laboratorio->nombre }}</h5>
                            <p class="card-text">
                                Estado: 
                                <span class="badge {{ $laboratorio->disponible ? 'bg-success' : 'bg-danger' }}">
                                    {{ $laboratorio->disponible ? 'Disponible' : 'En uso' }}
                                </span>
                            </p>
                            <a href="{{ route('laboratorio.show', $laboratorio->id) }}" 
                               class="btn btn-primary {{ !$laboratorio->disponible ? 'disabled' : '' }}">
                                Acceder al Lab
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection