@extends('layouts.app')

@section('title', "Edit Category: {$category->name}")

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Edit Category: <strong>{{ $category->name }}</strong>
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Current Category Info -->
                    <div class="alert alert-info">
                        <div class="row align-items-center">
                            <div class="col-md-2 text-center">
                                <div class="category-icon bg-primary text-white d-flex align-items-center justify-content-center mx-auto" 
                                     style="width: 50px; height: 50px; border-radius: 8px; font-weight: bold;">
                                    <i class="fas fa-tag"></i>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <h6 class="mb-1">Current Category Information</h6>
                                <p class="mb-1"><strong>Name:</strong> {{ $category->name }}</p>
                                <p class="mb-1"><strong>Slug:</strong> {{ $category->slug }}</p>
                                <p class="mb-0"><strong>ID:</strong> {{ $category->category_id }} | 
                                   <strong>Last Updated:</strong> {{ $category->last_update?->format('M j, Y') ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('categories.update', $category) }}">
                        @csrf
                        @method('PUT')

                        <!-- Name Field -->
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Category Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $category->name) }}" 
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
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <div class="category-icon bg-secondary text-white d-flex align-items-center justify-content-center" 
                                                     style="width: 30px; height: 30px; border-radius: 6px; font-size: 12px;">
                                                    <i class="fas fa-tag"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <span class="badge bg-secondary">{{ $category->name }}</span>
                                                <br>
                                                <small class="text-muted">{{ $category->slug }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>New:</strong></p>
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <div class="category-icon bg-primary text-white d-flex align-items-center justify-content-center" 
                                                     style="width: 30px; height: 30px; border-radius: 6px; font-size: 12px;">
                                                    <i class="fas fa-tag"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <span class="badge bg-primary" id="newNamePreview">{{ $category->name }}</span>
                                                <br>
                                                <small class="text-muted" id="newSlugPreview">{{ $category->slug }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('categories.show', $category) }}" class="btn btn-outline-info me-2">
                                    <i class="fas fa-eye me-1"></i>
                                    View Details
                                </a>
                                <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>
                                    Back to Categories
                                </a>
                            </div>
                            <div>
                                <button type="reset" class="btn btn-outline-warning me-2" onclick="resetForm()">
                                    <i class="fas fa-undo me-1"></i>
                                    Reset
                                </button>
                                <button type="submit" class="btn btn-primary" id="updateButton">
                                    <i class="fas fa-save me-1"></i>
                                    Update Category
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Warning Card (if category has films) -->
            {{-- 
            @if($category->films()->exists())
                <div class="card mt-4 border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Category In Use Warning
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">
                            <strong>This category is currently assigned to {{ $category->films()->count() }} film(s).</strong>
                        </p>
                        <p class="mb-0 text-muted">
                            Changing the category name will update how it appears for all associated films.
                            The films themselves will not be affected, only the category display name.
                        </p>
                    </div>
                </div>
            @endif
            --}}

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
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Use standard genre names</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Keep names concise and clear</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Check spelling and capitalization</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Ensure uniqueness</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-exclamation-triangle text-warning me-1"></i> Important Notes:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Category name must be unique</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Maximum 25 characters allowed</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> Changes affect all related films</li>
                                <li><i class="fas fa-chevron-right text-muted me-2"></i> URL slug will be updated automatically</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const originalName = "{{ $category->name }}";
const nameInput = document.getElementById('name');
const changePreview = document.getElementById('changePreview');
const newNamePreview = document.getElementById('newNamePreview');
const newSlugPreview = document.getElementById('newSlugPreview');
const updateButton = document.getElementById('updateButton');

function setCategory(categoryName) {
    nameInput.value = categoryName;
    nameInput.focus();
    checkForChanges();
}

function resetForm() {
    nameInput.value = originalName;
    changePreview.style.display = 'none';
    updateButton.innerHTML = '<i class="fas fa-save me-1"></i> Update Category';
}

function generateSlug(text) {
    return text.toLowerCase()
               .replace(/[^a-z0-9]+/g, '-')
               .replace(/^-+|-+$/g, '');
}

function checkForChanges() {
    const currentValue = nameInput.value.trim();
    
    if (currentValue !== originalName && currentValue !== '') {
        changePreview.style.display = 'block';
        newNamePreview.textContent = currentValue;
        newSlugPreview.textContent = generateSlug(currentValue);
        updateButton.innerHTML = '<i class="fas fa-save me-1"></i> Save Changes';
    } else {
        changePreview.style.display = 'none';
        updateButton.innerHTML = '<i class="fas fa-save me-1"></i> Update Category';
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
        alert('Please enter a category name.');
        nameInput.focus();
        return false;
    }
    
    if (name.length > 25) {
        e.preventDefault();
        alert('Category name cannot exceed 25 characters.');
        nameInput.focus();
        return false;
    }
    
    if (name.length < 2) {
        e.preventDefault();
        alert('Category name must be at least 2 characters long.');
        nameInput.focus();
        return false;
    }
    
    if (name === originalName) {
        e.preventDefault();
        alert('No changes were made to the category name.');
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