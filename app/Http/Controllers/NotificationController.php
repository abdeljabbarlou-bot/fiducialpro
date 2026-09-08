<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where(function ($q) {
            $q->whereNull('user_id')->orWhere('user_id', auth()->id());
        })
        ->orderByDesc('created_at')
        ->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification)
    {
        $notification->is_read = true;
        $notification->read_at = now();
        $notification->save();

        if ($notification->link) {
            return redirect($notification->link);
        }

        return back();
    }

    public function markAllAsRead()
    {
        Notification::where(function ($q) {
            $q->whereNull('user_id')->orWhere('user_id', auth()->id());
        })
        ->where('is_read', false)
        ->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }
}
