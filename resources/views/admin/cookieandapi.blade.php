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
            <div class="card-header bg-transparent border-0 pt-4 pb-2">
                <h2 class="h6 mb-0 fw-bold"><i class="fas fa-link me-2 text-primary"></i>Product APIs</h2>
            </div>
            <div class="card-body pt-0">
                <div class="row g-3">
                    <div class="col-12 col-lg-6">
                        <label class="form-label fw-semibold" for="mlbbApiBaseUrl">MLBB API Base URL</label>
                        <input type="text" class="form-control" id="mlbbApiBaseUrl" name="mlbb_api_base_url" value="{{ old('mlbb_api_base_url', $mlbbApiBaseUrl ?? '') }}" placeholder="http://192.168.196.37:8000">
                        <div class="form-text">Used by the MLBB “Fetch from API” action.</div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label fw-semibold" for="pubgApiProductsUrl">PUBG Products API URL</label>
                        <input type="text" class="form-control" id="pubgApiProductsUrl" name="pubg_api_products_url" value="{{ old('pubg_api_products_url', $pubgApiProductsUrlInput ?? '') }}" placeholder="http://192.168.196.37:8000/products">
                        <div class="form-text">Used by the PUBG “Fetch from API” action.</div>
                        @if(!empty($pubgApiProductsUrlResolved))
                            <div class="form-text">Resolved: {{ $pubgApiProductsUrlResolved }}</div>
                        @endif
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label fw-semibold" for="mcggApiProductsUrl">MCGG Products API URL</label>
                        <input type="text" class="form-control" id="mcggApiProductsUrl" name="mcgg_api_products_url" value="{{ old('mcgg_api_products_url', $mcggApiProductsUrlInput ?? '') }}" placeholder="http://192.168.196.37:8000/mcgg/products">
                        <div class="form-text">Used by the MCGG “Fetch from API” action.</div>
                        @if(!empty($mcggApiProductsUrlResolved))
                            <div class="form-text">Resolved: {{ $mcggApiProductsUrlResolved }}</div>
                        @endif
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label fw-semibold" for="wwmApiProductsUrl">WWM Products API URL</label>
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
