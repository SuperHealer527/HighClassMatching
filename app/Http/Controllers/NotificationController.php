<?php

namespace App\Http\Controllers;

class NotificationController extends Controller
{
    public function index()
    {
        return view('notifications.index', [
            'notifications' => auth()->user()->notifications()->latest()->paginate(20),
        ]);
    }

    public function read(string $notification)
    {
        $item = auth()->user()->notifications()->findOrFail($notification);
        $item->markAsRead();

        return redirect($item->data['url'] ?? '/dashboard');
    }

    public function readAll()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('status', 'すべての通知を既読にしました。');
    }
}
