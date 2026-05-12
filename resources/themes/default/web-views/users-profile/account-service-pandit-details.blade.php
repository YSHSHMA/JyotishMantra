@extends('layouts.front-end.app')
@section('title', translate('order_Details'))
@section('content')
    <div class="container pb-5 mb-2 mb-md-4 mt-3 rtl __inline-47 text-align-direction">
        <div class="row g-3">
            @include('web-views.partials._profile-aside')
            <section class="col-lg-9">
                @include('web-views.users-profile.service-details.service-order-partial')

                <div class="bg-white border-lg rounded mobile-full shadow-sm">
                    <div class="p-lg-3 p-0">
                        <div class="card border-0 shadow-sm rounded-lg">
                            <div class="p-lg-3 p-2">
                                <div class="payment mb-lg-3">
                                    <div class="row g-4">
                                        <!-- Pandit Info -->
                                        <div class="col-md-6">
                                            <div class="p-3 border rounded h-100 bg-light">
                                                <h6 class="fs-16 font-bold text-capitalize mb-3 text-dark">
                                                    {{ translate('pandit_info') }}
                                                </h6>

                                                @if (!empty($order->pandit))
                                                    <p class="fs-14 mb-2">
                                                        <span class="text-muted">{{ translate('Pandit Name') }}:</span>
                                                        <span
                                                            class="text-primary fw-semibold">{{ $order->pandit->name }}</span>
                                                    </p>
                                                    <p class="fs-14 mb-2">
                                                        <span class="text-muted">{{ translate('Temple') }}:</span>
                                                        <span
                                                            class="text-primary fw-semibold">{{ $order->pandit->is_pandit_primary_mandir }}</span>
                                                    </p>
                                                    <p class="fs-14">
                                                        <span class="text-muted">{{ translate('Location') }}:</span>
                                                        <span
                                                            class="text-primary fw-semibold">{{ $order->pandit->is_pandit_primary_mandir_location }}</span>
                                                    </p>
                                                @else
                                                    <p class="fs-14 text-muted">
                                                        {{ translate('waiting_for_pandit') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Member Info -->
                                        <div class="col-md-6">
                                            <div class="p-3 border rounded h-100 bg-light">
                                                <h6 class="fs-16 font-bold text-capitalize mb-3 text-dark">
                                                    {{ translate('Attended Pooja Members') }}
                                                    <span class="text-muted fs-14">
                                                        ({{ date('d F Y, l', strtotime($order->booking_date)) }})
                                                    </span>
                                                </h6>

                                                @php
                                                    $members = json_decode($order->members);
                                                    $gotras = json_decode($order->gotra);
                                                @endphp

                                                @if (!empty($members))
                                                    @foreach ($members as $index => $member)
                                                        <div class="mb-2">
                                                            <span
                                                                class="fs-14 text-muted">{{ translate('Member Name') }}:</span>
                                                            <span
                                                                class="fs-14 text-primary fw-semibold">{{ ucwords($member) }}</span>
                                                            @if (!empty($gotras[$index]))
                                                                <br>
                                                                <span
                                                                    class="fs-14 text-muted">{{ translate('Gotra') }}:</span>
                                                                <span
                                                                    class="fs-14 text-success fw-semibold">{{ $gotras[$index] }}</span>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <p class="fs-14 text-muted">
                                                        {{ translate('No Members Found') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div> <!-- row end -->

                                    @php
                                        // Convert YouTube links to embed links
                                        function getYoutubeEmbed($url)
                                        {
                                            if (empty($url)) {
                                                return null;
                                            }
                                            $patterns = [
                                                '/youtu\.be\/([^\?&#]+)/i',
                                                '/youtube\.com\/watch\?v=([^\?&#]+)/i',
                                                '/youtube\.com\/embed\/([^\?&#]+)/i',
                                                '/youtube\.com\/shorts\/([^\?&#]+)/i',
                                            ];
                                            foreach ($patterns as $pattern) {
                                                if (preg_match($pattern, $url, $matches)) {
                                                    return 'https://www.youtube.com/embed/' . $matches[1];
                                                }
                                            }
                                            return str_contains($url, 'youtube.com/embed/') ? $url : null;
                                        }

                                        $liveEmbed = getYoutubeEmbed($order->live_stream ?? null);
                                        $poojaEmbed = getYoutubeEmbed($order->pooja_video ?? null);
                                    @endphp

                                    @if ($liveEmbed || $poojaEmbed)
                                        <div class="mt-4">
                                            <h6
                                                class="fs-16 font-bold text-capitalize mb-3 text-dark d-flex align-items-center gap-2">
                                                {{ translate('Live / Recorded Stream') }}
                                                @if ($liveEmbed)
                                                    <span class="badge bg-danger d-inline-flex align-items-center gap-1">
                                                        <span class="rounded-circle"
                                                            style="width:8px;height:8px;background:#fff;display:inline-block;"></span>
                                                        LIVE
                                                    </span>
                                                @endif
                                            </h6>

                                            <!-- Live Stream -->
                                            @if ($liveEmbed)
                                                <div class="mb-3">
                                                    <div class="video-frame position-relative rounded shadow" style="overflow:hidden; border: 4px solid #fe9802;">
                                                        <iframe src="{{ $liveEmbed }}" title="Live Stream"
                                                            class="w-100 rounded" style="height:300px;" frameborder="0"
                                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                            allowfullscreen>
                                                        </iframe>
                                                        <!-- Logo Overlay -->
                                                        <div class="video-logo position-absolute top-0 start-0 p-2"
                                                            style="background: rgba(0,0,0,0.5); border-bottom-right-radius: 8px;">
                                                            <img  src="{{ getValidImage(path: 'storage/app/public/company/' . $web_config['mob_logo']->value, type: 'logo') }}" alt="{{ $web_config['name']->value }}"  style="height: 40px;"/>
                                                        </div>
                                                    </div>
                                                    @if (!empty($order->live_created_stream))
                                                        <div class="text-muted fs-12 mt-1">
                                                            {{ date('h:i A, d F Y', strtotime($order->live_created_stream)) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif

                                            <!-- Recorded Pooja Video -->
                                            @if ($poojaEmbed)
                                                <div class="mb-3">
                                                    <div class="video-frame position-relative rounded shadow" style="overflow:hidden; border: 4px solid #fe9802;">
                                                        <iframe src="{{ $poojaEmbed }}"  title="Live Stream"
                                                            class="w-100 rounded" style="height:300px;" frameborder="0"
                                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                            allowfullscreen>
                                                        </iframe>
                                                    </div>
                                                    @if (!empty($order->video_created_sharing))
                                                        <div class="text-muted fs-12 mt-1">
                                                            {{ date('h:i A, d F Y', strtotime($order->video_created_sharing)) }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/spartan-multi-image-picker.js') }}"></script>
    <script src="{{ theme_asset(path: 'public/assets/front-end/js/account-order-details.js') }}"></script>
@endpush