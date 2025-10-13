@extends('layouts.app')

@section('title', "Edit Language: {$language->name}")

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Edit Language: <strong>{{ $language->name }}</strong>
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Current Language Info -->
                    <div class="alert alert-info">
                        <div class="row align-items-center">
                            <div class="col-md-2 text-center">
                                <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto" 
                                     style="width: 50px; height: 50px; border-radius: 50%; font-weight: bold;">
                                    {{ strtoupper(substr($language->name, 0, 2)) }}
                                </div>
                            </div>
                            <div class="col-md-10">
                                <h6 class="mb-1">Current Language Information</h6>
                                <p class="mb-1"><strong>Name:</strong> {{ $language->name }}</p>
                                <p class="mb-0"><strong>ID:</strong> {{ $language->language_id }} | 
                                   <strong>Last Updated:</strong> {{ $language->last_update?->format('M j, Y') ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('languages.update', $language) }}">
                        @csrf
                        @method('PUT')

                        <!-- Name Field -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Language Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $language->name) }}" 
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

                        <!-- Change Preview -->
                        <div class="card bg-light mb-4" id="changePreview" style="display: none;">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-eye me-2"></i>
                                    Change Preview
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Current:</strong></p>
                                        <span class="badge bg-secondary">{{ $language->name }}</span>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>New:</strong></p>
                                        <span class="badge bg-primary" id="newNamePreview">{{ $language->name }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('languages.show', $language) }}" class="btn btn-outline-info me-2">
                                    <i class="fas fa-eye me-1"></i>
                                    View Details
                                </a>
                                <a href="{{ route('languages.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>
                                    Back to Languages
                                </a>
                            </div>
                            <div>
                                <button type="reset" class="btn btn-outline-warning me-2" onclick="resetForm()">
                                    <i class="fas fa-undo me-1"></i>
                                    Reset
                                </button>
                                <button type="submit" class="btn btn-primary" id="updateButton">
                                    <i class="fas fa-save me-1"></i>
                                    Update Language
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
                        Editing Guidelines
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-check-circle text-success me-1"></i> Best Practices:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Use proper capitalization</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Keep names standard and recognizable</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Verify spelling before saving</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Check for existing duplicates</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-exclamation-triangle text-warning me-1"></i> Important Notes:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Language name must be unique</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Maximum 20 characters allowed</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Changes affect all related films</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Last update timestamp will be refreshed</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const originalName = "{{ $language->name }}";
const nameInput = document.getElementById('name');
const changePreview = document.getElementById('changePreview');
const newNamePreview = document.getElementById('newNamePreview');
const updateButton = document.getElementById('updateButton');

function setLanguage(languageName) {
    nameInput.value = languageName;
    nameInput.focus();
    checkForChanges();
}

function resetForm() {
    nameInput.value = originalName;
    changePreview.style.display = 'none';
    updateButton.innerHTML = '<i class="fas fa-save me-1"></i> Update Language';
}

function checkForChanges() {
    const currentValue = nameInput.value.trim();
    
    if (currentValue !== originalName && currentValue !== '') {
        changePreview.style.display = 'block';
        newNamePreview.textContent = currentValue;
        updateButton.innerHTML = '<i class="fas fa-save me-1"></i> Save Changes';
    } else {
        changePreview.style.display = 'none';
        updateButton.innerHTML = '<i class="fas fa-save me-1"></i> Update Language';
    }
}

// Auto-capitalize first letter
nameInput.addEventListener('input', function(e) {
    let value = e.target.value;
    if (value.length > 0) {
        e.target.value = value.charAt(0).toUpperCase() + value.slice(1);
    }
    checkForChanges();
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const name = nameInput.value.trim();
    
    if (!name) {
        e.preventDefault();
        alert('Please enter a language name.');
        nameInput.focus();
        return false;
    }
    
    if (name.length > 20) {
        e.preventDefault();
        alert('Language name cannot exceed 20 characters.');
        nameInput.focus();
        return false;
    }
    
    if (name === originalName) {
        e.preventDefault();
        alert('No changes were made to the language name.');
        nameInput.focus();
        return false;
    }
});

// Check for changes on page load
document.addEventListener('DOMContentLoaded', function() {
    checkForChanges();
});
</script>
@endsection