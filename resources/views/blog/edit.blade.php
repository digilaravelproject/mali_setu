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

                        <!-- Title and Type in one row (50-50) -->
                        <div class="row g-3 mb-3">
                            <!-- Blog Title -->
                            <div class="col-md-6">
                                <label for="title" class="form-label fw-bold text-dark">Blog Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" class="form-control border-2 @error('title') is-invalid @enderror" placeholder="Enter Your Blog title" value="{{ old('title', $blog->title) }}" style="border-radius: 12px; border-color: #e5cbd6;">
                                @error('title')
                                    <div class="text-danger small mt-1 fw-semibold" style="color: #c92f54 !important;">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Blog Type -->
                            <div class="col-md-6">
                                <label for="blog_type" class="form-label fw-bold text-dark">Blog Type <span class="text-danger">*</span></label>
                                <select name="blog_type" id="blog_type" class="form-select border-2 @error('blog_type') is-invalid @enderror" style="border-radius: 12px; border-color: #e5cbd6; appearance: none; background-image: url('data:image/svg+xml;utf8,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;24&quot; height=&quot;24&quot; viewBox=&quot;0 0 24 24&quot; fill=&quot;none&quot; stroke=&quot;%23ff4757&quot; stroke-width=&quot;2&quot; stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot;><polyline points=&quot;6 9 12 15 18 9&quot;></polyline></svg>'); background-repeat: no-repeat; background-position: right 15px center; background-size: 16px;">
                                    <option value="" disabled {{ old('blog_type', $blog->blog_type) ? '' : 'selected' }}>Select Blog Category</option>
                                    @php
                                        $categories = [
                                            'Technology', 'Business', 'Finance', 'Marketing', 'Startups', 
                                            'Artificial Intelligence (AI)', 'Software Development', 'Web Development', 
                                            'Mobile App Development', 'Cybersecurity', 'Cloud Computing', 'Data Science', 
                                            'Health & Fitness', 'Lifestyle', 'Travel', 'Food & Recipes', 
                                            'Fashion & Beauty', 'Education', 'Career & Jobs', 'Personal Development', 
                                            'Entertainment', 'Movies & TV', 'Music', 'Sports', 'Gaming', 
                                            'News & Current Affairs', 'Politics', 'Science', 'Environment', 
                                            'Parenting', 'Relationships', 'Real Estate', 'Automotive', 
                                            'Photography', 'Home Improvement', 'E-commerce', 'Product Reviews', 
                                            'Tutorials & Guides', 'Case Studies', 'Interviews'
                                        ];
                                    @endphp
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}" {{ old('blog_type', $blog->blog_type) === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                                @error('blog_type')
                                    <div class="text-danger small mt-1 fw-semibold" style="color: #c92f54 !important;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Blog Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold text-dark">Blog Description <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" rows="5" class="form-control border-2 @error('description') is-invalid @enderror" placeholder="Enter Your Blog description" style="border-radius: 12px; resize: vertical; border-color: #e5cbd6;">{{ old('description', $blog->description) }}</textarea>
                            <div class="form-text small d-flex justify-content-between mt-1 text-muted">
                                <span>Write in detail about your topic. Plain text with line breaks is supported.</span>
                                <span id="charCount" class="fw-semibold">0 characters</span>
                            </div>
                            @error('description')
                                <div class="text-danger small mt-1 fw-semibold" style="color: #c92f54 !important;">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tags and Media in one row (50-50) -->
                        <div class="row g-3 mb-4">
                            <!-- Tags -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Tags <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" id="tagInput" class="form-control border-2 @error('tags') is-invalid @enderror" placeholder="Add a tag" style="border-top-left-radius: 12px; border-bottom-left-radius: 12px; border-right: none; border-color: #e5cbd6;">
                                    <button class="btn btn-primary d-flex align-items-center justify-content-center" type="button" id="addTagBtn" style="border-top-right-radius: 12px; border-bottom-right-radius: 12px; background-color: #ff4757; color: white; border-color: #ff4757; border-left: none; width: 48px; font-size: 1.2rem;">
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
                            <div class="col-md-6">
                                <label for="media" class="form-label fw-bold text-dark">Media (Image/Video)</label>
                                <div class="media-upload-dropzone border-2 border-dashed p-3 text-center rounded-4 position-relative" style="cursor: pointer; border-color: #ff4757 !important; background-color: #fff9fa; transition: all 0.3s ease;">
                                    <input type="file" name="media[]" id="media" class="position-absolute top-0 start-0 opacity-0 w-100 h-100" style="cursor: pointer;" accept="image/*,video/*" multiple onchange="previewMedia(this)">
                                    
                                    <!-- No current media or media removed -->
                                    <div id="dropzoneContent" class="{{ $blog->media_path ? 'd-none' : '' }}">
                                        <h6 class="fw-bold mb-1 text-primary" style="font-size:0.95rem;">Tap to upload media</h6>
                                        <p class="text-muted small mb-0" style="font-size:0.75rem;">Upload one or more photos/videos (Max 10MB each)</p>
                                    </div>
                                    
                                    <!-- Show current media OR new uploaded file preview -->
                                    <div id="previewContainer" class="{{ !$blog->media_path ? 'd-none' : '' }} mt-1 w-100">
                                        @if($blog->media_path)
                                            @php
                                                $paths = is_array($blog->media_path) ? $blog->media_path : json_decode($blog->media_path, true);
                                            @endphp
                                            <div class="row g-2 justify-content-center mb-2" id="existingMediaGrid">
                                                @if(is_array($paths))
                                                    @foreach($paths as $path)
                                                        <div class="col-4">
                                                            @php
                                                                $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                                                                $isVid = in_array($ext, ['mp4', 'mov', 'avi', 'webm', 'ogg']);
                                                            @endphp
                                                            @if($isVid)
                                                                <video src="{{ asset('storage/' . $path) }}" class="img-thumbnail w-100" style="height: 80px; object-fit: cover; border-radius: 8px;" muted controls></video>
                                                            @else
                                                                <img src="{{ asset('storage/' . $path) }}" class="img-thumbnail w-100" style="height: 80px; object-fit: cover; border-radius: 8px;">
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        @endif
                                        <div id="newMediaGrid" class="row g-2 justify-content-center mb-2 d-none"></div>
                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill d-none" id="removeAllBtn" onclick="removeMedia(event)"><i class="fa-solid fa-trash me-1"></i> Remove All Media</button>
                                        @if($blog->media_path)
                                            <p class="text-muted small mt-2 mb-0 text-center" id="mediaHint" style="font-size: 0.75rem;">Uploading new files will replace all current attachments.</p>
                                        @endif
                                    </div>
                                </div>
                                @error('media')
                                    <div class="text-danger small mt-1 fw-semibold" style="color: #c92f54 !important;">{{ $message }}</div>
                                @enderror
                                @error('media.*')
                                    <div class="text-danger small mt-1 fw-semibold" style="color: #c92f54 !important;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="{{ route('blogs.index') }}" class="btn btn-light px-4 py-2 rounded-pill me-md-2 fw-semibold">Cancel</a>
                            <button type="submit" class="btn btn-primary px-5 py-2 rounded-pill fw-semibold shadow d-flex align-items-center justify-content-center gap-2" id="submitBtn">
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
});// Drag and drop preview for multiple files
function previewMedia(input) {
    const files = input.files;
    const dropzone = document.getElementById('dropzoneContent');
    const previewContainer = document.getElementById('previewContainer');
    const existingMediaGrid = document.getElementById('existingMediaGrid');
    const newMediaGrid = document.getElementById('newMediaGrid');
    const removeAllBtn = document.getElementById('removeAllBtn');
    const mediaHint = document.getElementById('mediaHint');
    
    // Hide existing media grid if it exists
    if (existingMediaGrid) {
        existingMediaGrid.classList.add('d-none');
    }
    
    // Clear new media previews
    newMediaGrid.innerHTML = '';
    
    if (files.length > 0) {
        dropzone.classList.add('d-none');
        previewContainer.classList.remove('d-none');
        newMediaGrid.classList.remove('d-none');
        removeAllBtn.classList.remove('d-none');
        
        if (mediaHint) {
            mediaHint.textContent = 'New files selected. Save to update.';
            mediaHint.classList.remove('d-none');
        }
        
        Array.from(files).forEach((file, index) => {
            const col = document.createElement('div');
            col.className = 'col-4 position-relative';
            
            const reader = new FileReader();
            reader.onload = function(e) {
                if (file.type.startsWith('video/')) {
                    const video = document.createElement('video');
                    video.src = e.target.result;
                    video.className = 'img-thumbnail w-100';
                    video.style.height = '85px';
                    video.style.objectFit = 'cover';
                    video.style.borderRadius = '12px';
                    video.muted = true;
                    video.controls = true;
                    col.appendChild(video);
                } else {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'img-thumbnail w-100';
                    img.style.height = '85px';
                    img.style.objectFit = 'cover';
                    img.style.borderRadius = '12px';
                    col.appendChild(img);
                }
            };
            reader.readAsDataURL(file);
            newMediaGrid.appendChild(col);
        });
    }
}

function removeMedia(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    const input = document.getElementById('media');
    const dropzone = document.getElementById('dropzoneContent');
    const previewContainer = document.getElementById('previewContainer');
    const existingMediaGrid = document.getElementById('existingMediaGrid');
    const newMediaGrid = document.getElementById('newMediaGrid');
    const removeAllBtn = document.getElementById('removeAllBtn');
    const mediaHint = document.getElementById('mediaHint');
    
    input.value = '';
    newMediaGrid.innerHTML = '';
    newMediaGrid.classList.add('d-none');
    removeAllBtn.classList.add('d-none');
    
    if (existingMediaGrid) {
        existingMediaGrid.classList.remove('d-none');
        if (mediaHint) {
            mediaHint.textContent = 'Uploading new files will replace all current attachments.';
        }
    } else {
        previewContainer.classList.add('d-none');
        dropzone.classList.remove('d-none');
        if (mediaHint) {
            mediaHint.classList.add('d-none');
        }
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
