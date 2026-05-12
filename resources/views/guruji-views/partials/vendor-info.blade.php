<div class="flex-between row align-items-center mx-1">
    <div>
        @if(auth('guruji')->check())
        <h1 class="page-header-title text-capitalize">{{translate('welcome').' '.auth('guruji')->user()->name }}</h1>
        @endif
        <p>{{ translate('monitor_your_individual_service').'.'}}</p>
    </div>
</div>

<div class="col-md-3">
    <div class="card card-body h-100 justify-content-center">
        <div class="d-flex gap-2 justify-content-between align-items-center">
            <div class="d-flex flex-column align-items-start">
                <h3 class="mb-1 fz-24 text-success">
                {{ $activeCounselling }}
                </h3>
                <div class="text-capitalize mb-0">Active Counselling</div>
            </div>
            <div>
                <img width="40" class="mb-2"src="{{dynamicAsset(path: 'public/assets/back-end/img/business_analytics.png')}}"
                    alt="">
            </div>
        </div>
    </div>
</div>
<div class="col-md-3">
    <div class="card card-body h-100 justify-content-center">
        <div class="d-flex gap-2 justify-content-between align-items-center">
            <div class="d-flex flex-column align-items-start">
                <h3 class="mb-1 fz-24 text-success">
                    {{ $activeServices }}
                </h3>
                <div class="text-capitalize mb-0">Active Service</div>
            </div>
            <div>
                <img width="40"
                    class="mb-2" src="{{dynamicAsset(path: 'public/assets/back-end/img/business_analytics.png')}}" alt="">
            </div>
        </div>
    </div>
</div>


