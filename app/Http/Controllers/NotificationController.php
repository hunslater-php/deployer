<?php namespace App\Http\Controllers;

use Queue;
use App\Notification;
use App\Commands\Notify;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNotificationRequest;

/**
 * Controller for managing notifications
 */
class NotificationController extends Controller
{
    /**
     * Store a newly created notification in storage.
     *
     * @param StoreNotificationRequest $request
     * @return Response
     * @todo Shouldn't the queue::pushOn be some sort of hook on save of the model?
     */
    public function store(StoreNotificationRequest $request)
    {
        $notification = Notification::create($request->only(
            'name',
            'channel',
            'webhook',
            'project_id'
        ));

        Queue::pushOn('notify', new Notify(
            $notification,
            $notification->testPayload()
        ));

        return $notification;
    }

    /**
     * Update the specified notification in storage.
     *
     * @param Notification $notification
     * @param StoreNotificationRequest $request
     * @return Response
     * @todo Shouldn't the queue::pushOn be some sort of hook on save of the model?
     */
    public function update(Notification $notification, StoreNotificationRequest $request)
    {
        $notification->update($request->only(
            'name',
            'channel',
            'webhook',
            'project_id'
        ));

        Queue::pushOn('notify', new Notify(
            $notification,
            $notification->testPayload()
        ));

        return $notification;
    }

    /**
     * Remove the specified notification from storage.
     *
     * @param Notification $notification
     * @return Response
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();

        return [
            'success' => true
        ];
    }
}