@extends('layouts.app')

@section('title', 'Languages Alphabetical View')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-sort-alpha-down me-2"></i>
                    Languages - Alphabetical View
                </h2>
                <div class="d-flex gap-2">
                    <a href="{{ route('languages.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list me-1"></i>
                        List View
                    </a>
                    <a href="{{ route('languages.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Add Language
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($languageGroups->count() > 0)
        <!-- Quick Navigation -->
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="card-title">Quick Navigation</h6>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($languageGroups->keys()->sort() as $letter)
                        <a href="#letter-{{ $letter }}" class="btn btn-outline-secondary btn-sm">
                            {{ strtoupper($letter) }}
                            <span class="badge bg-secondary ms-1">{{ $languageGroups[$letter]->count() }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Language Groups -->
        @foreach($languageGroups->keys()->sort() as $letter)
            <div class="card mb-4" id="letter-{{ $letter }}">
                <div class="card-header">
                    <h4 class="mb-0">
                        <span class="badge bg-primary me-2" style="font-size: 1.2em;">{{ strtoupper($letter) }}</span>
                        Languages starting with "{{ strtoupper($letter) }}"
                        <span class="badge bg-secondary ms-2">{{ $languageGroups[$letter]->count() }}</span>
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($languageGroups[$letter] as $language)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center" 
                                                     style="width: 50px; height: 50px; border-radius: 50%; font-weight: bold;">
                                                    {{ strtoupper(substr($language->name, 0, 2)) }}
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="card-title mb-1">{{ $language->formatted_name }}</h6>
                                                <p class="card-text text-muted mb-2">
                                                    <small>ID: {{ $language->language_id }}</small>
                                                </p>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('languages.show', $language) }}" 
                                                       class="btn btn-outline-info" 
                                                       title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('languages.edit', $language) }}" 
                                                       class="btn btn-outline-warning" 
                                                       title="Edit Language">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Back to Top Button -->
        <div class="text-center mb-4">
            <a href="#" class="btn btn-outline-secondary" onclick="window.scrollTo(0,0); return false;">
                <i class="fas fa-arrow-up me-1"></i>
                Back to Top
            </a>
        </div>
    @else
        <!-- Empty State -->
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-language fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Languages Available</h5>
                <p class="text-muted">Start by adding your first language to see the alphabetical view.</p>
                <a href="{{ route('languages.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>
                    Add First Language
                </a>
            </div>
        </div>
    @endif

    <!-- Summary Card -->
    @if($languageGroups->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    Summary Statistics
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <h4 class="text-primary">{{ $languageGroups->sum(function($group) { return $group->count(); }) }}</h4>
                        <small class="text-muted">Total Languages</small>
                    </div>
                    <div class="col-md-3">
                        <h4 class="text-success">{{ $languageGroups->count() }}</h4>
                        <small class="text-muted">Letter Groups</small>
                    </div>
                    <div class="col-md-3">
                        <h4 class="text-info">{{ $languageGroups->max(function($group) { return $group->count(); }) }}</h4>
                        <small class="text-muted">Largest Group</small>
                    </div>
                    <div class="col-md-3">
                        <h4 class="text-warning">{{ round($languageGroups->avg(function($group) { return $group->count(); }), 1) }}</h4>
                        <small class="text-muted">Average per Letter</small>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.avatar-circle {
    transition: all 0.3s ease;
}

.card:hover .avatar-circle {
    transform: scale(1.1);
}

#letter-a, #letter-b, #letter-c, #letter-d, #letter-e, #letter-f, #letter-g, #letter-h, #letter-i, #letter-j, #letter-k, #letter-l, #letter-m, #letter-n, #letter-o, #letter-p, #letter-q, #letter-r, #letter-s, #letter-t, #letter-u, #letter-v, #letter-w, #letter-x, #letter-y, #letter-z {
    scroll-margin-top: 100px;
}
</style>

<script>
// Smooth scrolling for navigation links
document.querySelectorAll('a[href^="#letter-"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});
</script>
@endsection