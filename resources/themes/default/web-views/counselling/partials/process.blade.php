<div class="tab-pane fade show" id="process" role="tabpanel">
    {{-- <div class="row d-flex justify-content-between  mb-3">
        <div>
            <span class="font-bold pl-1">Process</span>
        </div>
    </div> --}}
    <div class="row pt-2 p-3 specification">
        <div class="text-body col-lg-12 col-md-12 overflow-scroll fs-13 text-justify details-text-justify">
            @if ($counsellingDetails['process'])
                {!! $counsellingDetails['process'] !!}
            @else
                <p class="text-center my-5">Process Not Found</p>
            @endif
        </div>
    </div>
</div>
