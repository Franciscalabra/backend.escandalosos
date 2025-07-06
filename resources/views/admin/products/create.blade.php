@extends('layouts.admin')

@section('title', 'Crear Producto')
@section('header', 'Crear Nuevo Producto')

@section('content')
    <div class="row">
        <div class="col-md-8">
            {{-- Mostrar errores --}}
            @if ($errors->any())
                <div class="alert alert-danger mb-3">
                    <h5 class="alert-heading">Por favor corrige los siguientes errores:</h5>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger mb-3">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success mb-3">
                    {{ session('success') }}
                </div>
            @endif
            
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre del Producto *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Categoría *</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" 
                                    id="category_id" name="category_id" required>
                                <option value="">Seleccionar categoría</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Tipo de Precio -->
                        <div class="mb-3">
                            <label class="form-label">Tipo de Precio</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="price_type" 
                                       id="single_price" value="single" checked>
                                <label class="form-check-label" for="single_price">
                                    Precio único
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="price_type" 
                                       id="multiple_sizes" value="sizes">
                                <label class="form-check-label" for="multiple_sizes">
                                    Múltiples tamaños
                                </label>
                            </div>
                        </div>
                        
                        <!-- Precio Único -->
                        <div id="single_price_div" class="mb-3">
                            <label for="base_price" class="form-label">Precio *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control @error('base_price') is-invalid @enderror" 
                                       id="base_price" name="base_price" value="{{ old('base_price') }}" 
                                       min="0" step="0.01">
                            </div>
                            @error('base_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Múltiples Tamaños -->
                        <div id="sizes_div" style="display: none;">
                            <label class="form-label">Tamaños y Precios *</label>
                            <div id="sizes_container">
                                <div class="size-row mb-2">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" 
                                                   name="sizes[0][name]" placeholder="Nombre del tamaño">
                                        </div>
                                        <div class="col-md-5">
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control" 
                                                       name="sizes[0][price]" placeholder="Precio" 
                                                       min="0" step="0.01">
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-danger btn-sm remove-size">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-secondary mt-2" id="add_size">
                                <i class="fas fa-plus"></i> Agregar Tamaño
                            </button>
                        </div>
                        
                        <div class="mb-3">
                            <label for="preparation_time" class="form-label">Tiempo de Preparación (minutos) *</label>
                            <input type="number" class="form-control @error('preparation_time') is-invalid @enderror" 
                                   id="preparation_time" name="preparation_time" 
                                   value="{{ old('preparation_time', 20) }}" min="1" required>
                            @error('preparation_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="customizable" 
                                       name="customizable" value="1" {{ old('customizable') ? 'checked' : '' }}>
                                <label class="form-check-label" for="customizable">
                                    Producto personalizable (permite agregar ingredientes)
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Imagen del Producto</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*">
                            <small class="text-muted">Formatos: JPG, PNG. Tamaño máximo: 2MB</small>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="imagePreview" class="mt-2"></div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Producto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">Ayuda</h5>
                    <p class="small">
                        <strong>Precio único:</strong> Para productos con un solo precio (bebidas, postres).
                    </p>
                    <p class="small">
                        <strong>Múltiples tamaños:</strong> Para pizzas con diferentes tamaños (personal, mediana, familiar).
                    </p>
                    <p class="small">
                        <strong>Personalizable:</strong> Permite que los clientes agreguen ingredientes extra.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Debug: Ver qué se envía en el formulario
    document.querySelector('form').addEventListener('submit', function(e) {
        console.log('Formulario enviándose...');
        const formData = new FormData(this);
        for (let [key, value] of formData.entries()) {
            console.log(key + ':', value);
        }
    });

    // Cambiar entre precio único y múltiples tamaños
    document.querySelectorAll('input[name="price_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'single') {
                document.getElementById('single_price_div').style.display = 'block';
                document.getElementById('sizes_div').style.display = 'none';
                // Hacer el precio requerido
                document.getElementById('base_price').setAttribute('required', 'required');
                // Limpiar los campos de tamaños
                document.querySelectorAll('#sizes_container input').forEach(input => {
                    input.value = '';
                    input.removeAttribute('required');
                });
            } else {
                document.getElementById('single_price_div').style.display = 'none';
                document.getElementById('sizes_div').style.display = 'block';
                // Quitar requerido del precio base
                document.getElementById('base_price').removeAttribute('required');
                document.getElementById('base_price').value = '';
                // Hacer requeridos los primeros campos de tamaño
                document.querySelector('input[name="sizes[0][name]"]').setAttribute('required', 'required');
                document.querySelector('input[name="sizes[0][price]"]').setAttribute('required', 'required');
            }
        });
    });
    
    // Agregar nuevo tamaño
    let sizeIndex = 1;
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
                               name="sizes[${sizeIndex}][price]" placeholder="Precio" 
                               min="0" step="0.01">
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

    // Preview de imagen
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('imagePreview');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" class="img-thumbnail" style="max-width: 200px;">`;
            }
            reader.readAsDataURL(file);
        } else {
            preview.innerHTML = '';
        }
    });
</script>
@endpus