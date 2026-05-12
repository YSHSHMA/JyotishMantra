@extends('layouts.back-end.app')
@section('title', translate('masik_rashi_Edit'))
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
               <form action="{{ route('admin.masikrashi.edits',['id'=>$masikrashi['id']]) }}" method="post" enctype="multipart/form-data">
                  @csrf
                  
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Rashi</label>
                                <select name="name" id="name" class="form-control">
                                    <option value="aries"  {{ (( $masikrashi['name'] ==  'aries' ) ?'selected':'' ) }}>Aries</option>
                                    <option value="taurus" {{ (( $masikrashi['name'] ==  'taurus' ) ?'selected':'' ) }}>Taurus</option>
                                    <option value="gemini" {{ (( $masikrashi['name'] ==  'gemini' ) ?'selected':'' ) }}>Gemini</option>
                                    <option value="cancer" {{ (( $masikrashi['name'] ==  'cancer' ) ?'selected':'' ) }}>Cancer</option>
                                    <option value="leo" {{ (( $masikrashi['name'] ==  'leo' ) ?'selected':'' ) }}>Leo</option>
                                    <option value="virgo" {{ (( $masikrashi['name'] ==  'virgo' ) ?'selected':'' ) }}>Virgo</option>
                                    <option value="libra" {{ (( $masikrashi['name'] ==  'libra' ) ?'selected':'' ) }}>Libra</option>
                                    <option value="scorpio" {{ (( $masikrashi['name'] ==  'scorpio' ) ?'selected':'' ) }}>Scorpio</option>
                                    <option value="sagittarius" {{ (( $masikrashi['name'] ==  'sagittarius' ) ?'selected':'' ) }}>Sagittarius</option>
                                    <option value="capricorn" {{ (( $masikrashi['name'] ==  'capricorn' ) ?'selected':'' ) }}>Capricorn</option>
                                    <option value="aquarius" {{ (( $masikrashi['name'] ==  'aquarius' ) ?'selected':'' ) }}>Aquarius</option>
                                    <option value="pisces" {{ (( $masikrashi['name'] ==  'pisces' ) ?'selected':'' ) }}>Pisces</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Month</label>
                                <select name="month" id="month" class="form-control">
                                    <option value="jan" {{ (( $masikrashi['month'] ==  'jan' ) ?'selected':'' ) }}>January</option>
                                    <option value="feb" {{ (( $masikrashi['month'] ==  'feb' ) ?'selected':'' ) }}>February</option>
                                    <option value="march" {{ (( $masikrashi['month'] ==  'march' ) ?'selected':'' ) }}>March</option>
                                    <option value="april" {{ (( $masikrashi['month'] ==  'april' ) ?'selected':'' ) }}>April</option>
                                    <option value="may" {{ (( $masikrashi['month'] ==  'may' ) ?'selected':'' ) }}>May</option>
                                    <option value="june" {{ (( $masikrashi['month'] ==  'june' ) ?'selected':'' ) }}>June</option>
                                    <option value="july" {{ (( $masikrashi['month'] ==  'july' ) ?'selected':'' ) }}>July</option>
                                    <option value="august" {{ (( $masikrashi['month'] ==  'august' ) ?'selected':'' ) }}>August</option>
                                    <option value="sep" {{ (( $masikrashi['month'] ==  'sep' ) ?'selected':'' ) }}>September</option>
                                    <option value="oct" {{ (( $masikrashi['month'] ==  'oct' ) ?'selected':'' ) }}>October</option>
                                    <option value="nov" {{ (( $masikrashi['month'] ==  'nov' ) ?'selected':'' ) }}>November</option>
                                    <option value="dec" {{ (( $masikrashi['month'] ==  'dec' ) ?'selected':'' ) }}>December</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Language</label>
                                <select name="lang" id="lang" class="form-control">
                                    <option value="hi" {{ (( $masikrashi['lang'] ==  'hi' ) ?'selected':'' ) }}>Hindi</option>
                                    <option value="en" {{ (( $masikrashi['lang'] ==  'en' ) ?'selected':'' ) }}>English</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Akshar</label>
                                <input type="text" name="akshar" class="form-control ckeditor" id="akshar" value="{{old('akshar',$masikrashi['akshar'])}}" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="input-label d-flex" for="module_type">{{ translate('masik_rashi_detail')}} </label>
                                <textarea class="ckeditor form-control" name="detail" placeholder="Detail">{{old('detail',$masikrashi['detail'])}}</textarea>
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
<script src="{{ dynamicAsset(path: 'public/js/ckeditor.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.ckeditor').ckeditor();
    });
</script>

@endpush