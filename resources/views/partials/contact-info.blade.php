@php
    $contacts = \App\Models\Contact::where('is_active', true)->get();
@endphp

@if($contacts->count() > 0)
<div class="d-flex flex-column gap-2">
    @foreach($contacts as $contact)
        <div class="card border-0 shadow-sm contact-row overflow-hidden" 
             style="background: linear-gradient(145deg, #ffffff, {{ $contact->color ? $contact->color.'08' : '#f8f9fa' }});">
            <div class="card-body p-3 d-flex align-items-center">
                {{-- Icon --}}
                <div class="icon-box rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 me-3"
                     style="width: 50px; height: 50px; background-color: #ffffff; color: {{ $contact->color ?? '#6c757d' }}; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                    <i class="{{ $contact->icon ?? 'fas fa-link' }} fa-lg"></i>
                </div>
                
                {{-- Content --}}
                <div class="flex-grow-1 min-w-0 me-3">
                    <h6 class="fw-bold mb-0 text-dark">{{ $contact->platform }}</h6>
                    <div class="text-muted small text-break font-monospace">{{ $contact->value }}</div>
                </div>
                
                {{-- Actions --}}
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-light btn-sm rounded-circle shadow-sm copy-btn" 
                            data-clipboard-text="{{ $contact->value }}" 
                            title="Copy">
                        <i class="fas fa-copy text-secondary"></i>
                    </button>
                </div>
            </div>
            {{-- Decorative left border --}}
            <div class="position-absolute top-0 start-0 h-100" style="width: 4px; background-color: {{ $contact->color ?? '#0d6efd' }}"></div>
        </div>
    @endforeach
</div>

<style>
    .contact-row {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
    }
    .contact-row:hover {
        transform: translateY(-2px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.08)!important;
    }
    .copy-btn {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .copy-btn:hover {
        background-color: #e9ecef;
        color: #0d6efd;
    }
    .copy-btn.copied {
        background-color: #198754;
        color: white;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const copyBtns = document.querySelectorAll('.copy-btn');
        
        copyBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const text = this.getAttribute('data-clipboard-text');
                
                navigator.clipboard.writeText(text).then(() => {
                    // Show feedback
                    const originalIcon = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-check"></i>';
                    this.classList.add('copied');
                    
                    setTimeout(() => {
                        this.innerHTML = originalIcon;
                        this.classList.remove('copied');
                    }, 2000);
                }).catch(err => {
                    console.error('Failed to copy: ', err);
                });
            });
        });
    });
</script>
@endif
