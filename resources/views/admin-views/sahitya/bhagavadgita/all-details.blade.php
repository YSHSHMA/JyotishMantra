@extends('layouts.back-end.app')
@section('title', translate('Bhagavad Gita List'))
@section('content')
@push('css_or_js')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
@endpush
<div class="content container-fluid">
    <div class="row g-2"></div>
    <div class="row mt-20">
        <div class="col-md-12">
            <div class="card">
                @forelse ($bhagavadgitaDetails as $key => $detail)
                <div class="px-3 py-4">
                    <div class="row g-2 flex-grow-1 justify-content-between align-items-center">
                        <h2>{{ $currentChapter ? $currentChapter->name : translate('Chapter not found') }}</h2>
                        <a href="{{ route('admin.bhagavadgita.editVerse', $detail->id) }}" class="btn btn--primary text-nowrap text-capitalize"
                           title="{{ translate('edit') }}">
                             <i class="tio-edit"></i>
                              {{ translate('edit') }}
                        </a>
                    </div>
                    <!-- Content Row -->
                    
                    <div class="row mt-4">

                        <div class="col-md-8">
                            <div class="mb-4">
                                <h4>{{ translate('Chapter') }}: {{ $detail->chapter }}</h4>
                                <h5>{{ translate('Verse') }}: {{ $detail->verse }}</h5>

                                <!-- Show Description Based on Availability -->
                                @if(isset($detail->hi_description) && !empty($detail->hi_description))
                                    <h5>{{ translate('Description') }} :</h5>
                                    <p>{!! $detail->hi_description !!}</p>
                                @else
                                    <h5>{{ translate('Description') }} :</h5> 
                                    <p>{!! $detail->description !!}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Right Column: Image -->
                        <div class="col-md-4">
                            @if($detail->image)
                            <div class="text-center">
                                <img src="{{ asset('storage/app/public/sahitya/bhagavad-gita/' . $detail->image) }}" alt="Image" style="width: 100%; max-width: 300px; height: auto;">
                            </div>
                            @endif
                        </div>
                    </div>
                    <hr>
                    @empty
                    <p>{{ translate('No details available') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endpush
