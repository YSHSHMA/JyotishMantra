<?php

namespace App\Services;

use Illuminate\Support\Str;

use App\Traits\FileManagerTrait;

class EventSponsorService
{
    use FileManagerTrait;

    public function getAddData(object $request): array
    {
        $event_image = '';
        if ($request->file('image')) {
            $event_image = $this->upload(dir: 'event/sponsor/', format: 'webp', image: $request->file('image'));
        }
        
        return [
            'type' => $request['type'],
            "name" =>  $request['name'],
            'company_name' => $request['company_name'],
            'phone' => $request['person_phone'],
            'link' => $request['link'],
            'package_id' => json_encode($request['package_id']),
            'image' => $event_image,            
        ];
    }

    public function deleteImage(object $old_data): bool
    {
        if ($old_data['image']) {
            $this->delete('event/sponsor/' . $old_data['image']);
        }
        return true;
    }

    public function getUpdateData(object $request, object $old_data)
    {

        $returnArrayData = [
            'type' => $request['type'],
            "name" =>  $request['name'],
            'company_name' => $request['company_name'],
            'phone' => $request['person_phone'],
            'link' => $request['link'],
            'package_id' => json_encode($request['package_id']),
        ];       
        if ($request->file('image')) {
            $this->delete('event/sponsor/' . $old_data['image']);
            $returnArrayData['image'] = $this->upload(dir: 'event/sponsor/', format: 'webp', image: $request->file('sponsor'));
        }
        return $returnArrayData;
    }

}
