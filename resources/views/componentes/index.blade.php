@extends('layouts.app')

@section('title', 'Componentes | TECH HOME')

@section('content')
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h1>Centro de Componentes</h1>
                <p>Inventario de componentes electrónicos y robóticos</p>
            </div>
        </div>
        
        <div class="row">
            @foreach($componentes as $componente)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $componente->nombre }}</h5>
                            <p class="card-text">Cantidad disponible: <strong>{{ $componente->cantidad }}</strong></p>
                            <button class="btn btn-primary">Solicitar</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection