"use strict";
function orderStatistics(){
    $('.order-statistics').on('click', function () {
        let value = $(this).attr('data-date-type');
        let url = $('#order-statistics').data('action');
        $.ajax({
            url: url,
            type: 'GET',
            data: {
                type: value
            },
            beforeSend: function () {
                $('#loading').fadeIn();
            },
            success: function (data) {
                console.log(data.view);
                $('#order-statistics-div').empty().html(data.view);
                orderStatisticsApexChart();
                orderStatistics();
            },
            complete: function () {
                $('#loading').fadeOut();
            }
        });
    });
}
orderStatistics();

function orderStatisticsApexChart(){
    let orderStatisticsData = $('#order-statistics-data');
    const inHouseOrderEarn = orderStatisticsData.data('inhouse-order-earn');
    const vendorOrderEarn = orderStatisticsData.data('vendor-order-earn');
    const label = orderStatisticsData.data('label');
    var options = {
        series: [
            {
                name: orderStatisticsData.data('inhouse-text'),
                data: Object.values(inHouseOrderEarn)
            },
            {
                name: orderStatisticsData.data('vendor-text'),
                data: Object.values(vendorOrderEarn)
            }
        ],
        chart: {
            height: 386,
            type: 'line',
            dropShadow: {
                enabled: true,
                color: '#000',
                top: 18,
                left: 7,
                blur: 10,
                opacity: 0.2
            },
            toolbar: {
                show: false
            }
        },
        yaxis: {
            labels: {
                offsetX: 0,
                formatter: function(value) {
                    return  "$"+value
                }
            },
        },
        colors: ['#4FA7FF', '#82C662'],
        dataLabels: {
            enabled: false,
        },
        stroke: {
            curve: 'smooth',
        },
        grid: {
            xaxis: {
                lines: {
                    show: true
                }
            },
            yaxis: {
                lines: {
                    show: true
                }
            },
            borderColor: '#CAD2FF',
            strokeDashArray: 5,
        },
        markers: {
            size: 1
        },
        theme: {
            mode: 'light',
        },
        xaxis: {
            categories: Object.values(label)
        },
        legend: {
            position: 'top',
            horizontalAlign: 'center',
            floating: false,
            offsetY: -10,
            offsetX: 0,
            itemMargin: {
                horizontal: 10,
                vertical: 10
            },
        },
        padding: {
            top: 0,
            right: 0,
            bottom: 200,
            left: 10
        },
    };
    var chart = new ApexCharts(document.querySelector("#apex-line-chart"), options);
    chart.render();
}
orderStatisticsApexChart();
function UserOverViewChart(){
    const userOverViewData = $('#user-overview-data');
    var options = {
        series: [userOverViewData.data('customer'), userOverViewData.data('vendor'), userOverViewData.data('delivery-man')],
        labels: [userOverViewData.data('customer-title'), userOverViewData.data('vendor-title'), userOverViewData.data('delivery-man-title')],
        chart: {
            width: 320,
            type: 'donut',
        },
        dataLabels: {
            enabled: false
        },
        colors: ['#017EFA', '#51CBFF',"#56E7E7"],
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
            }
        }],
        legend: {
            show: false
        }
    };
    var chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render();
}
UserOverViewChart();
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
                            label: $('#in-house-text').data('text'),
                            data: [],
                            backgroundColor: "#ACDBAB",
                            hoverBackgroundColor: "#ACDBAB",
                            borderColor: "#ACDBAB",
                            fill: false,
                            lineTension: 0.3,
                            radius: 0
                        },
                        {
                            label: $('#seller-text').data('text'),
                            data: [],
                            backgroundColor: "#0177CD",
                            hoverBackgroundColor: "#0177CD",
                            borderColor: "#0177CD",
                            fill: false,
                            lineTension: 0.3,
                            radius: 0
                        },
                        {
                            label: $('#message-commission-text').data('text'),
                            data: [],
                            backgroundColor: "#FFB36D",
                            hoverBackgroundColor: "FFB36D",
                            borderColor: "#FFB36D",
                            fill: false,
                            lineTension: 0.3,
                            radius: 0
                        }
                    ]
                },
                options: options
            });

            myChart.data.labels = response_data.inhouse_label;
            myChart.data.datasets[0].data = response_data.inhouse_earn;
            myChart.data.datasets[1].data = response_data.seller_earn;
            myChart.data.datasets[2].data = response_data.commission_earn;

            myChart.update();
        },
        complete: function () {
            $('#loading').fadeOut();
        }
    });
}


$("#statistics_type").on("change", function () {
    let type = $(this).val();
    let url = $('#order-status-url').data('url');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.post({
        url: url,
        data: {
            statistics_type: type
        },
        beforeSend: function () {
            $('#loading').fadeIn();
        },
        success: function (data) {
            $('#order_stats').html(data.view)
        },
        complete: function () {
            $('#loading').fadeOut();
        }
    });
});

$('#withdraw_method').on('change', function () {
    withdraw_method_field(this.value);
});

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
        labels: ["Customer", "Seller", "Delivery Man"],
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

$(function () {
    //get the line chart canvas
    var ctx = $("#order_statictics");

    //line chart data
    var data = {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        datasets: [
            {
                label: "In-house",
                data: [10000, 50000, 100000, 140000, 40000, 10000, 50000, 100000, 130000, 40000, 80000, 120000],
                backgroundColor: "#FFB36D",
                borderColor: "#FFB36D",
                fill: false,
                lineTension: 0.3,
                radius: 2
            },
            {
                label: "Seller",
                data: [9000, 60000, 110000, 130000, 50000, 29000, 60000, 110000, 100000, 50000, 70000, 90000],
                backgroundColor: "#0177CD",
                borderColor: "#0177CD",
                fill: false,
                lineTension: 0.3,
                radius: 2
            }
        ]
    };

    //options
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
        }
    };

    //create Chart class object
    var chart = new Chart(ctx, {
        type: "line",
        data: data,
        options: options
    });
});
