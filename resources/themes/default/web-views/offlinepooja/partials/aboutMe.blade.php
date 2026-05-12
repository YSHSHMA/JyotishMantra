<div class="tab-pane fade show active text-justify" id="about_pooja"
  role="tabpanel">
  <div class="row pt-2 specification">
    @if ($details['details'])
    <div
      class="text-body col-lg-12 col-md-12 overflow-scroll fs-13 text-justify details-text-justify">
        {!! $details['details'] !!}
    </div>
    @else
        <p class="text-center my-5">Detail Not Found</p>
    @endif
   
  </div>
</div>

