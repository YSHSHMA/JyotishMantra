@extends('layouts.back-end.app')
@section('title', translate('varshik_rashi_edit'))
@section('content')
<div class="content container-fluid">
   <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
      <h2 class="h1 mb-0 d-flex align-items-center gap-2">
         <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/varshik_rashi.png') }}" alt="">
         {{ translate('varshik_rashi_edit') }}
      </h2>
   </div>
   <div class="row">
      <div class="col-md-12">
         <div class="card mb-3">
            <div class="card-body text-start">
               <form action="{{ route('admin.varshikrashi.update',[$varshikrashi['id']]) }}" method="post" enctype="multipart/form-data">
                  @csrf
                  
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Rashi</label>
                                <select name="name" id="name" class="form-control">
                                    <option value="aries" {{ (( $varshikrashi['name'] == 'aries' ) ?'selected':'' ) }}>Aries</option>
                                    <option value="taurus" {{ (( $varshikrashi['name'] ==  'taurus' ) ?'selected':'' ) }}>Taurus</option>
                                    <option value="gemini" {{ (( $varshikrashi['name'] ==  'gemini' ) ?'selected':'' ) }}>Gemini</option>
                                    <option value="cancer" {{ (( $varshikrashi['name'] ==  'cancer' ) ?'selected':'' ) }}>Cancer</option>
                                    <option value="leo" {{ (( $varshikrashi['name'] ==  'leo' ) ?'selected':'' ) }}>Leo</option>
                                    <option value="virgo" {{ (( $varshikrashi['name'] ==  'virgo' ) ?'selected':'' ) }}>Virgo</option>
                                    <option value="libra" {{ (( $varshikrashi['name'] ==  'libra' ) ?'selected':'' ) }}>Libra</option>
                                    <option value="scorpio" {{ (( $varshikrashi['name'] ==  'scorpio' ) ?'selected':'' ) }}>Scorpio</option>
                                    <option value="sagittarius" {{ (( $varshikrashi['name'] ==  'sagittarius' ) ?'selected':'' ) }}>Sagittarius</option>
                                    <option value="capricorn" {{ (( $varshikrashi['name'] ==  'capricorn' ) ?'selected':'' ) }}>Capricorn</option>
                                    <option value="aquarius" {{ (( $varshikrashi['name'] ==  'aquarius' ) ?'selected':'' ) }}>Aquarius</option>
                                    <option value="pisces" {{ (( $varshikrashi['name'] ==  'pisces' ) ?'selected':'' ) }}>Pisces</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Language</label>
                                <select name="lang" id="lang" class="form-control">
                                    <option value="hi" {{ (( $varshikrashi['lang'] ==  'hi' ) ?'selected':'' ) }}>Hindi</option>
                                    <option value="en" {{ (( $varshikrashi['lang'] ==  'en' ) ?'selected':'' ) }}>English</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Akshar</label>
                                <input type="text" name="akshar" class="form-control" id="akshar" value="{{old('akshar',$varshikrashi['akshar'])}}" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="input-label d-flex" for="module_type">{{ translate('varshik_rashi_detail')}} </label>
                                <textarea class="ckeditor form-control" name="detail" placeholder="Detail"> {{old('detail',$varshikrashi['detail'])}}</textarea>
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
<!-- <script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script> -->
<script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.ckeditor').ckeditor();
    });
</script>

@endpush