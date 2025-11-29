@extends('layouts.app')

@section('title', 'Estudiante: {{ $estudiante->name }} | TECH HOME')

@section('content')
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h1>{{ $estudiante->name }}</h1>
                <p>Detalles del estudiante</p>
                <a href="{{ route('estudiantes') }}" class="btn btn-secondary">Volver a Estudiantes</a>
            </div>
        </div>
    </div>
@endsection