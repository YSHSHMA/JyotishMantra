'use strict';

$('.form-submit').on('click',function (){
    let getText = $('#get-confirm-and-cancel-button-text');
    const getFormId =  $(this).data('form-id');
    Swal.fire({
        title: getText.data('sure'),
        text:  getText.data('message'),
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: getText.data('cancel'),
        confirmButtonText: getText.data('confirm'),
        reverseButtons: true
    }).then((result) => {
        if (result.value) {

            let formData = new FormData(document.getElementById(getFormId));
            $.ajaxSetup({
                headers: {
                    'X-XSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: $('#'+getFormId).attr('action'),
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').fadeIn();
                },
                success: function (data) {
                    if (data.errors) {
                        for (let index = 0; index < data.errors.length; index++) {
                            toastr.error(data.errors[index].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else if(data.error){
                        toastr.error(data.error, {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }else {
                        $('.registration-success-modal').modal('show');
                        setTimeout(function () {
                            location.href = data.redirectRoute;
                        }, 4000);
                    }
                },complete: function () {
                    $('#loading').fadeOut();
                },
            })
        }
    })
});
