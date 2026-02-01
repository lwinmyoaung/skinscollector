@props(['gameKey' => null, 'showZoneId' => true, 'itemSuffix' => '', 'priceSuffix' => ''])

<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2"><strong>Nickname:</strong> <span id="confirmNickname" class="text-primary">Loading...</span></div>
                <div class="mb-2"><strong>Player ID:</strong> <span id="confirmPlayerId"></span></div>
                
                @if($showZoneId)
                <div class="mb-2"><strong>Zone ID:</strong> <span id="confirmServerId"></span></div>
                @endif
                
                <div class="mb-2"><strong>Item:</strong> <span id="confirmItem"></span>{{ $itemSuffix ? ' '.$itemSuffix : '' }}</div>
                <div class="mb-2"><strong>Quantity:</strong> <span id="confirmQuantity"></span></div>
                <div class="mb-2"><strong>Price:</strong> <span id="confirmPrice"></span>{{ $priceSuffix ? ' '.$priceSuffix : '' }}</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="finalSubmitBtn">Confirm & Pay</button>
            </div>
        </div>
    </div>
</div>
