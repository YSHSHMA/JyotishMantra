@if ($nearbyTemple->count() > 0)
<div class="row mt-2 p-2 partial-pooja">
     <div class="col-12 pt-3">
            <div class="col-12 feature-product-title mt-0 text-center">
                {{ translate('nearby_Temple') }}
                <h4 class="mt-2 height-10">
                    <span class="divider">&nbsp;</span>
                </h4>
            </div>
            <div class="col-12 p-0 feature-product">
                <div class="carousel-wrap p-1">
                    <div class="owl-carousel owl-theme nearby-slider-1">
                        @foreach($nearbyTemple as $temple)
                        <div>
                            <div class="product-single-hover shadow-none rtl">
                                <div class="overflow-hidden position-relative">
                                    <div class="inline_product clickable">
                                        <a href="{{route('temple-details',[$temple['slug']])}}">                                          
                                            <img src="{{ getValidImage(path: 'storage/app/public/temple/thumbnail/'.$temple['thumbnail'], type: 'product') }}" alt="">
                                        </a>

                                        <div class="quick-view">
                                            <a href="{{route('temple-details',[$temple['slug']])}}" class="btn-circle stopPropagation action-product-quick-view">
                                                <i class="czi-eye align-middle"></i>
                                            </a>
                                        </div>

                                    </div>
                                    <div class="single-product-details">
                                    @php 
                                    $createquery = \App\Models\TempleReview::where('temple_id',$temple['id'])->where('status',1);
                                    $overallRating = $createquery->avg('star');
                                    $reviewcount = $createquery->count();
                                    @endphp
                                    @if($overallRating > 0 )
                                    <div class="rating-show justify-content-between">
                                        <span class="d-inline-block font-size-sm text-body">
                                            @for($inc=1;$inc<=5;$inc++)
                                                @if ($inc <=(int)$overallRating)
                                                <i class="tio-star text-warning"></i>
                                                @elseif ($overallRating != 0 && $inc <= (int)$overallRating + 1.1 && $overallRating> ((int)$overallRating))
                                                    <i class="tio-star-half text-warning"></i>
                                                    @else
                                                    <i class="tio-star-outlined text-warning"></i>
                                                    @endif
                                                    @endfor
                                                    <label class="badge-style">( {{ ($reviewcount??0) }} )</label>
                                        </span>
                                    </div>
                                    @endif
                                        <div>
                                            <a href="{{route('temple-details',[$temple['slug']])}}" class="text-capitalize fw-semibold">
                                                {{ ucwords($temple['name']) }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
    @endif