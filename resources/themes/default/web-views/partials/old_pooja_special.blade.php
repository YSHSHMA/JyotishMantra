<?php
$current_date = date('Y-m-d');
$earliestDate = null;
$earliestTime = null;
if (isset($poojaD->schedule) && !empty($poojaD->schedule)) {
    $event_date = json_decode($poojaD->schedule);
    usort($event_date, function ($a, $b) {
        return strtotime($a->schedule) - strtotime($b->schedule);
    });
    foreach ($event_date as $entry) {
        $dt = date('Y-m-d', strtotime($entry->schedule));
        if (strtotime($dt) > strtotime($current_date)) {
            $earliestDate = $dt;
            break;
        }
    }
}

// pick last date
$jsonData = $poojaD->schedule;
$data = json_decode($jsonData, true);
$collection = collect($data);
$sorted = $collection->sortBy('schedule');
$lastSchedule = $sorted->last();
$lastScheduleDate = !empty($lastSchedule) ? strtotime($lastSchedule['schedule']) : '';
$todayDate = strtotime(date('Y-m-d'));
?>

@if ($todayDate < $lastScheduleDate)
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
                    <p class="pooja-heading underborder">{{ strtoupper($poojaD->pooja_heading) }}</p>
                <div class="w-bar h-bar bg-gradient mt-2"></div>
                    <p class="pooja-name">{{ Str::words($poojaD->name, 20, '...') }}</p>
                    <p class="card-text mt-2 pb-2">{{ $poojaD->short_benifits }}</p>
                    <p class="pooja-venue"> <i class="fa fa-map-marker"></i>{{ $poojaD->pooja_venue }} </p>

                    <p class="pooja-calendar"><i class="fa fa-calendar"></i>
                        <?php if ($earliestDate !== null): ?>
                        {{date('d', strtotime($earliestDate)) }},
                        {{ translate(date('F', strtotime($earliestDate))) }} ,
                        {{ translate(date('l', strtotime($earliestDate))) }}
                        <?php else: ?> <?php endif; ?>
                        {{ date('l', strtotime($earliestDate)) }},</p>


                    <a href="{{ route('epooja', $poojaD->slug) }}"
                        class="btn btn--primary btn-block btn-shadow mt-4 font-weight-bold"> {{ translate('GO_PARTICIPATE') }}</a>
                </div>
            </div>
        </div>
    </div>
@endif
