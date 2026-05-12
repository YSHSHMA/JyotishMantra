<div class="row pt-2 p-3 specification">
    <div class="col-12 col-md-12 col-lg-12">
        <div class="accordion" id="accordionExample">

            <div class="cards">
                <div class="card-header" id="heading{{ $faq->id }}">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block  text-left btnClr" type="button" data-toggle="collapse"
                            data-target="#collapse{{ $faq->id }}" aria-expanded="true" aria-controls="collapseOne">
                            {{ $faq->question }}
                        </button>
                    </h2>
                </div>
                <div id="collapse{{ $faq->id }}" class="collapse" aria-labelledby="heading{{ $faq->id }}"
                    data-parent="#accordionExample">
                    <div class="card-body">
                        {!! $faq->detail !!}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
