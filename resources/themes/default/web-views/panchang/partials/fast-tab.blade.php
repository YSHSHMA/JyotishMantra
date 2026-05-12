<div class="tab-pane show active" id="fast" role="tabpanel" aria-labelledby="fast-tab">
    <div class="row">
        <div class="col-md-12">
            <div class="tab-details-block" style="padding: 0px !important; padding: 0px !important;height: 598px;overflow-y: scroll;margin-bottom: 10px;">
                <div class="row">
                    <table class="table kundli-basic-details">
                        <tbody>
                            @if (count($fastData) > 0)
                                @foreach ($fastData as $fast)
                                    <tr data-title="{{$fast['event_name_hi']}}" data-hidescription="{{$fast['hi_description']}}" data-image="{{$fast['image']}}" onclick="fastFestivalModal(this)">
                                        @php
                                            $date = explode(',', $fast['eventDate']);
                                        @endphp
                                        <!-- Date Section -->
                                        <td class="event-date">
                                            <span>{{ date('d', strtotime($date[1])) }}</span>
                                            <div class="event-day">{{ date('l', strtotime($date[1])) }}</div>
                                        </td>
                                        <!-- Event Info Section -->
                                        <td class="event-info">
                                            <h5>{{ $fast['event_name_hi'] }}</h5>
                                            <p class="p1">{{ date('F d, Y, l', strtotime($date[1])) }}</p>
                                        </td>
                                        <!-- Image Section -->
                                        <td align="right" class="event-image">
                                            <img class="img-fluid img-thumbnail" src="{{ $fast['image'] }}"
                                                alt="{{ $fast['eventName'] }}">
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                    <p>No Data Found</p>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
