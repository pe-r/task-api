<?php

namespace Tests\Unit;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskCreationTest extends TestCase
{
    use RefreshDatabase;
    
    public function testTaskCreation(): void
    {
        $task = Task::create([
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'todo',
            'user_id' => 1,
            'project_id' => 1,
            'deadline' => '2025-01-31 00:00:00' 
        ]);
        $createdTask = Task::find($task->id);
        $this->assertNotNull($createdTask);
        $this->assertEquals('Test Task', $createdTask->title);
        $this->assertEquals('Test Description', $createdTask->description);
        $this->assertEquals('todo', $createdTask->status);
        $this->assertEquals(1, $createdTask->user_id);
        $this->assertEquals(1, $createdTask->project_id);
        $this->assertEquals('2025-01-31 00:00:00', $createdTask->deadline);
    }
}
