<?php

namespace App\Http\Controllers\RestAPI\v4;

use App\Http\Controllers\Controller;
use App\Models\Attribute;

class AttributeController extends Controller
{
    public function get_attributes()
    {
        $attributes = Attribute::all();
        return response()->json($attributes,200);
    }
}
