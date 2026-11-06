<div class="col-6 col-md-4 col-lg-3">
    <a href="{{ route('shop.product', $product->slug) }}" class="card product-card h-100 text-decoration-none text-dark position-relative">
        @if ($product->has_discount)
            <span class="badge text-bg-danger position-absolute top-0 start-0 m-2">-{{ $product->discount_percent }}%</span>
        @endif
        <img src="{{ $product->image_url }}" class="card-img-top" alt="{{ $product->name }}" loading="lazy">
        <div class="card-body">
            <h6 class="card-title mb-1 text-truncate" title="{{ $product->name }}">{{ $product->name }}</h6>
            <div class="small text-muted mb-2">{{ $product->subcategory->name ?? '' }}</div>
            <x-price :product="$product" :badge="false" />
        </div>
    </a>
</div>
