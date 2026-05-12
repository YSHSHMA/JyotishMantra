<?php

namespace App\Services;

use App\Traits\FileManagerTrait;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;

class BannerService
{
    use FileManagerTrait;

    public function getProcessedData(object $request, string $image = null): array
    {
       
        if ($image) {
            $imageName = $request->file('image') 
                ? $this->update(dir: 'banner/', oldImage: $image, format: 'webp', image: $request->file('image')) 
                : $image;
        } else {
            $imageName = $request->hasFile('image') 
                ? $this->upload(dir: 'banner/', format: 'webp', image: $request->file('image')) 
                : null; 
        }
    
        $currentDateTime = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
        $currentDate = $currentDateTime->format('Y-m-d'); 
    
        $startDate = new DateTime($request['start_date']);
        $endDate = new DateTime($request['end_date']);
    
        $published = 0;
        if ($currentDate >= $startDate->format('Y-m-d') && $currentDate <= $endDate->format('Y-m-d')) {
            $published = 1;
        }
    
        return [
            'banner_type' => $request['banner_type'],
            'resource_type' => $request['resource_type'],
            'resource_id' => $request[$request->resource_type . '_id'],
            'pooja_id' => $request->pooja_id ?? null,
            'theme' => theme_root_path(),
            'title' => $request['title'],
            'sub_title' => $request['sub_title'],
            'button_text' => $request['button_text'],
            'background_color' => $request['background_color'],
            'url' => $request['url'],
            'photo' => $imageName,
            'image_type' => $request['image_type'],
            'app_section_resource_type' => $request['app_section_resource_type'],
            'app_section_resource_id' => $request['app_section_resource_id'],
            'start_date' => $request['start_date'],
            'end_date' => $request['end_date'],
            'published' => $published, 
        ];
    }

    public function getBannerTypes(): array
    {
        $isReactActive = getWebConfig(name: 'react_setup')['status'];
        $bannerTypes = [];
        if (theme_root_path() == 'default') {
            $bannerTypes = [
                "Main Banner" => translate('main_Banner'),
                "Popup Banner" => translate('popup_Banner'),
                "Footer Banner" => translate('footer_Banner'),
                "Main Section Banner" => translate('main_Section_Banner'),
                "Mahakal Banner" => translate('mahakal_Banner'),
                "Mahakal App Banner" => translate('mahakal_App_Banner'),
                "App Section Banner" => translate('app_Section_Banner'),
                "Astrology Banner" => translate('astrology_Banner'),
                "Auspicious Occasion Banner" => translate('auspicious_Occasion_Banner'),
                "Chat Banner" => translate('chat_Banner'),
                "Events Banner" => translate('events_Banner'),
                "E Commerece App Banner" => translate('e_Commerece_App_Banner'),
                "Tour Background Image" => translate('tour_background_image'),
            ];

        }elseif (theme_root_path() == 'theme_aster') {
            $bannerTypes = [
                "Main Banner" => translate('main_Banner'),
                "Popup Banner" => translate('popup_Banner'),
                "Footer Banner" => translate('footer_Banner'),
                "Main Section Banner" => translate('main_Section_Banner'),
                "Header Banner" => translate('header_Banner'),
                "Sidebar Banner" => translate('sidebar_Banner'),
                "Top Side Banner" => translate('top_Side_Banner'),
                "Mahakal Banner" => translate('mahakal_Banner'),
                "Mahakal App Banner" => translate('mahakal_App_Banner'),
                "App Section Banner" => translation('app_Section_Banner'),
                "Astrology Banner" => translate('astrology_Banner'),
                "Auspicious Occasion Banner" => translate('auspicious_Occasion_Banner'),
                "Chat Banner" => translate('chat_Banner'),
                "Events Banner" => translate('events_Banner'),
                "E Commerece App Banner" => translate('e_Commerece_App_Banner'),
                "Tour Background Image" => translate('tour_background_image'),
            ];
        }elseif (theme_root_path() == 'theme_fashion') {
            $bannerTypes = [
                "Main Banner" => translate('main_Banner'),
                "Popup Banner" => translate('popup_Banner'),
                "Promo Banner Left" => translate('promo_banner_left'),
                "Promo Banner Middle Top" => translate('promo_banner_middle_top'),
                "Promo Banner Middle Bottom" => translate('promo_banner_middle_bottom'),
                "Promo Banner Right" => translate('promo_banner_right'),
                "Promo Banner Bottom" => translate('promo_banner_bottom'),
                "Mahakal Banner" => translate('mahakal_Banner'),
                "Mahakal App Banner" => translate('mahakal_App_Banner'),
                "App Section Banner" => translation('app_Section_Banner'),
                "Astrology Banner" => translate('astrology_Banner'),
                "Auspicious Occasion Banner" => translate('auspicious_Occasion_Banner'),
                "Chat Banner" => translate('chat_Banner'),
                "Events Banner" => translate('events_Banner'),
                "E Commerece App Banner" => translate('e_Commerece_App_Banner'),
                "Tour Background Image" => translate('tour_background_image'),
            ];
        }

        if($isReactActive){
            $reactBanner = [
                'Main Banner' => translate('main_Banner'),
                'Main Section Banner' => translate('main_Section_Banner'),
                'Top Side Banner' => translate('top_Side_Banner'),
                'Footer Banner' => translate('footer_Banner'),
                'Popup Banner' => translate('popup_Banner'),
                'Mahakal Banner' => translate('mahakal_Banner'),
                'Mahakal App Banner' => translate('mahakal_App_Banner'),
                "App Section Banner" => translation('app_Section_Banner'),
                "Astrology Banner" => translate('astrology_Banner'),
                "Auspicious Occasion Banner" => translate('auspicious_Occasion_Banner'),
                "Chat Banner" => translate('chat_Banner'),
                "Events Banner" => translate('events_Banner'),
                "E Commerece App Banner" => translate('e_Commerece_App_Banner'),
                "Tour Background Image" => translate('tour_background_image'),
            ];
            $bannerTypes = array_unique(array_merge($bannerTypes, $reactBanner));
        }

        return $bannerTypes;
    }

}