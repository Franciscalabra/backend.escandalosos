@extends('layouts.admin')

@section('title', 'Editar Producto')
@section('header', 'Editar Producto')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre del Producto *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $product->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Categoría *</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" 
                                    id="category_id" name="category_id" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Tipo de Precio -->
                        <div class="mb-3">
                            <label class="form-label">Tipo de Precio</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="price_type" 
                                       id="single_price" value="single" 
                                       {{ !$product->sizes || count($product->sizes) == 0 ? 'checked' : '' }}>
                                <label class="form-check-label" for="single_price">
                                    Precio único
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="price_type" 
                                       id="multiple_sizes" value="sizes"
                                       {{ $product->sizes && count($product->sizes) > 0 ? 'checked' : '' }}>
                                <label class="form-check-label" for="multiple_sizes">
                                    Múltiples tamaños
                                </label>
                            </div>
                        </div>
                        
                        <!-- Precio Único -->
                        <div id="single_price_div" class="mb-3" 
                             style="{{ $product->sizes && count($product->sizes) > 0 ? 'display: none;' : '' }}">
                            <label for="base_price" class="form-label">Precio</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control @error('base_price') is-invalid @enderror" 
                                       id="base_price" name="base_price" 
                                       value="{{ old('base_price', $product->base_price) }}" min="0">
                            </div>
                            @error('base_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Múltiples Tamaños -->
                        <div id="sizes_div" style="{{ !$product->sizes || count($product->sizes) == 0 ? 'display: none;' : '' }}">
                            <label class="form-label">Tamaños y Precios</label>
                            <div id="sizes_container">
                                @if($product->sizes)
                                    @foreach($product->sizes as $sizeName => $price)
                                    <div class="size-row mb-2">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" 
                                                       name="sizes[{{ $loop->index }}][name]" 
                                                       value="{{ $sizeName }}" placeholder="Nombre del tamaño">
                                            </div>
                                            <div class="col-md-5">
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" class="form-control" 
                                                           name="sizes[{{ $loop->index }}][price]" 
                                                           value="{{ $price }}" placeholder="Precio" min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" class="btn btn-danger btn-sm remove-size">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="btn btn-sm btn-secondary mt-2" id="add_size">
                                <i class="fas fa-plus"></i> Agregar Tamaño
                            </button>
                        </div>
                        
                        <div class="mb-3">
                            <label for="preparation_time" class="form-label">Tiempo de Preparación (minutos) *</label>
                            <input type="number" class="form-control @error('preparation_time') is-invalid @enderror" 
                                   id="preparation_time" name="preparation_time" 
                                   value="{{ old('preparation_time', $product->preparation_time) }}" min="1" required>
                            @error('preparation_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="customizable" 
                                       name="customizable" value="1" 
                                       {{ old('customizable', $product->customizable) ? 'checked' : '' }}>
                                <label class="form-check-label" for="customizable">
                                    Producto personalizable (permite agregar ingredientes)
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Imagen del Producto</label>
                            @if($product->image)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($product->image) }}" 
                                         alt="{{ $product->name }}" 
                                         style="max-width: 200px; max-height: 200px;">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*">
                            <small class="text-muted">Dejar vacío para mantener la imagen actual</small>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Actualizar Producto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">Información Actual</h5>
                    <p class="small mb-2">
                        <strong>Categoría:</strong> {{ $product->category->name }}
                    </p>
                    <p class="small mb-2">
                        <strong>Estado:</strong> 
                        <span class="badge bg-{{ $product->active ? 'success' : 'danger' }}">
                            {{ $product->active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </p>
                    @if($product->sizes && count($product->sizes) > 0)
                        <p class="small mb-0">
                            <strong>Tamaños actuales:</strong><br>
                            @foreach($product->sizes as $size => $price)
                                {{ $size }}: ${{ number_format($price, 0, ',', '.') }}<br>
                            @endforeach
                        </p>
                    @else
                        <p class="small mb-0">
                            <strong>Precio actual:</strong> ${{ number_format($product->base_price, 0, ',', '.') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Script similar al de create.blade.php
    let sizeIndex = {{ $product->sizes ? count($product->sizes) : 1 }};
    
    // Cambiar entre precio único y múltiples tamaños
    document.querySelectorAll('input[name="price_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'single') {
                document.getElementById('single_price_div').style.display = 'block';
                document.getElementById('sizes_div').style.display = 'none';
            } else {
                document.getElementById('single_price_div').style.display = 'none';
                document.getElementById('sizes_div').style.display = 'block';
            }
        });
    });
    
    // Agregar nuevo tamaño
    document.getElementById('add_size').addEventListener('click', function() {
        const container = document.getElementById('sizes_container');
        const newRow = document.createElement('div');
        newRow.className = 'size-row mb-2';
        newRow.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <input type="text" class="form-control" 
                           name="sizes[${sizeIndex}][name]" placeholder="Nombre del tamaño">
                </div>
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control" 
                               name="sizes[${sizeIndex}][price]" placeholder="Precio" min="0">
                    </div>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-size">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
        container.appendChild(newRow);
        sizeIndex++;
    });
    
    // Eliminar tamaño
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-size') || e.target.parentElement.classList.contains('remove-size')) {
            const row = e.target.closest('.size-row');
            row.remove();
        }
    });
</script>
@endpush