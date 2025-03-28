<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    
    use RefreshDatabase;
    
    public function testTaskListNeedsAuthentication(): void
    {
        $this->json('GET', 'api/tasks')
            ->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'message' => __('app.unauthenticated')
            ]);
    }

    public function testUserCanSeeTaskList(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $taskHeaders = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $user->createToken('test_token')->plainTextToken
        ];

        $this->json('GET', 'api/tasks', [], $taskHeaders)
            ->assertStatus(Response::HTTP_OK);
    }
}
