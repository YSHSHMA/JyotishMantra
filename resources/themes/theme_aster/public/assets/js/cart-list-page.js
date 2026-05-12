"use strict";

function proceedToNextAction(){
    $('#proceed-to-next-action').on('click', function () {
        let orderNote = $('#order_note').val();
        $.post({
            url: $('#order_note_url').data('url'),
            data: {
                _token: $('meta[name="_token"]').attr('content'),
                order_note: orderNote,

            },
            beforeSend: function () {
                $('#loading').addClass('d-grid');
            },
            success: function (response) {
                if(response.status === 0) {
                    response.message.map(function (message) {
                        toastr.error(message)
                    })
                }else{
                    location.href = response.redirect ? response.redirect : $('#checkout_details_url').data('url');
                }
            },
            complete: function () {
                $('#loading').removeClass('d-grid');
            },
        });
    });
}
proceedToNextAction();
