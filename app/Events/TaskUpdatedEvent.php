<?php

namespace App\Events;

use App\Models\Task;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskUpdatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $task;

    /**
     * Create a new event instance.
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
        $this->user = $task->user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
