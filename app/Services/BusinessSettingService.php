<?php

namespace App\Services;

class BusinessSettingService
{

    public function getLanguageData(object $request, object $language): array
    {
        $languageArray = [];
        foreach (json_decode($language['value'], true) as $key => $data) {
            if ($data['code'] == $request['language']) {
                $lang = [
                    'id' => $data['id'],
                    'name' => $data['name'],
                    'direction' => $data['direction'] ?? 'ltr',
                    'code' => $data['code'],
                    'status' => 1,
                    'default' => true,
                ];
            } else {
                $lang = [
                    'id' => $data['id'],
                    'name' => $data['name'],
                    'direction' => $data['direction'] ?? 'ltr',
                    'code' => $data['code'],
                    'status' => $data['status'],
                    'default' => false,
                ];
            }
            $languageArray[] = $lang;
        }
        return $languageArray;
    }

}
