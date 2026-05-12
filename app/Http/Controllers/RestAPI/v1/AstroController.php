<?php

namespace App\Http\Controllers\RestAPI\v1;

use App\Http\Controllers\Controller;
use App\Models\UserKundali;
use App\Models\UserKundaliMilan;
use App\Models\Rashi;
use App\Models\VideoListType;
use App\Models\Service;
use App\Models\Category;
use App\Models\FastFestival;
use App\Models\Video;
use App\Models\Muhurat;
use App\Models\Service_order;
use App\Models\PanchangMoonImage;
use App\Models\ServiceReview;
use App\Models\VideoCategory;
use App\Models\VideoSubCategory;
use App\Utils\ApiHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;


class AstroController extends Controller
{
    public function panchang(Request $request)
    {
        $date = explode('/', $request->date);
        $time = explode(':', $request->time);
        $apiData = array(
            'day' => $date['0'],
            'month' => $date['1'],
            'year' => $date['2'],
            'hour' => $time['0'],
            'min' => $time['1'],
            'lat' => intval($request->latitutde),
            'lon' => intval($request->longitude),
            'tzone' => intval($request->timezone),
            'language' => $request->language
        );

        // Check if the requested language is 'hi' or 'en'
        $language = in_array($request->language, ['hi', 'en']) ? $request->language : 'hi';

        $panchang = json_decode(ApiHelper::astroApi('https://json.astrologyapi.com/v1/advanced_panchang/sunrise', $language, $apiData), true);

        if ($panchang) {
            return response()->json(['status' => 200, 'panchang' => $panchang]);
        }
        return response()->json(['status' => 400]);
    }

    public function planate_position(Request $request)
    {
        try {
            // Split date and time into their respective parts
            $date = explode('/', $request->date);
            $time = explode(':', $request->time);

            // Prepare the API data array
            $apiData = [
                'day' => $date[0],
                'month' => $date[1],
                'year' => $date[2],
                'hour' => $time[0],
                'min' => $time[1],
                'lat' => intval($request->latitude),
                'lon' => intval($request->longitude),
                'tzone' => intval($request->timezone),
                'language' => $request->language
            ];

            // Ensure the language is either 'hi' or 'en'
            $language = in_array($request->language, ['hi', 'en']) ? $request->language : 'hi';

            // Fetch the planetary data from the API
            $response = ApiHelper::astroApi('https://json.astrologyapi.com/v1/planet_panchang/sunrise', $language, $apiData);
            $planate = json_decode($response, true);

            // Check if the response from the API is valid
            if (!$planate) {
                return response()->json(['status' => 400, 'error' => 'Invalid API response']);
            }

            // Mapping of planet names to image filenames
            $planetImageMap = [
                'सूर्य ' => 'sun.png',
                'Sun' => 'sun.png',
                'चन्द्र ' => 'moon.png',
                'Moon' => 'moon.png',
                'मंगल ' => 'mars.png',
                'Mars' => 'mars.png',
                'बुध  ' => 'mercury.png',
                'Mercury' => 'mercury.png',
                'गुरु ' => 'jupiter.png',
                'Jupiter' => 'jupiter.png',
                'शुक्र ' => 'venus.png',
                'Venus' => 'venus.png',
                'शनि ' => 'saturn.png',
                'Saturn' => 'saturn.png',
                'राहु ' => 'rahu.png',
                'Rahu' => 'rahu.png',
                'केतु ' => 'ketu.png',
                'Ketu' => 'ketu.png',
                'लग्न' => 'ascendant.png',
                'Ascendant' => 'ascendant.png'
            ];

            // Array to hold the images for each planet
            $planate_images = [];

            // Iterate through each planet and find its corresponding image
            foreach ($planate as $planet) {
                if (isset($planetImageMap[$planet['name']])) {
                    $planate_images[] = [
                        'name' => $planet['name'],
                        'image' => asset("public/planet-image/" . $planetImageMap[$planet['name']])
                    ];
                } else {
                    // Handle case where image is not found for a specific planet
                    $planate_images[] = [
                        'name' => $planet['name'],
                        'image' => asset("public/planet-image/default.png") // Assuming a default image is present
                    ];
                }
            }

            // Return the response with all planet images
            return response()->json([
                'status' => 200,
                'planate' => $planate,
                'planate_images' => $planate_images
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'error' => 'Server error: ' . $e->getMessage()]);
        }
    }

    public function old_hora(Request $request)
    {
        $date = explode('/', $request->date);
        $time = explode(':', $request->time);
        $apiData = array(
            'day' => $date['0'],
            'month' => $date['1'],
            'year' => $date['2'],
            'hour' => $time['0'],
            'min' => $time['1'],
            'lat' => intval($request->latitutde),
            'lon' => intval($request->longitude),
            'tzone' => intval($request->timezone),
            'language' => $request->language
        );

        // Check if the requested language is 'hi' or 'en'
        $language = in_array($request->language, ['hi', 'en']) ? $request->language : 'hi';

        $hora = json_decode(ApiHelper::astroApi('https://json.astrologyapi.com/v1/hora_muhurta', $language, $apiData), true);

        if ($hora) {
            // Access the day and night arrays
            $dayHora = $hora['hora']['day'];
            $nightHora = $hora['hora']['night'];

            // Extract start and end times from each period
            $dayPeriod = [];
            foreach ($dayHora as $period) {
                $timeParts = explode(" : ", $period['time']);
                if ($period['hora'] == 'बुध  ') {
                    $hora_detail = 'बुध का होरा शास्त्र, ज्योतिष, लेखन, मुद्रण, और प्रकाशन के कार्यों के लिए शुभ होता है। आभूषण खरीदने या पहनने के लिए, सभी प्रकार के अध्ययन और शिक्षण के लिए, और दवाओं से संबंधित सभी कार्यों के लिए भी बुध का होरा बहुत शुभ माना जाता है। व्यापार और व्यवसाय से जुड़े मामलों के लिए और दूरसंचार, कंप्यूटर से संबंधित कार्यों के लिए भी यह होरा अनुकूल होता है।';
                    $hora_anukul = 'वार्तालाप, व्यापार, यात्रा की योजना बनाना, लंबी यात्रा करना, लेखन कार्य, और दैनिक जीवन के कार्यों के लिए बुध का होरा अनुकूल होता है।';
                    $hora_pratikul = 'दूसरों की आलोचना करना।';
                    $rang = 'हरा';
                    $bhojan = 'हरा मूंग';
                    $ratna = 'हरा पन्ना';
                    $fool = 'सफेद लिली पुष्प';
                    $sankhya = '5';
                    $vahan = 'गरूड़';
                    $dhatu = 'घोड़ा ';
                    $hora_image = asset("public/hora-image/mercury.png");
                }
                if ($period['hora'] == 'Mercury') {
                    $hora_detail = 'Mercury Hora is auspicious for works related to scriptures, astrology, writing, printing, and publishing. Mercury Hora is also considered very auspicious for buying or wearing jewelry, for all kinds of studies and teaching, and for all works related to medicines. It is also auspicious for matters related to trade and business, and for works related to telecommunication, computers.';
                    $hora_anukul = 'Mercury Hora is favorable for conversations, business, planning a trip, undertaking long journeys, writing work, and daily life works.';
                    $hora_pratikul = 'Criticizing others.';
                    $rang = 'green';
                    $bhojan = 'green mung bean';
                    $ratna = 'green emerald';
                    $fool = 'white lily';
                    $sankhya = '5';
                    $vahan = 'Garuda';
                    $dhatu = 'horse';
                    $hora_image = asset("public/hora-image/mercury.png");
                }
                if ($period['hora'] == 'चन्द्र ') {
                    $hora_detail = 'चंद्रमा का होरा सेवा में शामिल होने, वरिष्ठों से मिलने, स्थान और निवास बदलने के लिए, यात्रा करने के लिए, घर और संपत्ति से संबंधित कार्यों के लिए, विपरीत लिंग और रोमांस के लिए, आभूषण खरीदने और पहनने के लिए, मध्यस्थता के लिए, कपड़े और वस्त्रों की खरीद और बिक्री के लिए, पानी से जुड़े सभी कार्यों और रचनात्मक और कलात्मक कार्यों के लिए शुभ होता है।';
                    $hora_anukul = 'मातृ संबंधियों से मिलना, दूसरों की सहायता करना, आध्यात्मिक गतिविधियों में भाग लेना, मन और भावनाओं को संतुलित करने के लिए प्रार्थना करना, खुद और दूसरों की देखभाल करना, और अंतर्ज्ञान पर ध्यान केंद्रित करना अनुकूल होता है।';
                    $hora_pratikul = 'अत्यधिक अकेलापन, भावनात्मक असंतुलन, महत्वपूर्ण निर्णय लेना, कार्यों को टालना और महिलाओं का अनादर करना।';
                    $rang = 'सफेद';
                    $bhojan = 'सफेद चावल';
                    $ratna = 'मोती';
                    $fool = 'सफेद लिली';
                    $sankhya = '2';
                    $vahan = 'मोती निर्मित रथ';
                    $dhatu = 'सीसी रांगा';
                    $hora_image = asset("public/hora-image/moon.png");
                }
                if ($period['hora'] == 'Moon') {
                    $hora_detail = 'Moon Hora is auspicious for joining service, meeting superiors, changing place and residence, traveling, works related to home and property, for opposite sex and romance, for buying and wearing jewelry, for mediation, for buying clothes and garments. Auspicious for buying and selling, all work related to water, and creative and artistic work.';
                    $hora_anukul = 'Meeting maternal relatives, helping others, participating in spiritual activities, praying to balance the mind and emotions, taking care of oneself and others, and focusing on intuition.';
                    $hora_pratikul = 'Extreme loneliness, emotional imbalance, making important decisions, procrastinating, and disrespecting women.';
                    $rang = 'white';
                    $bhojan = 'white rice';
                    $ratna = 'pearl';
                    $fool = 'white lily';
                    $sankhya = '2';
                    $vahan = 'chariot made of pearl';
                    $dhatu = 'CC ranga';
                    $hora_image = asset("public/hora-image/moon.png");
                }
                if ($period['hora'] == 'शनि ') {
                    $hora_detail = 'शनि का होरा तेल और लोहे से संबंधित व्यवसायों के लिए और श्रम से संबंधित मामलों को संभालने के लिए उपयुक्त होता है। अन्य सभी कार्यों के लिए यह होरा अशुभ माना जाता है।';
                    $hora_anukul = 'आध्यात्मिक गतिविधियों में भाग लेना, जरूरतमंदों की देखभाल करना, शारीरिक रूप से विकलांग लोगों की सहायता करना, भूखों को भोजन कराना, और बुजुर्गों की सहायता करना।';
                    $hora_pratikul = 'स्वतंत्रता का दुरुपयोग करना और दूसरों के प्रति अनुचित या कठोर व्यवहार करना।';
                    $rang = 'काला';
                    $bhojan = 'काले तिल';
                    $ratna = 'नीलम';
                    $fool = 'सफेद चमेली';
                    $sankhya = '8';
                    $vahan = 'कौआ';
                    $dhatu = 'लोहा';
                    $hora_image = asset("public/hora-image/saturn.png");
                }
                if ($period['hora'] == 'Saturn') {
                    $hora_detail = 'Saturn"s Hora is suitable for businesses related to oil and iron, and for handling matters related to labor. This Hora is considered inauspicious for all other activities.';
                    $hora_anukul = 'Participating in spiritual activities, caring for the needy, helping the physically challenged, feeding the hungry, and assisting the elderly.';
                    $hora_pratikul = 'Abusing freedom and being unfair or harsh towards others.';
                    $rang = 'black';
                    $bhojan = 'black sesame';
                    $ratna = 'sapphire';
                    $fool = 'white jasmine';
                    $sankhya = '8';
                    $vahan = 'crow';
                    $dhatu = 'iron';
                    $hora_image = asset("public/hora-image/saturn.png");
                }
                if ($period['hora'] == 'गुरु ') {
                    $hora_detail = 'गुरु का होरा सभी शुभ कार्यों के लिए अत्यधिक शुभ माना जाता है। नौकरी में शामिल होने, व्यवसाय शुरू करने, वरिष्ठों से मिलने, नया कोर्स शुरू करने या अदालत से संबंधित मामलों के लिए, सभी धार्मिक कार्यों के लिए, विवाह वार्ता और यात्रा और तीर्थयात्राओं के लिए यह होरा शुभ होता है।';
                    $hora_anukul = 'उच्च अधिकारियों के साथ मुलाकात करना, शैक्षिक योजनाएं बनाना, दान करना, धार्मिक गतिविधियों में भाग लेना, कानूनी मामलों को संभालना, और गुरु से सलाह लेकर उनके मार्गदर्शन पर चलना।';
                    $hora_pratikul = 'आलोचना करना, अस्वास्थ्यकर भोजन की आदतें अपनाना, जंक फूड और मिठाई का अधिक सेवन करना क्योंकि गुरु ग्रह वसा का अधिपति है, जिससे वजन बढ़ सकता है या स्वास्थ्य समस्याएं हो सकती हैं।';
                    $rang = 'पीला';
                    $bhojan = 'चना';
                    $ratna = 'पुखराज';
                    $fool = 'जल कुंभी';
                    $sankhya = '1';
                    $vahan = 'सफेद हाथी';
                    $dhatu = 'सोना';
                    $hora_image = asset("public/hora-image/jupiter.png");
                }
                if ($period['hora'] == 'Jupiter') {
                    $hora_detail = 'Gurus Hora is considered highly auspicious for all auspicious activities. This Hora is auspicious for joining a job, starting a business, meeting superiors, starting a new course or court-related matters, for all religious functions, marriage negotiations, and travel and pilgrimages.';
                    $hora_anukul = 'Meeting with higher officials, making educational plans, giving charity, participating in religious activities, handling legal matters, and consulting Guru and following his guidance.';
                    $hora_pratikul = 'Being critical, adopting unhealthy food habits, overeating junk food and sweets as Jupiter is the ruler of fats, can lead to weight gain or health problems.';
                    $rang = 'yellow';
                    $bhojan = 'gram';
                    $ratna = 'topaz';
                    $fool = 'water pot';
                    $sankhya = '1';
                    $vahan = 'white elephant';
                    $dhatu = 'gold';
                    $hora_image = asset("public/hora-image/jupiter.png");
                }
                if ($period['hora'] == 'मंगल ') {
                    $hora_detail = 'मंगल का होरा भूमि और कृषि से संबंधित मामलों, वाहनों की खरीद और बिक्री, इलेक्ट्रिकल और इंजीनियरिंग कार्यों, साहसिक उपक्रमों और खेलों के लिए, ऋण देने और लेने के लिए, शारीरिक अभ्यास और मार्शल आर्ट के लिए, और भाइयों से संबंधित मामलों के लिए शुभ होता है। झगड़ों और टकराव से बचें।';
                    $hora_anukul = 'साहसी कार्य, जमीन विवाद के मामले आदि कार्यों को पूर्ण करने हेतु यह समय शुभ है। पुलिस अधिकारी, अग्निशमन कर्मी, सेना आदि क्षेत्रों में कार्यरत लोगों का सम्मान करें। उन लोगों के प्रति भी आभारी रहें जो स्वास्थ्य और उपचार क्षेत्रों से जुड़े हैं।';
                    $hora_pratikul = 'बहसबाजी में भाग लेना और किसी के विचारों पर जोर देना। बुरी आदतों में लिप्त लोग अपना स्वास्थ्य खराब कर सकते हैं।';
                    $rang = 'लाल';
                    $bhojan = 'अरहर';
                    $ratna = 'लाल मूंगा';
                    $fool = 'आबोली पुष्प';
                    $sankhya = '9 (6 पर कुछ प्रभाव)';
                    $vahan = 'हंस';
                    $dhatu = 'तांबा';
                    $hora_image = asset("public/hora-image/mars.png");
                }
                if ($period['hora'] == 'Mars') {
                    $hora_detail = 'Mars Hora is auspicious for matters related to land and agriculture, purchase and sale of vehicles, electrical and engineering works, adventurous ventures and sports, giving and taking loans, physical exercises and martial arts, and matters related to brothers. Avoid fights and conflicts.';
                    $hora_anukul = 'This is an auspicious time to complete adventure activities, land dispute cases etc. Respect people working in the fields like police officers, fire fighters, army etc. Also be grateful to those who are associated with health and treatment fields.","hora_pratikul": "Participate in debates and emphasize ones views. People indulging in bad habits can spoil their health.';
                    $hora_pratikul = 'Participate in debates and emphasize ones views. People indulging in bad habits can spoil their health.';
                    $rang = 'red';
                    $bhojan = 'arhar';
                    $ratna = 'red coral';
                    $fool = 'Aboli flower';
                    $sankhya = '9 (some effects on 6)';
                    $vahan = 'swan';
                    $dhatu = 'copper';
                    $hora_image = asset("public/hora-image/mars.png");
                }
                if ($period['hora'] == 'सूर्य ') {
                    $hora_detail = 'अपने राशि अनुसार आज का चढ़ावा अर्पित करें।';
                    $hora_anukul = 'अभी चढ़ावा चढ़ाएं।';
                    $hora_pratikul = 'मेष राशि के लिए आज का शुभ चढ़ावा।';
                    $rang = 'लाल';
                    $bhojan = 'गेहूँ';
                    $ratna = 'रत्न';
                    $fool = 'लाल कमल';
                    $sankhya = '1';
                    $vahan = 'सात घोड़ों वाला रथ';
                    $dhatu = 'तांबा';
                    $hora_image = asset("public/hora-image/sun.png");
                }
                if ($period['hora'] == 'Sun') {
                    $hora_detail = 'Offer todays offering according to your zodiac sign.';
                    $hora_anukul = 'Offer offering now.';
                    $hora_pratikul = 'Todays auspicious offering for Aries.';
                    $rang = 'red';
                    $bhojan = 'wheat';
                    $ratna = 'gem';
                    $fool = 'Red Lotus';
                    $sankhya = '1';
                    $vahan = 'chariot with seven horses';
                    $dhatu = 'copper';
                    $hora_image = asset("public/hora-image/sun.png");
                }
                if ($period['hora'] == 'शुक्र ') {
                    $hora_detail = 'शुक्र का होरा प्रेम और विवाह संबंधी मामलों के लिए, आभूषण और कपड़े खरीदने और बेचने के लिए, मनोरंजन और मनोरंजन से संबंधित मामलों के लिए, नए वाहन खरीदने और उपयोग करने और नृत्य और संगीत से संबंधित कार्यों के लिए शुभ होता है।';
                    $hora_anukul = 'शुक्र की होरा में सुलह, प्रेम, संगीत सुनना, नए व्यापार की शुरुआत करना, मदद मांगना, नृत्य, मनोरंजन और कला का प्रदर्शन करना।';
                    $hora_pratikul = 'शुक्र की होरा में महत्वपूर्ण निर्णय लेना और महिलाओं का अनादर करना।';
                    $rang = 'सफेद';
                    $bhojan = 'सफेद सेम';
                    $ratna = 'हीरा';
                    $fool = 'सफेद कमल';
                    $sankhya = '6';
                    $vahan = 'गरूड़';
                    $dhatu = 'चाँदी';
                    $hora_image = asset("public/hora-image/venus.png");
                }
                if ($period['hora'] == 'Venus') {
                    $hora_detail = 'Venus Hora is auspicious for matters related to love and marriage, buying and selling of jewellery and clothes, matters related to entertainment and recreation, buying and using new vehicles and activities related to dance and music.';
                    $hora_anukul = 'Venus Hora is auspicious for reconciliation, love, listening to music, starting a new business, seeking help, dancing, entertainment and performing arts.';
                    $hora_pratikul = 'Venus Hora is auspicious for important decisions and disrespecting women.';
                    $rang = 'white';
                    $bhojan = 'white beans';
                    $ratna = 'diamond';
                    $fool = 'white lotus';
                    $sankhya = '6';
                    $vahan = 'Garuda';
                    $dhatu = 'silver';
                    $hora_image = asset("public/hora-image/venus.png");
                }
                $dayPeriod[] = [
                    'start_time' => $timeParts[0],
                    'end_time' => $timeParts[1],
                    'hora' => $period['hora'],
                    'hora_detail' => $hora_detail,
                    'hora_anukul' => $hora_anukul,
                    'hora_pratikul' => $hora_pratikul,
                    'rang' => $rang,
                    'bhojan' => $bhojan,
                    'ratna' => $ratna,
                    'fool' => $fool,
                    'sankhya' => $sankhya,
                    'vahan' => $vahan,
                    'dhatu' => $dhatu,
                    'hora_image' => $hora_image,
                ];
            }

            $nightPeriod = [];
            foreach ($nightHora as $period) {
                $timeParts = explode(" : ", $period['time']);
                if ($period['hora'] == 'बुध  ') {
                    $hora_detail = 'बुध का होरा शास्त्र, ज्योतिष, लेखन, मुद्रण, और प्रकाशन के कार्यों के लिए शुभ होता है। आभूषण खरीदने या पहनने के लिए, सभी प्रकार के अध्ययन और शिक्षण के लिए, और दवाओं से संबंधित सभी कार्यों के लिए भी बुध का होरा बहुत शुभ माना जाता है। व्यापार और व्यवसाय से जुड़े मामलों के लिए और दूरसंचार, कंप्यूटर से संबंधित कार्यों के लिए भी यह होरा अनुकूल होता है।';
                    $hora_anukul = 'वार्तालाप, व्यापार, यात्रा की योजना बनाना, लंबी यात्रा करना, लेखन कार्य, और दैनिक जीवन के कार्यों के लिए बुध का होरा अनुकूल होता है।';
                    $hora_pratikul = 'दूसरों की आलोचना करना।';
                    $rang = 'हरा';
                    $bhojan = 'हरा मूंग';
                    $ratna = 'हरा पन्ना';
                    $fool = 'सफेद लिली पुष्प';
                    $sankhya = '5';
                    $vahan = 'गरूड़';
                    $dhatu = 'घोड़ा ';
                    $hora_image = asset("public/hora-image/mercury.png");
                }
                if ($period['hora'] == 'Mercury') {
                    $hora_detail = 'Mercury Hora is auspicious for works related to scriptures, astrology, writing, printing, and publishing. Mercury Hora is also considered very auspicious for buying or wearing jewelry, for all kinds of studies and teaching, and for all works related to medicines. It is also auspicious for matters related to trade and business, and for works related to telecommunication, computers.';
                    $hora_anukul = 'Mercury Hora is favorable for conversations, business, planning a trip, undertaking long journeys, writing work, and daily life works.';
                    $hora_pratikul = 'Criticizing others.';
                    $rang = 'green';
                    $bhojan = 'green mung bean';
                    $ratna = 'green emerald';
                    $fool = 'white lily';
                    $sankhya = '5';
                    $vahan = 'Garuda';
                    $dhatu = 'horse';
                    $hora_image = asset("public/hora-image/mercury.png");
                }
                if ($period['hora'] == 'चन्द्र ') {
                    $hora_detail = 'चंद्रमा का होरा सेवा में शामिल होने, वरिष्ठों से मिलने, स्थान और निवास बदलने के लिए, यात्रा करने के लिए, घर और संपत्ति से संबंधित कार्यों के लिए, विपरीत लिंग और रोमांस के लिए, आभूषण खरीदने और पहनने के लिए, मध्यस्थता के लिए, कपड़े और वस्त्रों की खरीद और बिक्री के लिए, पानी से जुड़े सभी कार्यों और रचनात्मक और कलात्मक कार्यों के लिए शुभ होता है।';
                    $hora_anukul = 'मातृ संबंधियों से मिलना, दूसरों की सहायता करना, आध्यात्मिक गतिविधियों में भाग लेना, मन और भावनाओं को संतुलित करने के लिए प्रार्थना करना, खुद और दूसरों की देखभाल करना, और अंतर्ज्ञान पर ध्यान केंद्रित करना अनुकूल होता है।';
                    $hora_pratikul = 'अत्यधिक अकेलापन, भावनात्मक असंतुलन, महत्वपूर्ण निर्णय लेना, कार्यों को टालना और महिलाओं का अनादर करना।';
                    $rang = 'सफेद';
                    $bhojan = 'सफेद चावल';
                    $ratna = 'मोती';
                    $fool = 'सफेद लिली';
                    $sankhya = '2';
                    $vahan = 'मोती निर्मित रथ';
                    $dhatu = 'सीसी रांगा';
                    $hora_image = asset("public/hora-image/moon.png");
                }
                if ($period['hora'] == 'Moon') {
                    $hora_detail = 'Moon Hora is auspicious for joining service, meeting superiors, changing place and residence, traveling, works related to home and property, for opposite sex and romance, for buying and wearing jewelry, for mediation, for buying clothes and garments. Auspicious for buying and selling, all work related to water, and creative and artistic work.';
                    $hora_anukul = 'Meeting maternal relatives, helping others, participating in spiritual activities, praying to balance the mind and emotions, taking care of oneself and others, and focusing on intuition.';
                    $hora_pratikul = 'Extreme loneliness, emotional imbalance, making important decisions, procrastinating, and disrespecting women.';
                    $rang = 'white';
                    $bhojan = 'white rice';
                    $ratna = 'pearl';
                    $fool = 'white lily';
                    $sankhya = '2';
                    $vahan = 'chariot made of pearl';
                    $dhatu = 'CC ranga';
                    $hora_image = asset("public/hora-image/moon.png");
                }
                if ($period['hora'] == 'शनि ') {
                    $hora_detail = 'शनि का होरा तेल और लोहे से संबंधित व्यवसायों के लिए और श्रम से संबंधित मामलों को संभालने के लिए उपयुक्त होता है। अन्य सभी कार्यों के लिए यह होरा अशुभ माना जाता है।';
                    $hora_anukul = 'आध्यात्मिक गतिविधियों में भाग लेना, जरूरतमंदों की देखभाल करना, शारीरिक रूप से विकलांग लोगों की सहायता करना, भूखों को भोजन कराना, और बुजुर्गों की सहायता करना।';
                    $hora_pratikul = 'स्वतंत्रता का दुरुपयोग करना और दूसरों के प्रति अनुचित या कठोर व्यवहार करना।';
                    $rang = 'काला';
                    $bhojan = 'काले तिल';
                    $ratna = 'नीलम';
                    $fool = 'सफेद चमेली';
                    $sankhya = '8';
                    $vahan = 'कौआ';
                    $dhatu = 'लोहा';
                    $hora_image = asset("public/hora-image/saturn.png");
                }
                if ($period['hora'] == 'Saturn') {
                    $hora_detail = 'Saturn"s Hora is suitable for businesses related to oil and iron, and for handling matters related to labor. This Hora is considered inauspicious for all other activities.';
                    $hora_anukul = 'Participating in spiritual activities, caring for the needy, helping the physically challenged, feeding the hungry, and assisting the elderly.';
                    $hora_pratikul = 'Abusing freedom and being unfair or harsh towards others.';
                    $rang = 'black';
                    $bhojan = 'black sesame';
                    $ratna = 'sapphire';
                    $fool = 'white jasmine';
                    $sankhya = '8';
                    $vahan = 'crow';
                    $dhatu = 'iron';
                    $hora_image = asset("public/hora-image/saturn.png");
                }
                if ($period['hora'] == 'गुरु ') {
                    $hora_detail = 'गुरु का होरा सभी शुभ कार्यों के लिए अत्यधिक शुभ माना जाता है। नौकरी में शामिल होने, व्यवसाय शुरू करने, वरिष्ठों से मिलने, नया कोर्स शुरू करने या अदालत से संबंधित मामलों के लिए, सभी धार्मिक कार्यों के लिए, विवाह वार्ता और यात्रा और तीर्थयात्राओं के लिए यह होरा शुभ होता है।';
                    $hora_anukul = 'उच्च अधिकारियों के साथ मुलाकात करना, शैक्षिक योजनाएं बनाना, दान करना, धार्मिक गतिविधियों में भाग लेना, कानूनी मामलों को संभालना, और गुरु से सलाह लेकर उनके मार्गदर्शन पर चलना।';
                    $hora_pratikul = 'आलोचना करना, अस्वास्थ्यकर भोजन की आदतें अपनाना, जंक फूड और मिठाई का अधिक सेवन करना क्योंकि गुरु ग्रह वसा का अधिपति है, जिससे वजन बढ़ सकता है या स्वास्थ्य समस्याएं हो सकती हैं।';
                    $rang = 'पीला';
                    $bhojan = 'चना';
                    $ratna = 'पुखराज';
                    $fool = 'जल कुंभी';
                    $sankhya = '1';
                    $vahan = 'सफेद हाथी';
                    $dhatu = 'सोना';
                    $hora_image = asset("public/hora-image/jupiter.png");
                }
                if ($period['hora'] == 'Jupiter') {
                    $hora_detail = 'Gurus Hora is considered highly auspicious for all auspicious activities. This Hora is auspicious for joining a job, starting a business, meeting superiors, starting a new course or court-related matters, for all religious functions, marriage negotiations, and travel and pilgrimages.';
                    $hora_anukul = 'Meeting with higher officials, making educational plans, giving charity, participating in religious activities, handling legal matters, and consulting Guru and following his guidance.';
                    $hora_pratikul = 'Being critical, adopting unhealthy food habits, overeating junk food and sweets as Jupiter is the ruler of fats, can lead to weight gain or health problems.';
                    $rang = 'yellow';
                    $bhojan = 'gram';
                    $ratna = 'topaz';
                    $fool = 'water pot';
                    $sankhya = '1';
                    $vahan = 'white elephant';
                    $dhatu = 'gold';
                    $hora_image = asset("public/hora-image/jupiter.png");
                }
                if ($period['hora'] == 'मंगल ') {
                    $hora_detail = 'मंगल का होरा भूमि और कृषि से संबंधित मामलों, वाहनों की खरीद और बिक्री, इलेक्ट्रिकल और इंजीनियरिंग कार्यों, साहसिक उपक्रमों और खेलों के लिए, ऋण देने और लेने के लिए, शारीरिक अभ्यास और मार्शल आर्ट के लिए, और भाइयों से संबंधित मामलों के लिए शुभ होता है। झगड़ों और टकराव से बचें।';
                    $hora_anukul = 'साहसी कार्य, जमीन विवाद के मामले आदि कार्यों को पूर्ण करने हेतु यह समय शुभ है। पुलिस अधिकारी, अग्निशमन कर्मी, सेना आदि क्षेत्रों में कार्यरत लोगों का सम्मान करें। उन लोगों के प्रति भी आभारी रहें जो स्वास्थ्य और उपचार क्षेत्रों से जुड़े हैं।';
                    $hora_pratikul = 'बहसबाजी में भाग लेना और किसी के विचारों पर जोर देना। बुरी आदतों में लिप्त लोग अपना स्वास्थ्य खराब कर सकते हैं।';
                    $rang = 'लाल';
                    $bhojan = 'अरहर';
                    $ratna = 'लाल मूंगा';
                    $fool = 'आबोली पुष्प';
                    $sankhya = '9 (6 पर कुछ प्रभाव)';
                    $vahan = 'हंस';
                    $dhatu = 'तांबा';
                    $hora_image = asset("public/hora-image/mars.png");
                }
                if ($period['hora'] == 'Mars') {
                    $hora_detail = 'Mars Hora is auspicious for matters related to land and agriculture, purchase and sale of vehicles, electrical and engineering works, adventurous ventures and sports, giving and taking loans, physical exercises and martial arts, and matters related to brothers. Avoid fights and conflicts.';
                    $hora_anukul = 'This is an auspicious time to complete adventure activities, land dispute cases etc. Respect people working in the fields like police officers, fire fighters, army etc. Also be grateful to those who are associated with health and treatment fields.","hora_pratikul": "Participate in debates and emphasize ones views. People indulging in bad habits can spoil their health.';
                    $hora_pratikul = 'Participate in debates and emphasize ones views. People indulging in bad habits can spoil their health.';
                    $rang = 'red';
                    $bhojan = 'arhar';
                    $ratna = 'red coral';
                    $fool = 'Aboli flower';
                    $sankhya = '9 (some effects on 6)';
                    $vahan = 'swan';
                    $dhatu = 'copper';
                    $hora_image = asset("public/hora-image/mars.png");
                }
                if ($period['hora'] == 'सूर्य ') {
                    $hora_detail = 'अपने राशि अनुसार आज का चढ़ावा अर्पित करें।';
                    $hora_anukul = 'अभी चढ़ावा चढ़ाएं।';
                    $hora_pratikul = 'मेष राशि के लिए आज का शुभ चढ़ावा।';
                    $rang = 'लाल';
                    $bhojan = 'गेहूँ';
                    $ratna = 'रत्न';
                    $fool = 'लाल कमल';
                    $sankhya = '1';
                    $vahan = 'सात घोड़ों वाला रथ';
                    $dhatu = 'तांबा';
                    $hora_image = asset("public/hora-image/sun.png");
                }
                if ($period['hora'] == 'Sun') {
                    $hora_detail = 'Offer todays offering according to your zodiac sign.';
                    $hora_anukul = 'Offer offering now.';
                    $hora_pratikul = 'Todays auspicious offering for Aries.';
                    $rang = 'red';
                    $bhojan = 'wheat';
                    $ratna = 'gem';
                    $fool = 'Red Lotus';
                    $sankhya = '1';
                    $vahan = 'chariot with seven horses';
                    $dhatu = 'copper';
                    $hora_image = asset("public/hora-image/sun.png");
                }
                if ($period['hora'] == 'शुक्र ') {
                    $hora_detail = 'शुक्र का होरा प्रेम और विवाह संबंधी मामलों के लिए, आभूषण और कपड़े खरीदने और बेचने के लिए, मनोरंजन और मनोरंजन से संबंधित मामलों के लिए, नए वाहन खरीदने और उपयोग करने और नृत्य और संगीत से संबंधित कार्यों के लिए शुभ होता है।';
                    $hora_anukul = 'शुक्र की होरा में सुलह, प्रेम, संगीत सुनना, नए व्यापार की शुरुआत करना, मदद मांगना, नृत्य, मनोरंजन और कला का प्रदर्शन करना।';
                    $hora_pratikul = 'शुक्र की होरा में महत्वपूर्ण निर्णय लेना और महिलाओं का अनादर करना।';
                    $rang = 'सफेद';
                    $bhojan = 'सफेद सेम';
                    $ratna = 'हीरा';
                    $fool = 'सफेद कमल';
                    $sankhya = '6';
                    $vahan = 'गरूड़';
                    $dhatu = 'चाँदी';
                    $hora_image = asset("public/hora-image/venus.png");
                }
                if ($period['hora'] == 'Venus') {
                    $hora_detail = 'Venus Hora is auspicious for matters related to love and marriage, buying and selling of jewellery and clothes, matters related to entertainment and recreation, buying and using new vehicles and activities related to dance and music.';
                    $hora_anukul = 'Venus Hora is auspicious for reconciliation, love, listening to music, starting a new business, seeking help, dancing, entertainment and performing arts.';
                    $hora_pratikul = 'Venus Hora is auspicious for important decisions and disrespecting women.';
                    $rang = 'white';
                    $bhojan = 'white beans';
                    $ratna = 'diamond';
                    $fool = 'white lotus';
                    $sankhya = '6';
                    $vahan = 'Garuda';
                    $dhatu = 'silver';
                    $hora_image = asset("public/hora-image/venus.png");
                }
                $nightPeriod[] = [
                    'start_time' => $timeParts[0],
                    'end_time' => $timeParts[1],
                    'hora' => $period['hora'],
                    'hora_detail' => $hora_detail,
                    'hora_anukul' => $hora_anukul,
                    'hora_pratikul' => $hora_pratikul,
                    'rang' => $rang,
                    'bhojan' => $bhojan,
                    'ratna' => $ratna,
                    'fool' => $fool,
                    'sankhya' => $sankhya,
                    'vahan' => $vahan,
                    'dhatu' => $dhatu,
                    'hora_image' => $hora_image,
                ];
            }

            return response()->json([
                'status' => 200,
                'dayHora' => $dayPeriod,
                'nightHora' => $nightPeriod
            ]);
        }

        // if($hora){
        //     return response()->json(['status'=>200,'hora'=>$hora]);
        // }
        return response()->json(['status' => 400]);
    }

    public function hora(Request $request)
    {
        $date = explode('/', $request['date']);
        $time = explode(':', $request['time']);
        $apiData = array(
            'day' => $date['0'],
            'month' => $date['1'],
            'year' => $date['2'],
            'hour' => $time['0'],
            'min' => $time['1'],
            'lat' => intval($request['latitutde']),
            'lon' => intval($request['longitude']),
            'tzone' => intval($request['timezone']),
            'language' => $request['language']
        );

        $previewdate = explode('/', date('d/m/Y', strtotime("-1 day")));
        $time = explode(':', $request['time']);
        $preApiData = array(
            'day' => $previewdate['0'],
            'month' => $previewdate['1'],
            'year' => $previewdate['2'],
            'hour' => $time['0'],
            'min' => $time['1'],
            'lat' => intval($request['latitutde']),
            'lon' => intval($request['longitude']),
            'tzone' => intval($request['timezone']),
            'language' => $request['language']
        );

        // Check if the requested language is 'hi' or 'en'
        $language = in_array($request['language'], ['hi', 'en']) ? $request['language'] : 'hi';

        $hora = json_decode(ApiHelper::astroApi('https://json.astrologyapi.com/v1/hora_muhurta', $language, $apiData), true);
        $previousHora = json_decode(ApiHelper::astroApi('https://json.astrologyapi.com/v1/hora_muhurta', $language, $preApiData), true);
        $completeData = [];

        if ($previousHora) {
            // Access the day and night arrays
            $previousDayHora = $previousHora['hora']['day'];
            $previousNightHora = $previousHora['hora']['night'];

            $previousNightPeriod = [];
            foreach ($previousNightHora as $period) {
                $timeParts = explode(" : ", $period['time']);
                $start_time_24hr = $timeParts[0];
                $end_time_24hr = $timeParts[1];
                $start_hour = intval(explode(':', $start_time_24hr)[0]);
                $end_hour = intval(explode(':', $end_time_24hr)[0]);

                if (($start_hour >= 23 && $start_time_24hr >= "23:13") || ($start_hour < 6 && $end_time_24hr <= "00:32")) {
                    // $dayPeriods[] = [
                    //     'start_time' => "00:00",
                    //     'end_time' => $end_time_24hr,
                    //     'muhurta' => $period['muhurta'],
                    //     'color' => '',
                    //     'chaughdiyadetail' => ''
                    // ];

                    if ($period['hora'] == 'बुध  ') {
                        $hora_detail = 'बुध का होरा शास्त्र, ज्योतिष, लेखन, मुद्रण, और प्रकाशन के कार्यों के लिए शुभ होता है। आभूषण खरीदने या पहनने के लिए, सभी प्रकार के अध्ययन और शिक्षण के लिए, और दवाओं से संबंधित सभी कार्यों के लिए भी बुध का होरा बहुत शुभ माना जाता है। व्यापार और व्यवसाय से जुड़े मामलों के लिए और दूरसंचार, कंप्यूटर से संबंधित कार्यों के लिए भी यह होरा अनुकूल होता है।';
                        $hora_anukul = 'वार्तालाप, व्यापार, यात्रा की योजना बनाना, लंबी यात्रा करना, लेखन कार्य, और दैनिक जीवन के कार्यों के लिए बुध का होरा अनुकूल होता है।';
                        $hora_pratikul = 'दूसरों की आलोचना करना।';
                        $rang = 'हरा';
                        $bhojan = 'हरा मूंग';
                        $ratna = 'हरा पन्ना';
                        $fool = 'सफेद लिली पुष्प';
                        $sankhya = '5';
                        $vahan = 'गरूड़';
                        $dhatu = 'घोड़ा ';
                        $hora_image = asset("public/hora-image/mercury.png");
                    }
                    if ($period['hora'] == 'Mercury') {
                        $hora_detail = 'Mercury Hora is auspicious for works related to scriptures, astrology, writing, printing, and publishing. Mercury Hora is also considered very auspicious for buying or wearing jewelry, for all kinds of studies and teaching, and for all works related to medicines. It is also auspicious for matters related to trade and business, and for works related to telecommunication, computers.';
                        $hora_anukul = 'Mercury Hora is favorable for conversations, business, planning a trip, undertaking long journeys, writing work, and daily life works.';
                        $hora_pratikul = 'Criticizing others.';
                        $rang = 'green';
                        $bhojan = 'green mung bean';
                        $ratna = 'green emerald';
                        $fool = 'white lily';
                        $sankhya = '5';
                        $vahan = 'Garuda';
                        $dhatu = 'horse';
                        $hora_image = asset("public/hora-image/mercury.png");
                    }
                    if ($period['hora'] == 'चन्द्र ') {
                        $hora_detail = 'चंद्रमा का होरा सेवा में शामिल होने, वरिष्ठों से मिलने, स्थान और निवास बदलने के लिए, यात्रा करने के लिए, घर और संपत्ति से संबंधित कार्यों के लिए, विपरीत लिंग और रोमांस के लिए, आभूषण खरीदने और पहनने के लिए, मध्यस्थता के लिए, कपड़े और वस्त्रों की खरीद और बिक्री के लिए, पानी से जुड़े सभी कार्यों और रचनात्मक और कलात्मक कार्यों के लिए शुभ होता है।';
                        $hora_anukul = 'मातृ संबंधियों से मिलना, दूसरों की सहायता करना, आध्यात्मिक गतिविधियों में भाग लेना, मन और भावनाओं को संतुलित करने के लिए प्रार्थना करना, खुद और दूसरों की देखभाल करना, और अंतर्ज्ञान पर ध्यान केंद्रित करना अनुकूल होता है।';
                        $hora_pratikul = 'अत्यधिक अकेलापन, भावनात्मक असंतुलन, महत्वपूर्ण निर्णय लेना, कार्यों को टालना और महिलाओं का अनादर करना।';
                        $rang = 'सफेद';
                        $bhojan = 'सफेद चावल';
                        $ratna = 'मोती';
                        $fool = 'सफेद लिली';
                        $sankhya = '2';
                        $vahan = 'मोती निर्मित रथ';
                        $dhatu = 'सीसी रांगा';
                        $hora_image = asset("public/hora-image/moon.png");
                    }
                    if ($period['hora'] == 'Moon') {
                        $hora_detail = 'Moon Hora is auspicious for joining service, meeting superiors, changing place and residence, traveling, works related to home and property, for opposite sex and romance, for buying and wearing jewelry, for mediation, for buying clothes and garments. Auspicious for buying and selling, all work related to water, and creative and artistic work.';
                        $hora_anukul = 'Meeting maternal relatives, helping others, participating in spiritual activities, praying to balance the mind and emotions, taking care of oneself and others, and focusing on intuition.';
                        $hora_pratikul = 'Extreme loneliness, emotional imbalance, making important decisions, procrastinating, and disrespecting women.';
                        $rang = 'white';
                        $bhojan = 'white rice';
                        $ratna = 'pearl';
                        $fool = 'white lily';
                        $sankhya = '2';
                        $vahan = 'chariot made of pearl';
                        $dhatu = 'CC ranga';
                        $hora_image = asset("public/hora-image/moon.png");
                    }
                    if ($period['hora'] == 'शनि ') {
                        $hora_detail = 'शनि का होरा तेल और लोहे से संबंधित व्यवसायों के लिए और श्रम से संबंधित मामलों को संभालने के लिए उपयुक्त होता है। अन्य सभी कार्यों के लिए यह होरा अशुभ माना जाता है।';
                        $hora_anukul = 'आध्यात्मिक गतिविधियों में भाग लेना, जरूरतमंदों की देखभाल करना, शारीरिक रूप से विकलांग लोगों की सहायता करना, भूखों को भोजन कराना, और बुजुर्गों की सहायता करना।';
                        $hora_pratikul = 'स्वतंत्रता का दुरुपयोग करना और दूसरों के प्रति अनुचित या कठोर व्यवहार करना।';
                        $rang = 'काला';
                        $bhojan = 'काले तिल';
                        $ratna = 'नीलम';
                        $fool = 'सफेद चमेली';
                        $sankhya = '8';
                        $vahan = 'कौआ';
                        $dhatu = 'लोहा';
                        $hora_image = asset("public/hora-image/saturn.png");
                    }
                    if ($period['hora'] == 'Saturn') {
                        $hora_detail = 'Saturn"s Hora is suitable for businesses related to oil and iron, and for handling matters related to labor. This Hora is considered inauspicious for all other activities.';
                        $hora_anukul = 'Participating in spiritual activities, caring for the needy, helping the physically challenged, feeding the hungry, and assisting the elderly.';
                        $hora_pratikul = 'Abusing freedom and being unfair or harsh towards others.';
                        $rang = 'black';
                        $bhojan = 'black sesame';
                        $ratna = 'sapphire';
                        $fool = 'white jasmine';
                        $sankhya = '8';
                        $vahan = 'crow';
                        $dhatu = 'iron';
                        $hora_image = asset("public/hora-image/saturn.png");
                    }
                    if ($period['hora'] == 'गुरु ') {
                        $hora_detail = 'गुरु का होरा सभी शुभ कार्यों के लिए अत्यधिक शुभ माना जाता है। नौकरी में शामिल होने, व्यवसाय शुरू करने, वरिष्ठों से मिलने, नया कोर्स शुरू करने या अदालत से संबंधित मामलों के लिए, सभी धार्मिक कार्यों के लिए, विवाह वार्ता और यात्रा और तीर्थयात्राओं के लिए यह होरा शुभ होता है।';
                        $hora_anukul = 'उच्च अधिकारियों के साथ मुलाकात करना, शैक्षिक योजनाएं बनाना, दान करना, धार्मिक गतिविधियों में भाग लेना, कानूनी मामलों को संभालना, और गुरु से सलाह लेकर उनके मार्गदर्शन पर चलना।';
                        $hora_pratikul = 'आलोचना करना, अस्वास्थ्यकर भोजन की आदतें अपनाना, जंक फूड और मिठाई का अधिक सेवन करना क्योंकि गुरु ग्रह वसा का अधिपति है, जिससे वजन बढ़ सकता है या स्वास्थ्य समस्याएं हो सकती हैं।';
                        $rang = 'पीला';
                        $bhojan = 'चना';
                        $ratna = 'पुखराज';
                        $fool = 'जल कुंभी';
                        $sankhya = '1';
                        $vahan = 'सफेद हाथी';
                        $dhatu = 'सोना';
                        $hora_image = asset("public/hora-image/jupiter.png");
                    }
                    if ($period['hora'] == 'Jupiter') {
                        $hora_detail = 'Gurus Hora is considered highly auspicious for all auspicious activities. This Hora is auspicious for joining a job, starting a business, meeting superiors, starting a new course or court-related matters, for all religious functions, marriage negotiations, and travel and pilgrimages.';
                        $hora_anukul = 'Meeting with higher officials, making educational plans, giving charity, participating in religious activities, handling legal matters, and consulting Guru and following his guidance.';
                        $hora_pratikul = 'Being critical, adopting unhealthy food habits, overeating junk food and sweets as Jupiter is the ruler of fats, can lead to weight gain or health problems.';
                        $rang = 'yellow';
                        $bhojan = 'gram';
                        $ratna = 'topaz';
                        $fool = 'water pot';
                        $sankhya = '1';
                        $vahan = 'white elephant';
                        $dhatu = 'gold';
                        $hora_image = asset("public/hora-image/jupiter.png");
                    }
                    if ($period['hora'] == 'मंगल ') {
                        $hora_detail = 'मंगल का होरा भूमि और कृषि से संबंधित मामलों, वाहनों की खरीद और बिक्री, इलेक्ट्रिकल और इंजीनियरिंग कार्यों, साहसिक उपक्रमों और खेलों के लिए, ऋण देने और लेने के लिए, शारीरिक अभ्यास और मार्शल आर्ट के लिए, और भाइयों से संबंधित मामलों के लिए शुभ होता है। झगड़ों और टकराव से बचें।';
                        $hora_anukul = 'साहसी कार्य, जमीन विवाद के मामले आदि कार्यों को पूर्ण करने हेतु यह समय शुभ है। पुलिस अधिकारी, अग्निशमन कर्मी, सेना आदि क्षेत्रों में कार्यरत लोगों का सम्मान करें। उन लोगों के प्रति भी आभारी रहें जो स्वास्थ्य और उपचार क्षेत्रों से जुड़े हैं।';
                        $hora_pratikul = 'बहसबाजी में भाग लेना और किसी के विचारों पर जोर देना। बुरी आदतों में लिप्त लोग अपना स्वास्थ्य खराब कर सकते हैं।';
                        $rang = 'लाल';
                        $bhojan = 'अरहर';
                        $ratna = 'लाल मूंगा';
                        $fool = 'आबोली पुष्प';
                        $sankhya = '9 (6 पर कुछ प्रभाव)';
                        $vahan = 'हंस';
                        $dhatu = 'तांबा';
                        $hora_image = asset("public/hora-image/mars.png");
                    }
                    if ($period['hora'] == 'Mars') {
                        $hora_detail = 'Mars Hora is auspicious for matters related to land and agriculture, purchase and sale of vehicles, electrical and engineering works, adventurous ventures and sports, giving and taking loans, physical exercises and martial arts, and matters related to brothers. Avoid fights and conflicts.';
                        $hora_anukul = 'This is an auspicious time to complete adventure activities, land dispute cases etc. Respect people working in the fields like police officers, fire fighters, army etc. Also be grateful to those who are associated with health and treatment fields.","hora_pratikul": "Participate in debates and emphasize ones views. People indulging in bad habits can spoil their health.';
                        $hora_pratikul = 'Participate in debates and emphasize ones views. People indulging in bad habits can spoil their health.';
                        $rang = 'red';
                        $bhojan = 'arhar';
                        $ratna = 'red coral';
                        $fool = 'Aboli flower';
                        $sankhya = '9 (some effects on 6)';
                        $vahan = 'swan';
                        $dhatu = 'copper';
                        $hora_image = asset("public/hora-image/mars.png");
                    }
                    if ($period['hora'] == 'सूर्य ') {
                        $hora_detail = 'अपने राशि अनुसार आज का चढ़ावा अर्पित करें।';
                        $hora_anukul = 'अभी चढ़ावा चढ़ाएं।';
                        $hora_pratikul = 'मेष राशि के लिए आज का शुभ चढ़ावा।';
                        $rang = 'लाल';
                        $bhojan = 'गेहूँ';
                        $ratna = 'रत्न';
                        $fool = 'लाल कमल';
                        $sankhya = '1';
                        $vahan = 'सात घोड़ों वाला रथ';
                        $dhatu = 'तांबा';
                        $hora_image = asset("public/hora-image/sun.png");
                    }
                    if ($period['hora'] == 'Sun') {
                        $hora_detail = 'Offer todays offering according to your zodiac sign.';
                        $hora_anukul = 'Offer offering now.';
                        $hora_pratikul = 'Todays auspicious offering for Aries.';
                        $rang = 'red';
                        $bhojan = 'wheat';
                        $ratna = 'gem';
                        $fool = 'Red Lotus';
                        $sankhya = '1';
                        $vahan = 'chariot with seven horses';
                        $dhatu = 'copper';
                        $hora_image = asset("public/hora-image/sun.png");
                    }
                    if ($period['hora'] == 'शुक्र ') {
                        $hora_detail = 'शुक्र का होरा प्रेम और विवाह संबंधी मामलों के लिए, आभूषण और कपड़े खरीदने और बेचने के लिए, मनोरंजन और मनोरंजन से संबंधित मामलों के लिए, नए वाहन खरीदने और उपयोग करने और नृत्य और संगीत से संबंधित कार्यों के लिए शुभ होता है।';
                        $hora_anukul = 'शुक्र की होरा में सुलह, प्रेम, संगीत सुनना, नए व्यापार की शुरुआत करना, मदद मांगना, नृत्य, मनोरंजन और कला का प्रदर्शन करना।';
                        $hora_pratikul = 'शुक्र की होरा में महत्वपूर्ण निर्णय लेना और महिलाओं का अनादर करना।';
                        $rang = 'सफेद';
                        $bhojan = 'सफेद सेम';
                        $ratna = 'हीरा';
                        $fool = 'सफेद कमल';
                        $sankhya = '6';
                        $vahan = 'गरूड़';
                        $dhatu = 'चाँदी';
                        $hora_image = asset("public/hora-image/venus.png");
                    }
                    if ($period['hora'] == 'Venus') {
                        $hora_detail = 'Venus Hora is auspicious for matters related to love and marriage, buying and selling of jewellery and clothes, matters related to entertainment and recreation, buying and using new vehicles and activities related to dance and music.';
                        $hora_anukul = 'Venus Hora is auspicious for reconciliation, love, listening to music, starting a new business, seeking help, dancing, entertainment and performing arts.';
                        $hora_pratikul = 'Venus Hora is auspicious for important decisions and disrespecting women.';
                        $rang = 'white';
                        $bhojan = 'white beans';
                        $ratna = 'diamond';
                        $fool = 'white lotus';
                        $sankhya = '6';
                        $vahan = 'Garuda';
                        $dhatu = 'silver';
                        $hora_image = asset("public/hora-image/venus.png");
                    }
                    $completeData[] = [
                        'start_time' => "00:00",
                        'end_time' => $end_time_24hr,
                        'hora' => $period['hora'],
                        'hora_detail' => $hora_detail,
                        'hora_anukul' => $hora_anukul,
                        'hora_pratikul' => $hora_pratikul,
                        'rang' => $rang,
                        'bhojan' => $bhojan,
                        'ratna' => $ratna,
                        'fool' => $fool,
                        'sankhya' => $sankhya,
                        'vahan' => $vahan,
                        'dhatu' => $dhatu,
                        'hora_image' => $hora_image,
                    ];
                }
                if (intval(explode(':', $start_time_24hr)[0]) >= 0) {
                    $hour = intval(explode(':', $start_time_24hr)[0]);
                    // if ($hour >= 0 && $hour < 6) { // Assuming night periods can go till 6 AM
                    //     $dayPeriods[] = [
                    //         'start_time' => $start_time_24hr,
                    //         'end_time' => $end_time_24hr,
                    //         'muhurta' => $period['muhurta'],
                    //         'color' => '',
                    //         'chaughdiyadetail' => ''
                    //     ];
                    // }

                    if ($hour >= 0 && $hour < 6) {
                        if ($period['hora'] == 'बुध  ') {
                            $hora_detail = 'बुध का होरा शास्त्र, ज्योतिष, लेखन, मुद्रण, और प्रकाशन के कार्यों के लिए शुभ होता है। आभूषण खरीदने या पहनने के लिए, सभी प्रकार के अध्ययन और शिक्षण के लिए, और दवाओं से संबंधित सभी कार्यों के लिए भी बुध का होरा बहुत शुभ माना जाता है। व्यापार और व्यवसाय से जुड़े मामलों के लिए और दूरसंचार, कंप्यूटर से संबंधित कार्यों के लिए भी यह होरा अनुकूल होता है।';
                            $hora_anukul = 'वार्तालाप, व्यापार, यात्रा की योजना बनाना, लंबी यात्रा करना, लेखन कार्य, और दैनिक जीवन के कार्यों के लिए बुध का होरा अनुकूल होता है।';
                            $hora_pratikul = 'दूसरों की आलोचना करना।';
                            $rang = 'हरा';
                            $bhojan = 'हरा मूंग';
                            $ratna = 'हरा पन्ना';
                            $fool = 'सफेद लिली पुष्प';
                            $sankhya = '5';
                            $vahan = 'गरूड़';
                            $dhatu = 'घोड़ा ';
                            $hora_image = asset("public/hora-image/mercury.png");
                        }
                        if ($period['hora'] == 'Mercury') {
                            $hora_detail = 'Mercury Hora is auspicious for works related to scriptures, astrology, writing, printing, and publishing. Mercury Hora is also considered very auspicious for buying or wearing jewelry, for all kinds of studies and teaching, and for all works related to medicines. It is also auspicious for matters related to trade and business, and for works related to telecommunication, computers.';
                            $hora_anukul = 'Mercury Hora is favorable for conversations, business, planning a trip, undertaking long journeys, writing work, and daily life works.';
                            $hora_pratikul = 'Criticizing others.';
                            $rang = 'green';
                            $bhojan = 'green mung bean';
                            $ratna = 'green emerald';
                            $fool = 'white lily';
                            $sankhya = '5';
                            $vahan = 'Garuda';
                            $dhatu = 'horse';
                            $hora_image = asset("public/hora-image/mercury.png");
                        }
                        if ($period['hora'] == 'चन्द्र ') {
                            $hora_detail = 'चंद्रमा का होरा सेवा में शामिल होने, वरिष्ठों से मिलने, स्थान और निवास बदलने के लिए, यात्रा करने के लिए, घर और संपत्ति से संबंधित कार्यों के लिए, विपरीत लिंग और रोमांस के लिए, आभूषण खरीदने और पहनने के लिए, मध्यस्थता के लिए, कपड़े और वस्त्रों की खरीद और बिक्री के लिए, पानी से जुड़े सभी कार्यों और रचनात्मक और कलात्मक कार्यों के लिए शुभ होता है।';
                            $hora_anukul = 'मातृ संबंधियों से मिलना, दूसरों की सहायता करना, आध्यात्मिक गतिविधियों में भाग लेना, मन और भावनाओं को संतुलित करने के लिए प्रार्थना करना, खुद और दूसरों की देखभाल करना, और अंतर्ज्ञान पर ध्यान केंद्रित करना अनुकूल होता है।';
                            $hora_pratikul = 'अत्यधिक अकेलापन, भावनात्मक असंतुलन, महत्वपूर्ण निर्णय लेना, कार्यों को टालना और महिलाओं का अनादर करना।';
                            $rang = 'सफेद';
                            $bhojan = 'सफेद चावल';
                            $ratna = 'मोती';
                            $fool = 'सफेद लिली';
                            $sankhya = '2';
                            $vahan = 'मोती निर्मित रथ';
                            $dhatu = 'सीसी रांगा';
                            $hora_image = asset("public/hora-image/moon.png");
                        }
                        if ($period['hora'] == 'Moon') {
                            $hora_detail = 'Moon Hora is auspicious for joining service, meeting superiors, changing place and residence, traveling, works related to home and property, for opposite sex and romance, for buying and wearing jewelry, for mediation, for buying clothes and garments. Auspicious for buying and selling, all work related to water, and creative and artistic work.';
                            $hora_anukul = 'Meeting maternal relatives, helping others, participating in spiritual activities, praying to balance the mind and emotions, taking care of oneself and others, and focusing on intuition.';
                            $hora_pratikul = 'Extreme loneliness, emotional imbalance, making important decisions, procrastinating, and disrespecting women.';
                            $rang = 'white';
                            $bhojan = 'white rice';
                            $ratna = 'pearl';
                            $fool = 'white lily';
                            $sankhya = '2';
                            $vahan = 'chariot made of pearl';
                            $dhatu = 'CC ranga';
                            $hora_image = asset("public/hora-image/moon.png");
                        }
                        if ($period['hora'] == 'शनि ') {
                            $hora_detail = 'शनि का होरा तेल और लोहे से संबंधित व्यवसायों के लिए और श्रम से संबंधित मामलों को संभालने के लिए उपयुक्त होता है। अन्य सभी कार्यों के लिए यह होरा अशुभ माना जाता है।';
                            $hora_anukul = 'आध्यात्मिक गतिविधियों में भाग लेना, जरूरतमंदों की देखभाल करना, शारीरिक रूप से विकलांग लोगों की सहायता करना, भूखों को भोजन कराना, और बुजुर्गों की सहायता करना।';
                            $hora_pratikul = 'स्वतंत्रता का दुरुपयोग करना और दूसरों के प्रति अनुचित या कठोर व्यवहार करना।';
                            $rang = 'काला';
                            $bhojan = 'काले तिल';
                            $ratna = 'नीलम';
                            $fool = 'सफेद चमेली';
                            $sankhya = '8';
                            $vahan = 'कौआ';
                            $dhatu = 'लोहा';
                            $hora_image = asset("public/hora-image/saturn.png");
                        }
                        if ($period['hora'] == 'Saturn') {
                            $hora_detail = 'Saturn"s Hora is suitable for businesses related to oil and iron, and for handling matters related to labor. This Hora is considered inauspicious for all other activities.';
                            $hora_anukul = 'Participating in spiritual activities, caring for the needy, helping the physically challenged, feeding the hungry, and assisting the elderly.';
                            $hora_pratikul = 'Abusing freedom and being unfair or harsh towards others.';
                            $rang = 'black';
                            $bhojan = 'black sesame';
                            $ratna = 'sapphire';
                            $fool = 'white jasmine';
                            $sankhya = '8';
                            $vahan = 'crow';
                            $dhatu = 'iron';
                            $hora_image = asset("public/hora-image/saturn.png");
                        }
                        if ($period['hora'] == 'गुरु ') {
                            $hora_detail = 'गुरु का होरा सभी शुभ कार्यों के लिए अत्यधिक शुभ माना जाता है। नौकरी में शामिल होने, व्यवसाय शुरू करने, वरिष्ठों से मिलने, नया कोर्स शुरू करने या अदालत से संबंधित मामलों के लिए, सभी धार्मिक कार्यों के लिए, विवाह वार्ता और यात्रा और तीर्थयात्राओं के लिए यह होरा शुभ होता है।';
                            $hora_anukul = 'उच्च अधिकारियों के साथ मुलाकात करना, शैक्षिक योजनाएं बनाना, दान करना, धार्मिक गतिविधियों में भाग लेना, कानूनी मामलों को संभालना, और गुरु से सलाह लेकर उनके मार्गदर्शन पर चलना।';
                            $hora_pratikul = 'आलोचना करना, अस्वास्थ्यकर भोजन की आदतें अपनाना, जंक फूड और मिठाई का अधिक सेवन करना क्योंकि गुरु ग्रह वसा का अधिपति है, जिससे वजन बढ़ सकता है या स्वास्थ्य समस्याएं हो सकती हैं।';
                            $rang = 'पीला';
                            $bhojan = 'चना';
                            $ratna = 'पुखराज';
                            $fool = 'जल कुंभी';
                            $sankhya = '1';
                            $vahan = 'सफेद हाथी';
                            $dhatu = 'सोना';
                            $hora_image = asset("public/hora-image/jupiter.png");
                        }
                        if ($period['hora'] == 'Jupiter') {
                            $hora_detail = 'Gurus Hora is considered highly auspicious for all auspicious activities. This Hora is auspicious for joining a job, starting a business, meeting superiors, starting a new course or court-related matters, for all religious functions, marriage negotiations, and travel and pilgrimages.';
                            $hora_anukul = 'Meeting with higher officials, making educational plans, giving charity, participating in religious activities, handling legal matters, and consulting Guru and following his guidance.';
                            $hora_pratikul = 'Being critical, adopting unhealthy food habits, overeating junk food and sweets as Jupiter is the ruler of fats, can lead to weight gain or health problems.';
                            $rang = 'yellow';
                            $bhojan = 'gram';
                            $ratna = 'topaz';
                            $fool = 'water pot';
                            $sankhya = '1';
                            $vahan = 'white elephant';
                            $dhatu = 'gold';
                            $hora_image = asset("public/hora-image/jupiter.png");
                        }
                        if ($period['hora'] == 'मंगल ') {
                            $hora_detail = 'मंगल का होरा भूमि और कृषि से संबंधित मामलों, वाहनों की खरीद और बिक्री, इलेक्ट्रिकल और इंजीनियरिंग कार्यों, साहसिक उपक्रमों और खेलों के लिए, ऋण देने और लेने के लिए, शारीरिक अभ्यास और मार्शल आर्ट के लिए, और भाइयों से संबंधित मामलों के लिए शुभ होता है। झगड़ों और टकराव से बचें।';
                            $hora_anukul = 'साहसी कार्य, जमीन विवाद के मामले आदि कार्यों को पूर्ण करने हेतु यह समय शुभ है। पुलिस अधिकारी, अग्निशमन कर्मी, सेना आदि क्षेत्रों में कार्यरत लोगों का सम्मान करें। उन लोगों के प्रति भी आभारी रहें जो स्वास्थ्य और उपचार क्षेत्रों से जुड़े हैं।';
                            $hora_pratikul = 'बहसबाजी में भाग लेना और किसी के विचारों पर जोर देना। बुरी आदतों में लिप्त लोग अपना स्वास्थ्य खराब कर सकते हैं।';
                            $rang = 'लाल';
                            $bhojan = 'अरहर';
                            $ratna = 'लाल मूंगा';
                            $fool = 'आबोली पुष्प';
                            $sankhya = '9 (6 पर कुछ प्रभाव)';
                            $vahan = 'हंस';
                            $dhatu = 'तांबा';
                            $hora_image = asset("public/hora-image/mars.png");
                        }
                        if ($period['hora'] == 'Mars') {
                            $hora_detail = 'Mars Hora is auspicious for matters related to land and agriculture, purchase and sale of vehicles, electrical and engineering works, adventurous ventures and sports, giving and taking loans, physical exercises and martial arts, and matters related to brothers. Avoid fights and conflicts.';
                            $hora_anukul = 'This is an auspicious time to complete adventure activities, land dispute cases etc. Respect people working in the fields like police officers, fire fighters, army etc. Also be grateful to those who are associated with health and treatment fields.","hora_pratikul": "Participate in debates and emphasize ones views. People indulging in bad habits can spoil their health.';
                            $hora_pratikul = 'Participate in debates and emphasize ones views. People indulging in bad habits can spoil their health.';
                            $rang = 'red';
                            $bhojan = 'arhar';
                            $ratna = 'red coral';
                            $fool = 'Aboli flower';
                            $sankhya = '9 (some effects on 6)';
                            $vahan = 'swan';
                            $dhatu = 'copper';
                            $hora_image = asset("public/hora-image/mars.png");
                        }
                        if ($period['hora'] == 'सूर्य ') {
                            $hora_detail = 'अपने राशि अनुसार आज का चढ़ावा अर्पित करें।';
                            $hora_anukul = 'अभी चढ़ावा चढ़ाएं।';
                            $hora_pratikul = 'मेष राशि के लिए आज का शुभ चढ़ावा।';
                            $rang = 'लाल';
                            $bhojan = 'गेहूँ';
                            $ratna = 'रत्न';
                            $fool = 'लाल कमल';
                            $sankhya = '1';
                            $vahan = 'सात घोड़ों वाला रथ';
                            $dhatu = 'तांबा';
                            $hora_image = asset("public/hora-image/sun.png");
                        }
                        if ($period['hora'] == 'Sun') {
                            $hora_detail = 'Offer todays offering according to your zodiac sign.';
                            $hora_anukul = 'Offer offering now.';
                            $hora_pratikul = 'Todays auspicious offering for Aries.';
                            $rang = 'red';
                            $bhojan = 'wheat';
                            $ratna = 'gem';
                            $fool = 'Red Lotus';
                            $sankhya = '1';
                            $vahan = 'chariot with seven horses';
                            $dhatu = 'copper';
                            $hora_image = asset("public/hora-image/sun.png");
                        }
                        if ($period['hora'] == 'शुक्र ') {
                            $hora_detail = 'शुक्र का होरा प्रेम और विवाह संबंधी मामलों के लिए, आभूषण और कपड़े खरीदने और बेचने के लिए, मनोरंजन और मनोरंजन से संबंधित मामलों के लिए, नए वाहन खरीदने और उपयोग करने और नृत्य और संगीत से संबंधित कार्यों के लिए शुभ होता है।';
                            $hora_anukul = 'शुक्र की होरा में सुलह, प्रेम, संगीत सुनना, नए व्यापार की शुरुआत करना, मदद मांगना, नृत्य, मनोरंजन और कला का प्रदर्शन करना।';
                            $hora_pratikul = 'शुक्र की होरा में महत्वपूर्ण निर्णय लेना और महिलाओं का अनादर करना।';
                            $rang = 'सफेद';
                            $bhojan = 'सफेद सेम';
                            $ratna = 'हीरा';
                            $fool = 'सफेद कमल';
                            $sankhya = '6';
                            $vahan = 'गरूड़';
                            $dhatu = 'चाँदी';
                            $hora_image = asset("public/hora-image/venus.png");
                        }
                        if ($period['hora'] == 'Venus') {
                            $hora_detail = 'Venus Hora is auspicious for matters related to love and marriage, buying and selling of jewellery and clothes, matters related to entertainment and recreation, buying and using new vehicles and activities related to dance and music.';
                            $hora_anukul = 'Venus Hora is auspicious for reconciliation, love, listening to music, starting a new business, seeking help, dancing, entertainment and performing arts.';
                            $hora_pratikul = 'Venus Hora is auspicious for important decisions and disrespecting women.';
                            $rang = 'white';
                            $bhojan = 'white beans';
                            $ratna = 'diamond';
                            $fool = 'white lotus';
                            $sankhya = '6';
                            $vahan = 'Garuda';
                            $dhatu = 'silver';
                            $hora_image = asset("public/hora-image/venus.png");
                        }
                        $completeData[] = [
                            'start_time' => $start_time_24hr,
                            'end_time' => $end_time_24hr,
                            'hora' => $period['hora'],
                            'hora_detail' => $hora_detail,
                            'hora_anukul' => $hora_anukul,
                            'hora_pratikul' => $hora_pratikul,
                            'rang' => $rang,
                            'bhojan' => $bhojan,
                            'ratna' => $ratna,
                            'fool' => $fool,
                            'sankhya' => $sankhya,
                            'vahan' => $vahan,
                            'dhatu' => $dhatu,
                            'hora_image' => $hora_image,
                        ];
                    }
                }
            }
        }


        if ($hora) {
            // Access the day and night arrays
            $dayHora = $hora['hora']['day'];
            $nightHora = $hora['hora']['night'];

            // Extract start and end times from each period
            $dayPeriod = [];
            foreach ($dayHora as $period) {
                $timeParts = explode(" : ", $period['time']);
                $start_time_24hr = $timeParts[0];
                $end_time_24hr = $timeParts[1];

                if (intval(explode(':', $end_time_24hr)[0]) <= 23) {


                    if ($period['hora'] == 'बुध  ') {
                        $hora_detail = 'बुध का होरा शास्त्र, ज्योतिष, लेखन, मुद्रण, और प्रकाशन के कार्यों के लिए शुभ होता है। आभूषण खरीदने या पहनने के लिए, सभी प्रकार के अध्ययन और शिक्षण के लिए, और दवाओं से संबंधित सभी कार्यों के लिए भी बुध का होरा बहुत शुभ माना जाता है। व्यापार और व्यवसाय से जुड़े मामलों के लिए और दूरसंचार, कंप्यूटर से संबंधित कार्यों के लिए भी यह होरा अनुकूल होता है।';
                        $hora_anukul = 'वार्तालाप, व्यापार, यात्रा की योजना बनाना, लंबी यात्रा करना, लेखन कार्य, और दैनिक जीवन के कार्यों के लिए बुध का होरा अनुकूल होता है।';
                        $hora_pratikul = 'दूसरों की आलोचना करना।';
                        $rang = 'हरा';
                        $bhojan = 'हरा मूंग';
                        $ratna = 'हरा पन्ना';
                        $fool = 'सफेद लिली पुष्प';
                        $sankhya = '5';
                        $vahan = 'गरूड़';
                        $dhatu = 'घोड़ा ';
                        $hora_image = asset("public/hora-image/mercury.png");
                    }
                    if ($period['hora'] == 'Mercury') {
                        $hora_detail = 'Mercury Hora is auspicious for works related to scriptures, astrology, writing, printing, and publishing. Mercury Hora is also considered very auspicious for buying or wearing jewelry, for all kinds of studies and teaching, and for all works related to medicines. It is also auspicious for matters related to trade and business, and for works related to telecommunication, computers.';
                        $hora_anukul = 'Mercury Hora is favorable for conversations, business, planning a trip, undertaking long journeys, writing work, and daily life works.';
                        $hora_pratikul = 'Criticizing others.';
                        $rang = 'green';
                        $bhojan = 'green mung bean';
                        $ratna = 'green emerald';
                        $fool = 'white lily';
                        $sankhya = '5';
                        $vahan = 'Garuda';
                        $dhatu = 'horse';
                        $hora_image = asset("public/hora-image/mercury.png");
                    }
                    if ($period['hora'] == 'चन्द्र ') {
                        $hora_detail = 'चंद्रमा का होरा सेवा में शामिल होने, वरिष्ठों से मिलने, स्थान और निवास बदलने के लिए, यात्रा करने के लिए, घर और संपत्ति से संबंधित कार्यों के लिए, विपरीत लिंग और रोमांस के लिए, आभूषण खरीदने और पहनने के लिए, मध्यस्थता के लिए, कपड़े और वस्त्रों की खरीद और बिक्री के लिए, पानी से जुड़े सभी कार्यों और रचनात्मक और कलात्मक कार्यों के लिए शुभ होता है।';
                        $hora_anukul = 'मातृ संबंधियों से मिलना, दूसरों की सहायता करना, आध्यात्मिक गतिविधियों में भाग लेना, मन और भावनाओं को संतुलित करने के लिए प्रार्थना करना, खुद और दूसरों की देखभाल करना, और अंतर्ज्ञान पर ध्यान केंद्रित करना अनुकूल होता है।';
                        $hora_pratikul = 'अत्यधिक अकेलापन, भावनात्मक असंतुलन, महत्वपूर्ण निर्णय लेना, कार्यों को टालना और महिलाओं का अनादर करना।';
                        $rang = 'सफेद';
                        $bhojan = 'सफेद चावल';
                        $ratna = 'मोती';
                        $fool = 'सफेद लिली';
                        $sankhya = '2';
                        $vahan = 'मोती निर्मित रथ';
                        $dhatu = 'सीसी रांगा';
                        $hora_image = asset("public/hora-image/moon.png");
                    }
                    if ($period['hora'] == 'Moon') {
                        $hora_detail = 'Moon Hora is auspicious for joining service, meeting superiors, changing place and residence, traveling, works related to home and property, for opposite sex and romance, for buying and wearing jewelry, for mediation, for buying clothes and garments. Auspicious for buying and selling, all work related to water, and creative and artistic work.';
                        $hora_anukul = 'Meeting maternal relatives, helping others, participating in spiritual activities, praying to balance the mind and emotions, taking care of oneself and others, and focusing on intuition.';
                        $hora_pratikul = 'Extreme loneliness, emotional imbalance, making important decisions, procrastinating, and disrespecting women.';
                        $rang = 'white';
                        $bhojan = 'white rice';
                        $ratna = 'pearl';
                        $fool = 'white lily';
                        $sankhya = '2';
                        $vahan = 'chariot made of pearl';
                        $dhatu = 'CC ranga';
                        $hora_image = asset("public/hora-image/moon.png");
                    }
                    if ($period['hora'] == 'शनि ') {
                        $hora_detail = 'शनि का होरा तेल और लोहे से संबंधित व्यवसायों के लिए और श्रम से संबंधित मामलों को संभालने के लिए उपयुक्त होता है। अन्य सभी कार्यों के लिए यह होरा अशुभ माना जाता है।';
                        $hora_anukul = 'आध्यात्मिक गतिविधियों में भाग लेना, जरूरतमंदों की देखभाल करना, शारीरिक रूप से विकलांग लोगों की सहायता करना, भूखों को भोजन कराना, और बुजुर्गों की सहायता करना।';
                        $hora_pratikul = 'स्वतंत्रता का दुरुपयोग करना और दूसरों के प्रति अनुचित या कठोर व्यवहार करना।';
                        $rang = 'काला';
                        $bhojan = 'काले तिल';
                        $ratna = 'नीलम';
                        $fool = 'सफेद चमेली';
                        $sankhya = '8';
                        $vahan = 'कौआ';
                        $dhatu = 'लोहा';
                        $hora_image = asset("public/hora-image/saturn.png");
                    }
                    if ($period['hora'] == 'Saturn') {
                        $hora_detail = 'Saturn"s Hora is suitable for businesses related to oil and iron, and for handling matters related to labor. This Hora is considered inauspicious for all other activities.';
                        $hora_anukul = 'Participating in spiritual activities, caring for the needy, helping the physically challenged, feeding the hungry, and assisting the elderly.';
                        $hora_pratikul = 'Abusing freedom and being unfair or harsh towards others.';
                        $rang = 'black';
                        $bhojan = 'black sesame';
                        $ratna = 'sapphire';
                        $fool = 'white jasmine';
                        $sankhya = '8';
                        $vahan = 'crow';
                        $dhatu = 'iron';
                        $hora_image = asset("public/hora-image/saturn.png");
                    }
                    if ($period['hora'] == 'गुरु ') {
                        $hora_detail = 'गुरु का होरा सभी शुभ कार्यों के लिए अत्यधिक शुभ माना जाता है। नौकरी में शामिल होने, व्यवसाय शुरू करने, वरिष्ठों से मिलने, नया कोर्स शुरू करने या अदालत से संबंधित मामलों के लिए, सभी धार्मिक कार्यों के लिए, विवाह वार्ता और यात्रा और तीर्थयात्राओं के लिए यह होरा शुभ होता है।';
                        $hora_anukul = 'उच्च अधिकारियों के साथ मुलाकात करना, शैक्षिक योजनाएं बनाना, दान करना, धार्मिक गतिविधियों में भाग लेना, कानूनी मामलों को संभालना, और गुरु से सलाह लेकर उनके मार्गदर्शन पर चलना।';
                        $hora_pratikul = 'आलोचना करना, अस्वास्थ्यकर भोजन की आदतें अपनाना, जंक फूड और मिठाई का अधिक सेवन करना क्योंकि गुरु ग्रह वसा का अधिपति है, जिससे वजन बढ़ सकता है या स्वास्थ्य समस्याएं हो सकती हैं।';
                        $rang = 'पीला';
                        $bhojan = 'चना';
                        $ratna = 'पुखराज';
                        $fool = 'जल कुंभी';
                        $sankhya = '1';
                        $vahan = 'सफेद हाथी';
                        $dhatu = 'सोना';
                        $hora_image = asset("public/hora-image/jupiter.png");
                    }
                    if ($period['hora'] == 'Jupiter') {
                        $hora_detail = 'Gurus Hora is considered highly auspicious for all auspicious activities. This Hora is auspicious for joining a job, starting a business, meeting superiors, starting a new course or court-related matters, for all religious functions, marriage negotiations, and travel and pilgrimages.';
                        $hora_anukul = 'Meeting with higher officials, making educational plans, giving charity, participating in religious activities, handling legal matters, and consulting Guru and following his guidance.';
                        $hora_pratikul = 'Being critical, adopting unhealthy food habits, overeating junk food and sweets as Jupiter is the ruler of fats, can lead to weight gain or health problems.';
                        $rang = 'yellow';
                        $bhojan = 'gram';
                        $ratna = 'topaz';
                        $fool = 'water pot';
                        $sankhya = '1';
                        $vahan = 'white elephant';
                        $dhatu = 'gold';
                        $hora_image = asset("public/hora-image/jupiter.png");
                    }
                    if ($period['hora'] == 'मंगल ') {
                        $hora_detail = 'मंगल का होरा भूमि और कृषि से संबंधित मामलों, वाहनों की खरीद और बिक्री, इलेक्ट्रिकल और इंजीनियरिंग कार्यों, साहसिक उपक्रमों और खेलों के लिए, ऋण देने और लेने के लिए, शारीरिक अभ्यास और मार्शल आर्ट के लिए, और भाइयों से संबंधित मामलों के लिए शुभ होता है। झगड़ों और टकराव से बचें।';
                        $hora_anukul = 'साहसी कार्य, जमीन विवाद के मामले आदि कार्यों को पूर्ण करने हेतु यह समय शुभ है। पुलिस अधिकारी, अग्निशमन कर्मी, सेना आदि क्षेत्रों में कार्यरत लोगों का सम्मान करें। उन लोगों के प्रति भी आभारी रहें जो स्वास्थ्य और उपचार क्षेत्रों से जुड़े हैं।';
                        $hora_pratikul = 'बहसबाजी में भाग लेना और किसी के विचारों पर जोर देना। बुरी आदतों में लिप्त लोग अपना स्वास्थ्य खराब कर सकते हैं।';
                        $rang = 'लाल';
                        $bhojan = 'अरहर';
                        $ratna = 'लाल मूंगा';
                        $fool = 'आबोली पुष्प';
                        $sankhya = '9 (6 पर कुछ प्रभाव)';
                        $vahan = 'हंस';
                        $dhatu = 'तांबा';
                        $hora_image = asset("public/hora-image/mars.png");
                    }
                    if ($period['hora'] == 'Mars') {
                        $hora_detail = 'Mars Hora is auspicious for matters related to land and agriculture, purchase and sale of vehicles, electrical and engineering works, adventurous ventures and sports, giving and taking loans, physical exercises and martial arts, and matters related to brothers. Avoid fights and conflicts.';
                        $hora_anukul = 'This is an auspicious time to complete adventure activities, land dispute cases etc. Respect people working in the fields like police officers, fire fighters, army etc. Also be grateful to those who are associated with health and treatment fields.","hora_pratikul": "Participate in debates and emphasize ones views. People indulging in bad habits can spoil their health.';
                        $hora_pratikul = 'Participate in debates and emphasize ones views. People indulging in bad habits can spoil their health.';
                        $rang = 'red';
                        $bhojan = 'arhar';
                        $ratna = 'red coral';
                        $fool = 'Aboli flower';
                        $sankhya = '9 (some effects on 6)';
                        $vahan = 'swan';
                        $dhatu = 'copper';
                        $hora_image = asset("public/hora-image/mars.png");
                    }
                    if ($period['hora'] == 'सूर्य ') {
                        $hora_detail = 'अपने राशि अनुसार आज का चढ़ावा अर्पित करें।';
                        $hora_anukul = 'अभी चढ़ावा चढ़ाएं।';
                        $hora_pratikul = 'मेष राशि के लिए आज का शुभ चढ़ावा।';
                        $rang = 'लाल';
                        $bhojan = 'गेहूँ';
                        $ratna = 'रत्न';
                        $fool = 'लाल कमल';
                        $sankhya = '1';
                        $vahan = 'सात घोड़ों वाला रथ';
                        $dhatu = 'तांबा';
                        $hora_image = asset("public/hora-image/sun.png");
                    }
                    if ($period['hora'] == 'Sun') {
                        $hora_detail = 'Offer todays offering according to your zodiac sign.';
                        $hora_anukul = 'Offer offering now.';
                        $hora_pratikul = 'Todays auspicious offering for Aries.';
                        $rang = 'red';
                        $bhojan = 'wheat';
                        $ratna = 'gem';
                        $fool = 'Red Lotus';
                        $sankhya = '1';
                        $vahan = 'chariot with seven horses';
                        $dhatu = 'copper';
                        $hora_image = asset("public/hora-image/sun.png");
                    }
                    if ($period['hora'] == 'शुक्र ') {
                        $hora_detail = 'शुक्र का होरा प्रेम और विवाह संबंधी मामलों के लिए, आभूषण और कपड़े खरीदने और बेचने के लिए, मनोरंजन और मनोरंजन से संबंधित मामलों के लिए, नए वाहन खरीदने और उपयोग करने और नृत्य और संगीत से संबंधित कार्यों के लिए शुभ होता है।';
                        $hora_anukul = 'शुक्र की होरा में सुलह, प्रेम, संगीत सुनना, नए व्यापार की शुरुआत करना, मदद मांगना, नृत्य, मनोरंजन और कला का प्रदर्शन करना।';
                        $hora_pratikul = 'शुक्र की होरा में महत्वपूर्ण निर्णय लेना और महिलाओं का अनादर करना।';
                        $rang = 'सफेद';
                        $bhojan = 'सफेद सेम';
                        $ratna = 'हीरा';
                        $fool = 'सफेद कमल';
                        $sankhya = '6';
                        $vahan = 'गरूड़';
                        $dhatu = 'चाँदी';
                        $hora_image = asset("public/hora-image/venus.png");
                    }
                    if ($period['hora'] == 'Venus') {
                        $hora_detail = 'Venus Hora is auspicious for matters related to love and marriage, buying and selling of jewellery and clothes, matters related to entertainment and recreation, buying and using new vehicles and activities related to dance and music.';
                        $hora_anukul = 'Venus Hora is auspicious for reconciliation, love, listening to music, starting a new business, seeking help, dancing, entertainment and performing arts.';
                        $hora_pratikul = 'Venus Hora is auspicious for important decisions and disrespecting women.';
                        $rang = 'white';
                        $bhojan = 'white beans';
                        $ratna = 'diamond';
                        $fool = 'white lotus';
                        $sankhya = '6';
                        $vahan = 'Garuda';
                        $dhatu = 'silver';
                        $hora_image = asset("public/hora-image/venus.png");
                    }
                    $dayPeriod[] = [
                        'start_time' => $start_time_24hr,
                        'end_time' => $end_time_24hr,
                        'hora' => $period['hora'],
                        'hora_detail' => $hora_detail,
                        'hora_anukul' => $hora_anukul,
                        'hora_pratikul' => $hora_pratikul,
                        'rang' => $rang,
                        'bhojan' => $bhojan,
                        'ratna' => $ratna,
                        'fool' => $fool,
                        'sankhya' => $sankhya,
                        'vahan' => $vahan,
                        'dhatu' => $dhatu,
                        'hora_image' => $hora_image,
                    ];
                }
            }

            $nightPeriod = [];
            $nightPeriodData = [];
            foreach ($nightHora as $period) {
                $timeParts = explode(" : ", $period['time']);
                $start_time_24hr = $timeParts[0];
                $end_time_24hr = $timeParts[1];
                if ($period['hora'] == 'बुध  ') {
                    $hora_detail = 'बुध का होरा शास्त्र, ज्योतिष, लेखन, मुद्रण, और प्रकाशन के कार्यों के लिए शुभ होता है। आभूषण खरीदने या पहनने के लिए, सभी प्रकार के अध्ययन और शिक्षण के लिए, और दवाओं से संबंधित सभी कार्यों के लिए भी बुध का होरा बहुत शुभ माना जाता है। व्यापार और व्यवसाय से जुड़े मामलों के लिए और दूरसंचार, कंप्यूटर से संबंधित कार्यों के लिए भी यह होरा अनुकूल होता है।';
                    $hora_anukul = 'वार्तालाप, व्यापार, यात्रा की योजना बनाना, लंबी यात्रा करना, लेखन कार्य, और दैनिक जीवन के कार्यों के लिए बुध का होरा अनुकूल होता है।';
                    $hora_pratikul = 'दूसरों की आलोचना करना।';
                    $rang = 'हरा';
                    $bhojan = 'हरा मूंग';
                    $ratna = 'हरा पन्ना';
                    $fool = 'सफेद लिली पुष्प';
                    $sankhya = '5';
                    $vahan = 'गरूड़';
                    $dhatu = 'घोड़ा ';
                    $hora_image = asset("public/hora-image/mercury.png");
                }
                if ($period['hora'] == 'Mercury') {
                    $hora_detail = 'Mercury Hora is auspicious for works related to scriptures, astrology, writing, printing, and publishing. Mercury Hora is also considered very auspicious for buying or wearing jewelry, for all kinds of studies and teaching, and for all works related to medicines. It is also auspicious for matters related to trade and business, and for works related to telecommunication, computers.';
                    $hora_anukul = 'Mercury Hora is favorable for conversations, business, planning a trip, undertaking long journeys, writing work, and daily life works.';
                    $hora_pratikul = 'Criticizing others.';
                    $rang = 'green';
                    $bhojan = 'green mung bean';
                    $ratna = 'green emerald';
                    $fool = 'white lily';
                    $sankhya = '5';
                    $vahan = 'Garuda';
                    $dhatu = 'horse';
                    $hora_image = asset("public/hora-image/mercury.png");
                }
                if ($period['hora'] == 'चन्द्र ') {
                    $hora_detail = 'चंद्रमा का होरा सेवा में शामिल होने, वरिष्ठों से मिलने, स्थान और निवास बदलने के लिए, यात्रा करने के लिए, घर और संपत्ति से संबंधित कार्यों के लिए, विपरीत लिंग और रोमांस के लिए, आभूषण खरीदने और पहनने के लिए, मध्यस्थता के लिए, कपड़े और वस्त्रों की खरीद और बिक्री के लिए, पानी से जुड़े सभी कार्यों और रचनात्मक और कलात्मक कार्यों के लिए शुभ होता है।';
                    $hora_anukul = 'मातृ संबंधियों से मिलना, दूसरों की सहायता करना, आध्यात्मिक गतिविधियों में भाग लेना, मन और भावनाओं को संतुलित करने के लिए प्रार्थना करना, खुद और दूसरों की देखभाल करना, और अंतर्ज्ञान पर ध्यान केंद्रित करना अनुकूल होता है।';
                    $hora_pratikul = 'अत्यधिक अकेलापन, भावनात्मक असंतुलन, महत्वपूर्ण निर्णय लेना, कार्यों को टालना और महिलाओं का अनादर करना।';
                    $rang = 'सफेद';
                    $bhojan = 'सफेद चावल';
                    $ratna = 'मोती';
                    $fool = 'सफेद लिली';
                    $sankhya = '2';
                    $vahan = 'मोती निर्मित रथ';
                    $dhatu = 'सीसी रांगा';
                    $hora_image = asset("public/hora-image/moon.png");
                }
                if ($period['hora'] == 'Moon') {
                    $hora_detail = 'Moon Hora is auspicious for joining service, meeting superiors, changing place and residence, traveling, works related to home and property, for opposite sex and romance, for buying and wearing jewelry, for mediation, for buying clothes and garments. Auspicious for buying and selling, all work related to water, and creative and artistic work.';
                    $hora_anukul = 'Meeting maternal relatives, helping others, participating in spiritual activities, praying to balance the mind and emotions, taking care of oneself and others, and focusing on intuition.';
                    $hora_pratikul = 'Extreme loneliness, emotional imbalance, making important decisions, procrastinating, and disrespecting women.';
                    $rang = 'white';
                    $bhojan = 'white rice';
                    $ratna = 'pearl';
                    $fool = 'white lily';
                    $sankhya = '2';
                    $vahan = 'chariot made of pearl';
                    $dhatu = 'CC ranga';
                    $hora_image = asset("public/hora-image/moon.png");
                }
                if ($period['hora'] == 'शनि ') {
                    $hora_detail = 'शनि का होरा तेल और लोहे से संबंधित व्यवसायों के लिए और श्रम से संबंधित मामलों को संभालने के लिए उपयुक्त होता है। अन्य सभी कार्यों के लिए यह होरा अशुभ माना जाता है।';
                    $hora_anukul = 'आध्यात्मिक गतिविधियों में भाग लेना, जरूरतमंदों की देखभाल करना, शारीरिक रूप से विकलांग लोगों की सहायता करना, भूखों को भोजन कराना, और बुजुर्गों की सहायता करना।';
                    $hora_pratikul = 'स्वतंत्रता का दुरुपयोग करना और दूसरों के प्रति अनुचित या कठोर व्यवहार करना।';
                    $rang = 'काला';
                    $bhojan = 'काले तिल';
                    $ratna = 'नीलम';
                    $fool = 'सफेद चमेली';
                    $sankhya = '8';
                    $vahan = 'कौआ';
                    $dhatu = 'लोहा';
                    $hora_image = asset("public/hora-image/saturn.png");
                }
                if ($period['hora'] == 'Saturn') {
                    $hora_detail = 'Saturn"s Hora is suitable for businesses related to oil and iron, and for handling matters related to labor. This Hora is considered inauspicious for all other activities.';
                    $hora_anukul = 'Participating in spiritual activities, caring for the needy, helping the physically challenged, feeding the hungry, and assisting the elderly.';
                    $hora_pratikul = 'Abusing freedom and being unfair or harsh towards others.';
                    $rang = 'black';
                    $bhojan = 'black sesame';
                    $ratna = 'sapphire';
                    $fool = 'white jasmine';
                    $sankhya = '8';
                    $vahan = 'crow';
                    $dhatu = 'iron';
                    $hora_image = asset("public/hora-image/saturn.png");
                }
                if ($period['hora'] == 'गुरु ') {
                    $hora_detail = 'गुरु का होरा सभी शुभ कार्यों के लिए अत्यधिक शुभ माना जाता है। नौकरी में शामिल होने, व्यवसाय शुरू करने, वरिष्ठों से मिलने, नया कोर्स शुरू करने या अदालत से संबंधित मामलों के लिए, सभी धार्मिक कार्यों के लिए, विवाह वार्ता और यात्रा और तीर्थयात्राओं के लिए यह होरा शुभ होता है।';
                    $hora_anukul = 'उच्च अधिकारियों के साथ मुलाकात करना, शैक्षिक योजनाएं बनाना, दान करना, धार्मिक गतिविधियों में भाग लेना, कानूनी मामलों को संभालना, और गुरु से सलाह लेकर उनके मार्गदर्शन पर चलना।';
                    $hora_pratikul = 'आलोचना करना, अस्वास्थ्यकर भोजन की आदतें अपनाना, जंक फूड और मिठाई का अधिक सेवन करना क्योंकि गुरु ग्रह वसा का अधिपति है, जिससे वजन बढ़ सकता है या स्वास्थ्य समस्याएं हो सकती हैं।';
                    $rang = 'पीला';
                    $bhojan = 'चना';
                    $ratna = 'पुखराज';
                    $fool = 'जल कुंभी';
                    $sankhya = '1';
                    $vahan = 'सफेद हाथी';
                    $dhatu = 'सोना';
                    $hora_image = asset("public/hora-image/jupiter.png");
                }
                if ($period['hora'] == 'Jupiter') {
                    $hora_detail = 'Gurus Hora is considered highly auspicious for all auspicious activities. This Hora is auspicious for joining a job, starting a business, meeting superiors, starting a new course or court-related matters, for all religious functions, marriage negotiations, and travel and pilgrimages.';
                    $hora_anukul = 'Meeting with higher officials, making educational plans, giving charity, participating in religious activities, handling legal matters, and consulting Guru and following his guidance.';
                    $hora_pratikul = 'Being critical, adopting unhealthy food habits, overeating junk food and sweets as Jupiter is the ruler of fats, can lead to weight gain or health problems.';
                    $rang = 'yellow';
                    $bhojan = 'gram';
                    $ratna = 'topaz';
                    $fool = 'water pot';
                    $sankhya = '1';
                    $vahan = 'white elephant';
                    $dhatu = 'gold';
                    $hora_image = asset("public/hora-image/jupiter.png");
                }
                if ($period['hora'] == 'मंगल ') {
                    $hora_detail = 'मंगल का होरा भूमि और कृषि से संबंधित मामलों, वाहनों की खरीद और बिक्री, इलेक्ट्रिकल और इंजीनियरिंग कार्यों, साहसिक उपक्रमों और खेलों के लिए, ऋण देने और लेने के लिए, शारीरिक अभ्यास और मार्शल आर्ट के लिए, और भाइयों से संबंधित मामलों के लिए शुभ होता है। झगड़ों और टकराव से बचें।';
                    $hora_anukul = 'साहसी कार्य, जमीन विवाद के मामले आदि कार्यों को पूर्ण करने हेतु यह समय शुभ है। पुलिस अधिकारी, अग्निशमन कर्मी, सेना आदि क्षेत्रों में कार्यरत लोगों का सम्मान करें। उन लोगों के प्रति भी आभारी रहें जो स्वास्थ्य और उपचार क्षेत्रों से जुड़े हैं।';
                    $hora_pratikul = 'बहसबाजी में भाग लेना और किसी के विचारों पर जोर देना। बुरी आदतों में लिप्त लोग अपना स्वास्थ्य खराब कर सकते हैं।';
                    $rang = 'लाल';
                    $bhojan = 'अरहर';
                    $ratna = 'लाल मूंगा';
                    $fool = 'आबोली पुष्प';
                    $sankhya = '9 (6 पर कुछ प्रभाव)';
                    $vahan = 'हंस';
                    $dhatu = 'तांबा';
                    $hora_image = asset("public/hora-image/mars.png");
                }
                if ($period['hora'] == 'Mars') {
                    $hora_detail = 'Mars Hora is auspicious for matters related to land and agriculture, purchase and sale of vehicles, electrical and engineering works, adventurous ventures and sports, giving and taking loans, physical exercises and martial arts, and matters related to brothers. Avoid fights and conflicts.';
                    $hora_anukul = 'This is an auspicious time to complete adventure activities, land dispute cases etc. Respect people working in the fields like police officers, fire fighters, army etc. Also be grateful to those who are associated with health and treatment fields.","hora_pratikul": "Participate in debates and emphasize ones views. People indulging in bad habits can spoil their health.';
                    $hora_pratikul = 'Participate in debates and emphasize ones views. People indulging in bad habits can spoil their health.';
                    $rang = 'red';
                    $bhojan = 'arhar';
                    $ratna = 'red coral';
                    $fool = 'Aboli flower';
                    $sankhya = '9 (some effects on 6)';
                    $vahan = 'swan';
                    $dhatu = 'copper';
                    $hora_image = asset("public/hora-image/mars.png");
                }
                if ($period['hora'] == 'सूर्य ') {
                    $hora_detail = 'अपने राशि अनुसार आज का चढ़ावा अर्पित करें।';
                    $hora_anukul = 'अभी चढ़ावा चढ़ाएं।';
                    $hora_pratikul = 'मेष राशि के लिए आज का शुभ चढ़ावा।';
                    $rang = 'लाल';
                    $bhojan = 'गेहूँ';
                    $ratna = 'रत्न';
                    $fool = 'लाल कमल';
                    $sankhya = '1';
                    $vahan = 'सात घोड़ों वाला रथ';
                    $dhatu = 'तांबा';
                    $hora_image = asset("public/hora-image/sun.png");
                }
                if ($period['hora'] == 'Sun') {
                    $hora_detail = 'Offer todays offering according to your zodiac sign.';
                    $hora_anukul = 'Offer offering now.';
                    $hora_pratikul = 'Todays auspicious offering for Aries.';
                    $rang = 'red';
                    $bhojan = 'wheat';
                    $ratna = 'gem';
                    $fool = 'Red Lotus';
                    $sankhya = '1';
                    $vahan = 'chariot with seven horses';
                    $dhatu = 'copper';
                    $hora_image = asset("public/hora-image/sun.png");
                }
                if ($period['hora'] == 'शुक्र ') {
                    $hora_detail = 'शुक्र का होरा प्रेम और विवाह संबंधी मामलों के लिए, आभूषण और कपड़े खरीदने और बेचने के लिए, मनोरंजन और मनोरंजन से संबंधित मामलों के लिए, नए वाहन खरीदने और उपयोग करने और नृत्य और संगीत से संबंधित कार्यों के लिए शुभ होता है।';
                    $hora_anukul = 'शुक्र की होरा में सुलह, प्रेम, संगीत सुनना, नए व्यापार की शुरुआत करना, मदद मांगना, नृत्य, मनोरंजन और कला का प्रदर्शन करना।';
                    $hora_pratikul = 'शुक्र की होरा में महत्वपूर्ण निर्णय लेना और महिलाओं का अनादर करना।';
                    $rang = 'सफेद';
                    $bhojan = 'सफेद सेम';
                    $ratna = 'हीरा';
                    $fool = 'सफेद कमल';
                    $sankhya = '6';
                    $vahan = 'गरूड़';
                    $dhatu = 'चाँदी';
                    $hora_image = asset("public/hora-image/venus.png");
                }
                if ($period['hora'] == 'Venus') {
                    $hora_detail = 'Venus Hora is auspicious for matters related to love and marriage, buying and selling of jewellery and clothes, matters related to entertainment and recreation, buying and using new vehicles and activities related to dance and music.';
                    $hora_anukul = 'Venus Hora is auspicious for reconciliation, love, listening to music, starting a new business, seeking help, dancing, entertainment and performing arts.';
                    $hora_pratikul = 'Venus Hora is auspicious for important decisions and disrespecting women.';
                    $rang = 'white';
                    $bhojan = 'white beans';
                    $ratna = 'diamond';
                    $fool = 'white lotus';
                    $sankhya = '6';
                    $vahan = 'Garuda';
                    $dhatu = 'silver';
                    $hora_image = asset("public/hora-image/venus.png");
                }
                $nightPeriod[] = [
                    'start_time' => $start_time_24hr,
                    'end_time' => $end_time_24hr,
                    'hora' => $period['hora'],
                    'hora_detail' => $hora_detail,
                    'hora_anukul' => $hora_anukul,
                    'hora_pratikul' => $hora_pratikul,
                    'rang' => $rang,
                    'bhojan' => $bhojan,
                    'ratna' => $ratna,
                    'fool' => $fool,
                    'sankhya' => $sankhya,
                    'vahan' => $vahan,
                    'dhatu' => $dhatu,
                    'hora_image' => $hora_image,
                ];
            }

            foreach ($nightPeriod as $entry) {
                // Convert start time to timestamp
                $start_time_timestamp = $entry['start_time'];

                if ((strtotime($entry['start_time']) < strtotime("23:59")) && strtotime($entry['start_time']) > strtotime('17:00')) {
                    // echo $entry['start_time'].'<br>';
                    $nightPeriodData[] = $entry;
                }
                if ($start_time_timestamp < "23:59" && $entry["end_time"] > '00:00' && $entry["end_time"] < "01:00") {
                    $nightPeriodData[] = $entry;
                }
            }
            if (!empty($nightPeriodData)) {
                $nightPeriodData[count($nightPeriodData) - 1]['end_time'] = '00:00';
            }


            $allPeriods = array_merge($completeData, $dayPeriod, $nightPeriodData);

            return response()->json([
                'status' => 200,
                'all_data' => $allPeriods,
                // 'nightHora' => $nightPeriod
            ]);
        }

        // if($hora){
        //     return response()->json(['status'=>200,'hora'=>$hora]);
        // }
        return response()->json(['status' => 400]);
    }

    public function chaughadiya(Request $request)
    {
        // $date = explode('/', $request->date);
        // $time = explode(':', $request->time);
        // $apiData = array(
        //     'day' => $date[0],
        //     'month' => $date[1],
        //     'year' => $date[2],
        //     'hour' => $time[0],
        //     'min' => $time[1],
        //     'lat' => intval($request->latitude),
        //     'lon' => intval($request->longitude),
        //     'tzone' => intval($request->timezone),
        //     'language' => $request->language
        // );

        // // Check if the requested language is 'hi' or 'en'
        // $language = in_array($request->language, ['hi', 'en']) ? $request->language : 'hi';
        // Get current date
        $currentDate = explode('/', $request->date);
        $currentDay = intval($currentDate[0]);
        $currentMonth = intval($currentDate[1]);
        $currentYear = intval($currentDate[2]);

        // Get previous date
        $previousDate = date('d/m/Y', strtotime('-1 day', strtotime("$currentYear-$currentMonth-$currentDay")));
        $previousDate = explode('/', $previousDate);
        $previousDay = intval($previousDate[0]);
        $previousMonth = intval($previousDate[1]);
        $previousYear = intval($previousDate[2]);

        $time = explode(':', $request->time);
        $apiDataCurrent = array(
            'day' => $currentDay,
            'month' => $currentMonth,
            'year' => $currentYear,
            'hour' => $time[0],
            'min' => $time[1],
            'lat' => floatval($request->latitude),
            'lon' => floatval($request->longitude),
            'tzone' => floatval($request->timezone),
            'language' => $request->language
        );

        $apiDataPrevious = array(
            'day' => $previousDay,
            'month' => $previousMonth,
            'year' => $previousYear,
            'hour' => $time[0],
            'min' => $time[1],
            'lat' => floatval($request->latitude),
            'lon' => floatval($request->longitude),
            'tzone' => floatval($request->timezone),
            'language' => $request->language
        );

        // ApiHelper::astroApi('https://json.astrologyapi.com/v1/chaughadiya_muhurta', $language, $apiData), true);

        $language = in_array($request->language, ['hi', 'en']) ? $request->language : 'hi';
        $data_get_current = ApiHelper::astroApi('https://json.astrologyapi.com/v1/chaughadiya_muhurta', $language, $apiDataCurrent);
        $data_get_previous = ApiHelper::astroApi('https://json.astrologyapi.com/v1/chaughadiya_muhurta', $language, $apiDataPrevious);

        $chaughadiya_current = json_decode($data_get_current, true);
        $chaughadiya_previous = json_decode($data_get_previous, true);
        $dayPeriods = [];
        $nightPeriods = [];
        $complete_data = [];

        if ($chaughadiya_previous) {
            $nightChaughdiya_previous = $chaughadiya_previous['chaughadiya']['night'];
            foreach ($nightChaughdiya_previous as $period) {
                $timeParts = explode(" - ", $period['time']);
                $start_time_24hr = ApiHelper::convertTo24Hour($timeParts[0]);
                $end_time_24hr = ApiHelper::convertTo24Hour($timeParts[1]);
                $start_hour = intval(explode(':', $start_time_24hr)[0]);
                $end_hour = intval(explode(':', $end_time_24hr)[0]);

                if (($start_hour >= 23 && $start_time_24hr >= "23:13") || ($start_hour < 6 && $end_time_24hr <= "00:32")) {
                    $dayPeriods[] = [
                        'start_time' => "00:00",
                        'end_time' => $end_time_24hr,
                        'muhurta' => $period['muhurta'],
                        'color' => '',
                        'chaughdiyadetail' => ''
                    ];
                }
                if (intval(explode(':', $start_time_24hr)[0]) >= 0) {
                    $hour = intval(explode(':', $start_time_24hr)[0]);
                    if ($hour >= 0 && $hour < 6) { // Assuming night periods can go till 6 AM
                        $dayPeriods[] = [
                            'start_time' => $start_time_24hr,
                            'end_time' => $end_time_24hr,
                            'muhurta' => $period['muhurta'],
                            'color' => '',
                            'chaughdiyadetail' => ''
                        ];
                    }
                }
            }
            // echo "<pre>";print_r($dayPeriods);die;
        }
        if ($chaughadiya_current) {
            $dayChaughdiya_current = $chaughadiya_current['chaughadiya']['day'];
            $nightChaughdiya_current = $chaughadiya_current['chaughadiya']['night'];
            $nighttimedata = [];
            foreach ($dayChaughdiya_current as $period) {
                $timeParts = explode(" - ", $period['time']);
                $start_time_24hr = ApiHelper::convertTo24Hour($timeParts[0]);
                $end_time_24hr = ApiHelper::convertTo24Hour($timeParts[1]);

                if (intval(explode(':', $end_time_24hr)[0]) <= 23) {
                    $dayPeriods[] = [
                        'start_time' => $start_time_24hr,
                        'end_time' => $end_time_24hr,
                        'muhurta' => $period['muhurta'],
                        'color' => '',
                        'chaughdiyadetail' => ''
                    ];
                }
            }

            foreach ($nightChaughdiya_current as $period) {
                $timeParts = explode(" - ", $period['time']);
                $start_time_24hr = ApiHelper::convertTo24Hour($timeParts[0]);
                $end_time_24hr = ApiHelper::convertTo24Hour($timeParts[1]);
                $nighttimedata[] = [
                    'start_time' => $start_time_24hr,
                    'end_time' => $end_time_24hr,
                    'muhurta' => $period['muhurta'],
                    'color' => '',
                    'chaughdiyadetail' => ''
                ];
            }
            foreach ($nighttimedata as $entry) {
                // Convert start time to timestamp
                $start_time_timestamp = strtotime($entry['start_time']);

                if (($start_time_timestamp < strtotime('23:59') && ($entry["end_time"] > "16:00"))) {
                    $nightPeriods[] = $entry;
                }
                if ($start_time_timestamp < strtotime("23:59") && $entry["end_time"] > '00:00' && $entry["end_time"] < "01:00") {
                    $nightPeriods[] = $entry;
                }
            }
            if (!empty($nightPeriods)) {
                $nightPeriods[count($nightPeriods) - 1]['end_time'] = '00:00';
            }
        }
        $allPeriods = array_merge($dayPeriods, $nightPeriods);
        $result = [];
        foreach ($allPeriods as $entry) {
            switch ($entry['muhurta']) {
                case 'शुभ':
                    $color = '5CCB9E';
                    $chaughdiyadetail = 'विवाह, धार्मिक, शिक्षा गतिविधियाँ';
                    break;
                case 'Shubh':
                    $color = '5CCB9E';
                    $chaughdiyadetail = 'Marriage, Religious, Education Activities';
                    break;
                case 'लाभ':
                    $color = '5CCB9E';
                    $chaughdiyadetail = 'नया व्यवसाय, शिक्षा प्रारंभ करे';
                    break;
                case 'Labh':
                    $color = '5CCB9E';
                    $chaughdiyadetail = 'Start a new business, education';
                    break;
                case 'अमृत':
                    $color = '5CCB9E';
                    $chaughdiyadetail = 'सभी प्रकार के कार्य (विशेष रूप से दुग्ध उत्पात संबंधित)';
                    break;
                case 'Amrit':
                    $color = '5CCB9E';
                    $chaughdiyadetail = 'All types of work (especially related to dairy production)';
                    break;
                case 'रोग':
                    $color = 'DB5171';
                    $chaughdiyadetail = 'वाद-विवाद, प्रतियोगीता, विवाद निपटारा';
                    break;
                case 'Rog':
                    $color = 'DB5171';
                    $chaughdiyadetail = 'Debate, competition, dispute resolution';
                    break;
                case 'उद्वेग':
                    $color = 'DB5171';
                    $chaughdiyadetail = 'सरकार से संबंधित कार्य';
                    break;
                case 'Udveg':
                    $color = 'DB5171';
                    $chaughdiyadetail = 'Government related work';
                    break;
                case 'काल':
                    $color = 'DB5171';
                    $chaughdiyadetail = 'मशीन, निर्माण और कृषि संबंधी गतिविधियाँ';
                    break;
                case 'Kaal':
                    $color = 'DB5171';
                    $chaughdiyadetail = 'Machine, construction and agricultural activities';
                    break;
                case 'चर':
                    $color = '6EAFF3';
                    $chaughdiyadetail = 'यात्रा, सौंदर्य/नृत्य/सांस्कृतिक गतिविधियाँ';
                    break;
                case 'Char':
                    $color = '6EAFF3';
                    $chaughdiyadetail = 'Travel, Beauty/Dance/Cultural Activities';
                    break;
                default:
                    $color = 'FFFFFF';
                    $chaughdiyadetail = 'Unknown';
                    break;
            }
            $result[] = [
                'start_time' => $entry['start_time'],
                'end_time' => $entry['end_time'],
                'muhurta' => $entry['muhurta'],
                'color' => $color,
                'chaughdiyadetail' => $chaughdiyadetail
            ];
        }

        return response()->json(['status' => 200, 'result' => $result]);
    }

    public function old_chaughadiya(Request $request)
    {
        $date = explode('/', $request->date);
        $time = explode(':', $request->time);
        $apiData = array(
            'day' => $date[0],
            'month' => $date[1],
            'year' => $date[2],
            'hour' => $time[0],
            'min' => $time[1],
            'lat' => intval($request->latitude),
            'lon' => intval($request->longitude),
            'tzone' => intval($request->timezone),
            'language' => $request->language
        );

        // Check if the requested language is 'hi' or 'en'
        $language = in_array($request->language, ['hi', 'en']) ? $request->language : 'hi';

        $chaughadiya = json_decode(ApiHelper::astroApi('https://json.astrologyapi.com/v1/chaughadiya_muhurta', $language, $apiData), true);

        if ($chaughadiya) {
            // Access the day and night arrays
            $dayChaughdiya = $chaughadiya['chaughadiya']['day'];
            $nightChaughdiya = $chaughadiya['chaughadiya']['night'];

            // Extract start and end times from each period
            $dayPeriod = [];
            foreach ($dayChaughdiya as $period) {
                $timeParts = explode(" - ", $period['time']);
                if (($period['muhurta'] == 'शुभ')) {
                    $color = '5CCB9E';
                    $chaughdiyadetail = 'विवाह, धार्मिक, शिक्षा गतिविधियाँ';
                }
                if (($period['muhurta'] == 'Shubh')) {
                    $color = '5CCB9E';
                    $chaughdiyadetail = 'Marriage, Religious, Education Activities';
                }
                if (($period['muhurta'] == 'लाभ')) {
                    $color = '5CCB9E';
                    $chaughdiyadetail = 'नया व्यवसाय, शिक्षा प्रारंभ करे';
                }
                if (($period['muhurta'] == 'Labh')) {
                    $color = '5CCB9E';
                    $chaughdiyadetail = 'Start a new business, education';
                }
                if (($period['muhurta'] == 'अमृत')) {
                    $color = '5CCB9E';
                    $chaughdiyadetail = 'सभी प्रकार के कार्य (विशेष रूप से दुग्ध उत्पात संबंधित)';
                }
                if (($period['muhurta'] == 'Amrit')) {
                    $color = '5CCB9E';
                    $chaughdiyadetail = 'All types of work (especially related to dairy production)';
                }
                if (($period['muhurta'] == 'रोग')) {
                    $color = 'DB5171';
                    $chaughdiyadetail = 'वाद-विवाद, प्रतियोगीता, विवाद निपटारा';
                }
                if (($period['muhurta'] == 'Rog')) {
                    $color = '5CCB9E';
                    $chaughdiyadetail = 'debate, competition, dispute resolution';
                }
                if (($period['muhurta'] == 'उद्वेग')) {
                    $color = 'DB5171';
                    $chaughdiyadetail = 'सरकार से संबंधित कार्य';
                }
                if (($period['muhurta'] == 'Udveg')) {
                    $color = 'DB5171';
                    $chaughdiyadetail = 'Government related work';
                }
                if (($period['muhurta'] == 'काल')) {
                    $color = 'DB5171';
                    $chaughdiyadetail = 'मशीन, निर्माण और कृषि संबंधी गतिविधियाँ';
                }
                if (($period['muhurta'] == 'Kaal')) {
                    $color = 'DB5171';
                    $chaughdiyadetail = 'Machine, construction and agricultural activities';
                }
                if (($period['muhurta'] == 'चर')) {
                    $color = '6EAFF3';
                    $chaughdiyadetail = 'यात्रा, सौंदर्य/नृत्य/सांस्कृतिक गतिविधियाँ';
                }
                if (($period['muhurta'] == 'Char')) {
                    $color = '6EAFF3';
                    $chaughdiyadetail = 'Travel, Beauty/Dance/Cultural Activities';
                }
                $dayPeriod[] = [
                    'start_time' => $timeParts[0],
                    'end_time' => $timeParts[1],
                    'muhurta' => $period['muhurta'],
                    'color' => $color,
                    'chaughdiyadetail' => $chaughdiyadetail
                ];
            }

            $nightPeriod = [];
            foreach ($nightChaughdiya as $period) {
                $timeParts = explode(" - ", $period['time']);
                if (($period['muhurta'] == 'शुभ')) {
                    $color = '5CCB9E';
                    $chaughdiyadetail = 'विवाह, धार्मिक, शिक्षा गतिविधियाँ';
                }
                if (($period['muhurta'] == 'Shubh')) {
                    $color = '5CCB9E';
                    $chaughdiyadetail = 'Marriage, Religious, Education Activities';
                }
                if (($period['muhurta'] == 'लाभ')) {
                    $color = '5CCB9E';
                    $chaughdiyadetail = 'नया व्यवसाय, शिक्षा प्रारंभ करे';
                }
                if (($period['muhurta'] == 'Labh')) {
                    $color = '5CCB9E';
                    $chaughdiyadetail = 'Start a new business, education';
                }
                if (($period['muhurta'] == 'अमृत')) {
                    $color = '5CCB9E';
                    $chaughdiyadetail = 'सभी प्रकार के कार्य (विशेष रूप से दुग्ध उत्पात संबंधित)';
                }
                if (($period['muhurta'] == 'Amrit')) {
                    $color = '5CCB9E';
                    $chaughdiyadetail = 'All types of work (especially related to dairy production)';
                }
                if (($period['muhurta'] == 'रोग')) {
                    $color = 'DB5171';
                    $chaughdiyadetail = 'वाद-विवाद, प्रतियोगीता, विवाद निपटारा';
                }
                if (($period['muhurta'] == 'Rog')) {
                    $color = '5CCB9E';
                    $chaughdiyadetail = 'debate, competition, dispute resolution';
                }
                if (($period['muhurta'] == 'उद्वेग')) {
                    $color = 'DB5171';
                    $chaughdiyadetail = 'सरकार से संबंधित कार्य';
                }
                if (($period['muhurta'] == 'Udveg')) {
                    $color = 'DB5171';
                    $chaughdiyadetail = 'Government related work';
                }
                if (($period['muhurta'] == 'काल')) {
                    $color = 'DB5171';
                    $chaughdiyadetail = 'मशीन, निर्माण और कृषि संबंधी गतिविधियाँ';
                }
                if (($period['muhurta'] == 'Kaal')) {
                    $color = 'DB5171';
                    $chaughdiyadetail = 'Machine, construction and agricultural activities';
                }
                if (($period['muhurta'] == 'चर')) {
                    $color = '6EAFF3';
                    $chaughdiyadetail = 'यात्रा, सौंदर्य/नृत्य/सांस्कृतिक गतिविधियाँ';
                }
                if (($period['muhurta'] == 'Char')) {
                    $color = '6EAFF3';
                    $chaughdiyadetail = 'Travel, Beauty/Dance/Cultural Activities';
                }
                $nightPeriod[] = [
                    'start_time' => $timeParts[0],
                    'end_time' => $timeParts[1],
                    'muhurta' => $period['muhurta'],
                    'color' => $color,
                    'chaughdiyadetail' => $chaughdiyadetail
                ];
            }

            return response()->json([
                'status' => 200,
                'dayChaughdiya' => $dayPeriod,
                'nightChaughdiya' => $nightPeriod
            ]);
        }

        return response()->json(['status' => 400]);
    }

    // public function kundali(Request $request) {
    //     // Validate date and time format
    //     if (!preg_match('/\d{2}\/\d{2}\/\d{4}/', $request->date) || !preg_match('/\d{2}:\d{2}/', $request->time)) {
    //         return response()->json(['status' => 400, 'message' => 'Invalid date or time format']);
    //     }

    //     $date = explode('/', $request->date);
    //     $time = explode(':', $request->time);

    //     // Ensure latitude, longitude, and timezone are floats
    //     $apiData = [
    //         'day' => $date[0],
    //         'month' => $date[1],
    //         'year' => $date[2],
    //         'hour' => $time[0],
    //         'min' => $time[1],
    //         'lat' => floatval($request->latitude),
    //         'lon' => floatval($request->longitude),
    //         'tzone' => floatval($request->timezone),
    //         'language' => $request->language,
    //         'planet_name' => $request->planet_name
    //     ];

    //     // Check if the requested language is 'hi' or 'en'
    //     $language = in_array($request->language, ['hi', 'en']) ? $request->language : 'hi';

    //     // Check if the requested planet name is valid
    //     $planetName = in_array($request->planet_name, ['sun', 'moon', 'mars', 'mercury', 'jupiter', 'venus', 'saturn']) ? $request->planet_name : 'sun';

    //     // Helper function for API calls
    //     function safeApiCall($url, $language, $data) {
    //         $result = json_decode(ApiHelper::astroApi($url, $language, $data), true);
    //         return $result ? $result : [];
    //     }

    //     // List of API endpoints
    //     $apiEndpoints = [
    //         'astroData' => 'https://json.astrologyapi.com/v1/astro_details',
    //         'birthData' => 'https://json.astrologyapi.com/v1/birth_details',
    //         'panchangData' => 'https://json.astrologyapi.com/v1/basic_panchang',
    //         'lagnaData' => 'https://json.astrologyapi.com/v1/general_ascendant_report',
    //         'vimshottariDasha' => 'https://json.astrologyapi.com/v1/current_vdasha',
    //         'yoginiDasha' => 'https://json.astrologyapi.com/v1/current_yogini_dasha',
    //         'planetResult' => 'https://json.astrologyapi.com/v1/general_house_report/' . $planetName,
    //         'rudrakshaSuggestion' => 'https://json.astrologyapi.com/v1/rudraksha_suggestion',
    //         'poojaSuggestion' => 'https://json.astrologyapi.com/v1/puja_suggestion',
    //         'pitraDosh' => 'https://json.astrologyapi.com/v1/pitra_dosha_report',
    //         'kalsarpDosha' => 'https://json.astrologyapi.com/v1/kalsarpa_details',
    //         'lalkitabRemedies' => 'https://json.astrologyapi.com/v1/lalkitab_remedies/' . $planetName,
    //         'lalKitabRin' => 'https://json.astrologyapi.com/v1/lalkitab_debts',
    //         'mangalDosh' => 'https://json.astrologyapi.com/v1/manglik',
    //         'gemStone' => 'https://json.astrologyapi.com/v1/basic_gem_suggestion',
    //         'nakshatra' => 'https://json.astrologyapi.com/v1/daily_nakshatra_prediction',
    //         'numerology' => 'https://json.astrologyapi.com/v1/numero_table',
    //         'numerologyReport' => 'https://json.astrologyapi.com/v1/numero_report',
    //         'mahaVimshottari' => 'https://json.astrologyapi.com/v1/major_vdasha',
    //         'sadhesatiShani' => 'https://json.astrologyapi.com/v1/sadhesati_current_status',
    //         'majorYoginiDasha' => 'https://json.astrologyapi.com/v1/major_yogini_dasha',
    //     ];

    //     // Initialize response data array
    //     $responseData = ['status' => 200];

    //     // Loop through endpoints and call APIs
    //     foreach ($apiEndpoints as $key => $url) {
    //         $responseData[$key] = safeApiCall($url, $language, $apiData);
    //     }

    //     // Return the combined response
    //     return response()->json($responseData);
    // }

    public function kundali(Request $request)
    {
        // Validate date and time format
        if (!preg_match('/\d{2}\/\d{2}\/\d{4}/', $request->date) || !preg_match('/\d{2}:\d{2}/', $request->time)) {
            return response()->json(['status' => 400, 'message' => 'Invalid date or time format']);
        }

        // Check if user record exists
        $userExists = UserKundali::where([
            'user_id' => $request->user_id,
            'device_id' => $request->device_id,
            'name' => $request->name,
            'dob' => date('Y-m-d', strtotime(str_replace('/', '-', $request->date))),
            'time' => $request->time,
            'country' => $request->country,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'timezone' => $request->timezone,
        ])->exists();

        if (!$userExists) {
            $userSave = new UserKundali;
            $userSave->user_id = $request->user_id;
            $userSave->device_id = $request->device_id;
            $userSave->name = $request->name;
            $userSave->dob = date('Y-m-d', strtotime(str_replace('/', '-', $request->date)));
            $userSave->time = $request->time;
            $userSave->country = $request->country;
            $userSave->city = $request->city;
            $userSave->latitude = $request->latitude;
            $userSave->longitude = $request->longitude;
            $userSave->timezone = $request->timezone;
            $userSave->save();
        }

        $date = explode('/', $request->date);
        $time = explode(':', $request->time);

        // Ensure latitude, longitude, and timezone are floats
        $apiData = [
            'day' => $date[0],
            'month' => $date[1],
            'year' => $date[2],
            'hour' => $time[0],
            'min' => $time[1],
            'lat' => floatval($request->latitude),
            'lon' => floatval($request->longitude),
            'tzone' => floatval($request->timezone),
            'language' => in_array($request->language, ['hi', 'en']) ? $request->language : 'hi',
            'planet_name' => in_array($request->planet_name, ['sun', 'moon', 'mars', 'mercury', 'jupiter', 'venus', 'saturn']) ? $request->planet_name : 'sun',
            'tab' => $request->tab,
        ];

        // Initialize response data array
        $responseData = ['status' => 200];

        // Helper function for API calls
        function safeApiCall($url, $language, $data)
        {
            $result = json_decode(ApiHelper::astroApi($url, $language, $data), true);
            return $result ? $result : [];
        }

        // List of API endpoints
        $apiEndpoints = [];

        // Determine which tab is requested and populate endpoints accordingly
        if ($request->tab == 'basic') {
            $apiEndpoints = [
                'astroData' => 'https://json.astrologyapi.com/v1/astro_details',
                'birthData' => 'https://json.astrologyapi.com/v1/birth_details',
                'panchangData' => 'https://json.astrologyapi.com/v1/basic_panchang'
            ];
        } elseif ($request->tab == 'dasha') {
            $apiEndpoints = [
                'vimshottariDasha' => 'https://json.astrologyapi.com/v1/current_vdasha',
                'yoginiDasha' => 'https://json.astrologyapi.com/v1/current_yogini_dasha',
                'mahaVimshottari' => 'https://json.astrologyapi.com/v1/major_vdasha',
                'majorYoginiDasha' => 'https://json.astrologyapi.com/v1/major_yogini_dasha'
            ];
        } elseif ($request->tab == 'fall') {
            $apiEndpoints = [
                'lagnaData' => 'https://json.astrologyapi.com/v1/general_ascendant_report',
                'planetResult' => 'https://json.astrologyapi.com/v1/general_house_report/' . $apiData['planet_name'],
                'nakshatra' => 'https://json.astrologyapi.com/v1/daily_nakshatra_prediction'
            ];
        } elseif ($request->tab == 'suggestion') {
            $apiEndpoints = [
                'rudrakshaSuggestion' => 'https://json.astrologyapi.com/v1/rudraksha_suggestion',
                'gemStone' => 'https://json.astrologyapi.com/v1/basic_gem_suggestion',
                'poojaSuggestion' => 'https://json.astrologyapi.com/v1/puja_suggestion'
            ];
        } elseif ($request->tab == 'dosh') {
            $apiEndpoints = [
                'mangalDosh' => 'https://json.astrologyapi.com/v1/manglik',
                'kalsarpDosha' => 'https://json.astrologyapi.com/v1/kalsarpa_details',
                'pitraDosh' => 'https://json.astrologyapi.com/v1/pitra_dosha_report',
                'sadhesatiShani' => 'https://json.astrologyapi.com/v1/sadhesati_current_status'
            ];
        } elseif ($request->tab == 'lalkitab') {
            $apiEndpoints = [
                'lalkitabRemedies' => 'https://json.astrologyapi.com/v1/lalkitab_remedies/' . $apiData['planet_name'],
                'lalKitabRin' => 'https://json.astrologyapi.com/v1/lalkitab_debts'
            ];
        } elseif ($request->tab == 'numero') {
            $apiEndpoints = [
                'numerology' => 'https://json.astrologyapi.com/v1/numero_table',
                'numerologyReport' => 'https://json.astrologyapi.com/v1/numero_report'
            ];
        }

        // Loop through endpoints and call APIs
        foreach ($apiEndpoints as $key => $url) {
            $responseData[$key] = safeApiCall($url, $apiData['language'], $apiData);
        }

        // Return the combined response
        return response()->json($responseData);
    }


    // kundali milan 
    public function kundali_milan(Request $request)
    {
        // Validate date and time format
        if (
            !preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $request->male_date) ||
            !preg_match('/^\d{2}:\d{2}$/', $request->male_time) ||
            !preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $request->female_date) ||
            !preg_match('/^\d{2}:\d{2}$/', $request->female_time)
        ) {
            return response()->json(['status' => 400, 'message' => 'Invalid date or time format']);
        }

        $maleDob = date('Y-m-d', strtotime(str_replace('/', '-', $request->male_date)));
        $femaleDob = date('Y-m-d', strtotime(str_replace('/', '-', $request->female_date)));

        $userExists = UserKundaliMilan::where([
            'user_id' => $request->user_id,
            'device_id' => $request->device_id,
            'male_name' => $request->male_name,
            'male_dob' => $maleDob,
            'male_time' => $request->male_time,
            'male_country' => $request->male_country,
            'male_latitude' => $request->male_latitude,
            'male_longitude' => $request->male_longitude,
            'male_timezone' => $request->male_timezone,
            'female_name' => $request->female_name,
            'female_dob' => $femaleDob,
            'female_time' => $request->female_time,
            'female_country' => $request->female_country,
            'female_latitude' => $request->female_latitude,
            'female_longitude' => $request->female_longitude,
            'female_timezone' => $request->female_timezone
        ])->exists();

        if (!$userExists) {
            $user = new UserKundaliMilan();
            $user->user_id = $request->user_id;
            $user->device_id = $request->device_id;
            $user->male_name = $request->male_name;
            $user->male_dob = $maleDob;
            $user->male_time = $request->male_time;
            $user->male_country = $request->male_country;
            $user->male_city = $request->male_city;
            $user->male_latitude = $request->male_latitude;
            $user->male_longitude = $request->male_longitude;
            $user->male_timezone = $request->male_timezone;
            $user->female_name = $request->female_name;
            $user->female_dob = $femaleDob;
            $user->female_time = $request->female_time;
            $user->female_country = $request->female_country;
            $user->female_city = $request->female_city;
            $user->female_latitude = $request->female_latitude;
            $user->female_longitude = $request->female_longitude;
            $user->female_timezone = $request->female_timezone;
            $user->save();
        }

        // Convert dates and times for API
        $male_date = explode('/', $request->male_date);
        $male_time = explode(':', $request->male_time);
        $female_date = explode('/', $request->female_date);
        $female_time = explode(':', $request->female_time);

        $apiData = [
            'm_day' =>  $male_date[0],
            'm_month' =>  $male_date[1],
            'm_year' =>  $male_date[2],
            'm_hour' =>  $male_time[0],
            'm_min' =>  $male_time[1],
            'm_lat' =>  $request->male_latitude,
            'm_lon' =>  $request->male_longitude,
            'm_tzone' =>  $request->male_timezone,
            'f_day' =>  $female_date[0],
            'f_month' =>  $female_date[1],
            'f_year' =>  $female_date[2],
            'f_hour' =>  $female_time[0],
            'f_min' =>  $female_time[1],
            'f_lat' =>  $request->female_latitude,
            'f_lon' =>  $request->female_longitude,
            'f_tzone' =>  $request->female_timezone,
            'tab' => $request->tab,
        ];

        $language = in_array($request->language, ['hi', 'en']) ? $request->language : 'hi';

        function safeApiMilanCall($url, $language, $data)
        {
            try {
                $result = json_decode(ApiHelper::astroApi($url, $language, $data), true);
                return $result ?: ['error' => 'No response from API'];
            } catch (Exception $e) {
                return ['error' => $e->getMessage()];
            }
        }

        $apiEndpoints = [];
        switch ($request->tab) {
            case 'birth-detail':
                $apiEndpoints = [
                    'astroData' => 'https://json.astrologyapi.com/v1/match_astro_details',
                    'birthData' => 'https://json.astrologyapi.com/v1/match_birth_details',
                ];
                break;
            case 'planet-detail':
                $apiEndpoints = [
                    'planetData' => 'https://json.astrologyapi.com/v1/match_planet_details/',
                ];
                break;
            case 'ashtakoot-detail':
                $apiEndpoints = [
                    'ashtakootData' => 'https://json.astrologyapi.com/v1/match_ashtakoot_points',
                ];
                break;
            case 'dashakoot-detail':
                $apiEndpoints = [
                    'dashakootData' => 'https://json.astrologyapi.com/v1/match_dashakoot_points',
                ];
                break;
            case 'manglik-detail':
                $apiEndpoints = [
                    'manglikData' => 'https://json.astrologyapi.com/v1/match_manglik_report',
                ];
                break;
            case 'match-making-detail':
                $apiEndpoints = [
                    'matchData' => 'https://json.astrologyapi.com/v1/match_making_report',
                ];
                break;
            default:
                return response()->json(['status' => 400, 'message' => 'Invalid tab value']);
        }

        $responseData = ['status' => 200];
        foreach ($apiEndpoints as $key => $url) {
            $responseData[$key] = safeApiMilanCall($url, $language, $apiData);
        }

        return response()->json($responseData);
    }



    // public function panchang_events($month, $year){
    //     // Set the year as per your requirement
    //     $filePath = public_path("event-json/events-{$year}.json");

    //     if (!File::exists($filePath)) {
    //         return response()->json(['error' => 'File not found'], 404);
    //     }

    //     $events = json_decode(File::get($filePath), true);

    //     if (json_last_error() !== JSON_ERROR_NONE) {
    //         return response()->json(['error' => 'Invalid JSON data'], 500);
    //     }

    //     // Filter events by month
    //     $filteredEvents = array_filter($events, function($event) use ($month) {
    //         // Assuming the eventDate format is like "Day, DD-MM-YYYY"
    //         return substr($event['eventDate'], -7, 2) === $month;
    //     });

    //     return response()->json(array_values($filteredEvents));
    // }
    public function panchang_events(Request $request)
    {
        $day = $request->input('day', '');
        // Get the request parameters with default values
        $month = $request->input('month', '');
        $year = $request->input('year', date('Y'));
        $type = $request->input('type', '');

        // Define the file path
        $filePath = public_path("event-json/events-{$year}.json");

        // Check if the file exists
        if (!File::exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        // Decode the JSON file content
        $events = json_decode(File::get($filePath), true);

        // Check for JSON decoding errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'Invalid JSON data'], 500);
        }

        // Filter events by month, year, and type
        $filteredEvents = array_filter($events, function ($event) use ($day, $month, $year, $type) {
            // Extract the month and year from the eventDate (format: "Day, DD-MM-YYYY")
            $eventDay = substr($event['eventDate'], -10, 2);
            $eventMonth = substr($event['eventDate'], -7, 2);
            $eventYear = substr($event['eventDate'], -4, 4);

            // Convert month and year to strings for comparison
            $dayMatch = ($day === '' || $eventDay === str_pad($day, 2, '0', STR_PAD_LEFT));
            $monthMatch = ($month === '' || $eventMonth === str_pad($month, 2, '0', STR_PAD_LEFT));
            $yearMatch = ($eventYear === $year);

            // Return true if month and year match
            return $dayMatch && $monthMatch && $yearMatch;
        });
        $added_data = [];
        // Enrich events with event type and remove eventUrl
        $finalEvents = [];
        foreach ($filteredEvents as $event) {
            // Retrieve event type from the database
            $festival = FastFestival::select('event_type', 'event_name', 'event_name_hi', 'en_description', 'hi_description', 'image')
                ->where('event_name', $event['eventName'])
                ->first();

            // Add the event type to the event
            if ($festival) {
                $event['eventType'] = $festival->event_type;
                $event['en_description'] = $festival->en_description;
                $event['hi_description'] = $festival->hi_description;
                $event['image'] = asset('storage/app/public/fastfestival-img/' . $festival->image);
                $event['event_name_hi'] = $festival->event_name_hi;
            } else {
                $event['eventType'] = 'Unknown';
                $event['en_description'] = null;
                $event['hi_description'] = null;
                $event['image'] = null;
                $event['event_name_hi'] = null;
            }

            // Add event to final list if type matches or no type filter is applied
            if ($type === '' || $event['eventType'] === $type) {
                // Remove the eventUrl field
                unset($event['eventUrl']);
                if (!in_array($festival->event_name, $added_data)) {
                    array_push($added_data, $festival->event_name);
                    $finalEvents[] = $event;
                }
            }
        }
        return response()->json(array_values($finalEvents));
    }


    //rashi-namakshar api
    public function rashi_namakshar(Request $request)
    {
        // Split the date and time from the request
        $date = explode('/', $request->date);
        $time = explode(':', $request->time);

        // Prepare the API data with the request parameters
        $apiData = array(
            'day' => $date[0],
            'month' => $date[1],
            'year' => $date[2],
            'hour' => $time[0],
            'min' => $time[1],
            'lat' => intval($request->latitutde),
            'lon' => intval($request->longitude),
            'tzone' => intval($request->timezone),
            'language' => $request->language
        );

        // Check if the requested language is 'hi' or 'en'
        $language = in_array($request->language, ['hi', 'en']) ? $request->language : 'hi';

        // Call the API with the prepared data
        $rashiNamakshar = json_decode(ApiHelper::astroApi('https://json.astrologyapi.com/v1/astro_details', $language, $apiData), true);

        //dd($rashiNamakshar['ascendant']);

        // Check if the API returned a valid response
        if ($rashiNamakshar) {
            return response()->json([
                'status' => 200,
                'rashiNamakshar' => $rashiNamakshar
            ]);
        }

        // Return an error response if the API call failed
        return response()->json(['status' => 400]);
    }


    //kalsarp-dosha api
    public function kalsarp_dosha(Request $request)
    {
        $date = explode('/', $request->date);
        $time = explode(':', $request->time);
        $apiData = array(
            'day' => $date['0'],
            'month' => $date['1'],
            'year' => $date['2'],
            'hour' => $time['0'],
            'min' => $time['1'],
            'lat' => intval($request->latitutde),
            'lon' => intval($request->longitude),
            'tzone' => intval($request->timezone),
            'language' => $request->language
        );

        // Check if the requested language is 'hi' or 'en'
        $language = in_array($request->language, ['hi', 'en']) ? $request->language : 'hi';

        $kalsarpDosha = json_decode(ApiHelper::astroApi('https://json.astrologyapi.com/v1/kalsarpa_details', $language, $apiData), true);

        if ($kalsarpDosha) {
            return response()->json(
                [
                    'status' => 200,
                    'kalsarpDosha' => $kalsarpDosha
                ]
            );
        }
        return response()->json(['status' => 400]);
    }

    //manglik api
    public function manglik_dosh(Request $request)
    {
        $date = explode('/', $request->date);
        $time = explode(':', $request->time);
        $apiData = array(
            'day' => $date['0'],
            'month' => $date['1'],
            'year' => $date['2'],
            'hour' => $time['0'],
            'min' => $time['1'],
            'lat' => intval($request->latitutde),
            'lon' => intval($request->longitude),
            'tzone' => intval($request->timezone),
            'language' => $request->language
        );

        // Check if the requested language is 'hi' or 'en'
        $language = in_array($request->language, ['hi', 'en']) ? $request->language : 'hi';

        $manglikDosh = json_decode(ApiHelper::astroApi('https://json.astrologyapi.com/v1/manglik', $language, $apiData), true);

        if ($manglikDosh) {
            return response()->json(
                [
                    'status' => 200,
                    'manglikDosh' => $manglikDosh
                ]
            );
        }
        return response()->json(['status' => 400]);
    }

    //pitra-dosha api
    public function pitra_dosha(Request $request)
    {
        $date = explode('/', $request->date);
        $time = explode(':', $request->time);
        $apiData = array(
            'day' => $date['0'],
            'month' => $date['1'],
            'year' => $date['2'],
            'hour' => $time['0'],
            'min' => $time['1'],
            'lat' => intval($request->latitutde),
            'lon' => intval($request->longitude),
            'tzone' => intval($request->timezone),
            'language' => $request->language
        );

        // Check if the requested language is 'hi' or 'en'
        $language = in_array($request->language, ['hi', 'en']) ? $request->language : 'hi';

        $pitraDosha = json_decode(ApiHelper::astroApi('https://json.astrologyapi.com/v1/pitra_dosha_report', $language, $apiData), true);

        if ($pitraDosha) {
            return response()->json(
                [
                    'status' => 200,
                    'pitraDosha' => $pitraDosha
                ]
            );
        }
        return response()->json(['status' => 400]);
    }

    //vimshottari-dasha api
    public function vimshottari_dasha(Request $request)
    {
        $date = explode('/', $request->date);
        $time = explode(':', $request->time);
        $apiData = array(
            'day' => $date['0'],
            'month' => $date['1'],
            'year' => $date['2'],
            'hour' => $time['0'],
            'min' => $time['1'],
            'lat' => intval($request->latitutde),
            'lon' => intval($request->longitude),
            'tzone' => intval($request->timezone),
            'language' => $request->language
        );

        // Check if the requested language is 'hi' or 'en'
        $language = in_array($request->language, ['hi', 'en']) ? $request->language : 'hi';

        $vimshottariDasha = json_decode(ApiHelper::astroApi('https://json.astrologyapi.com/v1/current_vdasha', $language, $apiData), true);

        if ($vimshottariDasha) {
            return response()->json(
                [
                    'status' => 200,
                    'vimshottariDasha' => $vimshottariDasha
                ]
            );
        }
        return response()->json(['status' => 400]);
    }

    //mool-ank api
    public function mool_ank(Request $request)
    {
        $date = explode('/', $request->date);
        $time = explode(':', $request->time);
        $apiData = array(
            'day' => $date['0'],
            'month' => $date['1'],
            'year' => $date['2'],
            'hour' => $time['0'],
            'min' => $time['1'],
            'lat' => intval($request->latitutde),
            'lon' => intval($request->longitude),
            'tzone' => intval($request->timezone),
            'language' => $request->language
        );

        // Check if the requested language is 'hi' or 'en'
        $language = in_array($request->language, ['hi', 'en']) ? $request->language : 'hi';

        $moolAnk = json_decode(ApiHelper::astroApi('https://json.astrologyapi.com/v1/numero_table', $language, $apiData), true);

        if ($moolAnk) {
            return response()->json(
                [
                    'status' => 200,
                    'moolAnk' => $moolAnk
                ]
            );
        }
        return response()->json(['status' => 400]);
    }

    //gem-suggestion api
    public function gem_suggestion(Request $request)
    {
        $date = explode('/', $request->date);
        $time = explode(':', $request->time);
        $apiData = array(
            'day' => $date['0'],
            'month' => $date['1'],
            'year' => $date['2'],
            'hour' => $time['0'],
            'min' => $time['1'],
            'lat' => intval($request->latitutde),
            'lon' => intval($request->longitude),
            'tzone' => intval($request->timezone),
            'language' => $request->language
        );

        // Check if the requested language is 'hi' or 'en'
        $language = in_array($request->language, ['hi', 'en']) ? $request->language : 'hi';

        $gemSuggestion = json_decode(ApiHelper::astroApi('https://json.astrologyapi.com/v1/basic_gem_suggestion', $language, $apiData), true);

        if ($gemSuggestion) {
            return response()->json(
                [
                    'status' => 200,
                    'gemSuggestion' => $gemSuggestion
                ]
            );
        }
        return response()->json(['status' => 400]);
    }

    //rudraksha-suggestion api
    public function rudraksha_suggestion(Request $request)
    {
        $date = explode('/', $request->date);
        $time = explode(':', $request->time);
        $apiData = array(
            'day' => $date['0'],
            'month' => $date['1'],
            'year' => $date['2'],
            'hour' => $time['0'],
            'min' => $time['1'],
            'lat' => intval($request->latitutde),
            'lon' => intval($request->longitude),
            'tzone' => intval($request->timezone),
            'language' => $request->language
        );

        // Check if the requested language is 'hi' or 'en'
        $language = in_array($request->language, ['hi', 'en']) ? $request->language : 'hi';

        $rudrakshaSuggestion = json_decode(ApiHelper::astroApi('https://json.astrologyapi.com/v1/rudraksha_suggestion', $language, $apiData), true);
        $rudrakshaImg = $rudrakshaSuggestion['img_url'];
        // Generate the full URL for the image
        $rudrakshaImgUrl = asset($rudrakshaImg);
        // Dump and die to check the generated URL
        //dd($rudrakshaImgUrl);

        if ($rudrakshaSuggestion) {
            return response()->json(
                [
                    'status' => 200,
                    'rudrakshaSuggestion' => $rudrakshaSuggestion,
                    'rudrakshaImgUrl' => $rudrakshaImgUrl
                ]
            );
        }
        return response()->json(['status' => 400]);
    }

    //prayer-suggestion api
    public function prayer_suggestion(Request $request)
    {
        $date = explode('/', $request->date);
        $time = explode(':', $request->time);
        $apiData = array(
            'day' => $date['0'],
            'month' => $date['1'],
            'year' => $date['2'],
            'hour' => $time['0'],
            'min' => $time['1'],
            'lat' => intval($request->latitutde),
            'lon' => intval($request->longitude),
            'tzone' => intval($request->timezone),
            'language' => $request->language
        );

        // Check if the requested language is 'hi' or 'en'
        $language = in_array($request->language, ['hi', 'en']) ? $request->language : 'hi';

        $prayerSuggestion = json_decode(ApiHelper::astroApi('https://json.astrologyapi.com/v1/puja_suggestion', $language, $apiData), true);

        if ($prayerSuggestion) {
            return response()->json(
                [
                    'status' => 200,
                    'prayerSuggestion' => $prayerSuggestion
                ]
            );
        }
        return response()->json(['status' => 400]);
    }

    //maha-vimshottari api
    public function maha_vimshottari(Request $request)
    {
        $date = explode('/', $request->date);
        $time = explode(':', $request->time);
        $apiData = array(
            'day' => $date['0'],
            'month' => $date['1'],
            'year' => $date['2'],
            'hour' => $time['0'],
            'min' => $time['1'],
            'lat' => intval($request->latitutde),
            'lon' => intval($request->longitude),
            'tzone' => intval($request->timezone),
            'language' => $request->language
        );

        // Check if the requested language is 'hi' or 'en'
        $language = in_array($request->language, ['hi', 'en']) ? $request->language : 'hi';

        $mahaVimshottari = json_decode(ApiHelper::astroApi('https://json.astrologyapi.com/v1/major_vdasha', $language, $apiData), true);

        if ($mahaVimshottari) {
            return response()->json(
                [
                    'status' => 200,
                    'mahaVimshottari' => $mahaVimshottari
                ]
            );
        }
        return response()->json(['status' => 400]);
    }

    public function importFromJson()
    {
        $path = public_path('muhurat-json/muhurats.json');

        if (!File::exists($path)) {
            return response()->json(['status' => false, 'error' => 'Data file not found'], 404);
        }

        $data = json_decode(File::get($path), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['status' => false, 'error' => 'Invalid JSON format'], 500);
        }

        $filteredData = array_filter($data, function ($item) {
            return isset($item['type'], $item['titleLink']) && $item['type'] !== 'special-muhurat' && strtotime($item['titleLink']);
        });

        $filteredData = array_map(function ($item) {
            $timestamp = strtotime($item['titleLink']);
            $item['formatted_date'] = date('F j, Y, l', $timestamp);
            $item['year'] = date('Y', $timestamp);
            $item['muhurat'] = null;
            $item['nakshatra'] = null;
            $item['tithi'] = null;

            if (isset($item['details'])) {
                preg_match('/Muhurat:([^;]+);/', $item['details'], $muhurat);
                preg_match('/Nakshatra:([^;]+);/', $item['details'], $nakshatra);
                preg_match('/Tithi:([^;]+)/', $item['details'], $tithi);

                $item['muhurat'] = trim($muhurat[1] ?? '');
                $item['nakshatra'] = trim($nakshatra[1] ?? '');
                $item['tithi'] = trim($tithi[1] ?? '');
            }

            return $item;
        }, $filteredData);

        foreach ($filteredData as $item) {
            $exists = Muhurat::where('titleLink', $item['titleLink'])->where('type', $item['type'])->exists();
            if (!$exists) {
                Muhurat::create([
                    'year' => $item['year'],
                    'type' => $item['type'],
                    'titleLink' => $item['titleLink'],
                    'formatted_date' => $item['formatted_date'],
                    'message' => $item['message'] ?? null,
                    'image' => $item['image'] ?? null,
                    'muhurat' => $item['muhurat'],
                    'nakshatra' => $item['nakshatra'],
                    'tithi' => $item['tithi'],
                ]);
            }
        }

        return response()->json(['status' => true, 'message' => 'Muhurat data imported successfully!']);
    }
    // muhurat api
    public function muhurat(Request $request)
    {
        // Get query parameters with default empty values
        $year = $request->input('year', '');
        $type = $request->input('type', '');
        $month = $request->input('month', '');
        $day = $request->input('day', '');

        // Validate the month to ensure it's a valid month name if not empty
        $validMonths = [
            'january',
            'february',
            'march',
            'april',
            'may',
            'june',
            'july',
            'august',
            'september',
            'october',
            'november',
            'december'
        ];
        if (!empty($month) && !in_array(strtolower($month), $validMonths)) {
            return response()->json(['status' => false, 'error' => 'Invalid month provided'], 400);
        }

        // Validate the day to ensure it's a valid day number if not empty
        if (!empty($day) && (!is_numeric($day) || $day < 1 || $day > 31)) {
            return response()->json(['status' => false, 'error' => 'Invalid day provided'], 400);
        }

        // Read the JSON file
        $path = public_path('muhurat-json/muhurats.json');
        if (!File::exists($path)) {
            return response()->json(['status' => false, 'error' => 'Data file not found'], 404);
        }

        $data = json_decode(File::get($path), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['status' => false, 'error' => 'Invalid JSON format'], 500);
        }

        // Get today's date
        $today = date('Y-m-d');

        // Filter the data
        $filteredData = array_filter($data, function ($item) use ($year, $type, $month, $day, $today) {
            // Exclude special-muhurat type
            if ($item['type'] === 'special-muhurat') {
                return false;
            }

            // Convert titleLink to a standard date format
            $timestamp = strtotime($item['titleLink']);
            if (!$timestamp) {
                return false;
            }

            $itemDate = date('Y-m-d', $timestamp);
            $itemMonth = date('F', $timestamp);
            $itemDay = date('j', $timestamp);
            $itemYear = date('Y', $timestamp);

            // Compare year, type, month, and day, allowing for empty values to match any
            $matchYear = empty($year) || $itemYear == $year;
            $matchType = empty($type) || $item['type'] == $type;
            $matchMonth = empty($month) || strtolower($itemMonth) == strtolower($month);
            $matchDay = empty($day) || $itemDay == $day;

            // Compare with today's date if no parameters provided
            $matchToday = empty($year) && empty($type) && empty($month) && empty($day) && $itemDate == $today;

            return $matchToday || ($matchYear && $matchType && $matchMonth && $matchDay);
        });

        // Check if filtered data is empty and return message if so
        if (empty($filteredData)) {
            return response()->json(['status' => false, 'message' => 'No muhurat found for the given parameters'], 200);
        }

        // Modify 'titleLink' and 'details' format in the filtered data
        $filteredData = array_map(function ($item) {
            // Format titleLink as "January 15, 2020, Wednesday"
            $timestamp = strtotime($item['titleLink']);
            if ($timestamp) {
                $item['titleLink'] = date('F j, Y, l', $timestamp);
            }

            if (isset($item['details'])) {
                // Extract the parts using regular expressions
                preg_match('/Muhurat:([^;]+);/', $item['details'], $muhurat);
                preg_match('/Nakshatra:([^;]+);/', $item['details'], $nakshatra);
                preg_match('/Tithi:([^;]+)/', $item['details'], $tithi);

                // Reformat the item to have separate fields
                $item['muhurat'] = trim($muhurat[1] ?? '');
                $item['nakshatra'] = trim($nakshatra[1] ?? '');
                $item['tithi'] = trim($tithi[1] ?? '');

                // Remove the old 'details' field
                unset($item['details']);
            }
            return $item;
        }, $filteredData);

        // Return the filtered data as JSON response
        return response()->json(['status' => true, 'data' => array_values($filteredData)]);
    }



    //special muhurat api
    public function special_muhurat(Request $request)
    {
        // Get query parameters with default empty values
        $year = $request->input('year', '');
        $type = $request->input('type', '');
        $month = $request->input('month', '');
        $day = $request->input('day', '');

        // Validate the month to ensure it's a valid month name if not empty
        $validMonths = [
            'january',
            'february',
            'march',
            'april',
            'may',
            'june',
            'july',
            'august',
            'september',
            'october',
            'november',
            'december'
        ];
        if (!empty($month) && !in_array(strtolower($month), $validMonths)) {
            return response()->json(['status' => false, 'error' => 'Invalid month provided'], 400);
        }

        // Validate the day to ensure it's a valid day number if not empty
        if (!empty($day) && (!is_numeric($day) || $day < 1 || $day > 31)) {
            return response()->json(['status' => false, 'error' => 'Invalid day provided'], 400);
        }

        // Read the JSON file
        $path = public_path('muhurat-json/muhurats.json');
        if (!File::exists($path)) {
            return response()->json(['status' => false, 'error' => 'Data file not found'], 404);
        }

        $data = json_decode(File::get($path), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['status' => false, 'error' => 'Invalid JSON format'], 500);
        }

        // Get today's date
        $today = date('Y-m-d');

        // Filter the data
        $filteredData = array_filter($data, function ($item) use ($year, $type, $month, $day, $today) {
            // Extract the date from the titleLink (assuming 'titleLink' contains a date string)
            $date = date_parse($item['titleLink']);
            $itemMonth = isset($date['month']) ? date('F', mktime(0, 0, 0, $date['month'], 10)) : '';
            $itemDay = isset($date['day']) ? $date['day'] : '';
            $itemDate = isset($date['year']) && isset($date['month']) && isset($date['day'])
                ? date('Y-m-d', mktime(0, 0, 0, $date['month'], $date['day'], $date['year']))
                : '';

            // Compare year, type, month, and day, allowing for empty values to match any
            $matchYear = empty($year) || $item['year'] == $year;
            $matchType = empty($type) || $item['type'] == $type;
            $matchMonth = empty($month) || strtolower($itemMonth) == strtolower($month);
            $matchDay = empty($day) || $itemDay == $day;

            // Compare with today's date if no parameters provided
            $matchToday = empty($year) && empty($type) && empty($month) && empty($day) && $itemDate == $today;

            return $matchToday || ($matchYear && $matchType && $matchMonth && $matchDay);
        });

        // Check if filtered data is empty and return message if so
        if (empty($filteredData)) {
            return response()->json(['status' => false, 'message' => 'No muhurat found for the given parameters'], 200);
        }

        // Modify the 'details' format in the filtered data
        $filteredData = array_map(function ($item) {
            if (isset($item['details'])) {
                // Extract the parts using regular expressions
                preg_match('/Muhurat:([^;]+);/', $item['details'], $muhurat);
                preg_match('/Nakshatra:([^;]+);/', $item['details'], $nakshatra);
                preg_match('/Tithi:([^;]+)/', $item['details'], $tithi);

                // Reformat the item to have separate fields
                $item['muhurat'] = trim($muhurat[1] ?? '');
                $item['nakshatra'] = trim($nakshatra[1] ?? '');
                $item['tithi'] = trim($tithi[1] ?? '');

                // Remove the old 'details' field
                unset($item['details']);
            }
            return $item;
        }, $filteredData);

        // Return the filtered data as JSON response
        return response()->json(['status' => true, 'data' => array_values($filteredData)]);
    }


    //fav-mantra api
    public function fav_mantra(Request $request)
    {
        $date = explode('/', $request->date);
        $time = explode(':', $request->time);
        $apiData = array(
            'day' => $date['0'],
            'month' => $date['1'],
            'year' => $date['2'],
            'hour' => $time['0'],
            'min' => $time['1'],
            'lat' => intval($request->latitutde),
            'lon' => intval($request->longitude),
            'tzone' => intval($request->timezone),
            'language' => $request->language
        );

        // Check if the requested language is 'hi' or 'en'
        $language = in_array($request->language, ['hi', 'en']) ? $request->language : 'hi';

        $favMantra = json_decode(ApiHelper::astroApi('https://json.astrologyapi.com/v1/numero_fav_mantra', $language, $apiData), true);

        if ($favMantra) {
            return response()->json(
                [
                    'status' => 200,
                    'favMantra' => $favMantra
                ]
            );
        }
        return response()->json(['status' => 400]);
    }

    //fav-lord api
    public function fav_lord(Request $request)
    {
        $date = explode('/', $request->date);
        $time = explode(':', $request->time);
        $apiData = array(
            'day' => $date['0'],
            'month' => $date['1'],
            'year' => $date['2'],
            'hour' => $time['0'],
            'min' => $time['1'],
            'lat' => intval($request->latitutde),
            'lon' => intval($request->longitude),
            'tzone' => intval($request->timezone),
            'language' => $request->language
        );

        // Check if the requested language is 'hi' or 'en'
        $language = in_array($request->language, ['hi', 'en']) ? $request->language : 'hi';

        $favLord = json_decode(ApiHelper::astroApi('https://json.astrologyapi.com/v1/numero_fav_lord', $language, $apiData), true);

        if ($favLord) {
            return response()->json(
                [
                    'status' => 200,
                    'favLord' => $favLord
                ]
            );
        }
        return response()->json(['status' => 400]);
    }

    //fasts api
    public function fast(Request $request)
    {
        $date = explode('/', $request->date);
        $time = explode(':', $request->time);
        $apiData = array(
            'day' => $date['0'],
            'month' => $date['1'],
            'year' => $date['2'],
            'hour' => $time['0'],
            'min' => $time['1'],
            'lat' => intval($request->latitutde),
            'lon' => intval($request->longitude),
            'tzone' => intval($request->timezone),
            'language' => $request->language
        );

        // Check if the requested language is 'hi' or 'en'
        $language = in_array($request->language, ['hi', 'en']) ? $request->language : 'hi';

        $fasts = json_decode(ApiHelper::astroApi('https://json.astrologyapi.com/v1/numero_fasts_report', $language, $apiData), true);

        if ($fasts) {
            return response()->json(
                [
                    'status' => 200,
                    'fasts' => $fasts
                ]
            );
        }
        return response()->json(['status' => 400]);
    }

    //fav-time api
    public function fav_time(Request $request)
    {
        $date = explode('/', $request->date);
        $time = explode(':', $request->time);
        $apiData = array(
            'day' => $date['0'],
            'month' => $date['1'],
            'year' => $date['2'],
            'hour' => $time['0'],
            'min' => $time['1'],
            'lat' => intval($request->latitutde),
            'lon' => intval($request->longitude),
            'tzone' => intval($request->timezone),
            'language' => $request->language
        );

        // Check if the requested language is 'hi' or 'en'
        $language = in_array($request->language, ['hi', 'en']) ? $request->language : 'hi';

        $favTime = json_decode(ApiHelper::astroApi('https://json.astrologyapi.com/v1/numero_fav_time', $language, $apiData), true);

        if ($favTime) {
            return response()->json(
                [
                    'status' => 200,
                    'favTime' => $favTime
                ]
            );
        }
        return response()->json(['status' => 400]);
    }
    public function rashi_list()
    {
        $rashis = Rashi::where('status', 1)->with('translations')->get();

        foreach ($rashis as $rashi) {
            $rashi->image = asset('storage/app/public/rashi/' . $rashi->image);
            $translations = $rashi->translations()->pluck('value', 'key')->toArray();
            $hiName = $translations['name'] ?? null;
            $rashi->hi_name = $hiName;
        }

        if ($rashis) {
            return response()->json(['status' => 200, 'rashi' => $rashis]);
        }
        return response()->json(['status' => 400, 'message' => 'rashi data not found']);
    }
    public function youtube_video_category()
    {
        $videoCategory = VideoCategory::with('translations')->get();

        foreach ($videoCategory as $video) {
            $translations = $video->translations()->pluck('value', 'key')->toArray();
            $hiName = $translations['name'] ?? null;
            $video->hi_name = $hiName;
        }

        if ($videoCategory) {
            return response()->json(['status' => 200, 'videoCategory' => $videoCategory]);
        }
        return response()->json(['status' => 400, 'message' => 'video category data not found']);
    }

    public function getByYoutube_category($category_id)
    {
        // Validate category_id
        if (!is_numeric($category_id)) {
            return response()->json(['error' => 'Invalid category ID'], 400);
        }

        // Fetch records by category_id
        $records = VideoSubCategory::where('category_id', $category_id)->with('translations')->get();

        foreach ($records as $data) {
            $translations = $data->translations()->pluck('value', 'key')->toArray();
            $hiName = $translations['name'] ?? null;
            $data->hi_name = $hiName;
        }

        // Return the records as JSON
        return response()->json($records);
    }

    // rashi detail api
    public function rashi_detail($name, $language)
    {
        $apiData = array(
            'tzone' => 5.5
        );

        $url = "https://json.astrologyapi.com/v1/sun_sign_prediction/daily/" . $name;
        $dailyRashiData = json_decode(ApiHelper::astroApi($url, $language, $apiData), true);

        if ($dailyRashiData) {
            return response()->json(['status' => 200, 'rashi' => $dailyRashiData]);
        }
        return response()->json(['status' => 400, 'message' => 'rashi data not found']);
    }

    public function save_kundali_list($user_id)
    {
        $userDetails = UserKundali::where('user_id', $user_id)->get();

        if ($userDetails->isEmpty()) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($userDetails);
    }

    public function save_kundali_milan_list($user_id)
    {
        $userDetails = UserKundaliMilan::where('user_id', $user_id)->get();

        if ($userDetails->isEmpty()) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($userDetails);
    }

    public function delete_kundali($id)
    {
        // Find the user kundali milan records by id
        $userDetails = UserKundali::where('id', $id);

        if ($userDetails->doesntExist()) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Delete the records
        $userDetails->delete();

        return response()->json(['message' => 'User kundali deleted successfully'], 200);
    }

    public function delete_kundali_milan($id)
    {
        // Find the user kundali milan records by id
        $userDetails = UserKundaliMilan::where('id', $id);

        if ($userDetails->doesntExist()) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Delete the records
        $userDetails->delete();

        return response()->json(['message' => 'User kundali milan deleted successfully'], 200);
    }

    public function events_name(Request $request)
    {
        // Get the request parameters with default values
        $eventName = $request->input('eventName');
        $month = $request->input('month', '');
        $year = $request->input('year', date('Y'));
        $type = $request->input('type', '');

        // Define the file path
        $filePath = public_path("event-json/events-{$year}.json");

        // Check if the file exists
        if (!File::exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        // Decode the JSON file content
        $events = json_decode(File::get($filePath), true);

        // Check for JSON decoding errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'Invalid JSON data'], 500);
        }

        // Filter events by month, year, type, and eventName
        $filteredEvents = array_filter($events, function ($event) use ($month, $year, $type, $eventName) {
            // Extract the month and year from the eventDate (format: "Day, DD-MM-YYYY")
            $eventMonth = substr($event['eventDate'], -7, 2);
            $eventYear = substr($event['eventDate'], -4, 4);

            // Convert month and year to strings for comparison
            $monthMatch = ($month === '' || $eventMonth === str_pad($month, 2, '0', STR_PAD_LEFT));
            $yearMatch = ($eventYear === $year);
            $nameMatch = ($eventName === '' || stripos($event['eventName'], $eventName) !== false);

            // Return true if month, year, and event name match
            return $monthMatch && $yearMatch && $nameMatch;
        });

        // Enrich events with event type and remove eventUrl
        $finalEvents = [];
        foreach ($filteredEvents as $event) {
            // Retrieve event type from the database
            $festival = FastFestival::select('event_type', 'event_name', 'en_description', 'hi_description', 'image')
                ->where('event_name', $event['eventName'])
                ->first();

            // Add the event type to the event
            if ($festival) {
                $event['eventType'] = $festival->event_type;
                $event['en_description'] = $festival->en_description;
                $event['hi_description'] = $festival->hi_description;
                $event['image'] = asset('storage/app/public/fastfestival-img/' . $festival->image);
            } else {
                $event['eventType'] = 'Unknown';
                $event['en_description'] = null;
                $event['hi_description'] = null;
                $event['image'] = null;
            }

            // Add event to final list if type matches or no type filter is applied
            if (($type === '' || $event['eventType'] === $type) && ($eventName === '' || stripos($event['eventName'], $eventName) !== false)) {
                // Remove the eventUrl field
                unset($event['eventUrl']);
                $finalEvents[] = $event;
            }
        }
        return response()->json(array_values($finalEvents));
    }

    public function north_charts(Request $request)
    {
        $type = $request->type;
        $data = array(
            'day' => $request->day,
            'month' => $request->month,
            'year' => $request->year,
            'hour' => $request->hour,
            'min' => $request->min,
            'lat' => $request->lat,
            'lon' => $request->lon,
            'tzone' => $request->tzone
        );
        return view('north-charts.index', compact('type', 'data'));
    }
    public function south_charts(Request $request)
    {
        $type = $request->type;
        $data = array(
            'day' => $request->day,
            'month' => $request->month,
            'year' => $request->year,
            'hour' => $request->hour,
            'min' => $request->min,
            'lat' => $request->lat,
            'lon' => $request->lon,
            'tzone' => $request->tzone
        );
        return view('south-charts.index', compact('type', 'data'));
    }

    public function milan_male_charts(Request $request)
    {
        $type = $request->type;
        $male_data = array(
            'male_day' => $request->male_day,
            'male_month' => $request->male_month,
            'male_year' => $request->male_year,
            'male_hour' => $request->male_hour,
            'male_min' => $request->male_min,
            'male_lat' => $request->male_lat,
            'male_lon' => $request->male_lon,
            'male_tzone' => $request->male_tzone
        );
        $female_data = array(
            'female_day' => $request->female_day,
            'female_month' => $request->female_month,
            'female_year' => $request->female_year,
            'female_hour' => $request->female_hour,
            'female_min' => $request->female_min,
            'female_lat' => $request->female_lat,
            'female_lon' => $request->female_lon,
            'female_tzone' => $request->female_tzone
        );
        return view('milan-male-charts.index', compact('type', 'male_data', 'female_data'));
    }

    public function milan_female_charts(Request $request)
    {
        $type = $request->type;
        $male_data = array(
            'male_day' => $request->male_day,
            'male_month' => $request->male_month,
            'male_year' => $request->male_year,
            'male_hour' => $request->male_hour,
            'male_min' => $request->male_min,
            'male_lat' => $request->male_lat,
            'male_lon' => $request->male_lon,
            'male_tzone' => $request->male_tzone
        );
        $female_data = array(
            'female_day' => $request->female_day,
            'female_month' => $request->female_month,
            'female_year' => $request->female_year,
            'female_hour' => $request->female_hour,
            'female_min' => $request->female_min,
            'female_lat' => $request->female_lat,
            'female_lon' => $request->female_lon,
            'female_tzone' => $request->female_tzone
        );
        return view('milan-female-charts.index', compact('type', 'male_data', 'female_data'));
    }

    public function moonimage()
    {
        $images = PanchangMoonImage::all();

        if ($images->isNotEmpty()) {
            $data = $images->map(function ($image) {
                // Assuming you want to get all values from translations
                $translations = $image->translations()->pluck('value')->toArray();

                return [
                    'id' => $image->id,
                    'en_name' => $image->name,
                    'hi_name' => implode(', ', $translations), // Join all values into a single string
                    'image' => $image->image,
                    'status' => $image->status,
                ];
            });

            // Conditionally filter data based on en_name
            $filteredData = $data->where('en_name')->values();

            //dd($filteredData);

            if ($filteredData->isNotEmpty()) {
                return response()->json([
                    'status' => 200,
                    'data' => $filteredData,
                ]);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'No images found matching the condition',
                ]);
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'No images found',
            ]);
        }
    }


    public function getVideosBySubcategory($id)
    {
        $videos = Video::where('subcategory_id', $id)->get();

        if ($videos->isEmpty()) {
            return response()->json([
                'status' => 400,
                'message' => 'No videos found for the given subcategory id',
            ]);
        } else {
            return response()->json([
                'status' => 200,
                'data' => $videos,
            ]);
        }
    }

    public function counselling()
    {
        // Fetch all services where product_type is 'counselling' and select specific fields
        $counselling = Service::where('product_type', 'counselling')->where('sub_category_id', 40)->get();

        if ($counselling->isNotEmpty()) {
            $data = $counselling->map(function ($item) {
                $images = $item->images ? json_decode($item->images, true) : null;
                $translations = $item->translations()->pluck('value', 'key')->toArray();

                return [
                    'id' => $item->id,
                    'en_name' => $item->name,
                    'hi_name' => $translations['name'] ?? null,
                    'images' => $images ? array_map(function ($img) {
                        return url('/storage/app/public/pooja/' . $img);
                    }, $images) : null,
                    'thumbnail' => $item->thumbnail ? url('/storage/app/public/pooja/thumbnail/' . $item->thumbnail) : null,
                    'product_type' => $item->product_type,
                    'counselling_main_price' => $item->counselling_main_price,
                    'counselling_selling_price' => $item->counselling_selling_price,
                ];
            });

            $filteredData = $data->filter(function ($item) {
                return !empty($item['en_name']);
            })->values();

            if ($filteredData->isEmpty()) {
                return response()->json([
                    'status' => 400,
                    'message' => 'No counselling services found.',
                ]);
            } else {
                return response()->json([
                    'status' => 200,
                    'data' => $filteredData,
                ]);
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'No counselling services available.',
            ]);
        }
    }

   
    public function counselling_detail($identifier)
    {
        if (is_numeric($identifier)) {
            $counselling = Service::where('product_type', 'counselling')
                ->where('id', $identifier)
                ->first();
        } else {
            $counselling = Service::where('product_type', 'counselling')
                ->where('slug', $identifier)
                ->first();
        }

        if (!$counselling) {
            return response()->json([
                'status' => 400,
                'message' => 'No counselling service found for the given ID or slug.',
            ]);
        }

        $images = $counselling->images ? json_decode($counselling->images, true) : null;

        $translations = $counselling->translations()->pluck('value', 'key')->toArray();
        $count = Service_order::where('type', 'counselling')->where('service_id', $counselling->id)->count();

        $data = [
            'count' => $count,
            'id' => $counselling->id,
            'en_name' => $counselling->name,
            'hi_name' => $translations['name'] ?? null,
            'images' => $images ? array_map(function ($img) {
                return url('/storage/app/public/pooja/' . $img);
            }, $images) : null,
            'thumbnail' => $counselling->thumbnail ? url('/storage/app/public/pooja/thumbnail/' . $counselling->thumbnail) : null,
            'product_type' => $counselling->product_type,
            'counselling_main_price' => $counselling->counselling_main_price,
            'counselling_selling_price' => $counselling->counselling_selling_price,
            'en_details' => $counselling->details,
            'hi_details' => $translations['description'] ?? null,
            'en_process' => $counselling->process,
            'hi_process' => $translations['process'] ?? null,
        ];

        return response()->json([
            'status' => 200,
            'data' => $data,
        ]);
    }

    public function shubhmuhurat()
    {
        // Fetch the category with the name 'auspicious occasion'
        $category = Category::where('name', 'Auspicious Muhurat Consultation')->first();

        if ($category) {
            $services = Service::where('sub_category_id', $category->id)->get();

            if ($services->isNotEmpty()) {
                $data = $services->map(function ($service) {
                    $images = $service->images ? json_decode($service->images, true) : null;
                    $translations = $service->translations()->pluck('value', 'key')->toArray();

                    return [
                        'id' => $service->id,
                        'en_name' => $service->name,
                        'hi_name' => $translations['name'] ?? null,
                        'images' => $images ? array_map(function ($img) {
                            return url('/storage/app/public/pooja/' . $img);
                        }, $images) : null,
                        'thumbnail' => $service->thumbnail ? url('/storage/app/public/pooja/thumbnail/' . $service->thumbnail) : null,
                        'sub_category_id' => $service['sub_category_id'],
                        'counselling_main_price' => $service->counselling_main_price,
                        'counselling_selling_price' => $service->counselling_selling_price,
                    ];
                });

                $filteredData = $data->filter(function ($item) {
                    return !empty($item['en_name']);
                })->values();

                if ($filteredData->isEmpty()) {
                    return response()->json([
                        'status' => 400,
                        'message' => 'No counselling services found.',
                    ]);
                } else {
                    return response()->json([
                        'status' => 200,
                        'data' => $filteredData,
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'No counselling services available.',
                ]);
            }
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'No category with the name "auspicious occasion" found.',
            ]);
        }
    }

    public function shubhmuhurat_detail($id)
    {
        // Fetch the category with the name 'auspicious occasion'
        $category = Category::where('name', 'auspicious occasion')->first();

        if ($category) {

            $services = Service::where('id', $id)
                ->where('sub_category_id', $category->id)
                ->first();

            if (!$services) {
                return response()->json([
                    'status' => 400,
                    'message' => 'No counselling service found for the given ID.',
                ]);
            }
            $images = $services->images ? json_decode($services->images, true) : null;
            $translations = $services->translations()->pluck('value', 'key')->toArray();

            $data = [
                'id' => $services->id,
                'en_name' => $services->name,
                'hi_name' => $translations['name'] ?? null,
                'images' => $images ? array_map(function ($img) {
                    return url('/storage/app/public/pooja/' . $img);
                }, $images) : null,
                'thumbnail' => $services->thumbnail ? url('/storage/app/public/pooja/thumbnail/' . $services->thumbnail) : null,
                'sub_category_id' => $services->sub_category_id,
                'counselling_main_price' => $services->counselling_main_price,
                'counselling_selling_price' => $services->counselling_selling_price,
                'en_details' => $services->details,
                'hi_details' => $translations['description'] ?? null,
                'en_process' => $services->process,
                'hi_process' => $translations['process'] ?? null,
            ];

            return response()->json([
                'status' => 200,
                'data' => $data,
            ]);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'No category with the name "auspicious occasion" found.',
            ]);
        }
    }


    // generate pdf
    public function generatePdf(Request $request)
    {
        $kundaliPdf = "";
        $apiData = array(
            'name' => $request->name,
            'gender' => $request->gender,
            'day' => $request->day,
            'month' => $request->month,
            'year' => $request->year,
            'hour' => $request->hour,
            'min' => $request->min,
            'lat' => $request->lat,
            'lon' => $request->lon,
            'language' => $request->language,
            'tzone' => $request->tzone,
            'place' => $request->place,
            'chart_style' => $request->chart_style,
            'footer_link' => 'mahakal.com',
            'logo_url' => 'https://mahakal.com/public/logo-full.gif',
            'company_name' => 'Mahakal Astrotech OPC Pvt Ltd.',
            'company_info' => 'Description of Mahakal Astrotech OPC Pvt Ltd.',
            'domain_url' => 'https://mahakal.com/',
            'company_email' => 'contact@mahakal.com',
            'company_landline' => '08069645013',
            'company_mobile' => '08069645013'
        );

        // Check if the requested language is 'hi' or 'en'
        $language = in_array($request->language, ['hi', 'en']) ? $request->language : 'hi';

        if ($request->type == "basic") {
            $kundaliPdf = json_decode(ApiHelper::astroApi('https://pdf.astrologyapi.com/v1/basic_horoscope_pdf', $language, $apiData), true);
        } else if ($request->type == "pro") {
            $kundaliPdf = json_decode(ApiHelper::astroApi('https://pdf.astrologyapi.com/v1/pro_horoscope_pdf', $language, $apiData), true);
        }

        if ($kundaliPdf) {
            return response()->json(['status' => 200, 'kundaliPdf' => $kundaliPdf]);
        }
        return response()->json(['status' => 400]);
    }

    public function counselling_order_list()
    {
        if (!empty(Auth::guard('api')->user()->id)) {
            $orderList = Service_order::where('type', 'counselling')->where('customer_id', Auth::guard('api')->user()->id)->with('services.category')->orderBy('created_at', 'desc')->get();
            if ($orderList) {
                return response()->json(['status' => true, 'orderList' => $orderList]);
            } else {
                return response()->json(['status' => true, 'orderList' => null]);
            }
        }
        return response()->json(['status' => false, 'message' => 'An authorized user']);
    }

    public function counselling_order_detail($orderId)
    {
        if (!empty(Auth::guard('api')->user()->id)) {
            $orderDetail = Service_order::where('order_id', $orderId)->with('services.category')->with('counselling_user')->first();
            if ($orderDetail) {
                return response()->json(['status' => true, 'orderDetail' => $orderDetail]);
            } else {
                return response()->json(['status' => true, 'orderDetail' => null]);
            }
        }
        return response()->json(['status' => false, 'message' => 'An authorized user']);
    }

    public function get_review($serviceId)
    {
        $review = ServiceReview::where('service_id', $serviceId)
            ->where('status', 1)
            ->with(['userData'])
            ->get();

        if ($review) {
            $review->each(function ($item) {
                if ($item->userData) {
                    $item->userData->image = $item->userData->image
                        ? 'https://mahakal.com/storage/app/public/profile/' . $item->userData->image
                        : null;
                }
            });

            return response()->json(['status' => true, 'review' => $review], 200);
        }
        return response()->json(['status' => false, 'message' => 'An error occured'], 400);
    }

    public function add_review(Request $request)
    {
        try {
            if (Auth::guard('api')->check()) {
                $userId = Auth::guard('api')->user()->id;
                if ($userId) {
                    $reviewStore = ServiceReview::where('order_id', $request->order_id)->first();
                    $reviewStore->astro_id = $request->astro_id;
                    $reviewStore->user_id = $userId;
                    $reviewStore->service_id = $request->service_id;
                    $reviewStore->comment = $request->comment;
                    $reviewStore->rating = $request->rating;
                    $reviewStore->is_edited = 1;
                    if ($reviewStore->save()) {
                        return response()->json(['status' => true, 'message' => 'Review store successfully'], 200);
                    }
                    return response()->json(['status' => false, 'message' => 'Unable to store review'], 200);
                }
                return response()->json(['status' => false, 'message' => 'Unauthorized user'], 401);
            } else {
                return response()->json(['status' => false, 'message' => 'Unauthorized user'], 401);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
