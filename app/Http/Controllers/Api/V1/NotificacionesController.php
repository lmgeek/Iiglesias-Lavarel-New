<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\NotificationLog;
use Illuminate\Http\Request;

class NotificacionesController extends Controller
{
    public function index(Request $request)
    {
        $query = NotificationLog::with(['event:id,title,start_date,start_time,color'])
            ->where('sent_at', '>=', now()->subDays(30));

        if ($request->has('limit')) {
            $query->limit($request->integer('limit', 0));
        }

        $notifications = $query->orderBy('sent_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json($notifications);
    }
}
