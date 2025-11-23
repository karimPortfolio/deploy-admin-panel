<?php

namespace App\Services;

use Illuminate\Http\Request;

class NotificationService
{
    public function listNotifications(Request $request)
    {
        $notifications = auth()->user()
            ->notifications()
            ->paginate($request->input('per_page') ?: 10);

        $this->markNotificationsAsReceived();

        return $notifications;
    }

    public function getUnreceivedNotificationsCount()
    {
        $notifications = auth()->user()
            ->notifications()
            ->whereNull('received_at')
            ->count();

        return $notifications;
    }

    public function markAsRead(string $id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);

        $notification->update([
            'read_at' => now()
        ]);
    }

    public function markAllAsRead()
    {
        auth()->user()->notifications()->update([ 'read_at' => now() ]);
    }

    public function deleteNotification(string $id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->delete();
    }

    protected function markNotificationsAsReceived()
    {
        auth()->user()
            ->notifications()
            ->whereNull('received_at')
            ->update([
                'received_at' => now()
            ]);
    }
}

