<div class="card remove-card-shadow h-100">
                                <div class="card-body p-3 p-sm-4">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-md-6">
                                            <h4 class="d-flex align-items-center text-capitalize gap-10 mb-0">
                                                <img src="{{dynamicAsset(path: 'public/assets/back-end/img/order-statistics.png')}}"
                                                    alt="">
                                                {{translate('order_statistics')}}
                                            </h4>
                                        </div>
                                        @php($dateType = session()->get('statistics_type')??'yearEarn')
                                        <div class="col-md-6 d-flex justify-content-center justify-content-md-end order-stat mb-3">
                                            <ul class="option-select-btn order-statistics-option">
                                                <li>
                                                    <label class="basic-box-shadow">
                                                        <input type="radio" name="statistics4" hidden="" value="yearEarn" {{$dateType == 'yearEarn' ? 'checked' : ''}}>
                                                        <span data-date-type="yearEarn" class="order-statistics" onclick="orderStatistics(this)">{{translate('this_Year')}}</span>
                                                    </label>
                                                </li>
                                                <li>
                                                    <label class="basic-box-shadow">
                                                        <input type="radio" name="statistics4" value="MonthEarn" hidden="" {{$dateType == 'MonthEarn' ? 'checked' : ''}}>
                                                        <span data-date-type="MonthEarn" class="order-statistics" onclick="orderStatistics(this)">{{translate('this_Month')}}</span>
                                                    </label>
                                                </li>
                                                <li>
                                                    <label class="basic-box-shadow">
                                                        <input type="radio" name="statistics4" value="WeekEarn" hidden="" {{$dateType == 'WeekEarn' ? 'checked' : ''}}>
                                                        <span data-date-type="WeekEarn" class="order-statistics" onclick="orderStatistics(this)">{{translate('this_Week')}}</span>
                                                    </label>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div id="apex-line-chart"></div>
                                </div>
                            </div>
                            <span id="order-statistics" data-action="{{route('trustees-vendor.dashboard.order-statistics')}}"></span>
                            <span id="order-statistics-data" data-inhouse-text="inhouse" data-vendor-text="vendor" data-inhouse-order-earn="{{json_encode($month_amount) }}" data-vendor-order-earn="{{json_encode($month_days)}}" data-label="{{json_encode(['Jan','Feb','Mar','April','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'])}}"></span>
