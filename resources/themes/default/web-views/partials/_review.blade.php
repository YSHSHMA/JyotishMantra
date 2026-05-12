<div class="mb-4" style="border:1px solid #99c7fd99;">
    <div class="card shadow-sm border-0 rounded-4 h-100 text-center p-4 d-flex flex-column" style="min-height: 450px;">
        <div class="" style="display: flex; justify-content: space-between; align-items: center">
        <img src="{{ $review->is_anonymous == 1 ? 'https://cdn-icons-png.flaticon.com/512/4140/4140048.png' : ($review->profile_image!='def.png' ? theme_asset(path: 'storage/app/public/general-review/' . $review->profile_image) : 'https://cdn-icons-png.flaticon.com/512/4140/4140048.png') }}"
            class="rounded-circle border shadow-sm mb-3" style="width: 90px; height: 90px; object-fit: cover;"
            alt="User">

            <div>
        <h6 class="fw-bold mb-0">{{ $review->is_anonymous == 1 ? 'Anonymous' : ucfirst($review->user_name) }}</h6>

        <div class="mb-3">
            @for ($i = 1; $i <= 5; $i++)
                @if ($i <= $review->star_rating)
                    <i class="fa fa-star text-warning"></i>
                @else
                    <i class="fa fa-star text-secondary"></i>
                @endif
            @endfor
        </div>
    </div>
    </div>

    <p class="review-text" id="review-{{ $review->id }}">
        {{ $review->review_text }}
    </p>
    
    <a href="javascript:void(0);" 
       class="read-more-btn text-primary"
       data-target="review-{{ $review->id }}">
        Read More
    </a>

        @if ($review->video_url)
        <div class=" mb-2" style="min-height: 180px; display: flex; align-items: center">
            <iframe src="{{ $review->video_url }}" 
                allowfullscreen 
                style="width:100%; height:100%; border:0;"></iframe>
        </div>
        @endif

    </div>
</div>
