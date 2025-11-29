@extends('layouts.app')

@section('title', 'Categoría: {{ $categoria->nombre }} | TECH HOME')

@section('content')
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h1>{{ $categoria->nombre }}</h1>
                <p>Explorando recursos de esta categoría</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <h3>Cursos</h3>
                @if($cursos->count() > 0)
                    <div class="list-group">
                        @foreach($cursos as $curso)
                            <div class="list-group-item">{{ $curso->titulo }}</div>
                        @endforeach
                    </div>
                @else
                    <p>No hay cursos disponibles en esta categoría.</p>
                @endif
            </div>
            
            <div class="col-md-6">
                <h3>Recursos</h3>
                @if($libros->count() > 0)
                    <div class="list-group">
                        @foreach($libros as $libro)
                            <div class="list-group-item">{{ $libro->titulo }}</div>
                        @endforeach
                    </div>
                @else
                    <p>No hay recursos disponibles en esta categoría.</p>
                @endif
            </div>
        </div>
    </div>
@endsection