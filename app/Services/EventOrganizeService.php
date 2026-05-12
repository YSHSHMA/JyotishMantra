<?php

namespace App\Services;

use Illuminate\Support\Str;

use App\Traits\FileManagerTrait;

class EventOrganizeService
{
    use FileManagerTrait;

    public function getAddData(object $request)
    {

        $pan_card_image = '';
        if ($request->file('pan_card_image')) {
            $pan_card_image = $this->upload(dir: 'event/organizer/', format: 'webp', image: $request->file('pan_card_image'));
        }
        $cancelled_cheque_image = '';
        if ($request->file('cancelled_cheque_image')) {
            $cancelled_cheque_image = $this->upload(dir: 'event/organizer/', format: 'webp', image: $request->file('cancelled_cheque_image'));
        }
        $organizer_image = '';
        if ($request->file('organizer_image')) {
            $organizer_image = $this->upload(dir: 'event/organizer/', format: 'webp', image: $request->file('organizer_image'));
        }
        $aadhar_image = '';
        if ($request->file('aadhar_image')) {
            $aadhar_image = $this->upload(dir: 'event/organizer/', format: 'webp', image: $request->file('aadhar_image'));
        }
        return [
            'organizer_name' => $request['organizer_name'][array_search('en', $request['lang'])],
            'organizer_pan_no' => $request['organizer_pan_no'],
            'organizer_address' => $request['organizer_address'][array_search('en', $request['lang'])],
            'gst_no_type' => $request['gst_no_type'],
            'gst_no' => $request['gst_no'] ?? "",
            'itr_return' => $request['itr_return'],
            'full_name' => $request['full_name'],
            'email_address' => $request['email_address'],
            'contact_number' => $request['contact_number'],
            'beneficiary_name' => $request['beneficiary_name'],
            'account_type' => $request['account_type'],
            'bank_name' => $request['bank_name'],
            'ifsc_code' => $request['ifsc_code'],
            'account_no' => $request['account_no'],
            'pan_card_image' => $pan_card_image,
            'cancelled_cheque_image' => $cancelled_cheque_image,
            'aadhar_image' => $aadhar_image,
            'image' => $organizer_image,
        ];
    }

    public function deleteImage($old_data)
    {
        $this->delete('event/organizer/' . $old_data['pan_card_image']);
        $this->delete('event/organizer/' . $old_data['aadhar_image']);
        $this->delete('event/organizer/' . $old_data['image']);
        return $this->delete('event/organizer/' . $old_data['cancelled_cheque_image']);
    }

    public function updateData(object $request, object $old_data)
    {
        $arrayData = [
            'organizer_name' => $request['organizer_name'][array_search('en', $request['lang'])],
            'organizer_pan_no' => $request['organizer_pan_no'],
            'organizer_address' => $request['organizer_address'][array_search('en', $request['lang'])],
            'gst_no_type' => $request['gst_no_type'],
            'gst_no' => (($request['gst_no_type'] == 1) ? ($request['gst_no'] ?? "") : ""),
            'itr_return' => $request['itr_return'],
            'full_name' => $request['full_name'],
            'email_address' => $request['email_address'],
            'contact_number' => $request['contact_number'],
            'beneficiary_name' => $request['beneficiary_name'],
            'account_type' => $request['account_type'],
            'bank_name' => $request['bank_name'],
            'ifsc_code' => $request['ifsc_code'],
            'account_no' => $request['account_no'],
        ];
        if ($request->file('pan_card_image')) {
            $arrayData['pan_card_image'] = $this->upload(dir: 'event/organizer/', format: 'webp', image: $request->file('pan_card_image'));
            $this->delete('event/organizer/' . $old_data['pan_card_image']);
        }

        if ($request->file('cancelled_cheque_image')) {
            $arrayData['cancelled_cheque_image'] = $this->upload(dir: 'event/organizer/', format: 'webp', image: $request->file('cancelled_cheque_image'));
            $this->delete('event/organizer/' . $old_data['cancelled_cheque_image']);
        }

        if ($request->file('organizer_image')) {
            $arrayData['image'] = $this->upload(dir: 'event/organizer/', format: 'webp', image: $request->file('organizer_image'));
        }

        if ($request->file('aadhar_image')) {
            $arrayData['aadhar_image'] = $this->upload(dir: 'event/organizer/', format: 'webp', image: $request->file('aadhar_image'));
        }
        return $arrayData;
    }


    public function ReCorrectEventData($request, $org_old, $vendor)
    {
        $dataArray = [];
        if ($vendor['all_doc_info'] && json_decode($vendor['all_doc_info'], true)) {
            $check_validate = json_decode($vendor['all_doc_info'], true);
            $getUniqueArray = [
                'full_name',
                'contact_number',
                'email_address',
                'organizer_name',
                'itr_return',
                "itr_return_image",
                "organizer_address",
                'user_image',
                "aadhar_number",
                'aadhar_image',
                'organizer_pan_no',
                'pan_card_image',
                'gst_no',
                'bank_name',
                'branch_name',
                'beneficiary_name',
                'ifsc_code',
                'account_no',
                'account_type',
                'cancelled_cheque_image',
            ];
            $dataArray['vendor']  = [];
            $dataArray['event']  = [];
            foreach ($getUniqueArray as $value) {
                if ($check_validate[$value] == 2) {
                    $dataArray['vendor']['reupload_doc_status'] = 3;
                    break;
                }
            }
            if ($check_validate['full_name'] == 2 || $check_validate['full_name'] == 0) {
                $check_validate['full_name'] = 0;
                $dataArray['event']['full_name'] = $request['f_name'];
                $dataArray['vendor']['f_name'] = explode(" ", $request['f_name'])[0] ?? '';
                $dataArray['vendor']['l_name'] = explode(" ", $request['f_name'])[1] ?? '';
            }
            if ($check_validate['contact_number'] == 2 || $check_validate['contact_number'] == 0) {
                $check_validate['contact_number'] = 0;
                $dataArray['event']['contact_number'] = $request['contact_number'];
                $dataArray['vendor']['phone'] = $request['contact_number'];
            }
            if ($check_validate['email_address'] == 2 || $check_validate['email_address'] == 0) {
                $check_validate['email_address'] = 0;
                $dataArray['event']['email_address'] = $request['email_address'];
                $dataArray['vendor']['email'] = $request['email_address'];
            }
            if ($check_validate['organizer_name'] == 2 || $check_validate['organizer_name'] == 0) {
                $check_validate['organizer_name'] = 0;
                $dataArray['event']['organizer_name'] = $request['organizer_name'];
            }
            if ($check_validate['itr_return'] == 2 || $check_validate['itr_return'] == 0) {
                $check_validate['itr_return'] = 0;
                $dataArray['event']['itr_return'] = $request['itr_return'];
            }
            if ($check_validate['itr_return_image'] == 2 || $check_validate['itr_return_image'] == 0) {
                $check_validate['itr_return_image'] = 0;
                $dataArray['event']['itr_return_image'] = $request->file('itr_return_image') ? $this->update(dir: 'event/organizer/', oldImage: $org_old['itr_return_image'], format: 'webp', image: $request->file('itr_return_image')) : $org_old['itr_return_image'];
            }

            if ($check_validate['aadhar_number'] == 2 || $check_validate['aadhar_number'] == 0) {
                $check_validate['aadhar_number'] = 0;
                $dataArray['event']['aadhar_number'] = $request['aadhar_number'];
            }
            if ($check_validate['user_image'] == 2 || $check_validate['user_image'] == 0) {
                $check_validate['user_image'] = 0;
                $dataArray['event']['image'] =  $request->file('image') ? $this->update(dir: 'event/organizer/', oldImage: $org_old['image'], format: 'webp', image: $request->file('image')) : $org_old['image'];
                $dataArray['vendor']['image'] = $dataArray['event']['image'];
            }
            if ($check_validate['gst_no'] == 2 || $check_validate['gst_no'] == 0) {
                $check_validate['gst_no'] = 0;
                $dataArray['event']['gst_no_type'] = ((empty($request['gst'])) ? 0 : 1);
                $dataArray['event']['gst_no'] = ((empty($request['gst'])) ? '' : $request['gst']);
                $dataArray['vendor']['gst'] = ((empty($request['gst'])) ? '' : $request['gst']);
            }

            if ($check_validate['organizer_address'] == 2 || $check_validate['organizer_address'] == 0) {
                $check_validate['organizer_address'] = 0;
                $dataArray['event']['organizer_address'] = $request['organizer_address'];
            }

            if ($check_validate['aadhar_image'] == 2 || $check_validate['aadhar_image'] == 0) {
                $check_validate['aadhar_image'] = 0;
                $dataArray['event']['aadhar_image'] = $request->file('aadhar_front_image') ? $this->update(dir: 'event/organizer/', oldImage: $org_old['aadhar_image'], format: 'webp', image: $request->file('aadhar_front_image')) : $org_old['aadhar_image'];
                $dataArray['vendor']['aadhar_front_image'] = $dataArray['event']['aadhar_image'];
            }
            if ($check_validate['organizer_pan_no'] == 2 || $check_validate['organizer_pan_no'] == 0) {
                $check_validate['organizer_pan_no'] = 0;
                $dataArray['event']['organizer_pan_no'] = $request['pan_number'];
                $dataArray['vendor']['pan_number'] = $request['pan_number'];
            }
            if ($check_validate['pan_card_image'] == 2 || $check_validate['pan_card_image'] == 0) {
                $check_validate['pan_card_image'] = 0;
                $dataArray['event']['pan_card_image'] = $request->file('pan_card_image') ? $this->update(dir: 'event/organizer/', oldImage: $org_old['pan_card_image'], format: 'webp', image: $request->file('pan_card_image')) : $org_old['pan_card_image'];
                $dataArray['vendor']['pancard_image'] = $dataArray['event']['pan_card_image'];
            }

            if ($check_validate['bank_name'] == 2 || $check_validate['bank_name'] == 0) {
                $check_validate['bank_name'] = 0;
                $dataArray['event']['bank_name'] = $request['bank_name'];
                $dataArray['vendor']['bank_name'] = $request['bank_name'];
            }
            if ($check_validate['branch_name'] == 2 || $check_validate['branch_name'] == 0) {
                $check_validate['branch_name'] = 0;
                $dataArray['event']['branch_name'] = $request['branch_name'];
                $dataArray['vendor']['branch'] = $request['branch_name'];
            }
            if ($check_validate['ifsc_code'] == 2 || $check_validate['ifsc_code'] == 0) {
                $check_validate['ifsc_code'] = 0;
                $dataArray['event']['ifsc_code'] = $request['ifsc'];
                $dataArray['vendor']['ifsc'] = $request['ifsc'];
            }
            if ($check_validate['account_type'] == 2 || $check_validate['account_type'] == 0) {
                $check_validate['account_type'] = 0;
                $dataArray['event']['account_type'] = $request['account_type'];
            }
            if ($check_validate['beneficiary_name'] == 2 || $check_validate['beneficiary_name'] == 0) {
                $check_validate['beneficiary_name'] = 0;
                $dataArray['event']['beneficiary_name'] = $request['holder_name'];
                $dataArray['vendor']['holder_name'] = $request['holder_name'];
            }
            if ($check_validate['account_no'] == 2 || $check_validate['account_no'] == 0) {
                $check_validate['account_no'] = 0;
                $dataArray['event']['account_no'] = $request['account_no'];
                $dataArray['vendor']['account_no'] = $request['account_no'];
            }
            $dataArray['vendor']['update_seller_status'] = 0;
            if ($check_validate['cancelled_cheque_image'] == 2 || $check_validate['cancelled_cheque_image'] == 0) {
                $check_validate['cancelled_cheque_image'] = 0;
                $dataArray['event']['cancelled_cheque_image'] =  $request->file('cancelled_cheque_image') ? $this->update(dir: 'event/organizer/', oldImage: $org_old['cancelled_cheque_image'], format: 'webp', image: $request->file('cancelled_cheque_image')) : $org_old['cancelled_cheque_image'];
                $dataArray['vendor']['cancel_check'] =  $dataArray['event']['cancelled_cheque_image'];
            }
            $dataArray['vendor']['all_doc_info'] = json_encode($check_validate);
        }
        return $dataArray;
    }
}
