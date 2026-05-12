<style>
    /* Prograss */
    @media (min-width: 768px) {
        .md\:top-\[68px\] {
            top: 68px;
        }
    }

    .w-full {
        width: 100%;
    }

    .z-20 {
        z-index: 1000;
    }

    .top-0 {
        top: 0;
    }

    .sticky {
        position: sticky;
    }

    .bg-bar {
        --tw-bg-opacity: 1;
        background-color: #f3f4f6;
    }

    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .overflow-x-scroll {
        overflow-x: scroll;
    }

    .max-w-screen-xl {
        max-width: 1280px;
    }

    .justify-center {
        justify-content: center;
    }

    .items-center {
        align-items: center;
    }

    .px-2 {
        padding-left: .5rem;
        padding-right: .5rem;
    }

    .shrink-0 {
        flex-shrink: 0;
    }

    .text-next {
        --tw-text-opacity: 1;
        color: #1573DF;
    }

    .text-disable {
        --tw-text-opacity: 1;
        color: #5f6672;
    }

    .border-bar {
        --tw-border-opacity: 1;
        border-color: #5f6672 !important;
    }

    .border {
        border-width: 1px;
    }

    .rounded-full {
        border-radius: 9999px;
    }
</style>

<div class="d-flex justify-center items-center pt-3 pb-3">
    <div class="d-flex justify-center items-center">
        <svg class="shrink-0" width="16" height="16" viewBox="0 0 16 16" fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <circle cx="8" cy="8" r="8" fill="#00BD68"></circle>
            <path
                d="M6.98587 10.3993L4.80078 8.1901L5.65181 7.33194L6.98587 8.68297L10.3497 5.2793L11.2008 6.13746L6.98587 10.3993Z"
                fill="white"></path>
        </svg>
        <div class="pl-1 !w-full flex break-words md:whitespace-nowrap text-xs text-[#6B7280] font-medium">
            {{ translate('add_details') }}</div>
    </div>
    <div class="px-2 shrink-0 flex text-next"><svg width="14" height="14" viewBox="0 0 14 14" fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd"
                d="M7.2051 10.9945C7.07387 10.8632 7.00015 10.6852 7.00015 10.4996C7.00015 10.314 7.07387 10.136 7.2051 10.0047L10.2102 6.99962L7.2051 3.99452C7.13824 3.92994 7.08491 3.8527 7.04822 3.7673C7.01154 3.6819 6.99223 3.59004 6.99142 3.4971C6.99061 3.40415 7.00832 3.31198 7.04352 3.22595C7.07872 3.13992 7.13069 3.06177 7.19642 2.99604C7.26214 2.93032 7.3403 2.87834 7.42633 2.84314C7.51236 2.80795 7.60453 2.79023 7.69748 2.79104C7.79042 2.79185 7.88228 2.81116 7.96768 2.84785C8.05308 2.88453 8.13032 2.93786 8.1949 3.00472L11.6949 6.50472C11.8261 6.63599 11.8998 6.814 11.8998 6.99962C11.8998 7.18523 11.8261 7.36325 11.6949 7.49452L8.1949 10.9945C8.06363 11.1257 7.88561 11.1995 7.7 11.1995C7.51438 11.1995 7.33637 11.1257 7.2051 10.9945Z"
                fill="#9CA3AF"></path>
            <path fill-rule="evenodd" clip-rule="evenodd"
                d="M3.0051 10.9949C2.87387 10.8636 2.80015 10.6856 2.80015 10.5C2.80015 10.3144 2.87387 10.1364 3.0051 10.0051L6.0102 6.99999L3.0051 3.99489C2.87759 3.86287 2.80703 3.68605 2.80863 3.50251C2.81022 3.31897 2.88384 3.1434 3.01363 3.01362C3.14341 2.88383 3.31898 2.81022 3.50252 2.80862C3.68606 2.80703 3.86288 2.87758 3.9949 3.00509L7.4949 6.50509C7.62613 6.63636 7.69985 6.81438 7.69985 6.99999C7.69985 7.18561 7.62613 7.36362 7.4949 7.49489L3.9949 10.9949C3.86363 11.1261 3.68561 11.1998 3.5 11.1998C3.31438 11.1998 3.13637 11.1261 3.0051 10.9949Z"
                fill="#9CA3AF"></path>
        </svg>
    </div>
    <div class="d-flex justify-center items-center">
        @if (Str::contains(Request::url(), 'poojacart') ||
                Str::contains(Request::url(), 'payment/razor-pay/pay') ||
                Str::contains(Request::url(), 'sankalp'))
            <svg class="shrink-0" width="16" height="16" viewBox="0 0 16 16" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <circle cx="8" cy="8" r="8" fill="#00BD68"></circle>
                <path
                    d="M6.98587 10.3993L4.80078 8.1901L5.65181 7.33194L6.98587 8.68297L10.3497 5.2793L11.2008 6.13746L6.98587 10.3993Z"
                    fill="white"></path>
            </svg>
        @else
            <div
                class="d-flex justify-center items-center w-4 h-4 rounded-full  text-next  text-[10px]  font-medium shrink-0 ">
                2</div>
        @endif
        <div class="pl-1 !w-full flex break-words md:whitespace-nowrap text-xs text-next font-medium">
            {{ translate('review_booking') }}</div>
    </div>
    <div class="px-2 shrink-0 flex text-next"><svg width="14" height="14" viewBox="0 0 14 14" fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd"
                d="M7.2051 10.9945C7.07387 10.8632 7.00015 10.6852 7.00015 10.4996C7.00015 10.314 7.07387 10.136 7.2051 10.0047L10.2102 6.99962L7.2051 3.99452C7.13824 3.92994 7.08491 3.8527 7.04822 3.7673C7.01154 3.6819 6.99223 3.59004 6.99142 3.4971C6.99061 3.40415 7.00832 3.31198 7.04352 3.22595C7.07872 3.13992 7.13069 3.06177 7.19642 2.99604C7.26214 2.93032 7.3403 2.87834 7.42633 2.84314C7.51236 2.80795 7.60453 2.79023 7.69748 2.79104C7.79042 2.79185 7.88228 2.81116 7.96768 2.84785C8.05308 2.88453 8.13032 2.93786 8.1949 3.00472L11.6949 6.50472C11.8261 6.63599 11.8998 6.814 11.8998 6.99962C11.8998 7.18523 11.8261 7.36325 11.6949 7.49452L8.1949 10.9945C8.06363 11.1257 7.88561 11.1995 7.7 11.1995C7.51438 11.1995 7.33637 11.1257 7.2051 10.9945Z"
                fill="#9CA3AF"></path>
            <path fill-rule="evenodd" clip-rule="evenodd"
                d="M3.0051 10.9949C2.87387 10.8636 2.80015 10.6856 2.80015 10.5C2.80015 10.3144 2.87387 10.1364 3.0051 10.0051L6.0102 6.99999L3.0051 3.99489C2.87759 3.86287 2.80703 3.68605 2.80863 3.50251C2.81022 3.31897 2.88384 3.1434 3.01363 3.01362C3.14341 2.88383 3.31898 2.81022 3.50252 2.80862C3.68606 2.80703 3.86288 2.87758 3.9949 3.00509L7.4949 6.50509C7.62613 6.63636 7.69985 6.81438 7.69985 6.99999C7.69985 7.18561 7.62613 7.36362 7.4949 7.49489L3.9949 10.9949C3.86363 11.1261 3.68561 11.1998 3.5 11.1998C3.31438 11.1998 3.13637 11.1261 3.0051 10.9949Z"
                fill="#9CA3AF"></path>
        </svg>
    </div>
    <div class="d-flex justify-center items-center">
        @if (Str::contains(Request::url(), 'payment/razor-pay/pay') || Str::contains(Request::url(), 'sankalp'))
            <svg class="shrink-0" width="16" height="16" viewBox="0 0 16 16" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <circle cx="8" cy="8" r="8" fill="#00BD68"></circle>
                <path
                    d="M6.98587 10.3993L4.80078 8.1901L5.65181 7.33194L6.98587 8.68297L10.3497 5.2793L11.2008 6.13746L6.98587 10.3993Z"
                    fill="white"></path>
            </svg>
        @else
            <div
                class="d-flex justify-center items-center w-4 h-4 rounded-full  text-next  text-[10px]  font-medium shrink-0 ">
                3</div>
        @endif
        <div class="pl-1 !w-full flex break-words md:whitespace-nowrap text-xs text-disable font-medium">
            {{ translate('make_payment') }}</div>
    </div>
    <div class="px-2 shrink-0 flex text-disable"><svg width="14" height="14" viewBox="0 0 14 14" fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd"
                d="M7.2051 10.9945C7.07387 10.8632 7.00015 10.6852 7.00015 10.4996C7.00015 10.314 7.07387 10.136 7.2051 10.0047L10.2102 6.99962L7.2051 3.99452C7.13824 3.92994 7.08491 3.8527 7.04822 3.7673C7.01154 3.6819 6.99223 3.59004 6.99142 3.4971C6.99061 3.40415 7.00832 3.31198 7.04352 3.22595C7.07872 3.13992 7.13069 3.06177 7.19642 2.99604C7.26214 2.93032 7.3403 2.87834 7.42633 2.84314C7.51236 2.80795 7.60453 2.79023 7.69748 2.79104C7.79042 2.79185 7.88228 2.81116 7.96768 2.84785C8.05308 2.88453 8.13032 2.93786 8.1949 3.00472L11.6949 6.50472C11.8261 6.63599 11.8998 6.814 11.8998 6.99962C11.8998 7.18523 11.8261 7.36325 11.6949 7.49452L8.1949 10.9945C8.06363 11.1257 7.88561 11.1995 7.7 11.1995C7.51438 11.1995 7.33637 11.1257 7.2051 10.9945Z"
                fill="#9CA3AF"></path>
            <path fill-rule="evenodd" clip-rule="evenodd"
                d="M3.0051 10.9949C2.87387 10.8636 2.80015 10.6856 2.80015 10.5C2.80015 10.3144 2.87387 10.1364 3.0051 10.0051L6.0102 6.99999L3.0051 3.99489C2.87759 3.86287 2.80703 3.68605 2.80863 3.50251C2.81022 3.31897 2.88384 3.1434 3.01363 3.01362C3.14341 2.88383 3.31898 2.81022 3.50252 2.80862C3.68606 2.80703 3.86288 2.87758 3.9949 3.00509L7.4949 6.50509C7.62613 6.63636 7.69985 6.81438 7.69985 6.99999C7.69985 7.18561 7.62613 7.36362 7.4949 7.49489L3.9949 10.9949C3.86363 11.1261 3.68561 11.1998 3.5 11.1998C3.31438 11.1998 3.13637 11.1261 3.0051 10.9949Z"
                fill="#9CA3AF"></path>
        </svg>
    </div>
    <div class="d-flex justify-center items-center">
        @if (Str::contains(Request::url(), 'sankalp'))
            <svg class="shrink-0" width="16" height="16" viewBox="0 0 16 16" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <circle cx="8" cy="8" r="8" fill="#00BD68"></circle>
                <path
                    d="M6.98587 10.3993L4.80078 8.1901L5.65181 7.33194L6.98587 8.68297L10.3497 5.2793L11.2008 6.13746L6.98587 10.3993Z"
                    fill="white"></path>
            </svg>
        @else
            <div
                class="d-flex justify-center items-center w-4 h-4 rounded-full  text-next  text-[10px]  font-medium shrink-0 ">
                4</div>
        @endif
        <div class="pl-1 !w-full flex break-words md:whitespace-nowrap text-xs text-disable font-medium">
            {{ translate('fill_name_gotra_&_address') }} </div>
    </div>
</div>
