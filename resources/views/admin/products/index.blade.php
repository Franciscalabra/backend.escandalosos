@extends('layouts.admin')

@section('title', 'Productos')
@section('header', 'Gestión de Productos')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Lista de Productos</h4>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Producto
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Precio</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td>
                                @if($product->image)
                                    <img src="{{ Storage::url($product->image) }}" 
                                         alt="{{ $product->name }}" 
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <span class="text-muted">Sin imagen</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $product->name }}</strong>
                                @if($product->customizable)
                                    <span class="badge bg-info ms-1">Personalizable</span>
                                @endif
                            </td>
                            <td>{{ $product->category->name }}</td>
                            <td>
                                @if($product->sizes)
                                    <small>Desde ${{ number_format(min($product->sizes), 0, ',', '.') }}</small>
                                @else
                                    ${{ number_format($product->base_price, 0, ',', '.') }}
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('admin.products.toggle-status', $product) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-{{ $product->active ? 'success' : 'secondary' }}">
                                        {{ $product->active ? 'Activo' : 'Inactivo' }}
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.products.edit', $product) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('¿Estás seguro de eliminar este producto?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                <p class="mb-0">No hay productos registrados</p>
                                <a href="{{ route('admin.products.create') }}" class="btn btn-sm btn-primary mt-2">
                                    Crear Primer Producto
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <div class="d-flex justify-content-center">
                {{ $products->links() }}
            </div>
        </div>
    </div>
@endsection