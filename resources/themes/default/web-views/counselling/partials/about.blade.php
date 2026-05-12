<div class="tab-pane fade show active text-justify" id="about_pooja" role="tabpanel">
    <div class="row pt-2 p-3 specification">
        <div class="text-body col-lg-12 col-md-12 overflow-scroll fs-13 text-justify details-text-justify">
            @if ($counsellingDetails['details'])
                {!! $counsellingDetails['details'] !!}
            @else
                <p class="text-center my-5">Detail Not Found</p>
            @endif

            @if ($counsellingDetails['video_url'] != null)
                <div class="col-12 mb-4">
                    <iframe width="420" height="315" src="{{ $counsellingDetails['video_url'] }}">
                    </iframe>
                </div>
            @endif
        </div>
    </div>
</div>
