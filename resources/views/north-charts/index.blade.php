<div id="northChart" class="text-center"></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script type="text/javascript" src="{{ asset('public/assets/front-end') }}/js/d3.min.js"></script>
<script type="text/javascript" src="{{ asset('public/assets/front-end') }}/js/kundaliChart.js"></script>
<script type="text/javascript">
var type = '{{$type}}';
var data = {
  day: {{$data['day']}},
  month: {{$data['month']}},
  year: {{$data['year']}},
  hour: {{$data['hour']}},
  min: {{$data['min']}},
  lat: {{$data['lat']}},
  lon: {{$data['lon']}},
  tzone: {{$data['tzone']}},
};
// console.log(data);
sendData(data);
var options = {
  lineColor: 'orange',
  planetColor: 'green',
  signColor: 'blue',
  width: $('#northChart').width()
};

let north = getNorthHoroCharts(options, type);
getSouthHoroCharts(options, type);
console.log(north);

// $(document).ready(function(){
//     var northChart = document.getElementById('northChart');
//     var chartWidth = $('#northChart svg').attr('width');
//     var chartHeight = $('#northChart svg').attr('height');
//     chartWidth = parseInt(chartWidth, 10);
//     chartHeight = parseInt(chartHeight, 10);
//     $('body').css('width', chartWidth + 'px');
//     $('body').css('height', chartHeight + 'px');
// });
</script> 