@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Detalles del Registro de Auditoría</h4>
                    <a href="{{ route('audit.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver a la Lista
                    </a>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Información General</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">ID:</th>
                                    <td>{{ $auditLog->id }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha/Hora:</th>
                                    <td>{{ \Carbon\Carbon::parse($auditLog->created_at)->format('d/m/Y H:i:s T') }}</td>
                                </tr>
                                <tr>
                                    <th>Usuario:</th>
                                    <td>
                                        @if($auditLog->user_email)
                                            <span class="badge bg-info">{{ $auditLog->user_role }}</span><br>
                                            {{ $auditLog->user_email }}
                                        @else
                                            <span class="text-muted">Usuario Desconocido (ID: {{ $auditLog->user_id }})</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Acción:</th>
                                    <td>
                                        <span class="badge bg-{{ $auditLog->action == 'login' ? 'success' : ($auditLog->action == 'logout' ? 'warning' : ($auditLog->action == 'delete' ? 'danger' : 'primary')) }}">
                                            @switch($auditLog->action)
                                                @case('login') Inicio de Sesión @break
                                                @case('logout') Cierre de Sesión @break
                                                @case('view') Visualizar @break
                                                @case('create') Crear @break
                                                @case('update') Actualizar @break
                                                @case('delete') Eliminar @break
                                                @default {{ ucfirst($auditLog->action) }}
                                            @endswitch
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Recurso:</th>
                                    <td>{{ $auditLog->resource ?: '-' }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h5>Información de la Petición</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Método:</th>
                                    <td>
                                        <span class="badge bg-{{ $auditLog->method == 'GET' ? 'info' : ($auditLog->method == 'POST' ? 'success' : ($auditLog->method == 'PUT' ? 'warning' : 'danger')) }}">
                                            {{ $auditLog->method }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>URL:</th>
                                    <td class="text-break">{{ $auditLog->url }}</td>
                                </tr>
                                <tr>
                                    <th>Dirección IP:</th>
                                    <td>{{ $auditLog->ip_address }}</td>
                                </tr>
                                <tr>
                                    <th>Código de Respuesta:</th>
                                    <td>
                                        <span class="badge bg-{{ $auditLog->response_code < 300 ? 'success' : ($auditLog->response_code < 400 ? 'warning' : 'danger') }}">
                                            {{ $auditLog->response_code }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h5>Agente de Usuario</h5>
                            <div class="alert alert-light">
                                {{ $auditLog->user_agent ?: 'No disponible' }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5>Datos de la Petición</h5>
                            <div class="alert alert-light">
                                @if($auditLog->request_data)
                                    <pre class="mb-0">{{ json_encode(json_decode($auditLog->request_data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                @else
                                    <em>No hay datos de petición disponibles</em>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($auditLog->action == 'login' || $auditLog->action == 'logout')
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle"></i> Evento de Autenticación</h6>
                                <p class="mb-0">
                                    Esta entrada de registro representa un evento de {{ $auditLog->action == 'login' ? 'inicio de sesión' : 'cierre de sesión' }} para el usuario 
                                    <strong>{{ $auditLog->user_email }}</strong> 
                                    desde la dirección IP <strong>{{ $auditLog->ip_address }}</strong> 
                                    en <strong>{{ \Carbon\Carbon::parse($auditLog->created_at)->format('d/m/Y H:i:s T') }}</strong>.
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5>Navegación</h5>
                            <div class="btn-group" role="group">
                                @if($previousLog)
                                    <a href="{{ route('audit.show', $previousLog->id) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-chevron-left"></i> Registro Anterior
                                    </a>
                                @endif
                                
                                <a href="{{ route('audit.index') }}" class="btn btn-primary">
                                    <i class="fas fa-list"></i> Volver a la Lista
                                </a>
                                
                                @if($nextLog)
                                    <a href="{{ route('audit.show', $nextLog->id) }}" class="btn btn-outline-primary">
                                        Siguiente Registro <i class="fas fa-chevron-right"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
.text-break {
    word-break: break-all;
}
pre {
    font-size: 0.875rem;
    max-height: 300px;
    overflow-y: auto;
}
</style>
@endpush