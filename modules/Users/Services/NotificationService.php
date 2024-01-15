<?php

namespace Modules\Users\Services;

use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class NotificationService extends BaseService
{
    public function getNotifications(array $params)
    {
        return auth()->user()->notifications()->paginate(data_get($params, 'limit', config('repository.pagination.limit')));
    }

    public function getNotificationUnreadCount()
    {
        return auth()->user()->unreadNotifications()->count();
    }

    public function markAsReadNotification($id)
    {
        try {
            DB::beginTransaction();

            $data = auth()->user()->notifications()->findOrFail($id);
            $data->markAsRead();

            DB::commit();

            return $data;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function markAllAsReadNotification()
    {
        try {
            DB::beginTransaction();

            auth()->user()->unreadNotifications()->each(function ($data) {
                $data->markAsRead();
            });

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }
}
