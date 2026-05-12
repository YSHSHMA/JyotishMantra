<div id="maleHoroscopeCharts" class="text-center"></div>

<!-- Bootstrap Bundle (only one version) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>

<!-- D3 and Custom Scripts -->
<script type="text/javascript" src="{{ asset('public/assets/front-end') }}/js/d3.min.js"></script>
<script type="text/javascript" src="{{ asset('public/assets/front-end') }}/js/kundaliChart.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    var type = '{{$type}}';
    var male_data = {
        day: {{$male_data['male_day']}},
        month: {{$male_data['male_month']}},
        year: {{$male_data['male_year']}},
        hour: {{$male_data['male_hour']}},
        min: {{$male_data['male_min']}},
        lat: {{$male_data['male_lat']}},
        lon: {{$male_data['male_lon']}},
        tzone: {{$male_data['male_tzone']}},
    };
    var female_data = {
        day: {{$female_data['female_day']}},
        month: {{$female_data['female_month']}},
        year: {{$female_data['female_year']}},
        hour: {{$female_data['female_hour']}},
        min: {{$female_data['female_min']}},
        lat: {{$female_data['female_lat']}},
        lon: {{$female_data['female_lon']}},
        tzone: {{$female_data['female_tzone']}},
    };

    // Call functions to process data
    sendMaleData(male_data);
    sendFemaleData(female_data);

    var options = {
        lineColor: '#FC8100',
        planetColor: '#555',
        signColor: '#555',
        isForMatching: false,
        width: $('#maleHoroscopeCharts').width()
    };

    // Generate charts
    getMaleCharts(options, type, 'maleHoroscopeCharts', true);
    //console.log(north);

    // Adjust chart container size
    var $maleHoroscopeCharts = $('#maleHoroscopeCharts');
    var $svg = $maleHoroscopeCharts.find('svg');

    if ($svg.length) {
        var chartWidth = parseInt($svg.attr('width'), 10);
        var chartHeight = parseInt($svg.attr('height'), 10);
        $maleHoroscopeCharts.css({
            'width': chartWidth + 'px',
            'height': chartHeight + 'px'
        });
    }
});
</script>
