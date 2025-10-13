@extends('layouts.app')

@section('title', "Language: {$language->name}")

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-language me-2"></i>
                        Language Details
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Language Header -->
                    <div class="row mb-4">
                        <div class="col-md-3 text-center">
                            <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto" 
                                 style="width: 80px; height: 80px; border-radius: 50%; font-size: 24px; font-weight: bold;">
                                {{ strtoupper(substr($language->name, 0, 2)) }}
                            </div>
                        </div>
                        <div class="col-md-9">
                            <h2 class="mb-2">{{ $language->formatted_name }}</h2>
                            <p class="text-muted mb-2">
                                <i class="fas fa-id-badge me-1"></i>
                                Language ID: <strong>{{ $language->language_id }}</strong>
                            </p>
                            <p class="text-muted mb-0">
                                <i class="fas fa-clock me-1"></i>
                                Last Updated: <strong>{{ $language->last_update?->format('F j, Y \a\t g:i A') ?? 'Not available' }}</strong>
                            </p>
                        </div>
                    </div>

                    <!-- Language Information Table -->
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td width="30%" class="fw-bold text-muted">Language ID:</td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $language->language_id }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Language Name:</td>
                                    <td>
                                        <strong>{{ $language->name }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Formatted Name:</td>
                                    <td>{{ $language->formatted_name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">First Letter:</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $language->first_letter }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Created At:</td>
                                    <td>
                                        @if($language->last_update)
                                            {{ $language->last_update->format('F j, Y \a\t g:i A') }}
                                            <small class="text-muted">
                                                ({{ $language->last_update->diffForHumans() }})
                                            </small>
                                        @else
                                            <em class="text-muted">Not available</em>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Quick Stats (if relationships exist in the future) -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Films Available</h5>
                                    <h3 class="text-primary">
                                        {{-- {{ $language->films_count ?? 0 }} --}}
                                        <span class="text-muted">Coming Soon</span>
                                    </h3>
                                    <small class="text-muted">When Film model is implemented</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Original Language Films</h5>
                                    <h3 class="text-success">
                                        {{-- {{ $language->original_language_films_count ?? 0 }} --}}
                                        <span class="text-muted">Coming Soon</span>
                                    </h3>
                                    <small class="text-muted">When Film model is implemented</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('languages.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Back to Languages
                        </a>
                        <div>
                            <a href="{{ route('languages.edit', $language) }}" class="btn btn-warning me-2">
                                <i class="fas fa-edit me-1"></i>
                                Edit Language
                            </a>
                            <form method="POST" 
                                  action="{{ route('languages.destroy', $language) }}" 
                                  class="d-inline" 
                                  onsubmit="return confirm('Are you sure you want to delete the language \'{{ $language->name }}\'? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash me-1"></i>
                                    Delete Language
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar with Additional Information -->
        <div class="col-md-4">
            <!-- Language Properties Card -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Language Properties
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Character Count:</span>
                        <span class="badge bg-info">{{ strlen($language->name) }} / 20</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Word Count:</span>
                        <span class="badge bg-secondary">{{ str_word_count($language->name) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Starts With:</span>
                        <span class="badge bg-primary">{{ $language->first_letter }}</span>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Card -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-history me-2"></i>
                        Recent Activity
                    </h6>
                </div>
                <div class="card-body">
                    @if($language->last_update)
                        <div class="timeline-item">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="timeline-icon bg-primary">
                                        <i class="fas fa-clock text-white"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">Last Updated</h6>
                                    <p class="text-muted mb-0">{{ $language->last_update->diffForHumans() }}</p>
                                    <small class="text-muted">{{ $language->last_update->format('M j, Y \a\t g:i A') }}</small>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-muted text-center">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            No activity data available
                        </p>
                    @endif
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('languages.edit', $language) }}" class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>
                            Edit This Language
                        </a>
                        <a href="{{ route('languages.create') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>
                            Add New Language
                        </a>
                        <a href="{{ route('languages.alphabetical') }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-sort-alpha-down me-1"></i>
                            Alphabetical View
                        </a>
                        {{-- 
                        <a href="{{ route('films.index', ['language' => $language->language_id]) }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-film me-1"></i>
                            View Films in {{ $language->name }}
                        </a>
                        --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline-icon {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endsection