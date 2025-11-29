@extends('layouts.app')

@section('title', 'Material: {{ $material->titulo }} | TECH HOME')

@section('content')
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h1>{{ $material->titulo }}</h1>
                <p>Detalles del material de estudio</p>
                <a href="{{ route('materiales') }}" class="btn btn-secondary">Volver a Materiales</a>
            </div>
        </div>
    </div>
@endsection