<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function index(Request $request)
    {
        $notifications = auth()->user()->notifications()->get();

        return NotificationResource::collection($notifications);
    }

    public function markAsRead(string $id)
    {
        $notification = auth()->user()->notifications()->find($id);

        $notification->update([
            'read_at' => now()
        ]);

        return response()->noContent();
    }

    public function markAllAsRead()
    {
        auth()->user()->notifications()->each(function ($notification) {
            $notification->update([
                'read_at' => now()
            ]);
        });

        return response()->noContent();
    }


    public function destroy(string $id)
    {
        $notification = auth()->user()->notifications()->find($id);
        $notification->delete();

        return response()->noContent();
    }
}
