<?php

namespace App\Http\Controllers\Home;

use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function index()
    {
        return view('frontend.home.notification');
    }
}
