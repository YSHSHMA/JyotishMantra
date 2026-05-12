@php use App\Utils\Helpers; @endphp
@extends('layouts.back-end.app-trustees')

@section('title', 'Puja Edit')

@section('content')
@php
if (auth('trust')->check()) {
$relationEmployees = auth('trust')->user()->relation_id;
} elseif (auth('trust_employee')->check()) {
$relationEmployees = auth('trust_employee')->user()->relation_id;
} elseif (auth('purohit')->check()) {
$relationEmployees = (\App\Models\Purohit::with(['temple'])->where('id',auth('purohit')->user()->id)->first()['temple']['trust_id']??0);
}
@endphp
<div class="content container-fluid">
    <div class="mb-3">
        <h2 class="h1 mb-0 d-flex gap-2">
            <img src="{{ dynamicAsset(path: 'public/assets/back-end/img/') }}" alt="">Puja Edit
        </h2>
    </div>
    <div class="row">
        <div class="col-md-12 mb-2">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('trustees-vendor.puja-management.puja-update',['id'=>$getpuja['id']]) }}" method="post">
                        @csrf
                        <div class='row'>
                            <div class="col-md-4 form-group">
                                <label for=""></label>
                                <input type="text" name="puja_name" autocomplete="off" value="{{ old('puja_name',$getpuja['puja_name']) }}" class="form-control" placeholder="Enter Puja Name">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for=""></label>
                                <input type="text" name="rprice" class="form-control" autocomplete="off" value="{{ old('rprice',$getpuja['rprice']) }}" placeholder="Enter Retailer Price" onkeyup="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for=""></label>
                                <input type="text" name="pprice" class="form-control" autocomplete="off" value="{{ old('pprice',$getpuja['pprice']) }}" placeholder="Enter Purchase Price" onkeyup="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
                            </div>
                            <div class="col-md-12">
                                <div class="form-group float-end">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

@endpush