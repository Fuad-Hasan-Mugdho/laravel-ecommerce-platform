<section class="mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="h5 section-title mb-0">
            <a href="{{ route('shop.subcategory', $subcategory->slug) }}" class="text-decoration-none text-dark">{{ $subcategory->name }}</a>
            <span class="badge text-bg-light fw-normal ms-1">{{ $subcategory->products_count }} items</span>
        </h3>
        @if ($subcategory->products_count > $subcategory->products->count())
            <a href="{{ route('shop.subcategory', $subcategory->slug) }}" class="small">View all <i class="bi bi-arrow-right"></i></a>
        @endif
    </div>
    <div class="row g-3">
        @foreach ($subcategory->products as $product)
            @include('shop.partials.product-card', ['product' => $product->setRelation('subcategory', $subcategory)])
        @endforeach
    </div>
</section>
