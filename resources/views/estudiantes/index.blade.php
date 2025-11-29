@extends('layouts.app')

@section('title', 'Estudiantes | TECH HOME')

@section('content')
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h1>Lista de Estudiantes</h1>
                <p>Estudiantes registrados en la plataforma</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Curso Actual</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($estudiantes as $estudiante)
                                <tr>
                                    <td>{{ $estudiante->id }}</td>
                                    <td>{{ $estudiante->name }}</td>
                                    <td>{{ $estudiante->curso }}</td>
                                    <td>
                                        <a href="{{ route('estudiantes.show', $estudiante->id) }}" class="btn btn-sm btn-primary">Ver</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection