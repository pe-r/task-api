<?php

namespace Tests\Unit;

use App\Events\TaskUpdatedEvent;
use App\Listeners\TaskUpdatedListener;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskOverdue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendOverdueTaskNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function testItSendsNotifications(): void
    {

        Notification::fake();

        $user = User::factory()->create([
            'name' => 'John Doe',
        ]);
 
        $task = Task::factory()->for($user)->create([
            'deadline' => now()->subDay()
        ]);
        $event = new TaskUpdatedEvent($task);
        
        $listener = new TaskUpdatedListener();
        $listener->handle($event);

        Notification::assertSentTo(
            $user,
            TaskOverdue::class
        );
    }

    public function testIsAttachedToEvent(): void
    {
        Event::fake();
        Event::assertListening(
            TaskUpdatedEvent::class,
            TaskUpdatedListener::class
        );
    }
}
