<?php

namespace Tests\Unit;

use App\Models\Project;
use Tests\TestCase;

class ProjectModelTest extends TestCase
{
    public function testAttributesAreSetCorrect(): void
    {
        $project = new Project([
            'name' => 'Test Project'
        ]);
        $this->assertEquals('Test Project', $project->name);
    }

    public function testNonFillableAttributesAreNotSet(): void
    {
        $project = new Project([
            'name' => 'Test Project',
            'id' => 1
        ]);
        $this->assertArrayNotHasKey('id', $project);
    }
}
