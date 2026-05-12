<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\BusinessSetting;

if (!function_exists('reactDomainStatusCheck')) {
    function reactDomainStatusCheck(): void
    {
        $data = getWebConfig('react_setup');
        if($data && !empty($data['react_domain']) && !empty($data['react_license_code'])){
            if(isset($data['react_platform']) && $data['react_platform'] == 'codecanyon'){
                $data['status'] = (int)activationSubmit(purchaseKey: $data['react_license_code']);
            }elseif(!reactActivationCheck(reactDomain: $data['react_domain'], reactLicenseCode: $data['react_license_code'])){
                $data['status']=0;
            }elseif($data['status'] != 1){
                $data['status']=1;
            }
            DB::table('business_settings')->updateOrInsert(['type' => 'react_setup'], [
                'value' => json_encode($data)
            ]);
        }
    }
}

if (!function_exists('reactActivationCheck')) {
    function reactActivationCheck(string $reactDomain, string $reactLicenseCode): bool
    {
        $scheme = str_contains($reactDomain, 'localhost')?'http://':'https://';
        $url = empty(parse_url($reactDomain)['scheme']) ? $scheme . ltrim($reactDomain, '/') : $reactDomain;
        $response = Http::post('https://store.6amtech.com/api/v1/customer/license-check', [
            'domain_name' => str_ireplace('www.', '', parse_url($url, PHP_URL_HOST)),
            'license_code' => $reactLicenseCode
        ]);
        return ($response->successful() && isset($response->json('content')['is_active']) && $response->json('content')['is_active']);
    }
}

if (!function_exists('activationSubmit')) {
    function activationSubmit(string $purchaseKey): bool
    {
        $post = [
            'purchase_key' => $purchaseKey
        ];
        $live = 'https://check.6amtech.com';
        $result = curl_init($live . '/api/v1/software-check');
        curl_setopt($result, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($result, CURLOPT_POSTFIELDS, $post);
        $response = curl_exec($result);
        curl_close($result);
        $responseBody = json_decode($response, true);

        try {
            if ($responseBody['is_valid'] && $responseBody['result']['item']['id'] == env('REACT_APP_KEY')) {
                $previousActive = json_decode(BusinessSetting::where('type', 'app_activation')->first()->value ?? '[]');
                $found = 0;
                foreach ($previousActive as $item) {
                    if ($item->software_id == env('REACT_APP_KEY')) {
                        $found = 1;
                    }
                }
                if (!$found) {
                    $previousActive[] = [
                        'software_id' => env('REACT_APP_KEY'),
                        'is_active' => 1
                    ];
                    DB::table('business_settings')->updateOrInsert(['type' => 'app_activation'], [
                        'value' => json_encode($previousActive)
                    ]);
                }
                return true;
            }

        } catch (\Exception $exception) {
            info((string)["line___{$exception->getLine()}", $exception->getMessage()]);

            $previousActive[] = [
                'software_id' => env('REACT_APP_KEY'),
                'is_active' => 1
            ];
            DB::table('business_settings')->updateOrInsert(['type' => 'app_activation'], [
                'value' => json_encode($previousActive)
            ]);

            return true;
        }
        return true;
    }
}


