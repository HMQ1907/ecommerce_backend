<?php

namespace Modules\Users\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Users\Services\NotificationService;
use Modules\Users\Transformers\NotificationTransformer;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function getNotifications(Request $request)
    {
        $data = $this->notificationService->getNotifications($request->all());
        $unreadCount = $this->notificationService->getNotificationUnreadCount();

        return responder()->success($data, NotificationTransformer::class)->meta([
            'unread_count' => $unreadCount,
        ])->respond();
    }

    public function markAsRead($id)
    {
        $data = $this->notificationService->markAsReadNotification($id);

        return responder()->success($data, NotificationTransformer::class)->respond();
    }

    public function markAllAsRead()
    {
        $this->notificationService->markAllAsReadNotification();

        return responder()->success()->respond();
    }
}
