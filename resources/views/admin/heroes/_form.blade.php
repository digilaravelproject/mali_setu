@csrf
<div class="mb-3">
    <label class="form-label">Title <span class="text-danger">*</span></label>
    <input type="text" name="title" class="form-control" value="{{ old('title', $hero->title ?? '') }}" required maxlength="255">
    @error('title') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">Banner Link</label>
    <input type="text" name="url" class="form-control" list="banner-link-options"
           value="{{ old('url', $hero->url ?? '') }}" maxlength="512"
           placeholder="Select an internal link or enter https://example.com">
    <datalist id="banner-link-options">
        <option value="/dashboard">Dashboard</option>
        <option value="/webinars">Webinars</option>
        <option value="/business">Business</option>
        <option value="/matrimony">Matrimony</option>
        <option value="/blogs">Blog Portal</option>
        <option value="/jobs">Jobs</option>
    </datalist>
    <div class="form-text text-muted mt-1">Choose a suggested app link or type any external URL. Leave blank for a non-clickable banner.</div>
    @error('url') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">Mobile Hero Image <span class="text-danger">*</span></label>
    <input type="file" name="mobile_image" class="form-control" accept="image/*" {{ isset($hero) ? '' : 'required' }}>
    <div class="form-text text-muted mt-1">Required dimensions: 300x168 px. Max size: 3 MB. Supported formats: JPG, JPEG, PNG, WEBP.</div>
    @error('mobile_image') <div class="text-danger small mt-1">{{ $message }}</div> @enderror

    @if(!empty($hero?->image_path))
        <div class="mt-2">
            <img src="{{ asset('storage/'.$hero->image_path) }}" alt="Current Mobile Image" style="max-height:120px">
        </div>
    @endif
</div>

<div class="mb-3">
    <label class="form-label">Desktop Hero Image <span class="text-danger">*</span></label>
    <input type="file" name="web_image" class="form-control" accept="image/*" {{ isset($hero) ? '' : 'required' }}>
    <div class="form-text text-muted mt-1">Required dimensions: 617x324 px. Max size: 5 MB. Supported formats: JPG, JPEG, PNG, WEBP.</div>
    @error('web_image') <div class="text-danger small mt-1">{{ $message }}</div> @enderror

    @if(!empty($hero?->web_image_path))
        <div class="mt-2">
            <img src="{{ asset('storage/'.$hero->web_image_path) }}" alt="Current Web Image" style="max-height:120px">
        </div>
    @endif
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary">{{ $submit ?? 'Save' }}</button>
    <a href="{{ route('heroes.index') }}" class="btn btn-secondary">Cancel</a>
</div>
