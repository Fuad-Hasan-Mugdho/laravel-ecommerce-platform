@props(['product', 'size' => '', 'badge' => true])
<span class="{{ $size }}">
    <span class="fw-bold text-primary">৳{{ number_format($product->new_price, 2) }}</span>
    @if ($product->has_discount)
        <span class="text-muted text-decoration-line-through ms-1 small">৳{{ number_format($product->old_price, 2) }}</span>
        @if ($badge)
            <span class="badge text-bg-danger ms-1">-{{ $product->discount_percent }}%</span>
        @endif
    @endif
</span>
