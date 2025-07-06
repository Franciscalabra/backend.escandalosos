
@extends('layouts.admin')

@section('title', 'Ingredientes')
@section('header', 'Gestión de Ingredientes')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Lista de Ingredientes</h4>
        <a href="{{ route('admin.ingredients.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Ingrediente
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Los ingredientes se pueden agregar a las pizzas personalizables.
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Precio Extra</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $categoryLabels = [
                                'cheese' => 'Quesos',
                                'meat' => 'Carnes',
                                'vegetable' => 'Vegetales',
                                'sauce' => 'Salsas'
                            ];
                        @endphp
                        @foreach($ingredients as $ingredient)
                        <tr>
                            <td><strong>{{ $ingredient->name }}</strong></td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ $categoryLabels[$ingredient->category] ?? $ingredient->category }}
                                </span>
                            </td>
                            <td>${{ number_format($ingredient->price, 0, ',', '.') }}</td>
                            <td>
                                <form action="{{ route('admin.ingredients.toggle-status', $ingredient) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-{{ $ingredient->active ? 'success' : 'secondary' }}">
                                        {{ $ingredient->active ? 'Activo' : 'Inactivo' }}
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.ingredients.edit', $ingredient) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.ingredients.destroy', $ingredient) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('¿Estás seguro de eliminar este ingrediente?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection