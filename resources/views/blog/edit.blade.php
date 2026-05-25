@extends('layouts.app')

@section('title', 'Edit Blog Article — Mali Setu')

@section('content')
<div class="container py-4">
    <!-- Back to Portal Link -->
    <div class="mb-4">
        <a href="{{ route('blogs.index') }}" class="text-primary text-decoration-none fw-bold small">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Blog Portal
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 24px; overflow: hidden; background: #fff;">
                <div class="card-header bg-primary text-white p-4 border-0 d-flex align-items-center gap-3">
                    <div class="icon-circle bg-white text-primary d-flex align-items-center justify-content-center" style="width:48px; height:48px; border-radius:50%;">
                        <i class="fa-solid fa-pen-nib fa-lg"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">Edit Blog Article</h4>
                        <p class="mb-0 text-white-50 small">Modify your published article and media attachments</p>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5">
                    @if($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 12px;">
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('blogs.update', $blog->id) }}" method="POST" enctype="multipart/form-data" id="blogEditForm">
                        @csrf

                        <!-- Article Title -->
                        <div class="mb-4">
                            <label for="title" class="form-label fw-semibold text-secondary">Article Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control form-control-lg border-2" placeholder="e.g. My Experience Visiting the Community Cultural Center" value="{{ old('title', $blog->title) }}" required style="border-radius: 12px;">
                            <div class="form-text small">Create an engaging title for your article. Keep it concise.</div>
                        </div>

                        <!-- Article Content Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold text-secondary">Article Content <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" rows="10" class="form-control border-2" placeholder="Start writing your article here..." required style="border-radius: 12px; resize: vertical;">{{ old('description', $blog->description) }}</textarea>
                            <div class="form-text small d-flex justify-content-between">
                                <span>Write in detail about your topic. Plain text with line breaks is supported.</span>
                                <span id="charCount" class="fw-semibold">0 characters</span>
                            </div>
                        </div>

                        <!-- Tags -->
                        <div class="mb-4">
                            <label for="tags" class="form-label fw-semibold text-secondary">Tags</label>
                            <input type="text" name="tags" id="tags" class="form-control border-2" placeholder="e.g. cultural, event, community, stories" value="{{ old('tags', $blog->tags ? implode(', ', $blog->tags) : '') }}" style="border-radius: 12px;">
                            <div class="form-text small">Enter comma-separated values (e.g. news, events, guides). Tags help users filter articles.</div>
                        </div>

                        <!-- Media File -->
                        <div class="mb-4">
                            <label for="media" class="form-label fw-semibold text-secondary font-weight-bold">Update Media (Image / Video)</label>
                            <div class="media-upload-dropzone border-2 border-dashed p-4 text-center bg-light rounded-4 position-relative" style="cursor: pointer; border-color: rgba(var(--primary-rgb), 0.3) !important;">
                                <input type="file" name="media" id="media" class="position-absolute top-0 start-0 opacity-0 w-100 h-100" style="cursor: pointer;" accept="image/*,video/*" onchange="previewMedia(this)">
                                
                                <!-- No current media or media removed -->
                                <div id="dropzoneContent" class="{{ $blog->media_path ? 'd-none' : '' }}">
                                    <i class="fa-solid fa-cloud-arrow-up fa-3x text-primary opacity-50 mb-3"></i>
                                    <h6 class="fw-bold mb-1">Drag & drop or click to replace file</h6>
                                    <p class="text-muted small mb-0">Supported files: JPG, PNG, GIF, MP4, MOV (Max size: 10MB)</p>
                                </div>
                                
                                <!-- Show current media OR new uploaded file preview -->
                                <div id="previewContainer" class="{{ !$blog->media_path ? 'd-none' : '' }} mt-2">
                                    <div class="position-relative d-inline-block">
                                        @if($blog->media_path)
                                            <img id="imagePreview" src="{{ $blog->media_type === 'image' ? asset('storage/' . $blog->media_path) : '' }}" class="img-thumbnail {{ $blog->media_type === 'image' ? '' : 'd-none' }}" style="max-height: 200px; border-radius: 12px;">
                                            <video id="videoPreview" src="{{ $blog->media_type === 'video' ? asset('storage/' . $blog->media_path) : '' }}" class="img-thumbnail {{ $blog->media_type === 'video' ? '' : 'd-none' }}" style="max-height: 200px; border-radius: 12px;" controls></video>
                                        @else
                                            <img id="imagePreview" src="" class="img-thumbnail d-none" style="max-height: 200px; border-radius: 12px;">
                                            <video id="videoPreview" src="" class="img-thumbnail d-none" style="max-height: 200px; border-radius: 12px;" controls></video>
                                        @endif
                                        <button type="button" class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 m-2 shadow" onclick="removeMedia(event)"><i class="fa-solid fa-xmark"></i></button>
                                    </div>
                                    @if($blog->media_path)
                                        <p class="text-muted small mt-2 mb-0" id="mediaHint">Currently displaying current attachment. Uploading a new file will replace it.</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-5">
                            <a href="{{ route('blogs.index') }}" class="btn btn-light px-4 py-2.5 rounded-pill me-md-2 fw-semibold">Cancel</a>
                            <button type="submit" class="btn btn-primary px-5 py-2.5 rounded-pill fw-semibold shadow d-flex align-items-center justify-content-center gap-2" id="submitBtn">
                                <span id="btnText"><i class="fa-solid fa-floppy-disk me-1"></i>Save Changes</span>
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Text area character count
const descArea = document.getElementById('description');
const charCountSpan = document.getElementById('charCount');

function updateCharCount() {
    charCountSpan.textContent = descArea.value.length + ' characters';
}

if (descArea) {
    descArea.addEventListener('input', updateCharCount);
    updateCharCount();
}

// Form loader spinner
document.getElementById('blogEditForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    const text = document.getElementById('btnText');
    const spinner = btn.querySelector('.spinner-border');
    
    btn.disabled = true;
    text.textContent = 'Saving Changes...';
    spinner.classList.remove('d-none');
});

// Drag and drop preview
function previewMedia(input) {
    const file = input.files[0];
    if (file) {
        const dropzone = document.getElementById('dropzoneContent');
        const previewContainer = document.getElementById('previewContainer');
        const imgPrev = document.getElementById('imagePreview');
        const vidPrev = document.getElementById('videoPreview');
        const mediaHint = document.getElementById('mediaHint');
        
        dropzone.classList.add('d-none');
        previewContainer.classList.remove('d-none');
        if (mediaHint) {
            mediaHint.textContent = 'New file selected. Save to update.';
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            if (file.type.startsWith('video/')) {
                imgPrev.classList.add('d-none');
                vidPrev.classList.remove('d-none');
                vidPrev.src = e.target.result;
            } else {
                vidPrev.classList.add('d-none');
                imgPrev.classList.remove('d-none');
                imgPrev.src = e.target.result;
            }
        }
        reader.readAsDataURL(file);
    }
}

function removeMedia(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const input = document.getElementById('media');
    const dropzone = document.getElementById('dropzoneContent');
    const previewContainer = document.getElementById('previewContainer');
    const imgPrev = document.getElementById('imagePreview');
    const vidPrev = document.getElementById('videoPreview');
    const mediaHint = document.getElementById('mediaHint');
    
    input.value = '';
    imgPrev.src = '';
    vidPrev.src = '';
    
    imgPrev.classList.add('d-none');
    vidPrev.classList.add('d-none');
    previewContainer.classList.add('d-none');
    dropzone.classList.remove('d-none');
    if (mediaHint) {
        mediaHint.classList.add('d-none');
    }
}
</script>
@endsection
