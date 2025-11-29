@extends('layouts.app')

@section('title', 'Usuario: {{ $usuario->name }} | TECH HOME')

@section('content')
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h1>{{ $usuario->name }}</h1>
                <p>Detalles del usuario</p>
                <a href="{{ route('usuarios') }}" class="btn btn-secondary">Volver a Usuarios</a>
            </div>
        </div>
    </div>
@endsection