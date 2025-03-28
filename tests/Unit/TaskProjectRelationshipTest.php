<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskProjectRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function testProjectHasTasks(): void
    {
        $project = Project::factory()->create();
        $tasks = Task::factory()->count(3)->create([
            'project_id' => $project->id
        ]);

        $this->assertEquals(3, $project->tasks()->count());
        $this->assertEquals($tasks->pluck('id')->toArray(), $project->tasks->pluck('id')->toArray());
    }

    public function testTaskBelongsToProject(): void
    {
        $project = Project::factory()->create();
        $task = Task::factory()->create([
            'project_id' => $project->id
        ]);

        $this->assertEquals($project->id, $task->project->id);
    }
}
