@extends('layouts.front-end.app')

@section('title', translate('feedback'))

@section('content')

    <div class="container py-2 py-md-4 p-0 p-md-2 user-profile-container px-5px">
        <div class="row">
            @include('web-views.partials._profile-aside')

            <section class="col-lg-9 __customer-profile customer-profile-wishlist px-0">
                <div class="card __card d-none d-lg-flex web-direction customer-profile-orders">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between gap-2 mb-0 mb-md-3">
                            <h5 class="font-bold mb-0 fs-16">{{ translate('feedback') }}</h5>
                        </div>

                        @if (!empty($feedback))
                            @if ($feedback->status == 1 && $feedback->is_edited == 0)
                            <form action="{{route('feedback-update')}}" method="post">
                                @csrf
                                <div class="form-group">
                                  <label for="message" class="form-lable">Your Message</label>
                                  <textarea class="form-control" name="message" id="" cols="30" rows="5" required></textarea>
                                </div>
                                <div class="form-group text-end">
                                  <input type="submit" class="btn btn--primary" value="Submit">
                                </div>
                            </form>
                            @elseif ($feedback->status == 0 && $feedback->is_edited == 1)
                                <h5 class="text-danger text-center mt-3">Your feedback is in review</h5>
                            @else    
                            <h6 class="text-center">Your Feedback</h6>
                            <div class="col-md-8 offset-md-2 border border-primary p-3">
                                <p class="text-dark">{{ Str::ucfirst($feedback->message) }}</p>
                            </div>
                            @endif
                        @else
                            <form action="{{route('feedback-store')}}" method="post">
                                @csrf
                                <div class="form-group">
                                  <label for="message" class="form-lable">Your Message</label>
                                  <textarea class="form-control" name="message" id="" cols="30" rows="5" required></textarea>
                                </div>
                                <div class="form-group text-end">
                                  <input type="submit" class="btn btn--primary" value="Submit">
                                </div>
                            </form>
                        @endif

                    </div>
                </div>
            </section>
        </div>

    </div>

@endsection
