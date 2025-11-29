@extends('layouts.app')

@section('title', 'Materiales | TECH HOME')

@section('content')
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h1>Materiales de Estudio</h1>
                <p>Recursos y documentos disponibles para descarga</p>
            </div>
        </div>
        
        <div class="row">
            @foreach($materiales as $material)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $material->titulo }}</h5>
                            <p class="card-text">Tipo: {{ $material->tipo }}</p>
                            <a href="{{ route('materiales.show', $material->id) }}" class="btn btn-primary">Ver Material</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection