<div class="tab-pane fade show" id="temple_details" role="tabpanel">
    <div class="row pt-2 p-3 specification">
        @if ($vip['temple_details'])
            <div class="text-body col-lg-12 col-md-12 overflow-scroll fs-13 text-justify details-text-justify">
                {!! $vip['temple_details'] !!}
            </div>
        @endif
    </div>
</div>
