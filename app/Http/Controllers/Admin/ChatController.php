<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ChatController extends Controller
{
    public function index()
    {
        return view('admin.chat.index');
    }
}
