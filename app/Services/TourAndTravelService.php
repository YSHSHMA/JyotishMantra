<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Traits\FileManagerTrait;

class TourAndTravelService
{


    use FileManagerTrait;


    public function getAddTourData(object $request): array
    {

        return [
            'owner_name' => $request['owner_name'],
            'company_name' => $request['company_name'],
            'phone_no' => $request['phone_no'],
            'email' => $request['email'],
            'state' => $request['state'],
            'city' => $request['city'],
            'address' => $request['address'],
            'web_site_link' => $request['web_site_link'],
            'services' => $request['services'],
            'area_of_operation' => $request['area_of_operation'],
            'person_name' => $request['person_name'],
            'person_phone' => $request['person_phone'],
            'person_email' => $request['person_email'],
            'person_address' => $request['person_address'],
            'bank_holder_name' => $request['bank_holder_name'],
            'bank_name' => $request['bank_name'],
            'bank_branch' => $request['bank_branch'],
            'ifsc_code' => $request['ifsc_code'],
            'account_number' => $request['account_number'],

            'pan_card_number' => $request['pan_card_number'] ?? "",
            'aadhar_card_number' => $request['aadhar_card_number'] ?? "",
            'gst_number' => $request['gst_number'] ?? "",
            "experience" => $request['experience'] ?? "",

            'gst_image' => $this->upload(dir: 'tour_and_travels/doc/', format: 'png', image: $request['gst_image']),
            'pan_card_image' => $this->upload(dir: 'tour_and_travels/doc/', format: 'png', image: $request['pan_card_image']),
            'aadhaar_card_image' => $this->upload(dir: 'tour_and_travels/doc/', format: 'png', image: $request['aadhaar_card_image']),
            'address_proof_image' => $this->upload(dir: 'tour_and_travels/doc/', format: 'png', image: $request['address_proof_image']),
            'image' => $this->upload(dir: 'tour_and_travels/doc/', format: 'png', image: $request['image']),
            'banner' => $this->upload(dir: 'tour_and_travels/doc/', format: 'png', image: $request['banner']),
        ];
    }


    // Update Service GET
    public function getUpdateTempleData(object $request, object $temple, string $updateBy): array
    {
        $dataArray = [
            'name' => $request['name'][array_search('en', $request['lang'])],
        ];

        if ($request->file('image')) {
            $dataArray += [
                'thumbnail' => $this->update(dir: 'temple/thumbnail/', oldImage: $temple['thumbnail'], format: 'webp', image: $request['image'], fileType: 'image')
            ];
        }
        return $dataArray;
    }


    public function locationRemove($name): bool|array
    {
        return $this->delete(filePath: '/temple/review/' . $name);
    }

    public function removedoc(object $old_data)
    {
        $this->delete(filePath: '/tour_and_travels/doc/' . $old_data['gst_image']);
        $this->delete(filePath: '/tour_and_travels/doc/' . $old_data['pan_card_image']);
        $this->delete(filePath: '/tour_and_travels/doc/' . $old_data['aadhaar_card_image']);
        $this->delete(filePath: '/tour_and_travels/doc/' . $old_data['banner']);
        return $this->delete(filePath: '/tour_and_travels/doc/' . $old_data['address_proof_image']);
    }


    public function getUpdateTourData(object $request): array
    {
        $dataArray = [
            'owner_name' => $request['owner_name'],
            'company_name' => $request['company_name'],
            'phone_no' => $request['phone_no'],
            'email' => $request['email'],
            'state' => $request['state'],
            'city' => $request['city'],
            'address' => $request['address'],
            'web_site_link' => $request['web_site_link'],
            'services' => $request['services'],
            'area_of_operation' => $request['area_of_operation'],
            'person_name' => $request['person_name'],
            'person_phone' => $request['person_phone'],
            'person_email' => $request['person_email'],
            'person_address' => $request['person_address'],
            'bank_holder_name' => $request['bank_holder_name'],
            'bank_name' => $request['bank_name'],
            'bank_branch' => $request['bank_branch'],
            'ifsc_code' => $request['ifsc_code'],
            'account_number' => $request['account_number'],
            'pan_card_number' => $request['pan_card_number'] ?? "",
            'aadhar_card_number' => $request['aadhar_card_number'] ?? "",
            'gst_number' => $request['gst_number'] ?? "",
            "experience" => $request['experience'] ?? "",
        ];

        if ($request->file('gst_image')) {
            $dataArray['gst_image'] = $this->upload(dir: 'tour_and_travels/doc/', format: 'png', image: $request['gst_image']);
        }
        if ($request->file('pan_card_image')) {
            $dataArray['pan_card_image'] = $this->upload(dir: 'tour_and_travels/doc/', format: 'png', image: $request['pan_card_image']);
        }
        if ($request->file('aadhaar_card_image')) {
            $dataArray['aadhaar_card_image'] = $this->upload(dir: 'tour_and_travels/doc/', format: 'png', image: $request['aadhaar_card_image']);
        }
        if ($request->file('address_proof_image')) {
            $dataArray['address_proof_image'] = $this->upload(dir: 'tour_and_travels/doc/', format: 'png', image: $request['address_proof_image']);
        }

        if ($request->file('image')) {
            $dataArray['image'] = $this->upload(dir: 'tour_and_travels/doc/', format: 'png', image: $request['image']);
        }
        if ($request->file('banner')) {
            $dataArray['banner'] = $this->upload(dir: 'tour_and_travels/doc/', format: 'png', image: $request['banner']);
        }
        return $dataArray;
    }
}
