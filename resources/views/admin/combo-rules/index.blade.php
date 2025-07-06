@extends('layouts.admin')

@section('title', 'Reglas de Combos')
@section('header', 'Gestión de Reglas de Combos')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Lista de Reglas de Combos</h4>
        <a href="{{ route('admin.combo-rules.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Regla
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Las reglas se aplican automáticamente cuando los clientes cumplen las condiciones.
                La regla con mayor prioridad se aplica primero.
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Prioridad</th>
                            <th>Nombre</th>
                            <th>Condiciones</th>
                            <th>Descuento</th>
                            <th>Vigencia</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rules as $rule)
                        <tr>
                            <td>
                                <span class="badge bg-primary">{{ $rule->priority }}</span>
                            </td>
                            <td><strong>{{ $rule->name }}</strong></td>
                            <td>
                                @if(isset($rule->conditions['min_pizzas']))
                                    <span class="badge bg-secondary">Min. {{ $rule->conditions['min_pizzas'] }} pizzas</span>
                                @endif
                                @if(isset($rule->conditions['min_total']))
                                    <span class="badge bg-secondary">${{ number_format($rule->conditions['min_total'], 0, ',', '.') }}+</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-success">{{ $rule->benefits['discount_percentage'] }}% OFF</span>
                            </td>
                            <td>
                                @if($rule->valid_from || $rule->valid_until)
                                    <small>
                                        {{ $rule->valid_from ? $rule->valid_from->format('d/m/Y') : 'Siempre' }}
                                        -
                                        {{ $rule->valid_until ? $rule->valid_until->format('d/m/Y') : 'Siempre' }}
                                    </small>
                                @else
                                    <small>Siempre vigente</small>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('admin.combo-rules.toggle-status', $rule) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-{{ $rule->active ? 'success' : 'secondary' }}">
                                        {{ $rule->active ? 'Activa' : 'Inactiva' }}
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.combo-rules.edit', $rule) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.combo-rules.destroy', $rule) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('¿Estás seguro de eliminar esta regla?')">
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
                                <p class="mb-0">No hay reglas de combo registradas</p>
                                <a href="{{ route('admin.combo-rules.create') }}" class="btn btn-sm btn-primary mt-2">
                                    Crear Primera Regla
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