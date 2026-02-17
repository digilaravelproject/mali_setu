@csrf
<div class="mb-3">
    <label class="form-label">Title <span class="text-danger">*</span></label>
    <input type="text" name="title" class="form-control" value="{{ old('title', $hero->title ?? '') }}" required maxlength="255">
    @error('title') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">Hero Image {{ isset($hero) ? '(leave blank to keep current)' : '*' }}</label>
    <input type="file" name="image" class="form-control" accept="image/*" {{ isset($hero) ? '' : 'required' }}>
    @error('image') <div class="text-danger small">{{ $message }}</div> @enderror

    @if(!empty($hero?->image_path))
        <div class="mt-2">
            <img src="{{ asset('storage/'.$hero->image_path) }}" alt="Current Image" style="max-height:120px">
        </div>
    @endif
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary">{{ $submit ?? 'Save' }}</button>
    <a href="{{ route('heroes.index') }}" class="btn btn-secondary">Cancel</a>
</div>
