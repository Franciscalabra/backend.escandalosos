@extends('layouts.admin')

@section('title', 'Categorías')
@section('header', 'Gestión de Categorías')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Lista de Categorías</h4>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Categoría
        </a>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Orden</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Slug</th>
                            <th>Productos</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr>
                            <td>{{ $category->order }}</td>
                            <td>
                                @if($category->image)
                                    <img src="{{ Storage::url($category->image) }}" 
                                         alt="{{ $category->name }}" 
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <span class="text-muted">Sin imagen</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $category->name }}</strong>
                            </td>
                            <td>{{ $category->slug }}</td>
                            <td>
                                <span class="badge bg-info">{{ $category->products_count }} productos</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $category->active ? 'success' : 'secondary' }}">
                                    {{ $category->active ? 'Activa' : 'Inactiva' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.categories.edit', $category) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($category->products_count == 0)
                                    <form action="{{ route('admin.categories.destroy', $category) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('¿Estás seguro de eliminar esta categoría?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                <p class="mb-0">No hay categorías registradas</p>
                                <a href="{{ route('admin.categories.create') }}" class="btn btn-sm btn-primary mt-2">
                                    Crear Primera Categoría
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection