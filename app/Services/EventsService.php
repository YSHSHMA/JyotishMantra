<?php

namespace App\Services;

use Illuminate\Support\Str;

use App\Traits\FileManagerTrait;

class EventsService
{
    use FileManagerTrait;

    public function getAddData(object $request): array
    {
        $event_image = '';
        if ($request->file('event_image')) {
            $event_image = $this->upload(dir: 'event/events/', format: 'webp', image: $request->file('event_image'));
        }
        $imageNames = [];
        if ($request->file('images')) {
            foreach ($request->file('images') as $image) {
                $images = $this->upload(dir: 'event/events/', format: 'webp', image: $image);
                $imageNames[] = $images;
            }
        }
        $all_venue_data = [];
        if (isset($request['venue'][0]) && !empty($request['venue'][0])) {
            for ($i = 0; $i < count($request['venue']); $i++) {
                $id = $i;
                $all_venue_data[$i] = $request['venue'][$i];
                $all_venue_data[$i]['id'] = ($id + 1);
                $packages = [];
                if (!empty($request['venue'][$i]['package_list']) && json_decode($request['venue'][$i]['package_list'])) {
                    foreach (json_decode($request['venue'][$i]['package_list']) as $key => $va) {
                        $packages[$key]['package_name'] = $va->package_name;
                        $packages[$key]['seats_no'] = $va->seats_no;
                        $packages[$key]['price_no'] = $va->price_no;
                        $packages[$key]['available'] = $va->seats_no;
                        $packages[$key]['sold'] = 0;
                    }
                }
                $all_venue_data[$i]['package_list'] = $packages;
            }
        }

        $meta_image = '';
        if ($request->file('meta_image')) {
            $meta_image = $this->upload(dir: 'event/events/', format: 'webp', image: $request->file('meta_image'));
        }
        return [
            'event_name' => $request['event_name'][array_search('en', $request['lang'])],
            "slug" =>  Str::slug($request['event_name'][array_search('en', $request['lang'])], '-') . '-' . Str::random(6),
            'category_id' => $request['category_id'],
            'organizer_by' => $request['organizer_by'],
            'informational_status' => $request['informational_status'],
            'required_aadhar_status' => (($request['required_aadhar_status'] == 1) ? 1 : 0),
            'event_organizer_id' => $request['event_organizer_id'],
            'event_about' => $request['event_about'][array_search('en', $request['lang'])],
            'event_schedule' => $request['event_schedule'][array_search('en', $request['lang'])],
            'event_attend' => $request['event_attend'][array_search('en', $request['lang'])],
            'event_team_condition' => $request['event_team_condition'][array_search('en', $request['lang'])],
            'age_group' => $request['age_group'],
            'event_artist' => $request['event_artist'],
            'language' => $request['language'][array_search('en', $request['lang'])],
            'days' => $request['days'],
            'all_venue_data' => json_encode($all_venue_data),
            "sponsor_id"=>json_encode($request['sponsor_id']),
            'start_to_end_date' => $request['start_to_end_date'],
            'event_image' => $event_image,
            'images' => json_encode($imageNames),
            'youtube_video' => $request['youtube_video'],
            'meta_title' => $request['meta_title'],
            'meta_description' => $request['meta_description'],
            'meta_image' => $meta_image,
            'commission_live' => (($request['organizer_by'] == "inhouse") ? 1 : 2),
            'commission_seats' => (($request['organizer_by'] == "inhouse") ? 1 : 2),
            "is_approve" => (($request['organizer_by'] == "inhouse") ? 1 : 0),
            "event_approve_amount" => 0,
            "approve_amount_status" => (($request['organizer_by'] == "inhouse") ? 1 : 0),
            "status" => 0,
        ];
    }

    public function getAddartistData(object $request): array
    {
        $image = '';
        if ($request->file('image')) {
            $image = $this->upload(dir: 'event/events/', format: 'webp', image: $request->file('image'));
        }
        return [
            'name' => $request['name'][array_search('en', $request['lang'])],
            'profession' => $request['profession'][array_search('en', $request['lang'])],
            'description' => $request['description'][array_search('en', $request['lang'])],
            'image' => $image,
        ];
    }

    public function deleteImage(object $old_data): bool
    {
        if ($old_data['event_image']) {
            $this->delete('event/events/' . $old_data['event_image']);
        }
        if ($old_data['meta_image']) {
            $this->delete('event/events/' . $old_data['meta_image']);
        }
        if ($old_data['images'] && json_decode($old_data['images'])) {
            foreach (json_decode($old_data['images']) as $image) {
                $this->delete('event/events/' . $image);
            }
        }

        return true;
    }

    public function deleteImageartist(object $old_data): bool
    {
        if ($old_data['image']) {
            $this->delete('event/events/' . $old_data['image']);
        }
        return true;
    }
    public function getUpdateData(object $request, object $old_data)
    {
        $all_venue_data = [];
        $Notinster = false;
        if (isset($request['venue'][0]) && !empty($request['venue'][0])) {
            $old_venue_data = json_decode($old_data['all_venue_data'] ?? '[]', true);
            for ($i = 0; $i < count($request['venue']); $i++) {
                $id = $i;
                $all_venue_data[$i] = $request['venue'][$i];
                $all_venue_data[$i]['id'] = ($id + 1);
                $packages = [];
                if (!empty($request['venue'][$i]['package_list']) && json_decode($request['venue'][$i]['package_list'])) {
                    foreach (json_decode($request['venue'][$i]['package_list']) as $key => $va) {
                        $packages[$key]['package_name'] = $va->package_name;
                        $packages[$key]['seats_no'] = $va->seats_no;
                        $packages[$key]['price_no'] = $va->price_no;

                        $old_available = $va->seats_no;
                        $old_sold = 0;

                        if (isset($old_venue_data[$i]['package_list'])) {
                            foreach ($old_venue_data[$i]['package_list'] as $old_pkg) {
                                if ($old_pkg['package_name'] == $va->package_name) {
                                    if ($va->seats_no > $old_pkg['sold']) {
                                        $old_available = $va->seats_no - $old_pkg['sold'];
                                        $old_sold = $old_pkg['sold'];
                                    } else {
                                        $Notinster = true;
                                    }
                                    break;
                                }
                            }
                        }
                        $packages[$key]['available'] = $old_available;
                        $packages[$key]['sold'] = $old_sold;
                    }
                }
                $all_venue_data[$i]['package_list'] = $packages;
            }
        }
        if ($Notinster) {
            return false;
        }

        $imageNames = [];
        if ($request['old_image']) {
            $imageNames = json_decode($request['old_image']);
        }
        if (!empty($old_data['images']) && json_decode($old_data['images'])) {
            $oldImages = json_decode($old_data['images']);
            foreach ($oldImages as $value1) {
                if ($imageNames && !in_array($value1, $imageNames)) {
                    $this->delete('event/events/' . $value1);
                }
            }
        }
        if ($request->file('images')) {
            foreach ($request->file('images') as $image) {
                $images = $this->upload(dir: 'event/events/', format: 'webp', image: $image);
                $imageNames[] = $images;
            }
        }

        $returnArrayData = [
            'event_name' => $request['event_name'][array_search('en', $request['lang'])],
            'category_id' => $request['category_id'],
            'organizer_by' => $request['organizer_by'],
            'informational_status' => $request['informational_status'],
            'required_aadhar_status' => $request['required_aadhar_status'] ?? 0,
            'event_organizer_id' => $request['event_organizer_id'],
            'event_about' => $request['event_about'][array_search('en', $request['lang'])],
            'event_schedule' => $request['event_schedule'][array_search('en', $request['lang'])],
            'event_attend' => $request['event_attend'][array_search('en', $request['lang'])],
            'event_team_condition' => $request['event_team_condition'][array_search('en', $request['lang'])],
            'age_group' => $request['age_group'],
            'event_artist' => $request['event_artist'],
            'language' => $request['language'][array_search('en', $request['lang'])],
            'days' => $request['days'],
            'all_venue_data' => json_encode($all_venue_data),
            "sponsor_id"=>json_encode($request['sponsor_id']),
            'start_to_end_date' => $request['start_to_end_date'],
            'images' => json_encode($imageNames),
            'youtube_video' => $request['youtube_video'],
            'meta_title' => $request['meta_title'],
            'meta_description' => $request['meta_description'],
        ];
        if (empty($old_data['slug'])) {
            $returnArrayData['slug'] =  Str::slug($request['event_name'][array_search('en', $request['lang'])], '-') . '-' . Str::random(6);
        }
        if ($request->file('event_image')) {
            $this->delete('event/events/' . $old_data['event_image']);
            $returnArrayData['event_image'] = $this->upload(dir: 'event/events/', format: 'webp', image: $request->file('event_image'));
        }
        if ($request->file('meta_image')) {
            $this->delete('event/events/' . $old_data['meta_image']);
            $returnArrayData['meta_image'] = $this->upload(dir: 'event/events/', format: 'webp', image: $request->file('meta_image'));
        }
        return $returnArrayData;
    }

    public function getUpdateartistData(object $request, object $old_data): array
    {
        $returnArrayData = [
            'name' => $request['name'][array_search('en', $request['lang'])],
            'profession' => $request['profession'][array_search('en', $request['lang'])],
            'description' => $request['description'][array_search('en', $request['lang'])],
        ];

        if ($request->file('image')) {
            $this->delete('event/events/' . $old_data['image']);
            $returnArrayData['image'] = $this->upload(dir: 'event/events/', format: 'webp', image: $request->file('image'));
        }

        return $returnArrayData;
    }

    public function getUpdateCommissionData(object $request): array
    {

        return [
            'commission_live' => $request['live_stream_commission'],
            'commission_seats' => $request['seats_commission'],
        ];
    }

    public function EmployeeCreate(object $request)
    {
        $data['identify_number'] = $request['identify_number'];
        $data['name'] = $request['name'];
        $data['type'] = 'event';
        $data['phone'] = $request['em_phone'];
        $data['email'] = $request['email'];
        $data['emp_role_id'] = $request['emp_role_id'];
        $data['password'] = bcrypt($request['password']);
        if ($request->hasFile('image')) {
            $imageName = $this->fileUpload(dir: 'event/employee/', format: $request->file('image')->getClientOriginalExtension(), file: $request->file('image'));
            $data['image'] = $imageName;
        }
        $data['relation_id'] = auth('event')->user()->relation_id;
        return $data;
    }
}
