<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'unread_count'  => $user->unreadNotifications()->count(),
            'notifications' => $user->notifications()
                ->latest()
                ->limit(20)
                ->get()
                ->map(fn ($n) => [
                    'id'      => $n->id,
                    'title'   => $n->data['title'] ?? '',
                    'body'    => $n->data['body'] ?? '',
                    'url'     => $n->data['url'] ?? null,
                    'read'    => ! is_null($n->read_at),
                    'created' => $n->created_at->diffForHumans(),
                ]),
        ]);
    }

    public function markRead(Request $request, string $id)
    {
        $notif = DatabaseNotification::findOrFail($id);

        abort_if($notif->notifiable_id !== $request->user()->id, 403);

        $notif->markAsRead();

        return response()->json(['ok' => true]);
    }

    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['ok' => true]);
    }
}