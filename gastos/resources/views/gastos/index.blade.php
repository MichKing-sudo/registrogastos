@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Mensajes de éxito/error -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 border-start border-success border-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle fa-2x me-3 text-success"></i>
                <div>
                    <strong>¡Excelente!</strong> {{ session('success') }}
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 border-start border-danger border-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle fa-2x me-3 text-danger"></i>
                <div>
                    <strong>¡Error!</strong> {{ session('error') }}
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif
    
    <!-- Header con título y fecha -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="display-6 fw-bold text-primary">
                <i class="fas fa-wallet me-2"></i>Registro de Gastos
            </h1>
            <p class="text-muted mb-0">
                <i class="fas fa-calendar-alt me-2"></i>{{ now()->format('l, d F Y') }}
            </p>
        </div>
        <div class="text-end">
            <span class="badge bg-light text-dark p-3 shadow-sm">
                <i class="fas fa-clock me-2 text-primary"></i>
                {{ now()->format('h:i A') }}
            </span>
        </div>
    </div>
    
    <!-- Tarjeta de Total Mejorada -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-lg bg-gradient-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-white-50 mb-1">Total Gastos Hoy</p>
                            <h2 class="display-4 fw-bold mb-0">${{ number_format($total, 2) }}</h2>
                            <small class="text-white-50">{{ count($gastos) }} transacciones</small>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-chart-pie fa-3x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Promedio por Gasto</p>
                            @php
                                $promedio = count($gastos) > 0 ? $total / count($gastos) : 0;
                            @endphp
                            <h3 class="fw-bold text-info mb-0">${{ number_format($promedio, 2) }}</h3>
                            <small class="text-muted">por transacción</small>
                        </div>
                        <div class="bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-calculator fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Gasto más alto</p>
                            @php
                                $maximo = count($gastos) > 0 ? max(array_column($gastos, 'monto')) : 0;
                            @endphp
                            <h3 class="fw-bold text-warning mb-0">${{ number_format($maximo, 2) }}</h3>
                            <small class="text-muted">máximo del día</small>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-arrow-up fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        <!-- Formulario para agregar gasto (Rediseñado) -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="fw-bold text-primary">
                        <i class="fas fa-plus-circle me-2"></i>Registrar Nuevo Gasto
                    </h5>
                    <p class="text-muted small">Completa los campos para agregar un gasto</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('gastos.store') }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        
                        <div class="mb-4">
                            <label for="descripcion" class="form-label fw-semibold">
                                <i class="fas fa-tag me-2 text-primary"></i>Descripción del Gasto
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0">
                                    <i class="fas fa-pen text-primary"></i>
                                </span>
                                <input type="text" 
                                       class="form-control form-control-lg border-0 bg-light @error('descripcion') is-invalid @enderror" 
                                       id="descripcion" 
                                       name="descripcion" 
                                       value="{{ old('descripcion') }}"
                                       placeholder="Ej: Café, almuerzo, taxi..."
                                       style="border-radius: 0 10px 10px 0;">
                            </div>
                            @error('descripcion')
                                <div class="text-danger small mt-2">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="monto" class="form-label fw-semibold">
                                <i class="fas fa-dollar-sign me-2 text-primary"></i>Monto
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0">
                                    <i class="fas fa-coins text-primary"></i>
                                </span>
                                <input type="number" 
                                       step="0.01" 
                                       min="0.01" 
                                       class="form-control form-control-lg border-0 bg-light @error('monto') is-invalid @enderror" 
                                       id="monto" 
                                       name="monto" 
                                       value="{{ old('monto') }}"
                                       placeholder="0.00"
                                       style="border-radius: 0 10px 10px 0;">
                            </div>
                            @error('monto')
                                <div class="text-danger small mt-2">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                            <i class="fas fa-save me-2"></i>Guardar Gasto
                            <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                        
                        @if(count($gastos) > 0)
                            <form action="{{ route('gastos.limpiar') }}" method="POST" 
                                  onsubmit="return confirm('¿Estás seguro de eliminar TODOS los gastos? Esta acción no se puede deshacer.');">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="fas fa-trash-alt me-2"></i>Limpiar Todos ({{ count($gastos) }})
                                </button>
                            </form>
                        @endif
                    </form>
                </div>
                <div class="card-footer bg-white border-0 pb-4">
                    <div class="d-flex justify-content-between text-muted small">
                        <span><i class="fas fa-info-circle me-1"></i>Campos requeridos</span>
                        <span>Registro rápido y seguro</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabla de gastos (Rediseñada) -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold text-primary mb-0">
                            <i class="fas fa-list me-2"></i>Lista de Gastos del Día
                        </h5>
                        <span class="badge bg-primary rounded-pill p-2">
                            {{ count($gastos) }} {{ count($gastos) == 1 ? 'gasto' : 'gastos' }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    @if(count($gastos) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0 rounded-start" width="50">#</th>
                                        <th class="border-0">Descripción</th>
                                        <th class="border-0">Monto</th>
                                        <th class="border-0">Hora</th>
                                        <th class="border-0 rounded-end text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($gastos as $gasto)
                                        <tr class="border-bottom">
                                            <td>
                                                <span class="badge bg-light text-dark rounded-circle p-2">
                                                    {{ $loop->iteration }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="fw-semibold">{{ $gasto['descripcion'] }}</div>
                                                <small class="text-muted">ID: #{{ $gasto['id'] }}</small>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-success">
                                                    ${{ number_format($gasto['monto'], 2) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="far fa-clock text-muted me-2"></i>
                                                    <span class="small">
                                                        {{ \Carbon\Carbon::parse($gasto['created_at'])->format('h:i A') }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <form action="{{ route('gastos.destroy', $gasto['id']) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('¿Eliminar este gasto?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-2" 
                                                            title="Eliminar gasto" style="width: 35px; height: 35px;">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-primary">
                                    <tr>
                                        <td colspan="2" class="text-end fw-bold">TOTAL:</td>
                                        <td colspan="3" class="fw-bold text-success h5 mb-0">${{ number_format($total, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-receipt fa-5x text-muted opacity-25"></i>
                            </div>
                            <h5 class="text-muted mb-3">No hay gastos registrados hoy</h5>
                            <p class="text-muted mb-4">¡Comienza agregando tu primer gasto!</p>
                            <button class="btn btn-primary" onclick="document.getElementById('descripcion').focus()">
                                <i class="fas fa-plus-circle me-2"></i>Agregar Gasto
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos personalizados -->
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .opacity-25 {
        opacity: 0.25;
    }
    
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 15px;
        overflow: hidden;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.175) !important;
    }
    
    .table tbody tr {
        transition: background-color 0.2s ease;
    }
    
    .table tbody tr:hover {
        background-color: rgba(102, 126, 234, 0.05);
    }
    
    .btn-outline-danger {
        transition: all 0.3s ease;
    }
    
    .btn-outline-danger:hover {
        transform: rotate(90deg);
    }
    
    .input-group-text {
        border-radius: 10px 0 0 10px;
    }
    
    .form-control-lg {
        padding: 0.75rem 1rem;
    }
    
    /* Animaciones */
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .card {
        animation: slideIn 0.5s ease-out;
    }
    
    /* Scrollbar personalizada */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    }
</style>
@endsection