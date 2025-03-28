<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTaskRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function testUserHasTasks(): void
    {
        $user = User::factory()->create();
        $tasks = Task::factory()->count(3)->create([
            'user_id' => $user->id
        ]);

        $this->assertEquals(3, $user->tasks()->count());
        $this->assertEquals($tasks->pluck('id')->toArray(), $user->tasks->pluck('id')->toArray());
    }

    public function testTaskBelongsToUser(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id
        ]);

        $this->assertEquals($user->id, $task->user->id);
    }
}
