<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TaskCRUDTest extends TestCase
{
    protected $user;
    protected $token;
    protected $task;
    protected $taskHeaders = [];
    protected $taskData = [];

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate');
        Artisan::call('db:seed', ['--class' => 'RolePermissionSeeder']);
    
        $this->user = User::factory()->create([
            'name' => 'John Doe',
        ]);
        $this->actingAs($this->user, 'api');

        $this->token = $this->user->createToken('Test Token')->plainTextToken;

        $this->taskHeaders = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ];
    }

    public function testUserCanCreateTask(): void
    {
        $this->taskHeaders = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ];

        $this->taskData = [
            "title" => "Create one test task",
            "description" => "task has to be created by api",
            "status" => "todo",
            "project" => "test project",
            "deadline" => "2025-04-30"
        ];

        $this->json('POST', 'api/tasks', $this->taskData, $this->taskHeaders)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'message' => __('app.task_added')
            ]);
              
        $this->assertDatabaseHas('tasks', [
            'title' => 'Create one test task',
            'status' => 'todo',
            'project_id' => Project::where('name', 'test project')->first()->id,
            'deadline' => '2025-04-30 00:00:00',
        ]);
    }

    public function testUserCanEditOwnTask(): void
    {
        $task = Task::factory()->for($this->user)->create();

        $this->taskHeaders = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ];

        $this->taskData = [
            "title" => $task->title,
            "description" => $task->description,
            "status" => "done",
            "user_id" => $task->user_id,
            "project" => "Update project",
            "deadline" => "2025-05-24"
        ];

        $this->json('PUT', 'api/tasks/' . $task->id, $this->taskData, $this->taskHeaders)
        ->assertStatus(Response::HTTP_OK)
        ->assertJson([
            'message' => __('app.task_updated')
        ]);
            
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'done',
            'project_id' => Project::where('name', 'Update project')->first()->id,
            'deadline' => '2025-05-24 00:00:00',
        ]);
    }

    public function testUserCannotEditTaskFromAnotherUser(): void
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->for($otherUser)->create();

        $this->taskData = [
            "title" => $task->title,
            "description" => $task->description,
            "status" => "done",
            "user_id" => $task->user_id,
            "project" => "Update project",
            "deadline" => "2025-05-31"
        ];

        $this->json('PUT', 'api/tasks/' . $task->id, $this->taskData, $this->taskHeaders)
        ->assertStatus(Response::HTTP_UNAUTHORIZED)
        ->assertJson([
            'message' => __('app.unauthorized')
        ]);
    }

    public function testAdminCanEditTaskFromAnotherUser(): void
    {
        $this->user->assignRole('admin');
        $otherUser = User::factory()->create();
        $task = Task::factory()->for($otherUser)->create(); 

        $this->taskData = [
            "title" => $task->title,
            "description" => $task->description,
            "status" => "done",
            "user_id" => $task->user_id,
            "project" => "Update project",
            "deadline" => "2025-05-31"
        ];

        $this->json('PUT', 'api/tasks/' . $task->id, $this->taskData, $this->taskHeaders)
        ->assertStatus(Response::HTTP_OK)
        ->assertJson([
            'message' => __('app.task_updated')
        ]);
            
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'done',
            'project_id' => Project::where('name', 'Update project')->first()->id,
            'deadline' => '2025-05-31 00:00:00',
        ]);

    }

    public function testUserCanDeleteTask(): void
    {
        $task = Task::factory()->for($this->user)->create();

        $this->taskHeaders = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ];

        $this->taskData = [];

        $this->json('DELETE', 'api/tasks/' . $task->id, $this->taskData, $this->taskHeaders)
        ->assertStatus(Response::HTTP_OK)
        ->assertJson([
            'message' => __('app.task_deleted')
        ]);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    public function testUserCannotDeleteTaskFromAnotherUser(): void
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->for($otherUser)->create();

        $this->taskData = [];

        $this->json('DELETE', 'api/tasks/' . $task->id, $this->taskData, $this->taskHeaders)
        ->assertStatus(Response::HTTP_UNAUTHORIZED)
        ->assertJson([
            'message' => __('app.unauthorized')
        ]);
    }

    public function testTaskOverdue(): void
    {
        $task = Task::factory()->for($this->user)->create([
            'deadline' => now()->subDay()
        ]);

        $this->taskHeaders = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token
        ];

        $this->json('GET', 'api/tasks.overdue', [], $this->taskHeaders)
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'description',
                    'status',
                    'user_id',
                    'created_at',
                    'updated_at',
                    'deadline',
                    'project_id',
                ]
            ]
        ])
        ->json();
    }
}
