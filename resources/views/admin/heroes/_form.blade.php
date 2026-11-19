@csrf
<div class="mb-3">
    <label class="form-label">Title <span class="text-danger">*</span></label>
    <input type="text" name="title" class="form-control" value="{{ old('title', $hero->title ?? '') }}" required maxlength="255">
    @error('title') <div class="text-danger small">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">Functionality Name</label>
    <select id="functionalityName" name="functionality_name" class="form-select">
        <option value="">Select functionality</option>
        @foreach($bannerFunctionalities as $functionalityName => $functionality)
            <option value="{{ $functionalityName }}"
                    {{ old('functionality_name', $hero->functionality_name ?? '') === $functionalityName ? 'selected' : '' }}>
                {{ $functionalityName }} — {{ $functionality['base_url'] }}
            </option>
        @endforeach
    </select>
    <div class="form-text text-muted mt-1">Select a Mali Setu functionality and its base API link.</div>
    @error('functionality_name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">Event Name</label>
    <select id="eventName" name="event_name" class="form-select" disabled>
        <option value="">Select event</option>
    </select>
    <div class="form-text text-muted mt-1">Events are loaded from the selected functionality.</div>
    @error('event_name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">Record / Destination</label>
    <select id="destinationRecord" class="form-select" disabled>
        <option value="">Select record</option>
    </select>
    <div class="form-text text-muted mt-1">Example: Darshan Kondekar — <code>/api/matrimony/profile/282</code>.</div>
</div>

<div class="mb-3">
    <label class="form-label">Internal API URL</label>
    <input type="text" id="internalUrl" name="url" class="form-control"
           value="{{ old('url', $hero->url ?? '') }}" maxlength="512" readonly
           placeholder="Automatically filled from the dropdown selection">
    <div class="form-text text-muted mt-1">The selected functionality, event, or record link is saved here.</div>
    @error('url') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label class="form-label">External URL</label>
    <input type="url" name="external_url" class="form-control"
           value="{{ old('external_url', $hero->external_url ?? '') }}" maxlength="512"
           placeholder="https://another-domain.com/page">
    <div class="form-text text-muted mt-1">Optional link to another domain. Enter the complete HTTP/HTTPS URL.</div>
    @error('external_url') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const destinations = @json($bannerFunctionalities);
    const functionalityInput = document.getElementById('functionalityName');
    const eventInput = document.getElementById('eventName');
    const destinationInput = document.getElementById('destinationRecord');
    const internalUrlInput = document.getElementById('internalUrl');
    const savedEvent = @js(old('event_name', $hero->event_name ?? ''));
    const savedUrl = @js(old('url', $hero->url ?? ''));

    function refreshEventOptions(selectedEvent = '') {
        const functionality = destinations[functionalityInput.value] || null;
        const events = functionality ? functionality.events : {};
        eventInput.innerHTML = '<option value="">Select event</option>';
        Object.keys(events).forEach(function (eventName) {
            const option = document.createElement('option');
            const exampleUrl = events[eventName][0]?.url || '';
            const endpointPattern = exampleUrl.replace(/\/\d+$/, '/{id}');
            option.value = eventName;
            option.textContent = endpointPattern ? `${eventName} — ${endpointPattern}` : eventName;
            option.selected = eventName === selectedEvent;
            eventInput.appendChild(option);
        });
        eventInput.disabled = Object.keys(events).length === 0;
    }

    function refreshDestinationOptions(selectedUrl = '') {
        const functionality = destinations[functionalityInput.value] || null;
        const records = functionality?.events?.[eventInput.value] || [];
        destinationInput.innerHTML = '<option value="">Select record</option>';
        records.forEach(function (record) {
            const option = document.createElement('option');
            option.value = record.url;
            option.textContent = `${record.label} — ${record.url}`;
            option.selected = record.url === selectedUrl;
            destinationInput.appendChild(option);
        });
        destinationInput.disabled = records.length === 0;
    }

    functionalityInput.addEventListener('change', function () {
        refreshEventOptions();
        refreshDestinationOptions();
        internalUrlInput.value = destinations[functionalityInput.value]?.base_url || '';
    });

    eventInput.addEventListener('change', function () {
        refreshDestinationOptions();
        internalUrlInput.value = destinations[functionalityInput.value]?.base_url || '';
    });

    destinationInput.addEventListener('change', function () {
        internalUrlInput.value = destinationInput.value
            || destinations[functionalityInput.value]?.base_url
            || '';
    });

    refreshEventOptions(savedEvent);
    refreshDestinationOptions(savedUrl);
});
</script>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary">{{ $submit ?? 'Save' }}</button>
    <a href="{{ route('heroes.index') }}" class="btn btn-secondary">Cancel</a>
</div>
