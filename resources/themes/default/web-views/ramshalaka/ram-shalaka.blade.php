@extends('layouts.front-end.app')

@section('title', translate('Ram Shalaka Prashnavali - Shri Ram Se Janein Apne Prashno Ka Uttar | Mahakal.com'))

@push('css_or_js')
    <meta name="description"
        content="Ram Shalaka Prashnavali ke madhyam se Shri Ram se apne jeevan ke sawalon ka uttar paaiye. Mahakal.com par Ramcharitmanas aadharit divya margdarshan ke liye abhi click karein.">
    <meta property="og:image"
        content="{{ dynamicStorage(path: 'storage/app/public/company') }}/{{ $web_config['web_logo']->value }}" />
    <meta property="og:title" content="Privacy policy of {{ $web_config['name']->value }} " />
    <meta property="og:url" content="{{ env('APP_URL') }}">
    <meta property="og:description"
        content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">
    <meta property="twitter:card"
        content="{{ dynamicStorage(path: 'storage/app/public/company') }}/{{ $web_config['web_logo']->value }}" />
    <meta property="twitter:title" content="Privacy policy of {{ $web_config['name']->value }}" />
    <meta property="twitter:url" content="{{ env('APP_URL') }}">
    <meta property="twitter:description"
        content="{{ substr(strip_tags(str_replace('&nbsp;', ' ', $web_config['about']->value)), 0, 160) }}">
    <style>
        .ramshalaka {
            text-align: center;
        }

        .ramshalaka .link {
            border: 1px solid #67031c;
            color: #fff !important;
            text-decoration: none;
            background: #fe9802;
            /* float: left; */
            font-weight: 500;
            font-size: 16px;
            text-align: center;
            line-height: 0.8;
            padding: 8px;
            margin-top: 5px;
            cursor: pointer;
            border-radius: 30px;
            /* margin-right: 5px; */
            /* width: 72px; */
            /* margin-bottom: 12px;*/
        }

        .ramshalaka .link:hover {
            background-color: #870625;
            border-color: #870625;
        }

        .result {
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        table {
            border-collapse: collapse;
            margin: 20px auto;
        }

        td {
            border: 1px solid #fe9802;
            /* width: 50px;
                                height: 50px;
                                text-align: center;
                                color: red;
                                padding: 5px;
                                font-size: 16px; */
        }

        .my_table {
            width: auto;
            table-layout: fixed;
            border-collapse: collapse;
            border: 4px solid #fe9802 !important;
        }
    </style>
@endpush

@section('content')
    {{-- main --}}
    <div class="inner-page-bg center bg-bla-7 py-4"
        style="background:url({{ asset('public/assets/front-end/img/bg.jpg') }}) no-repeat;background-size:cover;background-position:center center">
        <div class="container">
            <div class="row all-text-white">
                <div class="col-md-12 align-self-center">
                    <h1 class="innerpage-title">{{ translate('ram_shalaka') }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active"><a href="{{ url('/') }}" class="text-white"><i
                                        class="fa fa-home"></i> Home</a></li>
                            <li class="breadcrumb-item">{{ translate('ram_shalaka') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="container py-5 rtl text-align-direction">

        <div class="card __card">
            <div class="card-body text-justify">
                <h3>श्रीराम शलाका प्रश्नावली (Shri Ram Shalaka Prashnavali)</h3>
                <div id="ram-shalaka">
                    <p><img src="{{ asset('public/assets/front-end/img/kalash-1.png') }}" alt=""> हर व्यक्ति
                        चाहता है की उसका जीवन एक परी कथा की तरह हो, उसे जीवन में हर सुख
                        सुविधा मिले, सभी कार्य
                        उसके अनुरूप हों। लेकिन यह जीवन कोई परी कथा नहीं वरन इस जीवन में हमें नित्य नयी चुनौतियों का सामना
                        करना पड़ता है ।</p>
                    <p><img src="{{ asset('public/assets/front-end/img/kalash-1.png') }}" alt=""> हम कार्य तो बहुत
                        से करते है,सपने हमारे असीमित है लेकिन बहुत से कार्य बहुत से सपने पूरे नहीं हो पाते है, कई बार दूसरे
                        लोग जिस कार्य में सफल हो रहे होते है हम असफल हो जाते है या तमाम परिश्रम तमाम योजनाओं के बाद भी
                        अपेक्षित परिणाम नहीं मिलते है ……तब हम असमंजस में पड़ जाते है की हम क्या करे ……हमें अमुक कार्य करना
                        चाहिए अथवा नहीं हमें सफलता मिलगी अथवा हमारी मेहनत व्यर्थ चली जाएगी,ऐसी असमंज की स्तिथि से पार पाने
                        के लिए पवित्र श्रीराम शलाका(Shri Ram Shalaka Prashnavali) से हमें सच्चा मार्ग दर्शन प्राप्त हो सकता
                        है ।</p>
                    <p><img src="{{ asset('public/assets/front-end/img/kalash-1.png') }}" alt=""> हमारे धार्मिक
                        साहित्य में इस अदभुत पवित्र श्री राम शलाका ( Ram Shalaka ) की बहुत मान्यता है और इसका उपयोग भी बहुत
                        ही सरल है।</p>
                    <p><img src="{{ asset('public/assets/front-end/img/kalash-1.png') }}" alt=""> सर्वप्रथम प्रभु
                        श्री राम का सच्चे हर्दय से ध्यान करते हुए अपने मन में अपना प्रश्न सोचें जिस पर आप प्रभु की कृपा चाह
                        रहे है, फिर उस कार्य की सफलता की प्रार्थना करते हुए नीचे दिए गए “किसी भी शब्द पर अपनी आंख बंद करके
                        क्लिक कर दें .</p>
                    <div id="ram-shalaka" style="overflow:scroll">
                        <div class="ramshalaka">
                            <p>जिस शब्द पर आपने क्लिक किया है, उससे हर नौ खानों में दिए गए शब्दों को जोड़कर एक चौपाई बनती
                                है।</p>
                            <div>
                                <table class="my_table">
                                    <tbody>
                                        @foreach ($ramShalaka->chunk(15) as $row)
                                            <tr>
                                                @foreach ($row as $key => $item)
                                                    <td>
                                                        <label for="chk-link-{{ $key + 1 }}" class="link"
                                                            data-id="{{ $item->id }}"
                                                            data-letter="{{ $item->letter }}"
                                                            data-chaupai="{{ $item->chaupai }}"
                                                            data-description="{{ $item->description }}">
                                                            {{ $item->letter }}
                                                        </label>
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Chaupai and Description -->
    <div class="modal fade" id="chaupaiModal" role="dialog" aria-labelledby="chaupaiModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="row" style="width: 100%;">
                        <div class="col-2"><strong>अक्षर:</strong></div>
                        <div class="col-10" id="letterText"></div>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row pb-2">
                        <div class="col-2"><strong>चौपाई:</strong></div>
                        <div class="col-10" id="chaupaiText"></div>
                    </div>
                    <div class="row pb-2">
                        <div class="col-2"><strong>अर्थ:</strong></div>
                        <div class="col-10" id="descriptionText"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        document.querySelectorAll('.link').forEach(function(link) {
            link.addEventListener('click', function() {

                var letter = this.getAttribute('data-letter');
                var chaupai = this.getAttribute('data-chaupai');
                var description = this.getAttribute('data-description');

                document.getElementById('letterText').textContent = letter;
                document.getElementById('chaupaiText').textContent = chaupai;
                document.getElementById('descriptionText').textContent = description;

                $('#chaupaiModal').modal('show');
            });
        });

        document.querySelector('.close').addEventListener('click', function() {

            $('#chaupaiModal').modal('hide');
        });

        window.onclick = function(event) {
            if (event.target === document.getElementById('chaupaiModal')) {
                $('#chaupaiModal').modal('hide');
            }
        };
    </script>
@endpush
