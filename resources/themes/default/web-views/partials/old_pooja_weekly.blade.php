@php
    use App\Utils\Helpers;
    use function App\Utils\getNextPoojaDay;
    use function App\Utils\getNextChadhavaDay;
@endphp
<div class="portfolio {{ $poojaD['category']['slug'] }}" data-cat="{{ $poojaD['category']['slug'] }}">
    <div class="portfolio-wrapper">
        
        <div class="card">
            <span class="for-discount-value pooja-badge p-1 pl-2 pr-2 font-bold fs-13">
            <span class="direction-ltr blink d-block">{{ $poojaD['category']['name'] }}
            </span>
            </span>
            @if (!empty($poojaD->thumbnail))
                <a href="{{ route('epooja', $poojaD->slug) }}"><img
                        src="{{ getValidImage(path: 'storage/app/public/pooja/thumbnail/' . $poojaD->thumbnail) }}"
                        class="card-img-top puja-image" alt="..."></a>
            @else
                <a href="{{ route('epooja', $poojaD->slug) }}"><img
                        src="{{ getValidImage(path: 'storage/app/public/company/' . getWebConfig(name: 'loader_gif'), type: 'source', source: theme_asset(path: 'public/assets/front-end/img/kashi-vishwanath-temple.jpg')) }}"
                        class="card-img-top puja-image" alt="..."></a>
            @endif
            <div class="card-body">
                <p class="pooja-heading underborder">{{ strtoupper($poojaD->pooja_heading) }}
            </p>
                <div class="w-bar h-bar bg-gradient mt-2"></div>
                <p class="pooja-name">{{ Str::words($poojaD->name, 20, '...') }}</p>
                <p class="card-text mt-2 mb-2">{{ $poojaD->short_benifits }}</p>
                <p class="pooja-venue"><i class="fa fa-map-marker"></i>{{ $poojaD->pooja_venue }}</p>
                <?php
                $nextDate = '';
                $poojaw = json_decode($poojaD->week_days);
                $timedadat = date('H:i:s', strtotime($poojaD->pooja_time));
                $nextPoojaDay = getNextPoojaDay($poojaw, $timedadat);
                // print_r($nextPoojaDay) ;die;
                if ($nextPoojaDay) {
                    $nextDate = $nextPoojaDay->format('Y-m-d H:i:s');
                }
                ?>
                <p class="pooja-calendar"><i class="fa fa-calendar"></i>
                    {{date('d', strtotime($nextDate)) }},
                    {{ translate(date('F', strtotime($nextDate))) }} ,
                    {{ translate(date('l', strtotime($nextDate))) }} </p>


                <a href="{{ route('epooja', $poojaD->slug) }}"
                    class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold"> {{ translate('GO_PARTICIPATE') }}</a>
            </div>
        </div>
    </div>
</div>
