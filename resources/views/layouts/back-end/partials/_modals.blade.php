<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ translate('ready_to_Leave') . '?' }}</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                {{ translate('Select_Logout_below_if_you_are_ready_to_end_your_current_session') . '.' }}</div>
            <div class="modal-footer">
                <form action="{{ route('admin.logout') }}" method="post">
                    @csrf
                    <button class="btn btn-danger" type="button"
                        data-dismiss="modal">{{ translate('cancel') }}</button>
                    <button class="btn btn--primary" type="submit">{{ translate('logout') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="popup-modal">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex flex-wrap justify-content-center gap-2">
                            <div class="text-center w-100">
                                <h4 class="__color-8a8a8a">
                                    <i class="tio-shopping-cart-outlined"></i>
                                    {{ translate('you_have_new_order') . ', ' . translate('check_please') . '.' }}
                                </h4>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button
                                    class="btn btn--primary check-order">{{ translate('ok') . ',' . translate('let_me_check') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="popup-modal-pooja">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex flex-wrap justify-content-center gap-2">
                            <div class="text-center w-100">
                                <h4 class="__color-8a8a8a"><i class="tio-shopping-cart-outlined"></i>
                                    {{ translate('you_have_new_Pooja_Order') . ', ' . translate('check_please') . '.' }}
                                </h4>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn--primary"
                                    onclick="poojaModal('yes')">{{ translate('ok') . ',' . translate('let_me_check') }}</button>
                                <button type="button" class="btn btn-danger ml-2"
                                    onclick="poojaModal('no')">{{ translate('close') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="popup-modal-offlinepooja">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex flex-wrap justify-content-center gap-2">
                            <div class="text-center w-100">
                                <h4 class="__color-8a8a8a"><i class="tio-shopping-cart-outlined"></i>
                                    {{ translate('you_have_new_OfflinePooja_Order') . ', ' . translate('check_please') . '.' }}
                                </h4>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn--primary"
                                    onclick="offlinepoojaModal('yes')">{{ translate('ok') . ',' . translate('let_me_check') }}</button>
                                <button type="button" class="btn btn-danger ml-2"
                                    onclick="offlinepoojaModal('no')">{{ translate('close') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="popup-modal-counselling">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex flex-wrap justify-content-center gap-2">
                            <div class="text-center w-100">
                                <h4 class="__color-8a8a8a"><i class="tio-shopping-cart-outlined"></i>
                                    {{ translate('you_have_new_counselling_Order') . ', ' . translate('check_please') . '.' }}
                                </h4>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn--primary"
                                    onclick="counsellingModal('yes')">{{ translate('ok') . ',' . translate('let_me_check') }}</button>
                                <button type="button" class="btn btn-danger ml-2"
                                    onclick="counsellingModal('no')">{{ translate('close') }}</button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="popup-modal-vip">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex flex-wrap justify-content-center gap-2">
                            <div class="text-center w-100">
                                <h4 class="__color-8a8a8a"><i class="tio-shopping-cart-outlined"></i>
                                    {{ translate('you_have_new_vip_Order') . ', ' . translate('check_please') . '.' }}
                                </h4>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn--primary"
                                    onclick="vipModal('yes')">{{ translate('ok') . ',' . translate('let_me_check') }}</button>
                                <button type="button" class="btn btn-danger ml-2"
                                    onclick="vipModal('no')">{{ translate('close') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="popup-modal-chadhava">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex flex-wrap justify-content-center gap-2">
                            <div class="text-center w-100">
                                <h4 class="__color-8a8a8a"><i class="tio-shopping-cart-outlined"></i>
                                    {{ translate('you_have_new_chadhava_Order') . ', ' . translate('check_please') . '.' }}
                                </h4>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn--primary"
                                    onclick="chadhavaModal('yes')">{{ translate('ok') . ',' . translate('let_me_check') }}</button>
                                <button type="button" class="btn btn-danger ml-2"
                                    onclick="chadhavaModal('no')">{{ translate('close') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="popup-modal-anushthan">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex flex-wrap justify-content-center gap-2">
                            <div class="text-center w-100">
                                <h4 class="__color-8a8a8a"><i class="tio-shopping-cart-outlined"></i>
                                    {{ translate('you_have_new_Anushthan_Order') . ', ' . translate('check_please') . '.' }}
                                </h4>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn--primary"
                                    onclick="anushthanModal('yes')">{{ translate('ok') . ',' . translate('let_me_check') }}</button>
                                <button type="button" class="btn btn-danger ml-2"
                                    onclick="anushthanModal('no')">{{ translate('close') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="popup-modal-customer">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex flex-wrap justify-content-center gap-2">
                            <div class="text-center w-100">
                                <h4 class="__color-8a8a8a"><i class="tio-user"></i>
                                    {{ translate('you_have_new_customer') . ', ' . translate('check_please') . '.' }}
                                </h4>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn--primary"
                                    onclick="customerListModal('yes')">{{ translate('ok') . ',' . translate('let_me_check') }}</button>
                                <button type="button" class="btn btn-danger ml-2"
                                    onclick="customerListModal('no')">{{ translate('close') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="popup-modal-vendor">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex flex-wrap justify-content-center gap-2">
                            <div class="text-center w-100">
                                <h4 class="__color-8a8a8a"><i class="tio-shopping-cart-outlined"></i>
                                    {{ translate('you_have_new_Vendor_Register') . ', ' . translate('check_please') . '.' }}
                                </h4>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn--primary"
                                    onclick="vendorModal('yes')">{{ translate('ok') . ',' . translate('let_me_check') }}</button>
                                <button type="button" class="btn btn-danger ml-2"
                                    onclick="vendorModal('no')">{{ translate('close') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="qrModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="dateListModalLabel">Generate the QR Code</h5>
                    <p class="mb-0 text-muted" style="font-size: 14px;">Scan this code to access your public profile or
                        link.</p>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.dashboard.grcode') }}" method="POST" id="qrForm">
                    @csrf

                    <div class="form-group">
                        <label for="urlInput">Enter URL</label>
                        <input type="url" name="url" id="urlInput" class="form-control"
                            placeholder="https://mahakal.com" required>
                    </div>

                    <div class="modal-footer px-0">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">
                            {{ translate('Cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary">
                            {{ translate('Generate QR') }}
                        </button>
                    </div>
                </form>

                {{-- Preview will be shown here --}}
                    <div class="mt-4 text-center" id="qrPreview">
                    </div>
            
            </div>

        </div>
    </div>
</div>
