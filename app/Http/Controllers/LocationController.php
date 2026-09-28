<?php

namespace App\Http\Controllers;

use App\Models\Province;

class LocationController extends Controller
{
    /**
     * Get wards for a specific province.
     */
    public function getWards(Province $province)
    {
        return response()->json($province->wards);
    }
}
