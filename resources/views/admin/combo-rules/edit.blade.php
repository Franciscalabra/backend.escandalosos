
@extends('layouts.admin')

@section('title', 'Editar Regla de Combo')
@section('header', 'Editar Regla de Combo')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.combo-rules.update', $comboRule) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre de la Regla *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $comboRule->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <h5 class="mb-3">Condiciones</h5>
                        
                        <div class="mb-3">
                            <label for="min_pizzas" class="form-label">Mínimo de Pizzas</label>
                            <input type="number" class="form-control @error('min_pizzas') is-invalid @enderror" 
                                   id="min_pizzas" name="min_pizzas" 
                                   value="{{ old('min_pizzas', $comboRule->conditions['min_pizzas'] ?? '') }}" min="1">
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
                                       id="min_total" name="min_total" 
                                       value="{{ old('min_total', $comboRule->conditions['min_total'] ?? '') }}" min="0">
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
                                       value="{{ old('discount_percentage', $comboRule->benefits['discount_percentage']) }}" 
                                       min="0" max="100" required>
                                <span class="input-group-text">%</span>
                            </div>
                            @error('discount_percentage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="priority" class="form-label">Prioridad *</label>
                            <input type="number" class="form-control @error('priority') is-invalid @enderror" 
                                   id="priority" name="priority" 
                                   value="{{ old('priority', $comboRule->priority) }}" min="0" required>
                            <small class="text-muted">Mayor número = mayor prioridad</small>
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
                                           id="valid_from" name="valid_from" 
                                           value="{{ old('valid_from', $comboRule->valid_from?->format('Y-m-d')) }}">
                                    @error('valid_from')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="valid_until" class="form-label">Válido Hasta</label>
                                    <input type="date" class="form-control @error('valid_until') is-invalid @enderror" 
                                           id="valid_until" name="valid_until" 
                                           value="{{ old('valid_until', $comboRule->valid_until?->format('Y-m-d')) }}">
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
                                <i class="fas fa-save"></i> Actualizar Regla
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection