<div class="d-flex align-items-start justify-content-between gap-2">
    <div>
        <div class="d-flex align-items-center gap-2 text-capitalize">
            <h4 class="text-capitalize mb-0 mobile-fs-14 fs-18 font-bold">{{ translate('order') }}
                #{{ $order->order_id }} </h4>
            <span
                class="status-badge rounded-pill __badge badge-soft-badge-soft-{{ $order->status == 0 ? 'primary' : ($order->status == 1 ? 'success' : 'danger') }} fs-12 font-semibold text-capitalize ">{{ $order->status == 0 ? 'Pending' : ($order->status == 1 ? 'Completed' : 'Canceled') }}</span>
            <span
                class="text-danger">{{ empty($order['counselling_user']) ? '(Note:- Please fill your detail)' : '' }}</span>
        </div>

        <div class="date fs-12 font-semibold text-secondary-50 text-body mb-3 mt-2">
            {{ date('d M, Y h:i A', strtotime($order['created_at'])) }}
        </div>
    </div>

    <button class="profile-aside-btn btn btn--primary px-2 rounded px-2 py-1 d-lg-none">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
            <path fill-rule="evenodd" clip-rule="evenodd"
                d="M7 9.81219C7 9.41419 6.842 9.03269 6.5605 8.75169C6.2795 8.47019 5.898 8.31219 5.5 8.31219C4.507 8.31219 2.993 8.31219 2 8.31219C1.602 8.31219 1.2205 8.47019 0.939499 8.75169C0.657999 9.03269 0.5 9.41419 0.5 9.81219V13.3122C0.5 13.7102 0.657999 14.0917 0.939499 14.3727C1.2205 14.6542 1.602 14.8122 2 14.8122H5.5C5.898 14.8122 6.2795 14.6542 6.5605 14.3727C6.842 14.0917 7 13.7102 7 13.3122V9.81219ZM14.5 9.81219C14.5 9.41419 14.342 9.03269 14.0605 8.75169C13.7795 8.47019 13.398 8.31219 13 8.31219C12.007 8.31219 10.493 8.31219 9.5 8.31219C9.102 8.31219 8.7205 8.47019 8.4395 8.75169C8.158 9.03269 8 9.41419 8 9.81219V13.3122C8 13.7102 8.158 14.0917 8.4395 14.3727C8.7205 14.6542 9.102 14.8122 9.5 14.8122H13C13.398 14.8122 13.7795 14.6542 14.0605 14.3727C14.342 14.0917 14.5 13.7102 14.5 13.3122V9.81219ZM12.3105 7.20869L14.3965 5.12269C14.982 4.53719 14.982 3.58719 14.3965 3.00169L12.3105 0.915687C11.725 0.330188 10.775 0.330188 10.1895 0.915687L8.1035 3.00169C7.518 3.58719 7.518 4.53719 8.1035 5.12269L10.1895 7.20869C10.775 7.79419 11.725 7.79419 12.3105 7.20869ZM7 2.31219C7 1.91419 6.842 1.53269 6.5605 1.25169C6.2795 0.970186 5.898 0.812187 5.5 0.812187C4.507 0.812187 2.993 0.812187 2 0.812187C1.602 0.812187 1.2205 0.970186 0.939499 1.25169C0.657999 1.53269 0.5 1.91419 0.5 2.31219V5.81219C0.5 6.21019 0.657999 6.59169 0.939499 6.87269C1.2205 7.15419 1.602 7.31219 2 7.31219H5.5C5.898 7.31219 6.2795 7.15419 6.5605 6.87269C6.842 6.59169 7 6.21019 7 5.81219V2.31219Z"
                fill="white" />
        </svg>
    </button>
</div>

<div class="order-details-nav overflow-auto nav-menu gap-3 gap-xl-30 mb-4 text-capitalize d-flex">
    <button data-link="{{ route('account-counselling-order-details', ['order_id' => $order->order_id]) }}"
        class="get-view-by-onclick {{ Str::contains(Request::url(), 'account-counselling-order-details') ? 'active' : '' }}">{{ translate('order_summary') }}</button>
    <button data-link="{{ route('account-counselling-order-track', ['order_id' => $order->order_id]) }}"
        class="get-view-by-onclick {{ Str::contains(Request::url(), 'account-counselling-order-track') ? 'active' : '' }}">{{ translate('track_Order') }}</button>
    <button data-link="{{ route('account-counselling-order-user-detail', ['order_id' => $order->order_id]) }}"
        class="get-view-by-onclick {{ Str::contains(Request::url(), 'account-counselling-order-user-detail') ? 'active' : '' }}">{{ translate('user_Detail') }}</button>
    <button data-link="{{ route('account-counselling-review', ['order_id' => $order->order_id]) }}"
        class="get-view-by-onclick {{ Str::contains(Request::url(), 'account-counselling-review') ? 'active' : '' }}"
        @if ($order->status != 1) disabled @endif>{{ translate('reviews') }}</button>
</div>
