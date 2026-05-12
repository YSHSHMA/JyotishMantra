"use strict";

// INITIALIZATION OF CHARTJS
// =======================================================
Chart.plugins.unregister(ChartDataLabels);

$('.js-chart').each(function () {
    $.HSCore.components.HSChartJS.init($(this));
});

var updatingChart = $.HSCore.components.HSChartJS.init($('#updatingData'));

$(".earning-statistics").on("click", function () {
    earningStatisticsUpdate(this);
});

function earningStatisticsUpdate(t) {
    let value = $(t).attr('data-earn-type');
    let url = $('#earning-statistics-url').data('url');

    $.ajax({
        url: url,
        type: 'GET',
        data: {
            type: value
        },
        beforeSend: function () {
            $('#loading').fadeIn();
        },
        success: function (response_data) {
            document.getElementById("updatingData").remove();
            let graph = document.createElement('canvas');
            graph.setAttribute("id", "updatingData");
            document.getElementById("set-new-graph").appendChild(graph);

            var ctx = document.getElementById("updatingData").getContext("2d");
            var options = {
                responsive: true,
                bezierCurve: false,
                maintainAspectRatio: false,
                scales: {
                    xAxes: [{
                        gridLines: {
                            color: "rgba(180, 208, 224, 0.5)",
                            zeroLineColor: "rgba(180, 208, 224, 0.5)",
                        }
                    }],
                    yAxes: [{
                        gridLines: {
                            color: "rgba(180, 208, 224, 0.5)",
                            zeroLineColor: "rgba(180, 208, 224, 0.5)",
                            borderDash: [8, 4],
                        }
                    }]
                },
                legend: {
                    display: true,
                    position: "top",
                    labels: {
                        usePointStyle: true,
                        boxWidth: 6,
                        fontColor: "#758590",
                        fontSize: 14
                    }
                },
                plugins: {
                    datalabels: {
                        display: false
                    }
                },
            };
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [
                        {
                            label: $('#seller-text').data('text'),
                            data: [],
                            backgroundColor: "#0177CD",
                            borderColor: "#0177CD",
                            fill: false,
                            lineTension: 0.3,
                            radius: 0
                        },
                        {
                            label: $('#in-house-text').data('text'),
                            data: [],
                            backgroundColor: "#FFB36D",
                            borderColor: "#FFB36D",
                            fill: false,
                            lineTension: 0.3,
                            radius: 0
                        }
                    ]
                },
                options: options
            });

            myChart.data.labels = response_data.label;
            myChart.data.datasets[0].data = response_data.vendorEarningArray;
            myChart.data.datasets[1].data = response_data.commissionGivenToAdminArray;

            myChart.update();
        },
        complete: function () {
            $('#loading').fadeOut();
        }
    });
}

$(document).ready(function () {
    let method_id = $('#withdraw_method').val();

    if (method_id) {
        withdraw_method_field(method_id);
    }

    $("#statistics_type").on("change", function () {

        let type = $(this).val();
        let url = $('#order-status-url').data('url');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        console.log(url.replace(':type', type))
        $.get({
            url: url.replace(':type', type),
            success: function (data) {
                $('#order_stats').html(data.view)
            },
        });
    });
});

$('#withdraw_method').on('change', function () {
    withdraw_method_field(this.value);
});
function withdraw_method_field(method_id){
    let url = $('#withdraw-method-url').data('url');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: url + "?method_id=" + method_id,
        data: {},
        processData: false,
        contentType: false,
        type: 'get',
        success: function (response) {
            let method_fields = response.content.method_fields;
            $("#method-filed__div").html("");
            method_fields.forEach((element, index) => {
                $("#method-filed__div").append(`
                    <div class="mt-3">
                        <label for="wr_num" class="fz-16 c1 mb-2" style="color: #5b6777 !important;">${element.input_name.replaceAll('_', ' ')}</label>
                        <input type="${element.input_type}" class="form-control" name="${element.input_name}" placeholder="${element.placeholder}" ${element.is_required === 1 ? 'required' : ''}>
                    </div>
                `);
            })
        },
        error: function () {

        }
    });
}

try{
    var ctx = document.getElementById('business-overview');
    var myChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: [
                '$("#customer-text").data("text") ',
                '$("#store-text").data("text") ',
                '$("#product-text").data("text") ',
                '$("#order-text").data("text") ',
                '$("#brand-text").data("text") ',
            ],
            datasets: [{
                label: '$("#business-text").data("text")',
                data: ['$("#customers-text").data("text")','$("#products-text").data("text")', '$("#orders-text").data("text")', '$("#brands-text").data("text")'],
                backgroundColor: [
                    '#041562',
                    '#DA1212',
                    '#EEEEEE',
                    '#11468F',
                    '#000000',
                ],
                hoverOffset: 4
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}catch (e) {
}

$(function () {

    //get the doughnut chart canvas
    var ctx1 = $("#user_overview");

    //doughnut chart data
    var data1 = {
        labels: ["Customer", "Vendor", "Delivery Man"],
        datasets: [
            {
                label: "User Overview",
                data: [88297, 34546, 15000],
                backgroundColor: [
                    "#017EFA",
                    "#51CBFF",
                    "#56E7E7",
                ],
                borderColor: [
                    "#017EFA",
                    "#51CBFF",
                    "#56E7E7",
                ],
                borderWidth: [1, 1, 1]
            }
        ]
    };

    //options
    var options = {
        responsive: true,
        legend: {
            display: true,
            position: "bottom",
            align: "start",
            maxWidth: 100,
            labels: {
                usePointStyle: true,
                boxWidth: 6,
                fontColor: "#758590",
                fontSize: 14
            }
        },
        plugins: {
            datalabels: {
                display: false
            }
        },
    };

    //create Chart class object
    var chart1 = new Chart(ctx1, {
        type: "doughnut",
        data: data1,
        options: options
    });
});

// function call_duty() {
//     toastr.warning('{{translate('update_your_bank_info_first')}}!', '{{translate('warning')}}!', {
//         CloseButton: true,
//         ProgressBar: true
//     });
// }
