@extends('admin.layouts.app')

@section('title', 'App Version')
@section('page-title', 'App Version')

@push('styles')
<style>
    .version-page { max-width: 1220px; margin: 0 auto; }
    .version-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        box-shadow: 0 3px 8px rgba(15, 23, 42, .08);
        padding: 2.75rem 3rem;
    }
    .version-card + .version-card { margin-top: 2rem; }
    .platform-title { color: #111827; font-weight: 800; }
    .platform-icon { color: #10b981; }
    .edit-version-btn {
        width: 42px;
        height: 42px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        color: var(--primary-color);
        background: #fff;
        transition: .2s ease;
    }
    .edit-version-btn:hover { color: #fff; background: var(--primary-color); border-color: var(--primary-color); }
    .metric-box { background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 18px; padding: 1.5rem; height: 100%; }
    .metric-box.minimum.android { background: #fffbeb; border-color: #fef3c7; }
    .metric-box.minimum.ios { background: #eff6ff; border-color: #dbeafe; }
    .metric-label, .detail-label { color: #64748b; font-size: .78rem; font-weight: 800; letter-spacing: .09em; text-transform: uppercase; }
    .metric-box.minimum.android .metric-label, .metric-box.minimum.android .metric-value { color: #e99a00; }
    .metric-box.minimum.ios .metric-label, .metric-box.minimum.ios .metric-value { color: #2474d3; }
    .metric-value { color: #111827; font-size: 1.35rem; font-weight: 800; margin-top: .55rem; }
    .detail-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; color: #111827; padding: 1rem 1.35rem; overflow-wrap: anywhere; }
    .modal-content { border: 0; border-radius: 20px; }
    @media (max-width: 767px) {
        .version-card { padding: 1.5rem; }
    }
</style>
@endpush

@section('content')
<div class="version-page">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="mb-1 fw-bold">Mobile App Versions</h2>
            <p class="text-muted mb-0">Manage releases, minimum supported builds, store links, and update notes.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-circle-exclamation me-2"></i>{{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @foreach(['android' => 'Android Application', 'ios' => 'iOS Application'] as $platform => $title)
        @php($appVersion = $versions->get($platform))
        @continue(! $appVersion)

        <section class="version-card mb-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h3 class="platform-title h4 mb-0">
                    <i class="fas {{ $platform === 'android' ? 'fa-mobile-screen' : 'fa-mobile-screen-button' }} platform-icon me-3"></i>{{ $title }}
                </h3>
                <button type="button" class="edit-version-btn" data-bs-toggle="modal" data-bs-target="#editVersion{{ ucfirst($platform) }}" aria-label="Edit {{ $title }}" title="Edit {{ $title }}">
                    <i class="fas fa-pen"></i>
                </button>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-6 col-lg-3">
                    <div class="metric-box"><div class="metric-label">Version</div><div class="metric-value">{{ $appVersion->version }}</div></div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="metric-box"><div class="metric-label">Build Code</div><div class="metric-value">{{ $appVersion->build_code }}</div></div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="metric-box minimum {{ $platform }}"><div class="metric-label">Min Version</div><div class="metric-value">{{ $appVersion->min_version }}</div></div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="metric-box minimum {{ $platform }}"><div class="metric-label">Min Build</div><div class="metric-value">{{ $appVersion->min_build }}</div></div>
                </div>
            </div>

            <div class="mb-4">
                <div class="detail-label mb-2">Store URL</div>
                <div class="detail-box">{{ $appVersion->store_url }}</div>
            </div>
            <div>
                <div class="detail-label mb-2">Update Notes</div>
                <div class="detail-box">{{ $appVersion->update_notes ?: 'No update notes added.' }}</div>
            </div>
        </section>

        <div class="modal fade" id="editVersion{{ ucfirst($platform) }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="{{ route('admin.app-versions.update', $appVersion) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="editing_platform" value="{{ $platform }}">
                        <div class="modal-header px-4 py-3">
                            <h5 class="modal-title fw-bold"><i class="fas fa-pen me-2 text-success"></i>Edit {{ $title }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="{{ $platform }}_version">Version</label>
                                    <input class="form-control" id="{{ $platform }}_version" name="version" value="{{ old('editing_platform') === $platform ? old('version') : $appVersion->version }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="{{ $platform }}_build_code">Build Code</label>
                                    <input type="number" min="1" class="form-control" id="{{ $platform }}_build_code" name="build_code" value="{{ old('editing_platform') === $platform ? old('build_code') : $appVersion->build_code }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="{{ $platform }}_min_version">Minimum Version</label>
                                    <input class="form-control" id="{{ $platform }}_min_version" name="min_version" value="{{ old('editing_platform') === $platform ? old('min_version') : $appVersion->min_version }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="{{ $platform }}_min_build">Minimum Build</label>
                                    <input type="number" min="1" class="form-control" id="{{ $platform }}_min_build" name="min_build" value="{{ old('editing_platform') === $platform ? old('min_build') : $appVersion->min_build }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold" for="{{ $platform }}_store_url">Store URL</label>
                                    <input type="url" class="form-control" id="{{ $platform }}_store_url" name="store_url" value="{{ old('editing_platform') === $platform ? old('store_url') : $appVersion->store_url }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold" for="{{ $platform }}_update_notes">Update Notes</label>
                                    <textarea class="form-control" id="{{ $platform }}_update_notes" name="update_notes" rows="4" maxlength="2000">{{ old('editing_platform') === $platform ? old('update_notes') : $appVersion->update_notes }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer px-4 py-3">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-check me-1"></i> Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection

@if($errors->any() && in_array(old('editing_platform'), ['android', 'ios'], true))
    @push('scripts')
    <script>
        bootstrap.Modal.getOrCreateInstance(document.getElementById(@json('editVersion'.ucfirst(old('editing_platform'))))).show();
    </script>
    @endpush
@endif
