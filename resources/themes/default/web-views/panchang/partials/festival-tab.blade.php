<div class="tab-pane fade" id="festival" role="tabpanel" aria-labelledby="festival-tab">
    <div class="row">
        <div class="col-md-12">
            <div class="tab-details-block"
                style="padding: 0px !important; padding: 0px !important;height: 598px;overflow-y: scroll;margin-bottom: 10px;">
                <div class="row">
                    <table class="table kundli-basic-details">
                        <tbody>
                            @if (count($festivalData) > 0)
                                @foreach ($festivalData as $festival)
                                    <tr data-title="{{ $festival['event_name_hi'] }}"
                                        data-hidescription="{{ $festival['hi_description'] }}"
                                        data-image="{{ $festival['image'] }}" onclick="fastFestivalModal(this)">
                                        @php
                                            $date = explode(',', $festival['eventDate']);
                                        @endphp
                                        <!-- Date Section -->
                                        <td class="event-date">
                                            <span>{{ date('d', strtotime($date[1])) }}</span>
                                            <div class="event-day">{{ date('l', strtotime($date[1])) }}</div>
                                        </td>
                                        <!-- Event Info Section -->
                                        <td class="event-info">
                                            <h5>{{ $festival['event_name_hi'] }}</h5>
                                            <p class="p1">{{ date('F d, Y, l', strtotime($date[1])) }}</p>January
                                            29,
                                            2024, Monday
                                        </td>
                                        <!-- Image Section -->
                                        <td class="event-image">
                                            <img class="img-fluid img-thumbnail" src="{{ $festival['image'] }}"
                                                alt="{{ $festival['eventName'] }}">
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
