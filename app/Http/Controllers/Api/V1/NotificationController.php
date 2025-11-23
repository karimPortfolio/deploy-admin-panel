<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        private \App\Services\NotificationService $notificationService
    ) {}

    
    public function index(Request $request)
    {
        $notifications = $this->notificationService->listNotifications($request);

        return NotificationResource::collection($notifications);
    }

    public function unreceivedNotifications()
    {
        $notifications = $this->notificationService->getUnreceivedNotificationsCount();

        return response()->json([
            'data' => [
                'unreceived_notifications_count' => $notifications
            ]
        ]);
    }

    public function markAsRead(string $id)
    {
        $this->notificationService->markAsRead($id);

        return response()->noContent();
    }

    public function markAllAsRead()
    {
        $this->notificationService->markAllAsRead();

        return response()->noContent();
    }


    public function destroy(string $id)
    {
        $this->notificationService->deleteNotification($id);

        return response()->noContent();
    }
}
