@extends('layouts.app')

@section('title', 'Add New Category')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i>
                        Add New Category
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('categories.store') }}">
                        @csrf

                        <!-- Name Field -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Category Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   maxlength="25"
                                   placeholder="Enter category name (e.g., Action, Comedy, Drama...)"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Maximum 25 characters. Category name must be unique.
                            </div>
                        </div>

                        <!-- Common Categories Examples -->
                        <div class="mb-4">
                            <label class="form-label text-muted">Popular Movie Categories:</label>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="setCategory('Action')">Action</button>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="setCategory('Adventure')">Adventure</button>
                                <button type="button" class="btn btn-outline-warning btn-sm" onclick="setCategory('Comedy')">Comedy</button>
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="setCategory('Drama')">Drama</button>
                                <button type="button" class="btn btn-outline-info btn-sm" onclick="setCategory('Horror')">Horror</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setCategory('Thriller')">Thriller</button>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-dark btn-sm" onclick="setCategory('Romance')">Romance</button>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="setCategory('Sci-Fi')">Sci-Fi</button>
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="setCategory('Fantasy')">Fantasy</button>
                                <button type="button" class="btn btn-outline-warning btn-sm" onclick="setCategory('Animation')">Animation</button>
                                <button type="button" class="btn btn-outline-info btn-sm" onclick="setCategory('Documentary')">Documentary</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setCategory('Family')">Family</button>
                            </div>
                        </div>

                        <!-- Category Preview -->
                        <div class="card bg-light mb-4" id="categoryPreview" style="display: none;">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-eye me-2"></i>
                                    Category Preview
                                </h6>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="category-icon bg-primary text-white d-flex align-items-center justify-content-center" 
                                             style="width: 50px; height: 50px; border-radius: 8px; font-weight: bold;">
                                            <i class="fas fa-tag"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <strong id="previewName">Category Name</strong>
                                        <br>
                                        <small class="text-muted" id="previewSlug">category-slug</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>
                                Back to Categories
                            </a>
                            <div>
                                <button type="reset" class="btn btn-outline-warning me-2" onclick="resetForm()">
                                    <i class="fas fa-undo me-1"></i>
                                    Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>
                                    Save Category
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
                        Category Guidelines
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-check-circle text-success me-1"></i> Good Examples:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Action</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Comedy</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Sci-Fi</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Documentary</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Children & Family</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-times-circle text-danger me-1"></i> Avoid:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Vague names (e.g., "Good Movies")</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Too specific (e.g., "90s Action")</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Special characters</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Very long names</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Duplicate categories</li>
                            </ul>
                        </div>
                    </div>
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Tip:</strong> Think about how customers will browse movies. Use standard genre names that are widely recognized.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function setCategory(categoryName) {
    document.getElementById('name').value = categoryName;
    document.getElementById('name').focus();
    updatePreview();
}

function resetForm() {
    document.getElementById('name').value = '';
    document.getElementById('categoryPreview').style.display = 'none';
}

function updatePreview() {
    const nameInput = document.getElementById('name');
    const preview = document.getElementById('categoryPreview');
    const previewName = document.getElementById('previewName');
    const previewSlug = document.getElementById('previewSlug');
    
    if (nameInput.value.trim()) {
        preview.style.display = 'block';
        previewName.textContent = nameInput.value.trim();
        previewSlug.textContent = nameInput.value.trim().toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
    } else {
        preview.style.display = 'none';
    }
}

// Auto-capitalize first letter and update preview
document.getElementById('name').addEventListener('input', function(e) {
    let value = e.target.value;
    if (value.length > 0) {
        e.target.value = value.charAt(0).toUpperCase() + value.slice(1);
    }
    updatePreview();
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value.trim();
    
    if (!name) {
        e.preventDefault();
        alert('Please enter a category name.');
        document.getElementById('name').focus();
        return false;
    }
    
    if (name.length > 25) {
        e.preventDefault();
        alert('Category name cannot exceed 25 characters.');
        document.getElementById('name').focus();
        return false;
    }
    
    if (name.length < 2) {
        e.preventDefault();
        alert('Category name must be at least 2 characters long.');
        document.getElementById('name').focus();
        return false;
    }
});
</script>
@endsection