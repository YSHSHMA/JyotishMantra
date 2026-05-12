<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TeamController extends Controller
{
    public static function index(){
        return view('team.index');
    }

    public static function kabir(){
        return view('team.kabir');
    }

    public static function varshaa(){
        return view('team.varshaa');
    }

    public static function himanshu(){
        return view('team.himanshu');
    }

    public static function rahul(){
        return view('team.rahul');
    }

    public static function rishi(){
        return view('team.rishi');
    }
}
