<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TaskService
{
    public function getTask(int $id): Task
    {
        return Task::find($id);
    }
    
    public function getPaginatedTasksFromUser(User $user): LengthAwarePaginator
    {
        return $user->tasks()->paginate(30);
    }

    public function getTaskFromUser(User $user, int $taskId): Task
    {
        return $user->tasks()->findOrFail($taskId);
    }

    public function store(array $taskData, User $user): Task
    {
        $taskData['user_id'] = $user->id;
        $taskData['project_id'] = !empty($taskData['project']) ? $this->getProjectId($taskData['project']) : null;
        return Task::create($taskData);
    }

    public function update(array $taskData, Task $task): Task
    {
        $task->title = $taskData['title'] ?? $task->title;
        $task->description = $taskData['description'] ?? $task->description;
        $task->status = $taskData['status'] ?? $task->status;
        $task->project_id = !empty($taskData['project']) ? $this->getProjectId($taskData['project']) : $task->project_id;
        $task->deadline = $taskData['deadline'] ?? $task->deadline;

        $task->save();

        return $task;
    }

    public function destroy(User $user, int $taskId): void
    {
        $task = $user->tasks()->findOrFail($taskId);

        $task->delete();
    }

    public function getOverdueTasks(User $user): LengthAwarePaginator
    {
        if ($user->hasRole('admin')) {
            $tasks = Task::where('deadline', '<', now())->paginate(30);
        } else {
            $tasks = Task::where('user_id', $user->id)->where('deadline', '<', now())->paginate(30);
        }
        return $tasks;
    }

    private function getProjectId(string $projectName): int
    {
        $project = Project::firstOrCreate(['name' => $projectName]);
        return $project->id;
    }
}
