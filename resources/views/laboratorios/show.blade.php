@extends('layouts.app')

@section('title', 'Laboratorio: {{ $laboratorio->nombre }} | TECH HOME')

@section('content')
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h1>{{ $laboratorio->nombre }}</h1>
                <p>Entorno de laboratorio virtual</p>
                <a href="{{ route('laboratorios') }}" class="btn btn-secondary">Volver a Laboratorios</a>
            </div>
        </div>
    </div>
@endsection