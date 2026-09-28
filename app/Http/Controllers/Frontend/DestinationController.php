<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Destination;

class DestinationController extends Controller
{
    public function index()
    {
        $destinations = Destination::withCount('tours', 'tickets')->get();

        return view('frontend.destinations.index', compact('destinations'));
    }
}
