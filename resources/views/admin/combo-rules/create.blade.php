@extends('layouts.admin')

@section('title', 'Crear Regla de Combo')
@section('header', 'Crear Nueva Regla de Combo')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.combo-rules.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre de la Regla *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            <small class="text-muted">Ej: "2x1 en Pizzas Medianas", "15% en pedidos sobre $20.000"</small>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <h5 class="mb-3">Condiciones</h5>
                        
                        <div class="mb-3">
                            <label for="min_pizzas" class="form-label">Mínimo de Pizzas</label>
                            <input type="number" class="form-control @error('min_pizzas') is-invalid @enderror" 
                                   id="min_pizzas" name="min_pizzas" value="{{ old('min_pizzas') }}" min="1">
                            <small class="text-muted">Dejar vacío si no aplica</small>
                            @error('min_pizzas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="min_total" class="form-label">Monto Mínimo del Pedido</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control @error('min_total') is-invalid @enderror" 
                                       id="min_total" name="min_total" value="{{ old('min_total') }}" min="0">
                            </div>
                            <small class="text-muted">Dejar vacío si no aplica</small>
                            @error('min_total')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <h5 class="mb-3">Beneficios</h5>
                        
                        <div class="mb-3">
                            <label for="discount_percentage" class="form-label">Porcentaje de Descuento *</label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('discount_percentage') is-invalid @enderror" 
                                       id="discount_percentage" name="discount_percentage" 
                                       value="{{ old('discount_percentage') }}" min="0" max="100" required>
                                <span class="input-group-text">%</span>
                            </div>
                            @error('discount_percentage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="priority" class="form-label">Prioridad *</label>
                            <input type="number" class="form-control @error('priority') is-invalid @enderror" 
                                   id="priority" name="priority" value="{{ old('priority', 0) }}" min="0" required>
                            <small class="text-muted">Mayor número = mayor prioridad. Se aplica la primera regla que cumpla las condiciones.</small>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <h5 class="mb-3">Vigencia (Opcional)</h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="valid_from" class="form-label">Válido Desde</label>
                                    <input type="date" class="form-control @error('valid_from') is-invalid @enderror" 
                                           id="valid_from" name="valid_from" value="{{ old('valid_from') }}">
                                    @error('valid_from')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="valid_until" class="form-label">Válido Hasta</label>
                                    <input type="date" class="form-control @error('valid_until') is-invalid @enderror" 
                                           id="valid_until" name="valid_until" value="{{ old('valid_until') }}">
                                    @error('valid_until')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.combo-rules.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Regla
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">Ejemplos de Reglas</h5>
                    
                    <p class="small mb-3">
                        <strong>2x1 en Pizzas:</strong><br>
                        - Min pizzas: 2<br>
                        - Descuento: 50%
                    </p>
                    
                    <p class="small mb-3">
                        <strong>Descuento por monto:</strong><br>
                        - Monto mínimo: $20.000<br>
                        - Descuento: 15%
                    </p>
                    
                    <p class="small mb-0">
                        <strong>Prioridad:</strong> Si un pedido cumple varias reglas, 
                        se aplica la de mayor prioridad.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection