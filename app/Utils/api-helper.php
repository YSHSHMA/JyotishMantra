<?php

namespace App\Utils;

class ApiHelper
{
    public static function astroApi($url, $lang, $data)
    {
        // dd($data);
        $username = '6030';
        $password = 'e9c5f9c214dc6ef8f7b3e44ee550ac25';
        $auth = base64_encode($username . ':' . $password);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Basic ' . $auth,
            'Accept-Language: ' . $lang
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        // if ($response === false) {
        //     dd(curl_error($ch));
        // }
        return $response;

    }

    // Helper function to convert Vedic time to 24-hour time format
    public static function convertTo24Hour($time)
    {
        $time = trim($time);
        $timeParts = explode(':', $time);
        $hour = intval($timeParts[0]);
        $minute = $timeParts[1];

        if ($hour >= 24) {
            $hour -= 24;
        }

        return sprintf('%02d:%02d', $hour, $minute);
    }

    // global get api
    public static function GlobalGetApi($url)
    {
        // dd($url);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
    
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            return 'Curl error: ' . $error;
        }
    
        curl_close($ch);
        return json_decode($response,true);
    }

}
