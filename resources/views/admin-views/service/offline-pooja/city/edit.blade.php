@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app')

@section('title', translate('City_Detail_Update'))

@push('css_or_js')
@endpush
@section('content')
    <div class="content container-fluid">
        <div class="row g-2 flex-grow-1">
            <div class="col-sm-8 col-md-6 col-lg-4">
                <div class="mb-3">
                    <h2 class="h1 mb-0 d-flex gap-2">
                        <img width="20" src="{{ dynamicAsset(path: 'public/assets/back-end/img/rashi.png') }}"
                            alt="">
                        {{ translate('City_Update') }}

                        </span>
                    </h2>
                </div>
            </div>
        </div>

        <div class="row mt-20">
            <div class="col-md-12">
                <form class="product-form text-start" action="{{ route('admin.service.offline.pooja.city.update',$offlinePoojaCity['id']) }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="title-color">{{ translate('city') }}</label>
                                        <select name="city_id" class="form-select js-select2-custom" required>
                                            @forelse ($cities as $item)
                                                <option value="{{ $item->id }}" {{$item->id==$offlinePoojaCity->city_id?'selected':''}} >{{ $item->city }}</option>
                                            @empty
                                                <option value="">City Not Found</option>
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="title-color">{{ translate('pincode') }}</label>
                                        <input type="number" name="pincode" id="" class="form-control"
                                            placeholder="enter pincode" value="{{ $offlinePoojaCity->pincode }}" autocomplete="off"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="title-color">{{ translate('latitude') }}</label>
                                        <input type="text" name="latitude" id="" class="form-control"
                                            placeholder="enter latitude" value="{{ $offlinePoojaCity->latitude }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="title-color">{{ translate('longitude') }}</label>
                                        <input type="text" name="longitude" id="" class="form-control"
                                            placeholder="enter longitude" value="{{ $offlinePoojaCity->longitude }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn--primary px-4">{{ translate('update') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
   
@endsection

@push('script')
   
@endpush
