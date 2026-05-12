<?php

namespace App\Services;

use App\Traits\FileManagerTrait;
use Illuminate\Http\Request;

class FastFestivalService
{
    use FileManagerTrait;

    /**
     * Prepare data for adding a new FastFestival
     *
     * @param Request $request
     * @return array
     */
       public function getAddData(Request $request): array
    {
        $data = [];
        
        // Loop through each language submitted in the form
        foreach ($request->lang as $key => $lang) {
            $data[$key] = [
                'event_name' => $request->event_name[$key],
                'event_type' => $request->event_type[$key],
                'en_description' => $request->en_description[$key],
                'hi_description' => $request->hi_description[$key],
                'lang' => $lang,
                'image' => $this->upload('fastfestival-img/', 'webp', $request->file('image')),

                'status' => 1,
            ];
        }
        
        return $data;
    }

    /**
     * Prepare data for updating an existing FastFestival
     *
     * @param Request $request
     * @param object $data
     * @return array
     */
    public function getUpdateData(Request $request, object $data): array
    {
        $image = $request->file('image') ? $this->update('fastfestival-img/', $data['image'],'webp', $request->file('image')) : $data['image'];
        
        return [
            'event_name_hi' => $request->input('event_name_hi'),
            'event_type' => $request->input('event_type'),
            'en_description' => $request->input('en_description'),
            'hi_description' => $request->input('hi_description'),
            'image' => $image,
        ];
    }

    /**
     * Delete the image of a FastFestival
     *
     * @param object $data
     * @return bool
     */
    public function deleteImage(object $data): bool
    {
        if ($data->fastfestival_image) {
            $this->delete('fastfestival-img/' . $data->fastfestival_image);
        }
        return true;
    }
}
