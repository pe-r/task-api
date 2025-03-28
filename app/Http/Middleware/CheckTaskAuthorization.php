<?php

namespace App\Http\Middleware;

use App\Models\Task;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTaskAuthorization
{
    /**
     * Check if user is authorized to access the task.
     * A user is allowed to access a task if he/she owns it or is an admin.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $task = Task::find($request->route('taskId'));

        if (!$task) {
            return response()->json(['message' => __('app.task_not_found')], Response::HTTP_NOT_FOUND);
        }

        if (!$this->userIsAuthorized($task, auth('sanctum')->user())) {
            return response()->json(['message' => __('app.unauthorized')], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }

    private function userIsAuthorized(Task $task, User $user): bool
    {
        return $task->user_id === $user->id || $user->hasRole('admin');
    }
}
