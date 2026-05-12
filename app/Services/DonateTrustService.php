<?php

namespace App\Services;

use Illuminate\Support\Str;

use App\Traits\FileManagerTrait;
use Illuminate\Support\Facades\Storage;

class DonateTrustService
{
    use FileManagerTrait;
    public function getAddData(object $request): array
    {
        $memberList = [];
        if (isset($request['member_name'][0]) && isset($request['member_phone_no'][0]) && isset($request['member_position'][0])) {
            foreach ($request['member_name'] as $key => $val) {
                $memberList[$key]['member_name'] = $val;
                $memberList[$key]['member_phone_no'] = $request['member_phone_no'][$key];
                $memberList[$key]['member_position'] = $request['member_position'][$key];
            }
        }
        $images = $this->uploadImages($request);
        return [
            'category_id' => $request['category_id'],
            "trust_temple_id" => json_encode($request['temple'] ?? []),
            'name' => $request['name'][array_search('en', $request['lang'])],
            'trust_name' => $request['trust_name'][array_search('en', $request['lang'])],
            'slug' => Str::slug($request['trust_name'][array_search('en', $request['lang'])], '-') . '-' . Str::random(6),
            'description' => $request['description'][array_search('en', $request['lang'])],
            'memberlist' => json_encode($memberList),
            'trust_pan_card' => $request['trust_pan_card'],
            'pan_card' => $request['pan_card'],
            'website' => $request['website'] ?? '',
            'full_address' => $request['full_address'][array_search('en', $request['lang'])],
            'trust_email' => $request['trust_email'],
            'beneficiary_name' => $request['beneficiary_name'],
            'account_type' => $request['account_type'],
            'bank_name' => $request['bank_name'],
            'ifsc_code' => $request['ifsc_code'],
            'account_no' => $request['account_no'],
            'gallery_image' => json_encode($images['trust']),
            'theme_image' => (($request->hasfile('theme_image')) ? $this->upload(dir: 'donate/trust/', format: 'webp', image: $request->file('theme_image')) : ""),
            'astin_g_number' => $request['astin_g_number'],
            'twelve_a_number' => $request['twelve_a_number'],
            'eighty_g_number' => $request['eighty_g_number'],
            'niti_aayog_number' => $request['niti_aayog_number'],
            'csr_number' => $request['csr_number'],
            'e_anudhan_number' => $request['e_anudhan_number'],
            'frc_number' => $request['frc_number'],

            'pan_card_image' => (($request->hasfile('pan_card_image')) ? $this->fileUpload(dir: 'donate/document/', format: $request->file('pan_card_image')->getClientOriginalExtension(), file: $request->file('pan_card_image')) : ""),
            'trust_pan_card_image' => (($request->hasfile('trustees_pan_card_image')) ? $this->fileUpload(dir: 'donate/document/', format: $request->file('trustees_pan_card_image')->getClientOriginalExtension(), file: $request->file('trustees_pan_card_image')) : ""),
            'twelve_a_certificate' => (($request->hasfile('twelve_a_certificate')) ? $this->fileUpload(dir: 'donate/document/', format: $request->file('twelve_a_certificate')->getClientOriginalExtension(), file: $request->file('twelve_a_certificate')) : ""),
            'eighty_g_certificate' => (($request->hasfile('eighty_g_certificate')) ? $this->fileUpload(dir: 'donate/document/', format: $request->file('eighty_g_certificate')->getClientOriginalExtension(), file: $request->file('eighty_g_certificate')) : ""),
            'niti_aayog_certificate' => (($request->hasfile('niti_aayog_certificate')) ? $this->fileUpload(dir: 'donate/document/', format: $request->file('niti_aayog_certificate')->getClientOriginalExtension(), file: $request->file('niti_aayog_certificate')) : ""),
            'csr_certificate' => (($request->hasfile('csr_certificate')) ? $this->fileUpload(dir: 'donate/document/', format: $request->file('csr_certificate')->getClientOriginalExtension(), file: $request->file('csr_certificate')) : ""),
            'e_anudhan_certificate' => (($request->hasfile('e_anudhan_certificate')) ? $this->fileUpload(dir: 'donate/document/', format: $request->file('e_anudhan_certificate')->getClientOriginalExtension(), file: $request->file('e_anudhan_certificate')) : ""),
            'frc_certificate' => (($request->hasfile('frc_certificate')) ? $this->fileUpload(dir: 'donate/document/', format: $request->file('frc_certificate')->getClientOriginalExtension(), file: $request->file('frc_certificate')) : ""),

            'donate_commission' => 5,
            'ad_commission' => 5,
            'vip_darshan_commission' => 10,
        ];
    }



    public function uploadImages(object $request): array
    {
        $imageList['trust'] = [];
        if ($request->hasfile('gallery_image')) {
            $images = $request->file('gallery_image');
            foreach ($images as $image) {
                $imageList['trust'][] = $this->upload(dir: 'donate/trust/', format: 'webp', image: $image);
            }
        }

        return $imageList;
    }

    public function deleteTrustImage(object $old_data): bool
    {
        if (!empty($old_data['pan_card_image'])) {
            $this->delete(filePath: 'donate/document/' . $old_data['pan_card_image']);
        }
        if (!empty($old_data['trust_pan_card_image'])) {
            $this->delete(filePath: 'donate/document/' . $old_data['trust_pan_card_image']);
        }
        if (!empty($old_data['astin_g_certificate'])) {
            $this->delete(filePath: 'donate/document/' . $old_data['astin_g_certificate']);
        }
        if (!empty($old_data['twelve_a_certificate'])) {
            $this->delete(filePath: 'donate/document/' . $old_data['twelve_a_certificate']);
        }
        if (!empty($old_data['eighty_g_certificate'])) {
            $this->delete(filePath: 'donate/document/' . $old_data['eighty_g_certificate']);
        }
        if (!empty($old_data['niti_aayog_certificate'])) {
            $this->delete(filePath: 'donate/document/' . $old_data['niti_aayog_certificate']);
        }
        if (!empty($old_data['csr_certificate'])) {
            $this->delete(filePath: 'donate/document/' . $old_data['csr_certificate']);
        }
        if (!empty($old_data['e_anudhan_certificate'])) {
            $this->delete(filePath: 'donate/document/' . $old_data['e_anudhan_certificate']);
        }
        if (!empty($old_data['frc_certificate'])) {
            $this->delete(filePath: 'donate/document/' . $old_data['frc_certificate']);
        }
        if (!empty($old_data['gallery_image']) && json_decode($old_data['gallery_image'])) {
            $images = json_decode($old_data['gallery_image']);
            foreach ($images as $image) {
                $this->delete(filePath: 'donate/trust/' . $image);
            }
        }
        return true;
    }

    public function getUpdateData(object $request, object $old_data): array
    {
        $memberList = [];
        if (isset($request['member_name'][0]) && isset($request['member_phone_no'][0]) && isset($request['member_position'][0])) {
            foreach ($request['member_name'] as $key => $val) {
                $memberList[$key]['member_name'] = $val;
                $memberList[$key]['member_phone_no'] = $request['member_phone_no'][$key];
                $memberList[$key]['member_position'] = $request['member_position'][$key];
            }
        }
        $images = $this->uploadImages($request);
        $merged_images = [];
        if (!empty($old_data['gallery_image']) && json_decode($old_data['gallery_image'], true)) {
            $merged_images = json_decode($old_data['gallery_image'], true); // Convert old images to an array
        }
        if (!empty($images['trust'])) {
            $merged_images = array_merge($merged_images, $images['trust']);
        }
        $groupData = [
            'category_id' => $request['category_id'],
            "trust_temple_id" => json_encode($request['temple'] ?? []),
            'name' => $request['name'][array_search('en', $request['lang'])],
            'trust_name' => $request['trust_name'][array_search('en', $request['lang'])],
            // 'slug' => Str::slug($request['trust_name'][array_search('en', $request['lang'])], '-') . '-' . Str::random(6),
            'description' => $request['description'][array_search('en', $request['lang'])],
            'memberlist' => json_encode($memberList),
            'trust_pan_card' => $request['trust_pan_card'],
            'pan_card' => $request['pan_card'],
            'website' => $request['website'],
            'full_address' => $request['full_address'][array_search('en', $request['lang'])],
            'trust_email' => $request['trust_email'],
            'beneficiary_name' => $request['beneficiary_name'],
            'account_type' => $request['account_type'],
            'bank_name' => $request['bank_name'],
            'ifsc_code' => $request['ifsc_code'],
            'account_no' => $request['account_no'],
            'astin_g_number' => $request['astin_g_number'],
            'twelve_a_number' => $request['twelve_a_number'],
            'eighty_g_number' => $request['eighty_g_number'],
            'niti_aayog_number' => $request['niti_aayog_number'],
            'csr_number' => $request['csr_number'],
            'e_anudhan_number' => $request['e_anudhan_number'],
            'frc_number' => $request['frc_number'],
        ];
        if (empty($old_data['slug'])) {
            $groupData['slug'] = Str::slug($request['trust_name'][array_search('en', $request['lang'])], '-') . '-' . Str::random(6);
        }

        $groupData['gallery_image'] = json_encode($merged_images);

        if ($request->hasfile('pan_card_image')) {
            $groupData['pan_card_image'] = $this->fileUpload(dir: 'donate/document/', format: $request->file('pan_card_image')->getClientOriginalExtension(), file: $request->file('pan_card_image'));
            $this->delete(filePath: 'donate/document/' . $old_data['pan_card_image']);
        }
        if ($request->hasfile('trustees_pan_card_image')) {
            $groupData['trust_pan_card_image'] = $this->fileUpload(dir: 'donate/document/', format: $request->file('trustees_pan_card_image')->getClientOriginalExtension(), file: $request->file('trustees_pan_card_image'));
            $this->delete(filePath: 'donate/document/' . $old_data['trust_pan_card_image']);
        }

        if ($request->hasfile('theme_image')) {
            $groupData['theme_image'] = $this->upload(dir: 'donate/trust/', format: 'webp', image: $request->file('theme_image'));
            $this->delete(filePath: 'donate/trust/' . $old_data['theme_image']);
        }

        if ($request->hasfile('twelve_a_certificate')) {
            $groupData['twelve_a_certificate'] = $this->fileUpload(dir: 'donate/document/', format: $request->file('twelve_a_certificate')->getClientOriginalExtension(), file: $request->file('twelve_a_certificate'));
            $this->delete(filePath: 'donate/document/' . $old_data['twelve_a_certificate']);
        }
        if ($request->hasfile('eighty_g_certificate')) {
            $groupData['eighty_g_certificate'] = $this->fileUpload(dir: 'donate/document/', format: $request->file('eighty_g_certificate')->getClientOriginalExtension(), file: $request->file('eighty_g_certificate'));
            $this->delete(filePath: 'donate/document/' . $old_data['eighty_g_certificate']);
        }
        if ($request->hasfile('niti_aayog_certificate')) {
            $groupData['niti_aayog_certificate'] = $this->fileUpload(dir: 'donate/document/', format: $request->file('niti_aayog_certificate')->getClientOriginalExtension(), file: $request->file('niti_aayog_certificate'));
            $this->delete(filePath: 'donate/document/' . $old_data['niti_aayog_certificate']);
        }
        if ($request->hasfile('csr_certificate')) {
            $groupData['csr_certificate'] = $this->fileUpload(dir: 'donate/document/', format: $request->file('csr_certificate')->getClientOriginalExtension(), file: $request->file('csr_certificate'));
            $this->delete(filePath: 'donate/document/' . $old_data['csr_certificate']);
        }
        if ($request->hasfile('e_anudhan_certificate')) {
            $groupData['e_anudhan_certificate'] = $this->fileUpload(dir: 'donate/document/', format: $request->file('e_anudhan_certificate')->getClientOriginalExtension(), file: $request->file('e_anudhan_certificate'));
            $this->delete(filePath: 'donate/document/' . $old_data['e_anudhan_certificate']);
        }
        if ($request->hasfile('frc_certificate')) {
            $groupData['frc_certificate'] = $this->fileUpload(dir: 'donate/document/', format: $request->file('frc_certificate')->getClientOriginalExtension(), file: $request->file('frc_certificate'));
            $this->delete(filePath: 'donate/document/' . $old_data['frc_certificate']);
        }

        return $groupData;
    }

    public function getRemoveImage(object $request, object $old_data)
    {
        $images = [];
        if ($request['image_path'] && json_decode($old_data['gallery_image'])) {
            foreach (json_decode($old_data['gallery_image']) as $key => $value) {
                if ($value == $request['image_path']) {
                    $this->delete(filePath: 'donate/trust/' . $request['image_path']);
                } else {
                    $images[] = $value;
                }
            }
        }
        return [
            "gallery_image" => json_encode($images),
        ];
    }

    public function UploadVerifyDoc(object $request): array
    {
        $uploadedFile = $request->file('file');
        $extension = $uploadedFile->getClientOriginalExtension(); // e.g. pdf, docx, webp

        $path = $this->fileUpload(
            dir: 'donate/verified/',
            format: $extension,
            file: $uploadedFile
        );
        return [
            'is_approve' => 1,
            "verified_access_certificate" => $path,
        ];
    }

    public function getUpdateDataAPI(object $request, object $old_data): array
    {
        $memberList = [];
        if (isset($request['memberlist'][0]) && isset($request['memberlist'][0]['member_phone_no']) && isset($request['memberlist'][0]['member_position'])) {
            foreach ($request['memberlist'] as $key => $val) {
                $memberList[$key]['member_name'] = $request['memberlist'][$key]['member_name'];
                $memberList[$key]['member_phone_no'] = $request['memberlist'][$key]['member_phone_no'];
                $memberList[$key]['member_position'] = $request['memberlist'][$key]['member_position'];
            }
        }
        $images = $this->uploadImages($request);
        $merged_images = [];
        if (!empty($old_data['gallery_image']) && json_decode($old_data['gallery_image'], true)) {
            $merged_images = json_decode($old_data['gallery_image'], true);
        }
        if (!empty($images['trust'])) {
            $merged_images = array_merge($merged_images, $images['trust']);
        }
        $groupData = [
            'category_id' => $request['category_id'],
            "trust_temple_id" => json_encode($request['trust_temple_id'] ?? []),
            'name' => $request['name'],
            'trust_name' => $request['trust_name'],
            'description' => $request['description'],
            'memberlist' => json_encode($memberList),
            'trust_pan_card' => $request['trust_pan_card'],
            'pan_card' => $request['pan_card'],
            'website' => $request['website'],
            'full_address' => $request['full_address'],
            'trust_email' => $request['trust_email'],
            'beneficiary_name' => $request['beneficiary_name'],
            'account_type' => $request['account_type'],
            'bank_name' => $request['bank_name'],
            'ifsc_code' => $request['ifsc_code'],
            'account_no' => $request['account_no'],
            'astin_g_number' => $request['astin_g_number'],
            'twelve_a_number' => $request['twelve_a_number'],
            'eighty_g_number' => $request['eighty_g_number'],
            'niti_aayog_number' => $request['niti_aayog_number'],
            'csr_number' => $request['csr_number'],
            'e_anudhan_number' => $request['e_anudhan_number'],
            'frc_number' => $request['frc_number'],
        ];
        if (empty($old_data['slug'])) {
            $groupData['slug'] = Str::slug($request['trust_name'], '-') . '-' . Str::random(6);
        }

        $groupData['gallery_image'] = json_encode($merged_images);
        if ($request->hasfile('pan_card_image')) {
            $groupData['pan_card_image'] = $this->fileUpload(dir: 'donate/document/', format: $request->file('pan_card_image')->getClientOriginalExtension(), file: $request->file('pan_card_image'));
            $this->delete(filePath: 'donate/document/' . $old_data['pan_card_image']);
        }
        if ($request->hasfile('trustees_pan_card_image')) {
            $groupData['trust_pan_card_image'] = $this->fileUpload(dir: 'donate/document/', format: $request->file('trustees_pan_card_image')->getClientOriginalExtension(), file: $request->file('trustees_pan_card_image'));
            $this->delete(filePath: 'donate/document/' . $old_data['trust_pan_card_image']);
        }

        if ($request->hasfile('theme_image')) {
            $groupData['theme_image'] = $this->upload(dir: 'donate/trust/', format: 'webp', image: $request->file('theme_image'));
            $this->delete(filePath: 'donate/trust/' . $old_data['theme_image']);
        }

        if ($request->hasfile('twelve_a_certificate')) {
            $groupData['twelve_a_certificate'] = $this->fileUpload(dir: 'donate/document/', format: $request->file('twelve_a_certificate')->getClientOriginalExtension(), file: $request->file('twelve_a_certificate'));
            $this->delete(filePath: 'donate/document/' . $old_data['twelve_a_certificate']);
        }
        if ($request->hasfile('eighty_g_certificate')) {
            $groupData['eighty_g_certificate'] = $this->fileUpload(dir: 'donate/document/', format: $request->file('eighty_g_certificate')->getClientOriginalExtension(), file: $request->file('eighty_g_certificate'));
            $this->delete(filePath: 'donate/document/' . $old_data['eighty_g_certificate']);
        }
        if ($request->hasfile('niti_aayog_certificate')) {
            $groupData['niti_aayog_certificate'] = $this->fileUpload(dir: 'donate/document/', format: $request->file('niti_aayog_certificate')->getClientOriginalExtension(), file: $request->file('niti_aayog_certificate'));
            $this->delete(filePath: 'donate/document/' . $old_data['niti_aayog_certificate']);
        }
        if ($request->hasfile('csr_certificate')) {
            $groupData['csr_certificate'] = $this->fileUpload(dir: 'donate/document/', format: $request->file('csr_certificate')->getClientOriginalExtension(), file: $request->file('csr_certificate'));
            $this->delete(filePath: 'donate/document/' . $old_data['csr_certificate']);
        }
        if ($request->hasfile('e_anudhan_certificate')) {
            $groupData['e_anudhan_certificate'] = $this->fileUpload(dir: 'donate/document/', format: $request->file('e_anudhan_certificate')->getClientOriginalExtension(), file: $request->file('e_anudhan_certificate'));
            $this->delete(filePath: 'donate/document/' . $old_data['e_anudhan_certificate']);
        }
        if ($request->hasfile('frc_certificate')) {
            $groupData['frc_certificate'] = $this->fileUpload(dir: 'donate/document/', format: $request->file('frc_certificate')->getClientOriginalExtension(), file: $request->file('frc_certificate'));
            $this->delete(filePath: 'donate/document/' . $old_data['frc_certificate']);
        }

        return $groupData;
    }

    public function getUpdateDataAPIEmployee(object $request, object $old_data): array
    {
        $groupData = [
            'identify_number' => $request['identify_number'],
            'name' => $request['name'],
            'phone' => $request['phone'],
            'email' => $request['email'],

            'holdername' => $request['beneficiary_name'],
            'bankname' => $request['bank_name'],
            'ifsccode' => $request['ifsc_code'],
            'account_num' => $request['account_no'],
        ];
        if ($request->hasfile('image')) {
            $groupData['image'] = $this->upload(dir: 'event/employee/', format: 'webp', image: $request->file('image'));
            $this->delete(filePath: 'event/employee/' . $old_data['image']);
        }
        return $groupData;
    }
    public function getUpdateDataAPIPurohit(object $request, object $old_data): array
    {
        $groupData = [
            'name' => $request['name'],
            'mobile' => $request['phone'],
            'address' => $request['address'],
            'description' => $request['description'],

            'holdername' => $request['beneficiary_name'],
            'bankname' => $request['bank_name'],
            'ifsccode' => $request['ifsc_code'],
            'account_num' => $request['account_no'],
        ];
        if ($request->hasfile('image')) {
            $groupData['profile'] = 'purohit_images/'.$this->upload(dir: 'purohit_images/', format: 'webp', image: $request->file('image'));
            $this->delete(filePath: $old_data['profile']);
        }
        return $groupData;
    }
}
