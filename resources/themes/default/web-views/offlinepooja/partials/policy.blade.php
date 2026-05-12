<div class="tab-pane fade show" id="policy" role="tabpanel">
    <div class="row pt-2 specification">
        <div class="text-body col-md-12 overflow-scroll fs-13 text-justify details-text-justify">
            <div class="row">
                <div class="col-md-6" style="padding: 10px;">
                    <div style="background-color: #FBEBEB;">
                        <h5 style="padding-left: 20px; padding-top: 20px;">Refund Policy</h5>
                        @if (count($refundPolicy) > 0)
                            @foreach ($refundPolicy as $refund)
                                <div class="accordion" id="accordionExample">
                                    <div class="cards">
                                        <div class="card-header" id="heading{{ $refund->id }}">
                                            <h2 class="mb-0">
                                                <button class="btn btn-link btn-block  text-left btnClr" type="button"
                                                    data-toggle="collapse" data-target="#collapse{{ $refund->id }}"
                                                    aria-expanded="true" aria-controls="collapseOne">
                                                    {{ 'Day ' . $refund->days . ' - ' . $refund->percent . '%' }}
                                                </button>
                                            </h2>
                                        </div>
                                        <div id="collapse{{ $refund->id }}" class="collapse"
                                            aria-labelledby="heading{{ $refund->id }}"
                                            data-parent="#accordionExample">
                                            <div class="card-body">
                                                {!! $refund->message !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="col-md-6" style="padding: 10px;">
                    <div style="background-color: #EAFAEA;">
                        <h5 style="padding-left: 20px; padding-top: 20px;">Reschedule Policy</h5>
                        @if (count($schedulePolicy) > 0)
                            @foreach ($schedulePolicy as $schedule)
                                <div class="accordion" id="accordionExample">
                                    <div class="cards">
                                        <div class="card-header" id="heading{{ $schedule->id + 100 }}">
                                            <h2 class="mb-0">
                                                <button class="btn btn-link btn-block  text-left btnClr" type="button"
                                                    data-toggle="collapse" data-target="#collapse{{ $schedule->id + 100 }}"
                                                    aria-expanded="true" aria-controls="collapseOne">
                                                    {{ 'Day ' . $schedule->days . ' - ' . $schedule->percent . '%' }}
                                                </button>
                                            </h2>
                                        </div>
                                        <div id="collapse{{ $schedule->id + 100 }}" class="collapse"
                                            aria-labelledby="heading{{ $schedule->id + 100 }}"
                                            data-parent="#accordionExample">
                                            <div class="card-body">
                                                {!! $schedule->message !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
