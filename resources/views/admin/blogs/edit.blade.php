@extends('admin.layouts.app')

@section('title', 'Edit Blog')

@section('content')
<div class="content-area">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-4">
                <i class="fas fa-edit me-2"></i>
                Edit Blog
            </h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Blogs
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.blogs.update', $blog->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $blog->title) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Blog Type</label>
                    <select name="blog_type" class="form-select" required>
                        <option value="" disabled>Select Blog Category</option>
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
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="5">{{ old('description', $blog->description) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tags (comma separated)</label>
                    <input type="text" name="tags" class="form-control" value="{{ old('tags', is_array($blog->tags) ? implode(', ', $blog->tags) : $blog->tags) }}">
                </div>

                @if($blog->media_path)
                    @php
                        $mediaList = is_array($blog->media_path) ? $blog->media_path : json_decode($blog->media_path, true);
                    @endphp
                    @if(is_array($mediaList) && count($mediaList) > 0)
                        <div class="mb-3">
                            <label class="form-label">Existing Media</label>
                            <div class="row g-2">
                                @foreach($mediaList as $mPath)
                                    @php
                                        $ext = strtolower(pathinfo($mPath, PATHINFO_EXTENSION));
                                        $isVid = in_array($ext, ['mp4', 'mov', 'avi', 'webm', 'ogg']);
                                    @endphp
                                    <div class="col-md-3">
                                        @if($isVid)
                                            <video src="{{ asset('storage/' . $mPath) }}" class="img-fluid rounded border" style="max-height: 120px; width: 100%; object-fit: cover;" controls></video>
                                        @else
                                            <img src="{{ asset('storage/' . $mPath) }}" alt="Blog Media" class="img-fluid rounded border" style="max-height: 120px; width: 100%; object-fit: cover;">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif

                <div class="mb-3">
                    <label class="form-label">Replace Media (optional - select one or more)</label>
                    <input type="file" name="media[]" class="form-control" accept="image/*,video/*" multiple>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $blog->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        Active
                    </label>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Update Blog
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
