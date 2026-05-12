@php 
    $startDate = strtotime($chadhava->start_date);
    $endDate = strtotime($chadhava->end_date);
    $dateList = [];
    while ($startDate <= $endDate) {
        $dateList[] = date('Y-m-d', $startDate); 
        $startDate = strtotime('+1 day', $startDate);
        }
@endphp
@foreach($dateList as $key => $date)
    <tr class="text-center">
        <td>{{ date('d M Y', strtotime($date)) }}</td>
    </tr>
@endforeach