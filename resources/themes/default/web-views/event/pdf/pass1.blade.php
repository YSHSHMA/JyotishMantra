<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Concert Event Pass</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">



    <!-- Custom CSS -->
    <style>
        /* Event Pass Styles */
        svg {
            height: 120px;
            width: 120px;
        }

        .pass {
            /* background: linear-gradient(to right, #6a00ff, #c700e3, #ff4b2b); */
            background: linear-gradient(135deg, #ff8a00, #e52e71, #9b00ff);
            color: white;
            /* border-radius: 20px; */
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 900px;
            margin: auto;
            position: relative;
            /* box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); */
        }

        /* Left Side: Event details */
        .pass-left {
            display: flex;
            /* align-items: center; */
        }

        .singer-img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
            margin-right: 20px;
            border: 3px dotted white;
            margin-top: 47px;
        }

        /* Event details */
        .event-info {
            display: flex;
            flex-direction: column;
        }

        .event-title {
            font-size: 36px;
            font-weight: bold;
            color: #fff;
            margin-bottom: 10px;
        }

        .event-subtitle {
            font-size: 20px;
            margin-bottom: 11px;
            color: #f9f9f9;
        }

        /* Right Side: Ticket info and QR code */
        .pass-right {
            text-align: center;
        }

        .qr-code img {
            width: 119px;
            height: 119px;
            margin: 10px 0;
        }

        /* Price and Date */
        .ticket-info {
            margin-top: 10px;
            font-size: 16px;
            color: #fff;
        }

        .ticket-info i {
            margin-right: 10px;
        }

        /* Dotted border */
        .dotted-border {
            border-left: 3px dotted white;
            height: 150px;
            margin: 0 20px;
        }

        /* Background image for pass */
        .pass-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: -1;
            opacity: 0.2;
            background: url('https://via.placeholder.com/900x400') no-repeat center center/cover;
        }

        .event-footer {
            color: white;
            padding: 4px 0px;
            font-size: 14px;
            border-top: 1px solid #fff;
        }

        @media (max-width: 576px) {
            .pass {
                flex-direction: column;
                align-items: center;
            }

            .pass-left {
                display: block;
            }

            .dotted-border {
                border-left: none;
                border-top: 3px dotted white;
                width: 80%;
                height: 3px;
                margin: 20px 0;
            }

            .pass-left,
            .pass-right {
                width: 100%;
                text-align: center;
            }

            .singer-img {
                width: 89px;
                height: 89px;
                margin-top: 2px;
            }

            .event-title {
                font-size: 20px;
            }

            .event-subtitle {
                font-size: 13px;
            }

            .event-info span.mb-2 {
                font-size: 9px;
                text-align: left;
                margin-left: 14px;
            }

            .event-info .col-md-2.p-0 i {
                margin-left: 14px;
            }

            .event-info .col-md-10.p-0,
            .event-info .col-md-2.p-0 {
                font-size: 11px;
                text-align: left;
            }

            .event-footer {
                font-size: 8px;
            }
        }
    </style>
</head>

<body>
    @php
    $langs = (str_replace('_', '-', app()->getLocale()) == 'in')?'hi': str_replace('_', '-', app()->getLocale())
    @endphp
    <div class="container mt-5">
        <div class="pass">
            <div class="pass-bg"></div>
            <div class="pass-left">
                <img src="{{ getValidImage(path: 'storage/app/public/event/events/' . $orderData['eventid']['event_image'], type: 'product') }}" alt="Singer Image" class="singer-img">
                <div class="event-info">
                    <h2 class="event-title">LIVE
                        @if(($orderData['eventid']['categorys']??''))
                        {{strtoupper($orderData['eventid']['categorys']->getRawOriginal('category_name'))}}
                        @endif

                    </h2>
                    <p class="event-subtitle">Biggest
                        @if(($orderData['eventid']['categorys']??''))
                        {{ucwords($orderData['eventid']['categorys']->getRawOriginal('category_name'))}} of the Year
                        @endif
                    </p>
                    <span class="mb-2"><i class="fa fa-user" aria-hidden="true"></i> User Name: <strong>
                            @if($orderData['orderitem'][0] && json_decode($orderData['orderitem'][0]['user_information']))
                            <?php $getMemberList = json_decode($orderData['orderitem'][0]['user_information'], true); ?>
                            {{ ucwords($getMemberList[($num - 1)]['name']??"") }}
                            @endif
                        </strong> </span>
                    <span class="mb-2"><i class="fab fa-evernote"></i> Event Name: <strong>
                            @if($orderData['eventid'])
                            {{ucwords($orderData['eventid']->getRawOriginal('event_name'))}}
                            @endif
                        </strong></span>
                    <span class="mb-2"><i class="fas fa-user"></i> Performed by: <strong>
                            @if(($orderData['eventid']['eventArtist']??""))
                            {{strtoupper($orderData['eventid']['eventArtist']->getRawOriginal('name'))}}
                            @endif
                        </strong></span>
                    @php($venue_name = '')
                    @php($event_date = '')
                    @if(!empty($orderData['eventid']['all_venue_data']) && json_decode($orderData['eventid']['all_venue_data'],true))
                    @foreach(json_decode($orderData['eventid']['all_venue_data'],true) as $venue)
                    @if($venue['id'] == $orderData['venue_id'])
                    @php($venue_name = ((!empty($venue['en_event_venue_full_address']??'')) ? ucwords($venue['en_event_venue_full_address']??'') : ucwords($venue['en_event_venue']??'')))
                    @php($event_date = date('d M,Y h:i A',(strtotime($venue['date'].' '.$venue['start_time']))))
                    @break
                    @endif
                    @endforeach
                    @endif
                    <div class="container-fluid mb-3">
                        <div class="row p-0">
                            <div class="col-md-2 col-4 p-0"><i class="fas fa-map-marker-alt"></i> Venue:</div>
                            <div class="col-md-10 col-8 p-0">{{ $venue_name }}</div>
                        </div>
                    </div>
                    <footer class="event-footer text-center">
                        <a><i class="fa fa-solid fa-phone-volume"></i> {{$web_config['phone']->value}} | <i class="fa fa-solid fa-envelope"></i> {{$web_config['email']->value}} | <i class="fa fa-solid fa-globe"></i> {{ url('/') }}</a>
                        <br>
                        <a>&copy; {{ $web_config['copyright_text']->value }}</a>
                    </footer>
                </div>
            </div>
            <div class="dotted-border"></div>
            <div class="pass-right">
                <div class="qr-code">
                    {!! $imageData !!}
                </div>
                <p class="ticket-info"><i class="fas fa-calendar-alt"></i><span style="font-size: 12px;">{{ $event_date}}</span></p>
            </div>
        </div>


    </div>



    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

</body>

<script>
    function read() {
        html2canvas(document.querySelector('.pass'), {
            useCORS: true,
            backgroundColor: null
        }).then(canvas => {
            const link = document.createElement('a');
            link.href = canvas.toDataURL('image/png');
            link.download = 'member_{{$num??""}}.png';
            link.click();
            window.location.href = "{{ route('event-order-details',[$id])}}"
        });
    }
    // function read() {
    //     html2canvas(document.querySelector('.pass'), {
    //         useCORS: true,
    //         backgroundColor: null
    //     }).then(canvas => {
    //         canvas.toBlob(blob => {
    //             let formData = new FormData();
    //             formData.append('image', blob, 'member_image.png');
    //             fetch("{{ url('api/v1/event/pass-downloads') }}", {
    //                     method: "POST",
    //                     body: formData,
    //                     headers: {
    //                         'X-CSRF-TOKEN': "{{ csrf_token() }}"
    //                     }
    //                 })
    //                 .then(response => response.json())
    //                 .then(data => {
    //                     console.log("Image uploaded successfully", data);
    //                     window.location.href="{{ route('event-order-details',[$id])}}"
    //                 })
    //                 .catch(error => console.error("Error uploading image:", error));
    //         }, 'image/png');
    //     });
    // }

    read();
</script>

</html>