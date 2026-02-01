@extends('admin.layout')

@section('page_title', 'Cookie and API Manager')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-2 mb-4">
        <div>
            <h1 class="h4 mb-1 fw-bold">Cookie and API Manager</h1>
            <div class="text-muted">Manage purchase cookie and product API endpoints</div>
        </div>
    </div>



    <form action="{{ route('admin.cookieandapi.update') }}" method="POST">
        @csrf

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 pt-4 pb-2">
                <h2 class="h6 mb-0 fw-bold"><i class="fas fa-cookie-bite me-2 text-primary"></i>SO Miniapp (so.miniapp.zone)</h2>
            </div>
            <div class="card-body pt-0">
                <div class="row g-3">
                    <div class="col-12 col-lg-6">
                        <label class="form-label fw-semibold" for="soMiniappBaseUri">Base URL</label>
                        <input type="text" class="form-control" id="soMiniappBaseUri" name="so_miniapp_base_uri" value="{{ old('so_miniapp_base_uri', $soMiniappBaseUri ?? '') }}" placeholder="https://so.miniapp.zone">
                        <div class="form-text">Leave empty to use config/services.php default.</div>
                    </div>
                    <div class="col-12 col-lg-3">
                        <label class="form-label fw-semibold" for="soMiniappTimeout">Timeout (seconds)</label>
                        <input type="number" class="form-control" id="soMiniappTimeout" name="so_miniapp_timeout" value="{{ old('so_miniapp_timeout', $soMiniappTimeout ?? 15) }}" min="1" max="60">
                    </div>
                    <div class="col-12 col-lg-3 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="soMiniappVerify" name="so_miniapp_verify" value="1" {{ old('so_miniapp_verify', ($soMiniappVerify ?? true) ? 1 : 0) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="soMiniappVerify">SSL Verify</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" for="soMiniappCookie">Cookie (for buy / name-check)</label>
                        <textarea class="form-control" id="soMiniappCookie" name="so_miniapp_cookie" rows="6" placeholder="__cf_bm=...; _cfuvid=...; XSRF-TOKEN=...; alpha_cloud_session=...">{{ old('so_miniapp_cookie', $soMiniappCookie ?? '') }}</textarea>
                        <div class="form-text">Paste the full browser cookie string (semicolon-separated).</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
                <h2 class="h6 mb-0 fw-bold"><i class="fas fa-link me-2 text-primary"></i>Product APIs</h2>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-info me-2" id="btnCheckCache">
                        <i class="fas fa-search me-1"></i>Check Status
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-warning" id="btnRefreshCache">
                        <i class="fas fa-sync me-1"></i>Refresh Data
                    </button>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="row g-3">
                    <div class="col-12 col-lg-6">
                        <label class="form-label fw-semibold" for="mlbbApiBaseUrl">
                            MLBB API Base URL
                            <span id="mlbbStatusBadge">
                                @php
                                    $msg = $cacheStatus['mlbb_message'] ?? 'Not Checked';
                                    $isActive = $msg === 'Active';
                                    $isNotChecked = $msg === 'Not Checked';
                                @endphp
                                @if($isActive)
                                    <span class="badge bg-success ms-1">Active</span>
                                @elseif($isNotChecked)
                                    <span class="badge bg-secondary ms-1">Not Checked</span>
                                @else
                                    <span class="badge bg-danger ms-1" title="{{ $msg }}">Inactive</span>
                                @endif
                            </span>
                        </label>
                        <div id="mlbbStatusError" class="text-danger small mb-1">
                            @if(!$isActive && !$isNotChecked)
                                {{ $msg }}
                            @endif
                        </div>
                        <input type="text" class="form-control" id="mlbbApiBaseUrl" name="mlbb_api_base_url" value="{{ old('mlbb_api_base_url', $mlbbApiBaseUrl ?? '') }}" placeholder="http://192.168.196.37:8000">
                        <div class="form-text">Used by the MLBB “Fetch from API” action.</div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label fw-semibold" for="pubgApiProductsUrl">
                            PUBG Products API URL
                            <span id="pubgStatusBadge">
                                @php
                                    $msg = $cacheStatus['pubg_message'] ?? 'Inactive';
                                    $isActive = $msg === 'Active';
                                @endphp
                                @if($isActive)
                                    <span class="badge bg-success ms-1">Active</span>
                                @else
                                    <span class="badge bg-danger ms-1" title="{{ $msg }}">Inactive</span>
                                @endif
                            </span>
                        </label>
                        <div id="pubgStatusError" class="text-danger small mb-1">
                            @if(!$isActive && $msg !== 'Inactive')
                                {{ $msg }}
                            @endif
                        </div>
                        <input type="text" class="form-control" id="pubgApiProductsUrl" name="pubg_api_products_url" value="{{ old('pubg_api_products_url', $pubgApiProductsUrlInput ?? '') }}" placeholder="http://192.168.196.37:8000/products">
                        <div class="form-text">Used by the PUBG “Fetch from API” action.</div>
                        @if(!empty($pubgApiProductsUrlResolved))
                            <div class="form-text">Resolved: {{ $pubgApiProductsUrlResolved }}</div>
                        @endif
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label fw-semibold" for="mcggApiProductsUrl">
                            MCGG Products API URL
                            <span id="mcggStatusBadge">
                                @php
                                    $msg = $cacheStatus['mcgg_message'] ?? 'Inactive';
                                    $isActive = $msg === 'Active';
                                @endphp
                                @if($isActive)
                                    <span class="badge bg-success ms-1">Active</span>
                                @else
                                    <span class="badge bg-danger ms-1" title="{{ $msg }}">Inactive</span>
                                @endif
                            </span>
                        </label>
                        <div id="mcggStatusError" class="text-danger small mb-1">
                            @if(!$isActive && $msg !== 'Inactive')
                                {{ $msg }}
                            @endif
                        </div>
                        <input type="text" class="form-control" id="mcggApiProductsUrl" name="mcgg_api_products_url" value="{{ old('mcgg_api_products_url', $mcggApiProductsUrlInput ?? '') }}" placeholder="http://192.168.196.37:8000/mcgg/products">
                        <div class="form-text">Used by the MCGG “Fetch from API” action.</div>
                        @if(!empty($mcggApiProductsUrlResolved))
                            <div class="form-text">Resolved: {{ $mcggApiProductsUrlResolved }}</div>
                        @endif
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label fw-semibold" for="wwmApiProductsUrl">
                            WWM Products API URL
                            <span id="wwmStatusBadge">
                                @php
                                    $msg = $cacheStatus['wwm_message'] ?? 'Not Checked';
                                    $isActive = $msg === 'Active';
                                    $isNotChecked = $msg === 'Not Checked';
                                @endphp
                                @if($isActive)
                                    <span class="badge bg-success ms-1">Active</span>
                                @elseif($isNotChecked)
                                    <span class="badge bg-secondary ms-1">Not Checked</span>
                                @else
                                    <span class="badge bg-danger ms-1" title="{{ $msg }}">Inactive</span>
                                @endif
                            </span>
                        </label>
                        <div id="wwmStatusError" class="text-danger small mb-1">
                            @if(!$isActive && !$isNotChecked)
                                {{ $msg }}
                            @endif
                        </div>
                        <input type="text" class="form-control" id="wwmApiProductsUrl" name="wwm_api_products_url" value="{{ old('wwm_api_products_url', $wwmApiProductsUrlInput ?? '') }}" placeholder="http://192.168.196.37:8000/wwm/products">
                        <div class="form-text">Used by the WWM “Fetch from API” action.</div>
                        @if(!empty($wwmApiProductsUrlResolved))
                            <div class="form-text">Resolved: {{ $wwmApiProductsUrlResolved }}</div>
                        @endif
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold mb-2">Available endpoints (current)</label>
                        <div class="table-responsive border rounded">
                            <table class="table table-sm mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 280px;">Endpoint</th>
                                        <th>URL</th>
                                        <th style="width: 80px;" class="text-end">Open</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(($apiEndpointList ?? []) as $ep)
                                        <tr>
                                            <td class="fw-semibold">{{ $ep['name'] ?? '' }}</td>
                                            <td class="text-break">
                                                <code>{{ $ep['url'] ?? '' }}</code>
                                            </td>
                                            <td class="text-end">
                                                @if(!empty($ep['url']))
                                                    <a class="btn btn-sm btn-outline-primary" href="{{ $ep['url'] }}" target="_blank" rel="noopener">Open</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold mb-2">MLBB API URLs (all regions)</label>
                        <div class="table-responsive border rounded">
                            <table class="table table-sm mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 160px;">Region</th>
                                        <th>URL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $regions = ['myanmar', 'malaysia', 'philippines', 'singapore', 'indonesia', 'russia'];
                                    @endphp
                                    @foreach($regions as $region)
                                        <tr>
                                            <td class="text-capitalize fw-semibold">{{ $region }}</td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm" name="mlbb_api_urls[{{ $region }}]" value="{{ old('mlbb_api_urls.'.$region, ($mlbbApiUrlInputs[$region] ?? '')) }}" placeholder="{{ $mlbbApiDefaultUrls[$region] ?? '' }}">
                                                @if(!empty($mlbbApiUrlInputs[$region] ?? '') && !empty($mlbbApiUrlsResolved[$region] ?? ''))
                                                    <div class="form-text">Resolved: {{ $mlbbApiUrlsResolved[$region] }}</div>
                                                @endif
                                                @if(!empty($mlbbApiDefaultUrls[$region]))
                                                    <div class="form-text">Default: {{ $mlbbApiDefaultUrls[$region] }}</div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="form-text">Enter endpoint path only (example: mlproductsmm).</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Save Settings
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnCheck = document.getElementById('btnCheckCache');
    const btnRefresh = document.getElementById('btnRefreshCache');
    
    // Helper function to update UI for a specific key
    function updateStatusUI(key, data) {
        const badgeContainer = document.getElementById(key + 'StatusBadge');
        const errorContainer = document.getElementById(key + 'StatusError');
        
        if (!badgeContainer || !errorContainer) return;
        
        const status = data.status_message;
        const isActive = status === 'Active';
        const isNotChecked = status === 'Not Checked';
        
        // Update Badge
        if (isActive) {
            badgeContainer.innerHTML = '<span class="badge bg-success ms-1">Active</span>';
        } else if (isNotChecked) {
            badgeContainer.innerHTML = '<span class="badge bg-secondary ms-1">Not Checked</span>';
        } else {
            // Error or Inactive
            badgeContainer.innerHTML = `<span class="badge bg-danger ms-1" title="${status}">Inactive</span>`;
        }
        
        // Update Error Message
        // Always show the message if it's not Active and not "Not Checked" (unless user wants to see "Not Checked" explicitly, but usually that's neutral)
        // If it is "Inactive" (literal), we might want to show it or hide it. 
        // But if it's an error message like "Fetch failed...", we MUST show it.
        
        if (!isActive && !isNotChecked) {
            errorContainer.textContent = status;
            errorContainer.style.display = 'block';
        } else {
            errorContainer.textContent = '';
            errorContainer.style.display = 'none';
        }
    }

    // Status Check
    if (btnCheck) {
        btnCheck.addEventListener('click', function() {
            const originalHtml = btnCheck.innerHTML;
            btnCheck.disabled = true;
            btnCheck.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Checking...';
            
            fetch('{{ route("api.cache.status") }}')
                .then(response => response.json())
                .then(data => {
                    updateStatusUI('mlbb', data.mlbb);
                    updateStatusUI('pubg', data.pubg);
                    updateStatusUI('mcgg', data.mcgg);
                    updateStatusUI('wwm', data.wwm);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to check status');
                })
                .finally(() => {
                    btnCheck.disabled = false;
                    btnCheck.innerHTML = originalHtml;
                });
        });
    }
    
    // Refresh Data
    if (btnRefresh) {
        btnRefresh.addEventListener('click', function() {
            if(!confirm('Are you sure you want to refresh all data from APIs? This might take a few seconds.')) return;
            
            const originalHtml = btnRefresh.innerHTML;
            btnRefresh.disabled = true;
            btnRefresh.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Refreshing...';
            
            fetch('{{ route("api.refresh_cache") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    // Trigger a status check to update UI
                    if(btnCheck) btnCheck.click();
                    alert(data.message || 'Refresh successful');
                } else {
                    alert('Refresh failed: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to refresh data');
            })
            .finally(() => {
                btnRefresh.disabled = false;
                btnRefresh.innerHTML = originalHtml;
            });
        });
    }
});
</script>
@endsection
