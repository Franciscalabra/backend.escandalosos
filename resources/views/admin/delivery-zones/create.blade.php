@extends('layouts.admin')

@section('title', 'Crear Zona de Delivery')
@section('header', 'Crear Nueva Zona de Delivery')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.delivery-zones.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre de la Zona *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="communes" class="form-label">Comunas *</label>
                            <textarea class="form-control @error('communes') is-invalid @enderror" 
                                      id="communes" name="communes" rows="3" required>{{ old('communes') }}</textarea>
                            <small class="text-muted">Separar las comunas por comas. Ej: Las Condes, Vitacura, Providencia</small>
                            @error('communes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="delivery_fee" class="form-label">Tarifa de Delivery *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control @error('delivery_fee') is-invalid @enderror" 
                                       id="delivery_fee" name="delivery_fee" value="{{ old('delivery_fee') }}" 
                                       min="0" required>
                            </div>
                            @error('delivery_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="estimated_time" class="form-label">Tiempo Estimado (minutos) *</label>
                            <input type="number" class="form-control @error('estimated_time') is-invalid @enderror" 
                                   id="estimated_time" name="estimated_time" value="{{ old('estimated_time', 30) }}" 
                                   min="1" required>
                            @error('estimated_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.delivery-zones.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Zona
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">Información</h5>
                    <p class="small mb-2">
                        <strong>Zonas de Delivery:</strong> Define las áreas donde realizas entregas.
                    </p>
                    <p class="small mb-2">
                        <strong>Comunas:</strong> Lista todas las comunas que cubre esta zona.
                    </p>
                    <p class="small mb-0">
                        <strong>Tarifa:</strong> Costo del delivery para esta zona.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection