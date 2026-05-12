<?php

namespace App\Enums\ViewPaths\Admin;

enum TourBookingPath
{
    const ALL = [
        URI => "all-list",
        VIEW=>"admin-views.tour_and_travels.booking.all-list",
    ];
    const PENDING =[
        URI =>"pending-booking",
        VIEW=>"admin-views.tour_and_travels.booking.pending",
    ];
    
    const CONFIRM= [
        URI =>"confirm-booking",
        VIEW=>"admin-views.tour_and_travels.booking.confirm",
    ];
    const COMPLETED =[
        URI =>"complete-booking",
        VIEW=>"admin-views.tour_and_travels.booking.completed",
    ];
    const CANCEL =[
        URI =>"cancel-booking",
        VIEW=>"admin-views.tour_and_travels.booking.cancel",
    ];
    
    const DETAILS =[
        URI =>"user-booking-details",
        VIEW=>"admin-views.tour_and_travels.booking.details",
    ];
    const ASSIGNEDCAB =[
        URI =>"assigned-cab",
    ];
    const UPDATE_DATE =[
        URI =>"update-booking-date",
    ];

    const REFUND =[
        URI =>"refund",
    ];

    const TICKET =[
        URI =>"all-ticket",
    ];

}
?>