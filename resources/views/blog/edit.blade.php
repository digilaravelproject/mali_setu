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
        <div class="col-md-12 col-lg-12">
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
                    <form action="{{ route('blogs.update', $blog->id) }}" method="POST" enctype="multipart/form-data" id="blogEditForm">
                        @csrf

                        <!-- Blog Title -->
                        <div class="mb-4">
                            <label for="title" class="form-label fw-bold text-dark fs-5">Blog Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control form-control-lg border-2 @error('title') is-invalid @enderror" placeholder="Enter Your Blog title" value="{{ old('title', $blog->title) }}" style="border-radius: 12px; height: 52px; border-color: #e5cbd6;">
                            @error('title')
                                <div class="text-danger small mt-1 fw-semibold" style="color: #c92f54 !important;">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Blog Type -->
                        <div class="mb-4">
                            <label for="blog_type" class="form-label fw-bold text-dark fs-5">Blog Type <span class="text-danger">*</span></label>
                            <select name="blog_type" id="blog_type" class="form-select form-select-lg border-2 @error('blog_type') is-invalid @enderror" style="border-radius: 12px; height: 52px; border-color: #e5cbd6; appearance: none; background-image: url('data:image/svg+xml;utf8,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;24&quot; height=&quot;24&quot; viewBox=&quot;0 0 24 24&quot; fill=&quot;none&quot; stroke=&quot;%23ff4757&quot; stroke-width=&quot;2&quot; stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot;><polyline points=&quot;6 9 12 15 18 9&quot;></polyline></svg>'); background-repeat: no-repeat; background-position: right 15px center; background-size: 16px;">
                                <option value="" disabled {{ old('blog_type', $blog->blog_type) ? '' : 'selected' }}>Enter Your Blog type</option>
                                <option value="News" {{ old('blog_type', $blog->blog_type) === 'News' ? 'selected' : '' }}>News</option>
                                <option value="Success Story" {{ old('blog_type', $blog->blog_type) === 'Success Story' ? 'selected' : '' }}>Success Story</option>
                                <option value="Event" {{ old('blog_type', $blog->blog_type) === 'Event' ? 'selected' : '' }}>Event</option>
                                <option value="Opinion" {{ old('blog_type', $blog->blog_type) === 'Opinion' ? 'selected' : '' }}>Opinion</option>
                                <option value="Other" {{ old('blog_type', $blog->blog_type) === 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('blog_type')
                                <div class="text-danger small mt-1 fw-semibold" style="color: #c92f54 !important;">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Blog Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold text-dark fs-5">Blog Description <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" rows="8" class="form-control border-2 @error('description') is-invalid @enderror" placeholder="Enter Your Blog description" style="border-radius: 12px; resize: vertical; border-color: #e5cbd6;">{{ old('description', $blog->description) }}</textarea>
                            <div class="form-text small d-flex justify-content-between mt-1 text-muted">
                                <span>Write in detail about your topic. Plain text with line breaks is supported.</span>
                                <span id="charCount" class="fw-semibold">0 characters</span>
                            </div>
                            @error('description')
                                <div class="text-danger small mt-1 fw-semibold" style="color: #c92f54 !important;">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tags -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark fs-5">Tags <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" id="tagInput" class="form-control form-control-lg border-2 @error('tags') is-invalid @enderror" placeholder="Add a tag" style="border-top-left-radius: 12px; border-bottom-left-radius: 12px; border-right: none; height: 52px; border-color: #e5cbd6;">
                                <button class="btn btn-primary d-flex align-items-center justify-content-center" type="button" id="addTagBtn" style="border-top-right-radius: 12px; border-bottom-right-radius: 12px; background-color: #ff4757; color: white; border-color: #ff4757; border-left: none; width: 60px; height: 52px; font-size: 1.5rem;">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                            <input type="hidden" name="tags" id="tags" value="{{ old('tags', $blog->tags ? implode(', ', $blog->tags) : '') }}">
                            <div id="tagsList" class="mt-2 d-flex flex-wrap gap-2"></div>
                            @error('tags')
                                <div class="text-danger small mt-1 fw-semibold" style="color: #c92f54 !important;">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Media (Image/Video) -->
                        <div class="mb-4">
                            <label for="media" class="form-label fw-bold text-dark fs-5">Media (Image/Video)</label>
                            <div class="media-upload-dropzone border-2 border-dashed p-4 text-center rounded-4 position-relative" style="cursor: pointer; border-color: #ff4757 !important; background-color: #fff9fa; transition: all 0.3s ease;">
                                <input type="file" name="media" id="media" class="position-absolute top-0 start-0 opacity-0 w-100 h-100" style="cursor: pointer;" accept="image/*,video/*" onchange="previewMedia(this)">
                                
                                <!-- No current media or media removed -->
                                <div id="dropzoneContent" class="{{ $blog->media_path ? 'd-none' : '' }}">
                                    <div class="mb-3 text-center">
                                        <div class="d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px; background-color: rgba(255, 71, 87, 0.08); border-radius: 16px; color: #ff4757;">
                                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-folder-plus"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path><line x1="12" y1="11" x2="12" y2="17"></line><line x1="9" y1="14" x2="15" y2="14"></line></svg>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold mb-1" style="color: #ff4757;">Tap to upload media</h6>
                                    <p class="text-muted small mb-0">Image (Max 2MB) | Video (Max 10MB)</p>
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

// Dynamic Tags System
document.addEventListener("DOMContentLoaded", function() {
    const tagInput = document.getElementById('tagInput');
    const addTagBtn = document.getElementById('addTagBtn');
    const tagsHiddenInput = document.getElementById('tags');
    const tagsListDiv = document.getElementById('tagsList');

    let tagsArr = [];

    // Load old tags if any
    if (tagsHiddenInput && tagsHiddenInput.value) {
        tagsArr = tagsHiddenInput.value.split(',').map(t => t.trim()).filter(t => t.length > 0);
        renderTags();
    }

    function renderTags() {
        tagsListDiv.innerHTML = '';
        tagsArr.forEach((tag, idx) => {
            const chip = document.createElement('span');
            chip.className = 'badge d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill';
            chip.style.backgroundColor = 'rgba(255,71,87,0.08)';
            chip.style.color = '#ff4757';
            chip.style.border = '1px solid rgba(255,71,87,0.2)';
            chip.style.fontWeight = '600';
            chip.style.fontSize = '0.9rem';
            
            chip.innerHTML = `
                ${tag}
                <i class="fa-solid fa-xmark text-danger cursor-pointer" style="cursor: pointer;" onclick="removeTag(${idx})"></i>
            `;
            tagsListDiv.appendChild(chip);
        });
        tagsHiddenInput.value = tagsArr.join(',');
    }

    window.removeTag = function(idx) {
        tagsArr.splice(idx, 1);
        renderTags();
    };

    function addTag() {
        const val = tagInput.value.trim();
        if (val) {
            const parts = val.split(',').map(t => t.trim()).filter(t => t.length > 0 && !tagsArr.includes(t));
            tagsArr.push(...parts);
            tagInput.value = '';
            renderTags();
        }
    }

    if (addTagBtn) {
        addTagBtn.addEventListener('click', function(e) {
            e.preventDefault();
            addTag();
        });
    }

    if (tagInput) {
        tagInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addTag();
            }
        });
    }
});
</script>
@endsection
