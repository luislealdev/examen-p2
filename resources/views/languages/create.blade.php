@extends('layouts.app')

@section('title', 'Add New Language')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i>
                        Add New Language
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('languages.store') }}">
                        @csrf

                        <!-- Name Field -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Language Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   maxlength="20"
                                   placeholder="Enter language name (e.g., English, Spanish, French...)"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Maximum 20 characters. Language name must be unique.
                            </div>
                        </div>

                        <!-- Common Languages Examples -->
                        <div class="mb-4">
                            <label class="form-label text-muted">Common Language Examples:</label>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setLanguage('English')">English</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setLanguage('Spanish')">Spanish</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setLanguage('French')">French</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setLanguage('German')">German</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setLanguage('Italian')">Italian</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setLanguage('Japanese')">Japanese</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setLanguage('Mandarin')">Mandarin</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setLanguage('Portuguese')">Portuguese</button>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('languages.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>
                                Back to Languages
                            </a>
                            <div>
                                <button type="reset" class="btn btn-outline-warning me-2">
                                    <i class="fas fa-undo me-1"></i>
                                    Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>
                                    Save Language
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Help Card -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-question-circle me-2"></i>
                        Help & Guidelines
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-check-circle text-success me-1"></i> Do:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Use proper language names</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Check for spelling accuracy</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Use standard language names</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Keep names concise</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-times-circle text-danger me-1"></i> Don't:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Use abbreviations (unless standard)</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Include dialect specifications</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Use special characters</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Exceed 20 characters</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function setLanguage(languageName) {
    document.getElementById('name').value = languageName;
    document.getElementById('name').focus();
}

// Auto-capitalize first letter
document.getElementById('name').addEventListener('input', function(e) {
    let value = e.target.value;
    if (value.length > 0) {
        e.target.value = value.charAt(0).toUpperCase() + value.slice(1);
    }
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value.trim();
    
    if (!name) {
        e.preventDefault();
        alert('Please enter a language name.');
        document.getElementById('name').focus();
        return false;
    }
    
    if (name.length > 20) {
        e.preventDefault();
        alert('Language name cannot exceed 20 characters.');
        document.getElementById('name').focus();
        return false;
    }
});
</script>
@endsection