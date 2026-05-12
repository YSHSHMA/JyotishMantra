<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait Whatsapp
{
    // public static function messageSend($data,$from, $reciver,$type,$filter=false,$delay = 0)
    // {
    //     $delay = $delay == 0 ? env('DELAY_TIME',1000) : $delay;

    //     if ($delay < 500) {
    //         $delay = 1;
    //     }
    //     else{
    //        $delay =  $delay/1000;
    //        $delay = round($delay);
    //     }

    //     sleep($delay);


    //     //creating session id
    //     $session_id=$from;

    //     //formating message     
    //     $message=isset($data['message'])?$data['message']:'';

    //     //formating array context
    //     $formatedBody= $filter == false ? $this->formatArray($data,$message,$type) : $data;


    //     // echo "<pre>"; print_r($formatedBody);die;
    //     //get server url
    //     $whatsServer=env('WA_SERVER_URL');

    //     //formating array before sending data to server
    //     $body['receiver']=$reciver;
    //     $body['delay']=0;
    //     $body['message']=$formatedBody;

    //     //sending data to whatsapp server       
    //     try {
    //         $response=Http::post($whatsServer.'/chats/send?id='.$session_id,$body);
    //         $status=$response->status();

    //         if ($status != 200) {
    //             $responseBody=json_decode($response->body());
    //             $responseData['message']=$responseBody->message;
    //             $responseData['status']=$status;
    //         }
    //         else{
    //             $responseData['status'] = 200;
    //         }

    //         return $responseData;
    //    } catch (Exception $e) {
    //        $responseData['status'] = 403;
    //        return $responseData;
    //    }

    // }

    public static function messageSend($data, $from, $reciver, $type, $filter = false, $delay = 0)
    {
        try {

            $delay = $delay == 0 ? env('DELAY_TIME', 1000) : $delay;
            $delay = ($delay < 500) ? 1 : round($delay / 1000);
            sleep($delay);

            $session_id = $from;
            $message = isset($data['message']) ? $data['message'] : '';

            $formatedBody = $filter === false ? self::formatArray($data, $message, $type) : $data;

            $whatsServer =env('WA_SERVER_URL');
            if (empty($whatsServer)) {
                \Log::error('WA_SERVER_URL is not configured.');
                return ['status' => 500, 'message' => 'WhatsApp server URL not configured'];
            }

            $body = [
                'receiver' => $reciver,
                'delay' => 0,
                'message' => $formatedBody
            ];

            $response = Http::post(rtrim($whatsServer, '/') . '/chats/send?id=' . $session_id, $body);
            $status = $response->status();

            if ($status !== 200) {
                $responseBody = json_decode($response->body());
                return [
                    'status' => $status,
                    'message' => $responseBody->message ?? 'Unknown error'
                ];
            }

            return ['status' => 200];
        } catch (\Exception $e) {
            \Log::error('WhatsApp message sending failed: ' . $e->getMessage());
            return ['status' => 403, 'message' => 'Message send failed'];
        }
    }

    public static function formatText($context = '', $contact_data = null, $senderdata = null)
    {
        if ($context == '') {
            return $context;
        }
        if ($contact_data != null) {
            $name = $contact_data['name'] ?? '';
            $phone = $contact_data['phone'] ?? '';

            $context = str_replace('{name}', $name, $context);
            $context = str_replace('{phone_number}', $phone, $context);
        }

        if ($senderdata != null) {
            $sender_name = $senderdata['name'] ?? '';
            $sender_phone = $senderdata['phone'] ?? '';
            $sender_email = $senderdata['email'] ?? '';

            $context = str_replace('{my_name}', $sender_name, $context);
            $context = str_replace('{my_contact_number}', $sender_phone, $context);
            $context = str_replace('{my_email}', $sender_email, $context);
        }

        return $context;
    }


    private function formatArray($data, $message, $type)
    {

        if ($type == 'plain-text') {
            $content['text'] = $message;
        } elseif ($type == 'text-with-media') {
            $content['caption'] = $message;
            $explode = explode('.', $data['attachment']);
            $file_type = strtolower(end($explode));
            $extentions = [
                'jpg' => 'image',
                'jpeg' => 'image',
                'png' => 'image',
                'webp' => 'image',
                'pdf' => 'document',
                'docx' => 'document',
                'xlsx' => 'document',
                'csv' => 'document',
                'txt' => 'document'
            ];

            $content[$extentions[$file_type]] = ['url' => asset($data['attachment'])];
        } elseif ($type == 'text-with-button') {
            $buttons = [];
            foreach ($data['buttons'] as $key => $button) {
                $button_content['buttonId'] = 'id' . $key;
                $button_content['buttonText'] = array('displayText' => $button);
                $button_content['type'] = 1;

                array_push($buttons, $button_content);
            }


            $content['text'] = $message;
            $content['footer'] = $data['footer_text'];
            $content['buttons'] = $buttons;
            $content['headerType'] = 1;
        } elseif ($type == 'text-with-template') {
            $templateButtons = [];
            foreach ($data['buttons'] as $key => $button) {
                $button_type = '';
                $button_action_content = '';

                if ($button['type'] == 'urlButton') {
                    $button_type = 'url';
                    $button_action_content = $button['action'];
                } elseif ($button['type'] == 'callButton') {
                    $button_type = 'phoneNumber';
                    $button_action_content = $button['action'];
                } else {
                    $button_type = 'id';
                    $button_action_content = 'action-id-' . $key;
                }

                $button_actions = [];
                $button_actions['displayText'] = $button['displaytext'];
                $button_actions[$button_type] = $button_action_content;



                $button_context['index'] = $key;
                $button_context[$button['type']] = $button_actions;

                array_push($templateButtons, $button_context);
                $button_context = null;
            }


            $content['text'] = $message;
            $content['footer'] = $data['footer_text'];
            $content['templateButtons'] = $templateButtons;
        } elseif ($type == 'text-with-location') {
            $content['location'] = array(
                'degreesLatitude' => $data['degreesLatitude'],
                'degreesLongitude' => $data['degreesLongitude']
            );
        } elseif ($type == 'text-with-vcard') {
            $vcard = 'BEGIN:VCARD\n' // metadata of the contact card
                . 'VERSION:3.0\n'
                . 'FN:' . $data['full_name'] . '\n' // full name
                . 'ORG:' . $data['org_name'] . ';\n' // the organization of the contact
                . 'TEL;type=CELL;type=VOICE;waid=' . $data['contact_number'] . ':' . $data['wa_number'] . '\n' // WhatsApp ID + phone number
                . 'END:VCARD';


            $content = [
                "contacts" => [
                    "displayName" => "maruf",
                    "contacts" => [[$vcard]]
                ]
            ];
        } elseif ($type == 'text-with-list') {

            $templateButtons = [];

            foreach ($data['section'] as $section_key => $sections) {

                $rows = [];

                foreach ($sections['value'] as $value_key => $value) {

                    $rowArr['title'] = $value['title'];
                    $rowArr['rowId'] = 'option-' . $section_key . '-' . $value_key;

                    if ($value['description'] != null) {
                        $rowArr['description'] = $value['description'];
                    }
                    array_push($rows, $rowArr);
                    $rowArr = [];
                }

                $row['title'] = $sections['title'];
                $row['rows'] = $rows;


                array_push($templateButtons, $row);
                $row = [];
            }

            $content = [
                "text" => $message,
                "footer" =>  $data['footer_text'],
                "title" => $data['header_title'],
                "buttonText" => $data['button_text'],
                "sections" => $templateButtons
            ];
        }

        // dd($content);
        return $content;
    }

    private function formatBody($context = '', $user_id)
    {
        if ($context == '') {
            return $context;
        }
        return $context;
    }
}