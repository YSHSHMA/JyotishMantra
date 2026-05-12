<div class="product-item col-xl-3 col-lg-4 col-md-4 col-sm-6 col-6">
    <div class="product-horizontal-card mt-2">
        <!-- Image -->
        <div class="phc-image-box">
            <img src="{{ getValidImage('storage/app/public/product/' . $product->thumbnail) }}"
                 alt="{{ $product->name }}">
        </div>

        <!-- Content -->
        <div class="phc-content-box mt-3">

            <!-- Product Name -->
            <h4 class="phc-title two-lines-only product-name">
                {{ $product->name }}
            </h4>

            <!-- Rating -->
            <div class="phc-rating">
                @php($overallRating = getOverallRating($product->reviews))
                <span class="d-inline-block font-size-sm text-body">

                    @for ($inc = 1; $inc <= 5; $inc++)
                        @if ($inc <= (int) $overallRating[0])
                            <i class="tio-star text-warning"></i>
                        @elseif ($overallRating[0] != 0 && $inc <= (int) $overallRating[0] + 1.1 && $overallRating[0] > ((int) $overallRating[0]))
                            <i class="tio-star-half text-warning"></i>
                        @else
                            <i class="tio-star-outlined text-warning"></i>
                        @endif
                    @endfor

                    <label class="badge-style">( {{ count($product->reviews) }} )</label>
                </span>
            </div>

            <!-- Price Row -->
            <div class="phc-price-row">
                <span class="phc-new-price">
                    ₹{{ number_format($product->unit_price) }}
                </span>

                <!-- Quick View Button -->
                <div class="quick-view">
                    <a class="btn-circle phc-cart-btn stopPropagation action-product-quick-view"
                       href="javascript:" data-product-id="{{ $product->id }}">
                        <i class="czi-eye align-middle"></i>
                    </a>
                </div>

                <!-- Out of stock -->
                @if($product->product_type == 'physical' && $product->current_stock <= 0)
                    <span class="out_fo_stock">{{ translate('out_of_stock') }}</span>
                @endif
            </div>

        </div>
    </div>
</div>
