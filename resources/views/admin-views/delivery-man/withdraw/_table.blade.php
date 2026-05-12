@php use App\Utils\Helpers; @endphp
<div class="table-responsive">
    <table id="datatable"
           class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
        <thead class="thead-light thead-50 text-capitalize">
        <tr>
            <th>{{translate('SL')}}</th>
            <th>{{translate('amount')}}</th>
            <th>{{translate('name') }}</th>
            <th>{{translate('request_time')}}</th>
            <th class="text-center">{{translate('status')}}</th>
            @if (Helpers::modules_permission_check('Delivery Men', 'Withdraw', 'detail'))
            <th class="text-center">{{translate('action')}}</th>
            @endif
        </tr>
        </thead>
        <tbody>
            @foreach($withdrawRequests as $key=>$withdraw)
            <tr>
                <td>{{$withdrawRequests->firstItem()+$key}}</td>
                <td>{{setCurrencySymbol(amount: usdToDefaultCurrency(amount: $withdraw['amount']), currencyCode: getCurrencyCode())}}</td>

                <td>
                    @if ($withdraw->deliveryMan)
                        <span
                            class="title-color hover-c1">{{ $withdraw->deliveryMan->f_name . ' ' . $withdraw->deliveryMan->l_name }}</span>
                    @else
                        <span>{{translate('not_found')}}</span>
                    @endif
                </td>
                <td>{{ date_format( $withdraw->created_at, 'd-M-Y, h:i:s A') }}</td>
                <td class="text-center">
                    @if($withdraw->approved==0)
                        <label class="badge badge-soft-primary">{{translate('pending')}}</label>
                    @elseif($withdraw->approved==1)
                        <label class="badge badge-soft-success">{{translate('approved')}}</label>
                    @else
                        <label class="badge badge-soft-danger">{{translate('denied')}}</label>
                    @endif
                </td>
                @if (Helpers::modules_permission_check('Delivery Men', 'Withdraw', 'detail'))
                <td>
                    <div class="d-flex justify-content-center">
                        @if (isset($withdraw->deliveryMan))
                            <button
                                class="btn btn-outline-info btn-sm square-btn withdraw-info-show"
                                data-action="{{route('admin.delivery-man.withdraw-view',[$withdraw['id']])}}"
                                title="{{translate('view')}}">
                                <i class="tio-invisible"></i>
                            </button>
                        @else
                            <a class="btn btn-outline-info btn-sm square-btn disabled" href="#">
                                <i class="tio-invisible"></i>
                            </a>
                        @endif
                    </div>
                </td>
                @endif
            </tr>
        @endforeach
        </tbody>
    </table>
    @if(count($withdrawRequests)==0)
        <div class="text-center p-4">
            <img class="mb-3 w-160"
                 src="{{dynamicAsset(path: 'public/assets/back-end/svg/illustrations/sorry.svg')}}"
                 alt="{{translate('image_description')}}">
            <p class="mb-0">{{translate('no_data_to_show')}}</p>
        </div>
    @endif
</div>
