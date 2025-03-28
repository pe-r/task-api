<?php

namespace App\Http\Controllers;

use App\Events\TaskUpdatedEvent;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\RequestValidators\CreateTaskRequest;
use App\RequestValidators\UpdateTaskRequest;
use App\Services\TaskService;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{

    public function __construct(private TaskService $taskService)
    {
    }

    public function index()
    {
        $tasks = Task::paginate(30);
        return response()->json($tasks);
    }

    public function show($taskId)
    {
        $task = $this->taskService->getTask($taskId);
        if (!$task) {
            return response()->json([
                'message' => __('app.task_not_found')
            ], Response::HTTP_NOT_FOUND);
        }
        return response()->json($task);
    }

    public function store(CreateTaskRequest $request)
    {

        $task = $this->taskService->store($request->validated(), auth('sanctum')->user());

        return response()->json([
            'message' => __('app.task_added')
        ], Response::HTTP_CREATED);    
    }

    public function update(UpdateTaskRequest $request, int $taskId)
    {
        $task = $this->taskService->getTask($taskId);
        if (!$task) {
            return response()->json([
                'message' => __('app.task_not_found')
            ], Response::HTTP_NOT_FOUND);
        }

        $task = $this->taskService->update($request->validated(), $task);

        TaskUpdatedEvent::dispatch($task);
        return response()->json([
            'message' => __('app.task_updated')
        ], Response::HTTP_OK);
    }

    public function destroy(int $taskId)
    {
        $task = $this->taskService->getTask($taskId);
        if (!$task) {
            return response()->json([
                'message' => __('app.task_not_found')
            ], Response::HTTP_NOT_FOUND);
        }
        $task->delete();
        return response()->json([
            'message' => __('app.task_deleted')
        ], Response::HTTP_OK);
    }

    public function listProjects()
    {
        $projects = Project::all();
        return response()->json($projects, Response::HTTP_OK);
    }

    public function showProjectTasks(int $projectId)
    {
        $tasks = Task::where('project_id', $projectId)->get();
        return response()->json($tasks);
    }

    public function showUserTasks(int $userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'message' => __('app.user_not_found')
            ], Response::HTTP_NOT_FOUND);
        }
        $tasks = $this->taskService->getPaginatedTasksFromUser($user);
        return response()->json($tasks, Response::HTTP_OK);
    }

    public function overdueTasks()
    {
        $user = auth('sanctum')->user();

        $tasks = $this->taskService->getOverdueTasks($user);
  
        return response()->json($tasks);
    }
}
