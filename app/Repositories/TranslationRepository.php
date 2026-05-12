<?php

namespace App\Repositories;

use App\Contracts\Repositories\TranslationRepositoryInterface;
use App\Models\Translation;

class TranslationRepository implements TranslationRepositoryInterface
{
    public function __construct(
        private readonly Translation $translation
    ) {}

    public function add(object $request, string $model, int|string $id): bool
    {
        foreach ($request->lang as $index => $key) {
            foreach (['name', 'short_benifits', 'description', 'title', 'process', 'benefits', 'profit', 'flow', 'temple_details', 'short_description', 'more_details', 'details', 'facilities', 'tips_restrictions', 'month_name', 'city', 'season', 'crowd', 'weather', 'sight', 'famous_for', 'short_desc', 'serdescription', 'serbenefits', 'serprocess', 'sertemple_details', 'festivals_and_events', 'room_amenities', 'room_types', 'expect_details', 'tips_details', 'temple_known', 'temple_services', 'temple_aarti', 'tourist_place', 'temple_local_food', 'hotel_name', 'booking_information', 'menu_highlights', 'restaurant_name', 'category_name', 'package_name', 'organizer_name', 'organizer_address', 'event_name', 'event_about', 'event_schedule', 'event_attend', 'event_team_condition', 'profession', 'chadhava_venue', 'short_details', 'question', 'detail', 'pooja_heading', 'pooja_venue', 'trust_name', 'full_address', 'language', 'mantra', 'owner_name', 'company_name', 'services', 'area_of_operation', 'person_name', 'person_address', 'tour_name', 'inclusion', 'exclusion', 'terms_and_conditions', 'cancellation_policy', 'time', 'highlights', 'cities_name', 'country_name', 'state_name', 'part_located', 'owner_name', 'company_name', 'address', 'services', 'area_of_operation', 'person_name', 'person_address', 'tour_name', 'inclusion', 'exclusion', 'terms_and_conditions', 'cancellation_policy', 'time', 'highlights', 'notes', 'cities_name', 'country_name', 'state_name', 'part_located','policy_name', 'message', 'terms_conditions', 'amenities', 'number_of_day','drivers_age_details','tip_for_driving','not_local_resident','local_resident'] as $type) {
                if (isset($request[$type][$index]) && $key != 'en') {
                    $this->translation->insert(
                        [
                            'translationable_type' => $model,
                            'translationable_id' => $id,
                            'locale' => $key,
                            'key' => $type,
                            'value' => $request[$type][$index]
                        ]
                    );
                }
            }
        }
        return true;
    }

    public function update(object $request, string $model, int|string $id): bool
    {
        foreach ($request->lang as $index => $key) {
            foreach (['name', 'short_benifits', 'description', 'title', 'process', 'benefits', 'profit', 'flow', 'temple_details', 'short_description', 'more_details', 'details', 'facilities', 'tips_restrictions', 'month_name', 'city', 'season', 'crowd', 'weather', 'sight', 'famous_for', 'short_desc', 'serdescription', 'serbenefits', 'serprocess', 'sertemple_details', 'festivals_and_events', 'room_amenities', 'room_types', 'expect_details', 'tips_details', 'temple_known', 'temple_services', 'temple_aarti', 'tourist_place', 'temple_local_food', 'hotel_name', 'booking_information', 'menu_highlights', 'restaurant_name', 'category_name', 'package_name', 'organizer_name', 'organizer_address', 'event_name', 'event_about', 'event_schedule', 'event_attend', 'event_team_condition', 'profession', 'chadhava_venue', 'short_details', 'question', 'detail', 'pooja_heading', 'pooja_venue', 'trust_name', 'full_address', 'language', 'mantra', 'owner_name', 'company_name', 'services', 'area_of_operation', 'person_name', 'person_address', 'tour_name', 'inclusion', 'exclusion', 'terms_and_conditions', 'cancellation_policy', 'time', 'highlights', 'cities_name', 'country_name', 'state_name', 'part_located', 'owner_name', 'company_name', 'address', 'services', 'area_of_operation', 'person_name', 'person_address', 'tour_name', 'inclusion', 'exclusion', 'terms_and_conditions', 'cancellation_policy', 'time', 'highlights', 'notes', 'cities_name', 'country_name', 'state_name', 'part_located','policy_name', 'message', 'terms_conditions', 'amenities', 'number_of_day','drivers_age_details','tip_for_driving','not_local_resident','local_resident'] as $type) {
                if (isset($request[$type][$index]) && $key != 'en') {
                    $this->translation->updateOrInsert(
                        [
                            'translationable_type' => $model,
                            'translationable_id' => $id,
                            'locale' => $key,
                            'key' => $type
                        ],
                        [
                            'value' => $request[$type][$index]
                        ]
                    );
                }
            }
        }
        return true;
    }
    public function updateDataForPushNotification(string $model, string $id, string $lang, string $key, string $value): bool
    {
        $this->translation->updateOrInsert(
            [
                'translationable_type' => $model,
                'translationable_id' => $id,
                'locale' => $lang,
                'key' => $key
            ],
            [
                'value' => $value
            ]
        );
        return true;
    }
    public function delete(string $model, int|string $id): bool
    {
        $this->translation->where('translationable_type', $model)->where('translationable_id', $id)->delete();
        return true;
    }
}