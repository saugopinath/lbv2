<?php

namespace App\Http\Controllers\Home;

use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function index()
    {
        return view('home.notification');
    }

    public function datatable(Request $request)
    {
        $query = Notification::query();

        // Filter by type
        if ($request->type && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Search
        if ($request->search['value']) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                    ->orWhere('message', 'ilike', "%{$search}%")
                    ->orWhere('scheme_name', 'ilike', "%{$search}%");
            });
        }

        $total = $query->count();

        $data = $query
            ->orderBy('notified_at', 'desc')
            ->offset($request->start)
            ->limit($request->length)
            ->get();

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data
        ]);

    }
}
