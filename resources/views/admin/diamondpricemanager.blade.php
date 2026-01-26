@extends('admin.layout')

@section('page_title', isset($isPubg) ? 'PUBG Prices' : (isset($isMcgg) ? 'MCGG Prices' : (isset($isWwm) ? 'WWM Prices' : 'MLBB Prices')))

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm mt-3">
        <ul class="nav nav-tabs px-3 pt-3 border-bottom-0 bg-white rounded-top">
            <li class="nav-item">
                <a class="nav-link {{ (!isset($isPubg) && !isset($isMcgg) && !isset($isWwm)) ? 'active fw-bold text-primary' : 'text-muted' }}" href="{{ route('admin.mlbb.prices') }}">
                    <i class="bi bi-controller me-1"></i>MLBB
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ isset($isPubg) ? 'active fw-bold text-primary' : 'text-muted' }}" href="{{ route('admin.pubg.prices') }}">
                    <i class="fas fa-gun me-1"></i>PUBG
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ isset($isMcgg) ? 'active fw-bold text-primary' : 'text-muted' }}" href="{{ route('admin.mcgg.prices') }}">
                    <i class="bi bi-gem me-1"></i>MCGG
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ isset($isWwm) ? 'active fw-bold text-primary' : 'text-muted' }}" href="{{ route('admin.wwm.prices') }}">
                    <i class="bi bi-wind me-1"></i>WWM
                </a>
            </li>
        </ul>
        <!-- Header & Filter -->
        <div class="card-header bg-white py-3 border-bottom-0 border-top">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <h4 class="mb-0 fw-bold text-primary">
                    @if(isset($isPubg))
                        <i class="fas fa-gun me-2"></i>PUBG Price Manager
                    @elseif(isset($isMcgg))
                        <i class="bi bi-gem me-2"></i>MCGG Price Manager
                    @elseif(isset($isWwm))
                        <i class="bi bi-wind me-2"></i>WWM Price Manager
                    @else
                        <i class="bi bi-gem me-2"></i>MLBB Price Manager
                    @endif
                </h4>
                
                <div class="d-flex align-items-center gap-2">
                    @if(isset($isMlbb))
                    <form method="GET" action="{{ route('admin.mlbb.prices') }}" class="d-flex align-items-center">
                        <label for="mlbbRegionSelect" class="form-label mb-0 me-2 text-muted fw-medium">Region:</label>
                        <select id="mlbbRegionSelect" name="region" class="form-select form-select-sm" style="width:150px" onchange="this.form.submit()">
                            <option value="" {{ empty($region) ? 'selected' : '' }}>All Regions</option>
                            <option value="myanmar" {{ $region === 'myanmar' ? 'selected' : '' }}>Myanmar</option>
                            <option value="malaysia" {{ $region === 'malaysia' ? 'selected' : '' }}>Malaysia</option>
                            <option value="philippines" {{ $region === 'philippines' ? 'selected' : '' }}>Philippines</option>
                            <option value="singapore" {{ $region === 'singapore' ? 'selected' : '' }}>Singapore</option>
                            <option value="indonesia" {{ $region === 'indonesia' ? 'selected' : '' }}>Indonesia</option>
                            <option value="russia" {{ $region === 'russia' ? 'selected' : '' }}>Russia</option>
                        </select>
                    </form>
                    @endif
                    
                    <form method="POST" action="{{ isset($isPubg) ? route('admin.pubg.prices.fetch') : (isset($isMcgg) ? route('admin.mcgg.prices.fetch') : (isset($isWwm) ? route('admin.wwm.prices.fetch') : route('admin.mlbb.prices.fetch'))) }}" onsubmit="this.querySelector('input[name=region]').value = (document.getElementById('mlbbRegionSelect')?.value || 'all');">
                        @csrf
                        <input type="hidden" name="region" value="{{ $region ?: 'all' }}">
                        <button class="btn btn-outline-primary btn-sm d-flex align-items-center" type="submit">
                            <i class="bi bi-cloud-download me-1"></i> Fetch API
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Action Toolbar -->
        <div class="card-body bg-light border-top border-bottom py-3">
            <div class="row g-2 align-items-center">
                <div class="col-md-8">
                    <form method="POST" action="{{ isset($isPubg) ? route('admin.pubg.prices.bulk') : (isset($isMcgg) ? route('admin.mcgg.prices.bulk') : (isset($isWwm) ? route('admin.wwm.prices.bulk') : route('admin.mlbb.prices.bulk'))) }}" class="d-flex align-items-center flex-wrap gap-2">
                        @csrf
                        <input type="hidden" name="region" value="{{ $region ?: 'all' }}">
                        <span class="fw-bold text-secondary"><i class="bi bi-magic me-1"></i>Bulk Adjust:</span>
                        <div class="input-group input-group-sm" style="width: 180px;">
                            <span class="input-group-text bg-white">Base Price +</span>
                            <input type="number" name="percentage" class="form-control" placeholder="10" step="0.1" required>
                            <span class="input-group-text">%</span>
                        </div>
                        <button class="btn btn-primary btn-sm" type="submit">Apply</button>
                        <small class="text-muted ms-1 d-none d-lg-inline">(-ve to decrease)</small>
                    </form>
                </div>
                <div class="col-md-4 text-md-end">
                    <small class="text-muted">Showing <strong>{{ count($products) }}</strong> items</small>
                </div>
            </div>
        </div>

        <!-- Product Table -->
        <div class="card-body p-0">
            <form method="POST" action="{{ isset($isPubg) ? route('admin.pubg.prices.update') : (isset($isMcgg) ? route('admin.mcgg.prices.update') : (isset($isWwm) ? route('admin.wwm.prices.update') : route('admin.mlbb.prices.update'))) }}">
                @csrf
                <div class="table-responsive" style="max-height: 70vh;">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th class="ps-4">Product ID</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Region</th>
                                <th>{{ isset($isPubg) ? 'UC' : 'Diamonds' }}</th>
                                <th style="width: 180px;" class="pe-4">Selling Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $index => $p)
                                <tr>
                                    <td class="ps-4 font-monospace small">{{ $p->product_id }}</td>
                                    <td class="fw-medium">{{ $p->name }}</td>
                                    <td><span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $p->category }}</span></td>
                                    <td>
                                        @php
                                            $flags = [
                                                'myanmar' => '🇲🇲', 'malaysia' => '🇲🇾', 'philippines' => '🇵🇭', 
                                                'singapore' => '🇸🇬', 'indonesia' => '🇮🇩', 'russia' => '🇷🇺'
                                            ];
                                        @endphp
                                        <span class="me-1">{{ $flags[strtolower($p->region)] ?? '🌐' }}</span>
                                        {{ ucfirst($p->region) }}
                                    </td>
                                    <td>
                                        <span class="d-flex align-items-center text-info">
                                            <i class="bi {{ isset($isPubg) ? 'bi-crosshair' : 'bi-gem' }} me-1"></i> 
                                            {{ isset($isPubg) ? $p->uc : $p->diamonds }}
                                        </span>
                                    </td>
                                    <td class="pe-4">
                                        <div class="input-group input-group-sm">
                                            <input type="hidden" name="updates[{{ $index }}][product_id]" value="{{ $p->product_id }}">
                                            <input type="hidden" name="updates[{{ $index }}][region]" value="{{ $p->region }}">
                                            @php 
                                                $keyPrefix = isset($isPubg) ? 'pubg_' : (isset($isMcgg) ? 'mcgg_' : (isset($isWwm) ? 'wwm_' : ''));
                                                $k = $keyPrefix . strtolower($p->product_id).'|'.strtolower($p->region); 
                                                $ov = isset($overrides[$k]) ? $overrides[$k] : null; 
                                            @endphp
                                            <input type="number" name="updates[{{ $index }}][price]" value="{{ $ov ? $ov->price : $p->price }}" min="0" step="1" class="form-control fw-bold text-end">
                                            <span class="input-group-text bg-white text-muted">MMK</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="bi bi-inbox fs-1 mb-2 opacity-25"></i>
                                            <p class="mb-0">No products found for this region.</p>
                                            <small>Try fetching from API or selecting a different region.</small>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Footer Action -->
                <div class="card-footer bg-white py-3 border-top sticky-bottom shadow-lg">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small"><i class="bi bi-info-circle me-1"></i>Changes are applied immediately to user view.</span>
                        <button class="btn btn-success px-4 fw-bold shadow-sm">
                            <i class="bi bi-save me-2"></i>Save All Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
