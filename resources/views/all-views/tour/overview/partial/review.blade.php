<div class="card-body">
    <div class="text-start">
        <div class="table-responsive">
            <table id="datatable_review" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                <thead class="thead-light thead-50 text-capitalize">
                    <tr>
                        <th>{{ translate('SL') }}</th>
                        <th>{{ translate('user_name') }}</th>
                        <th>{{ translate('image') }}</th>
                        <th>{{ translate('Rate') }}</th>
                        <th>{{ translate('comment') }}</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tour_reviews as $key => $items)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td><span>{{ $items['userData']['name']}}</span><br>
                        <span>{{ $items['userData']['phone']}}</span><br></td>
                        <td>
                            @if($items['image'])
                            <div class="custom_upload_input" style=" aspect-ratio: 1;">
                                <div class="img_area_with_preview position-absolute z-index-2">
                                    <img id="pre_img_viewer" class="h-auto aspect-1 bg-white" src="{{ getValidImage(path: 'storage/app/public/event/comment/'.($items['image']??''), type: 'backend-product') }}">
                                </div>
                            </div>
                            @endif
                        </td>
                        <td>{{ $items['star']}}</td>
                        <td>{{$items['comment']}}</td>
                        <td>
                        <form action="{{route('admin.tour_visits.comment-status-update') }}" method="post" id="items-status{{$items['id']}}-form">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$items['id']}}">
                                            <label class="switcher mx-auto">
                                                <input type="checkbox" class="switcher_input toggle-switch-message" name="status" id="items-status{{ $items['id'] }}" value="1" {{ $items['status'] == 1 ? 'checked' : '' }} data-modal-id="toggle-status-modal" data-toggle-id="items-status{{ $items['id'] }}" data-on-image="items-status-on.png" data-off-image="items-status-off.png" data-on-title="{{ translate('Want_to_Turn_ON').' Tour Review '. translate('status') }}" data-off-title="{{ translate('Want_to_Turn_OFF').' Tour Review '.translate('status') }}" data-on-message="<p>{{ translate('if_enabled_this_Tour_Review_will_be_available_on_the_website_and_customer_app') }}</p>" data-off-message="<p>{{ translate('if_disabled_this_Tour_Review_will_be_hidden_from_the_website_and_customer_app') }}</p>">
                                                <span class="switcher_control"></span>
                                            </label>
                                        </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- Pagination for event organizers list -->
    <div class="table-responsive mt-4">
        <div class="d-flex justify-content-lg-end">
        {!! $tour_reviews->appends(request()->except('page3'))->appends(['name' => 'order'])->links('pagination::bootstrap-4') !!}
        </div>
    </div>
    <!-- Message for no data to show -->
    @if(count($tour_reviews) == 0)
    <div class="text-center p-4">
        <img class="mb-3 w-160" src="{{ dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg') }}" alt="{{ translate('image') }}">
        <p class="mb-0">{{ translate('no_data_to_show') }}</p>
    </div>
    @endif


</div>