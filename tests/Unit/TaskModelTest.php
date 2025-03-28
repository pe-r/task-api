<?php

namespace Tests\Unit;

use App\Models\Task;
use Tests\TestCase;

class TaskModelTest extends TestCase
{
    public function testAttributesAreSetCorrect(): void
    {
        $task = new Task([
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'todo',
            'user_id' => 1,
            'project_id' => 1,
            'deadline' => '2025-01-31 00:00:00' 
        ]);
        $this->assertEquals('Test Task', $task->title);
        $this->assertEquals('Test Description', $task->description);
        $this->assertEquals('todo', $task->status);
        $this->assertEquals(1, $task->user_id);
        $this->assertEquals(1, $task->project_id);
        $this->assertEquals('2025-01-31 00:00:00', $task->deadline);
    }
}
