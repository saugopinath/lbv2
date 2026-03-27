<?php

namespace App\Http\Controllers\Home;

use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function index()
    {
        $latest = Notification::where('status', 'active')->orderBy('notified_at', 'desc')->first();
        return view('frontend.home.notification', [
            'latest' => $latest
        ]);
    }
}
