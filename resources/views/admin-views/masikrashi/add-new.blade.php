@extends('layouts.back-end.app')
@section('title', translate('masik_rashi_Add'))
@section('content')
<div class="content container-fluid">
   <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
      <h2 class="h1 mb-0 d-flex align-items-center gap-2">
         <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/masik_rashi.png') }}" alt="">
         {{ translate('masik_rashi_Setup') }}
      </h2>
   </div>
   <div class="row">
      <div class="col-md-12">
         <div class="card mb-3">
            <div class="card-body text-start">
               <form action="{{ route('admin.masikrashi.add-new') }}" method="post" enctype="multipart/form-data">
                  @csrf
                  
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Rashi</label>
                                <select name="name" id="name" class="form-control">
                                    <option value="aries" selected>Aries</option>
                                    <option value="taurus">Taurus</option>
                                    <option value="gemini">Gemini</option>
                                    <option value="cancer">Cancer</option>
                                    <option value="leo">Leo</option>
                                    <option value="virgo">Virgo</option>
                                    <option value="libra">Libra</option>
                                    <option value="scorpio">Scorpio</option>
                                    <option value="sagittarius">Sagittarius</option>
                                    <option value="capricorn">Capricorn</option>
                                    <option value="aquarius">Aquarius</option>
                                    <option value="pisces">Pisces</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Month</label>
                                <select name="month" id="month" class="form-control">
                                    <option value="jan" selected>January</option>
                                    <option value="feb">February</option>
                                    <option value="march">March</option>
                                    <option value="april">April</option>
                                    <option value="may">May</option>
                                    <option value="june">June</option>
                                    <option value="july">July</option>
                                    <option value="august">August</option>
                                    <option value="sep">September</option>
                                    <option value="oct">October</option>
                                    <option value="nov">November</option>
                                    <option value="dec">December</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Language</label>
                                <select name="lang" id="lang" class="form-control">
                                    <option value="hi" selected>Hindi</option>
                                    <option value="en">English</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Akshar</label>
                                <input type="text" name="akshar" class="form-control" id="akshar" value="{{old('akshar')}}" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="input-label d-flex" for="module_type">{{ translate('masik_rashi_detail')}} </label>
                                <textarea class="ckeditor form-control" name="detail" placeholder="Detail"></textarea>
                            </div>
                        </div>
                    </div>
                  <div class="d-flex gap-3 justify-content-end">
                     <button type="reset" id="reset"
                        class="btn btn-secondary px-4">{{ translate('reset') }}</button>
                     <button type="submit" class="btn btn--primary px-4">{{ translate('submit') }}</button>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection
@push('script')
<script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.ckeditor').ckeditor();
    });
</script>

@endpush