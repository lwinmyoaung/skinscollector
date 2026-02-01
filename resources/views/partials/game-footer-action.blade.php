<div class="mt-4 pt-2 border-top mobile-sticky-footer">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
        {{-- Price Section --}}
        <div class="d-flex align-items-baseline gap-2">
            <span class="text-muted small">Total:</span>
            <span class="total-amount fw-bold text-primary fs-5" id="footerTotal">0 Ks</span>
        </div>

        {{-- Action Section --}}
        <div class="d-flex align-items-center justify-content-between gap-2">
            {{-- Quantity --}}
            <div class="input-group shadow-sm" style="width: 110px; border-radius: 8px; overflow: hidden;">
                <button class="btn btn-light border btn-sm" type="button" id="qtyMinus" style="background: #f8f9fa;">
                    <i class="fas fa-minus small"></i>
                </button>
                <input type="number" class="form-control text-center border-top border-bottom bg-white fw-bold form-control-sm" 
                       id="qtyInput" name="quantity" value="1" min="1" readonly style="height: 31px;">
                <button class="btn btn-light border btn-sm" type="button" id="qtyPlus" style="background: #f8f9fa;">
                    <i class="fas fa-plus small"></i>
                </button>
            </div>

            {{-- Buy Button --}}
            <button type="button" class="ml-btn-buy flex-grow-1 d-flex align-items-center justify-content-center gap-2 shadow-sm" 
                    id="submitOrderBtn" style="padding: 8px 20px; border-radius: 8px;">
                <i class="fas fa-shopping-cart"></i>
                <span>ဝယ်ယူမည်</span>
            </button>
        </div>
    </div>
</div>