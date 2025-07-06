@extends('layouts.admin')

@section('title', 'Zonas de Delivery')
@section('header', 'Gestión de Zonas de Delivery')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Lista de Zonas de Delivery</h4>
        <a href="{{ route('admin.delivery-zones.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Zona
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Comunas</th>
                            <th>Tarifa</th>
                            <th>Tiempo Est.</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($zones as $zone)
                        <tr>
                            <td><strong>{{ $zone->name }}</strong></td>
                            <td>
                                <small>{{ implode(', ', $zone->communes) }}</small>
                            </td>
                            <td>${{ number_format($zone->delivery_fee, 0, ',', '.') }}</td>
                            <td>{{ $zone->estimated_time }} min</td>
                            <td>
                                <form action="{{ route('admin.delivery-zones.toggle-status', $zone) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-{{ $zone->active ? 'success' : 'secondary' }}">
                                        {{ $zone->active ? 'Activa' : 'Inactiva' }}
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.delivery-zones.edit', $zone) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.delivery-zones.destroy', $zone) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('¿Estás seguro de eliminar esta zona?')">
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
                            <td colspan="6" class="text-center">
                                <p class="mb-0">No hay zonas de delivery registradas</p>
                                <a href="{{ route('admin.delivery-zones.create') }}" class="btn btn-sm btn-primary mt-2">
                                    Crear Primera Zona
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