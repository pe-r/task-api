<?php

namespace App\Listeners;

use App\Events\TaskUpdatedEvent;
use App\Notifications\TaskOverdue;

class TaskUpdatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TaskUpdatedEvent $event): void
    {
        if ($event->task->deadline <= now()) {
            $event->user->notify(new TaskOverdue($event->task));
        }
    }
}
