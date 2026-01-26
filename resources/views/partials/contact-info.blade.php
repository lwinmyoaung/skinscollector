@php
    $contacts = \App\Models\Contact::where('is_active', true)->get();
@endphp

@if($contacts->count() > 0)
<div class="card border-0 shadow-sm h-100">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-4"><i class="fas fa-headset me-2 text-primary"></i>Contact Support</h5>
        <div class="d-flex flex-column gap-3">
            @foreach($contacts as $contact)
                <a href="{{ $contact->value }}" target="_blank" class="d-flex align-items-center text-decoration-none p-3 rounded-3 bg-light bg-opacity-50 hover-shadow transition-all" style="color: inherit;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" 
                         style="width: 45px; height: 45px; background-color: {{ $contact->color ? $contact->color.'20' : '#e9ecef' }}">
                        <i class="{{ $contact->icon ?? 'fas fa-link' }} fa-lg" style="color: {{ $contact->color ?? '#6c757d' }}"></i>
                    </div>
                    <div>
                        <div class="small text-muted text-uppercase fw-bold">{{ $contact->platform }}</div>
                        <div class="fw-medium text-break">{{ $contact->value }}</div>
                    </div>
                    <i class="fas fa-external-link-alt ms-auto text-muted small opacity-50"></i>
                </a>
            @endforeach
        </div>
    </div>
</div>

<style>
    .hover-shadow:hover {
        transform: translateY(-2px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.08)!important;
        background-color: #fff !important;
    }
    .transition-all {
        transition: all 0.2s ease;
    }
</style>
@endif
