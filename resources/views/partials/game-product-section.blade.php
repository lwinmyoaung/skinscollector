@if($products->count() > 0)
<div class="category-header">
    <i class="{{ $icon }}"></i>
    <span>{{ $title }}</span>
</div>
<div class="row row-cols-1 g-2 mb-4">
    @foreach($products as $product)
        <div class="col">
            <input type="radio" class="btn-check" name="product_id" 
                id="prod_{{ $product->product_id }}" 
                value="{{ $product->product_id }}"
                data-price="{{ number_format($product->price) }} Ks"
                data-raw-price="{{ $product->price }}"
                {{ isset($firstProductId) && $firstProductId == $product->product_id ? 'checked' : '' }}
                required>
            <label class="product-btn w-100 h-100 d-flex justify-content-between align-items-center" for="prod_{{ $product->product_id }}">
                <span class="product-name">{{ $product->name ?? $product->product_name }}</span>
                <span class="product-price">{{ number_format($product->price) }} Ks</span>
            </label>
        </div>
    @endforeach
</div>
@endif
