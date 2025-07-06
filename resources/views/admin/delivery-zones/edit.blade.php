
@extends('layouts.admin')

@section('title', 'Editar Zona de Delivery')
@section('header', 'Editar Zona de Delivery')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.delivery-zones.update', $deliveryZone) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre de la Zona *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $deliveryZone->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="communes" class="form-label">Comunas *</label>
                            <textarea class="form-control @error('communes') is-invalid @enderror" 
                                      id="communes" name="communes" rows="3" required>{{ old('communes', $deliveryZone->communes_string) }}</textarea>
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
                                       id="delivery_fee" name="delivery_fee" 
                                       value="{{ old('delivery_fee', $deliveryZone->delivery_fee) }}" 
                                       min="0" required>
                            </div>
                            @error('delivery_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="estimated_time" class="form-label">Tiempo Estimado (minutos) *</label>
                            <input type="number" class="form-control @error('estimated_time') is-invalid @enderror" 
                                   id="estimated_time" name="estimated_time" 
                                   value="{{ old('estimated_time', $deliveryZone->estimated_time) }}" 
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
                                <i class="fas fa-save"></i> Actualizar Zona
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">Zona Actual</h5>
                    <p class="small mb-2">
                        <strong>Comunas actuales:</strong><br>
                        @foreach($deliveryZone->communes as $commune)
                            <span class="badge bg-secondary me-1">{{ $commune }}</span>
                        @endforeach
                    </p>
                    <p class="small mb-0">
                        <strong>Tarifa actual:</strong> ${{ number_format($deliveryZone->delivery_fee, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection