@extends('admin.layout')

@section('page_title', 'Ads Manager')

@section('styles')
<style>
    .card-hover-effect {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card-hover-effect:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
</style>
@endsection

@section('content')

<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-2 mb-4">
        <div>
            <h1 class="h4 mb-1 fw-bold">Ads Manager</h1>
            <div class="text-muted">Welcome to your admin dashboard</div>
        </div>
        <a href="{{ url('/') }}" class="btn btn-outline-secondary d-md-none w-100">
            <i class="fas fa-arrow-left me-2"></i>Back to User Side
        </a>
    </div>





    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-0 pt-4 pb-2">
            <h2 class="h6 mb-0 fw-bold"><i class="fas fa-icons me-2 text-info"></i>App Icon Manager</h2>
        </div>
        <div class="card-body pt-0">
            <form action="{{ route('admin.app_icon.store') }}" method="POST" enctype="multipart/form-data" class="d-flex flex-column flex-md-row gap-2 align-items-start align-items-md-center mb-3">
                @csrf
                <input type="file" name="app_icon" class="form-control" accept="image/*" required>
                <button type="submit" class="btn btn-info text-white">
                    <i class="fas fa-save me-2"></i>Save App Icon
                </button>
            </form>
            @if($appIcon ?? false)
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-3">
                    <div class="border rounded bg-light p-2">
                        <img src="{{ $appIcon['url'] }}" alt="{{ $appIcon['name'] }}" loading="lazy" decoding="async" style="width: 100px; height: 100px; object-fit: contain;">
                    </div>
                    <div class="d-flex flex-column flex-md-row gap-2">
                        <div class="text-muted small">
                            <div class="fw-semibold">Current icon: {{ $appIcon['name'] }}</div>
                            <div>{{ number_format(($appIcon['size'] ?? 0) / 1024, 0) }} KB</div>
                        </div>
                        <form action="{{ route('admin.app_icon.destroy') }}" method="POST" onsubmit="return confirm('Remove current app icon?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-trash-alt me-1"></i>Remove
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="text-muted small">No app icon set yet.</div>
            @endif
            <div class="text-muted small mt-2">This icon will be stored in adminimages/logo/.</div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-0 pt-4 pb-2">
            <h2 class="h6 mb-0 fw-bold"><i class="fas fa-bullhorn me-2 text-danger"></i>Entry Popup Ad</h2>
        </div>
        <div class="card-body pt-0">
            <form action="{{ route('admin.entry_ad.store') }}" method="POST" enctype="multipart/form-data" class="d-flex flex-column flex-md-row gap-2 align-items-start align-items-md-center mb-3">
                @csrf
                <input type="file" name="entry_image" class="form-control" accept="image/*" required>
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-save me-2"></i>Save Entry Ad
                </button>
            </form>
            @if($entryAd ?? false)
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-3">
                    <div class="border rounded bg-light p-2">
                        <img src="{{ $entryAd['url'] }}" alt="{{ $entryAd['name'] }}" loading="lazy" decoding="async" style="height: 120px; width: 214px; object-fit: cover;">
                    </div>
                    <div class="d-flex flex-column flex-md-row gap-2">
                        <div class="text-muted small">
                            <div class="fw-semibold">Current entry ad: {{ $entryAd['name'] }}</div>
                            <div>{{ number_format(($entryAd['size'] ?? 0) / 1024, 0) }} KB</div>
                        </div>
                        <form action="{{ route('admin.entry_ad.destroy') }}" method="POST" onsubmit="return confirm('Remove current entry ad image?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-trash-alt me-1"></i>Remove
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="text-muted small">No entry popup ad set yet.</div>
            @endif
            <div class="text-muted small mt-2">This image is shown once when users open the homepage.</div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-0 pt-4 pb-2">
            <h2 class="h6 mb-0 fw-bold"><i class="fas fa-upload me-2 text-primary"></i>Upload Slides</h2>
        </div>
        <div class="card-body pt-0">
            <form action="{{ route('admin.advertise.store') }}" method="POST" enctype="multipart/form-data" class="d-flex flex-column flex-md-row gap-2 align-items-start align-items-md-center">
                @csrf
                <input type="file" name="images[]" class="form-control" accept="image/*" multiple required>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-cloud-upload-alt me-2"></i>Upload
                </button>
            </form>
            <div class="text-muted small mt-2">Images will appear in the homepage slideshow automatically.</div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-0 pt-4 pb-2 d-flex align-items-center justify-content-between">
            <h2 class="h6 mb-0 fw-bold"><i class="fas fa-images me-2 text-primary"></i>Current Slides</h2>
            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">{{ count($slides ?? []) }} files</span>
        </div>
        <div class="card-body pt-0 p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase small text-muted">
                        <tr>
                            <th class="ps-4" style="width: 120px;">Preview</th>
                            <th>File</th>
                            <th style="width: 160px;">Size</th>
                            <th class="text-end pe-4" style="width: 140px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($slides ?? []) as $slide)
                            <tr>
                                <td class="ps-4">
                                    <div class="p-1 border rounded bg-light d-inline-block">
                                        <img src="{{ $slide['url'] }}" alt="{{ $slide['name'] }}" loading="lazy" class="d-block" style="height: 54px; width: 92px; object-fit: cover;">
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $slide['name'] }}</div>
                                    <a href="{{ $slide['url'] }}" target="_blank" class="small text-primary">Open</a>
                                </td>
                                <td class="text-muted">
                                    {{ number_format(($slide['size'] ?? 0) / 1024, 0) }} KB
                                </td>
                                <td class="text-end pe-4">
                                    <form action="{{ route('admin.advertise.destroy', ['filename' => $slide['name']]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this slide?');">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <div class="mb-3">
                                        <i class="fas fa-images fa-3x text-light"></i>
                                    </div>
                                    <p class="mb-0">No slides found in ads/slides.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
