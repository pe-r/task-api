<?php

namespace Tests\Unit;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectCreationTest extends TestCase
{
    use RefreshDatabase;
    
    public function testProjectCreation(): void
    {
        $project = Project::create([
            'name' => 'Test Project'
        ]);
        $createdProject = Project::find($project->id);
        $this->assertNotNull($createdProject);
        $this->assertEquals('Test Project', $createdProject->name);
    }
}
