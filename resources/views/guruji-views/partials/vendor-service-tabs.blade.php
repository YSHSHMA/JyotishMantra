
    <ul class="nav nav-pills page-header-tabs gap-2 mb-3 bg-white p-2 rounded shadow-sm">

        <li class="nav-item">
            <a class="nav-link px-4
                {{ request()->routeIs('guruji.services.puja.individual.view') ? 'active' : '' }}"
               href="{{ route('guruji.services.puja.individual.view', $vendor->id) }}">
                {{ translate('individual_package') }}
            </a>
        </li>

        
        <li class="nav-item">
            <a class="nav-link px-4
                {{ request()->routeIs('guruji.services.details.add-detail') ? 'active' : '' }}"
               href="{{ route('guruji.services.details.add-detail', $vendor->id) }}">
                {{ translate('detail') }}
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link px-4
                {{ request()->routeIs('guruji.services.gallery.individual.view') ? 'active' : '' }}"
               href="{{ route('guruji.services.gallery.individual.view', $vendor->id) }}">
                {{ translate('gallery') }}
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link px-4
                {{ request()->routeIs('guruji.services.counselling.individual.view') ? 'active' : '' }}"
               href="{{ route('guruji.services.counselling.individual.view', $vendor->id) }}">
                {{ translate('individual_counselling') }}
            </a>
        </li>

    </ul>
