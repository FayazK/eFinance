<?php

declare(strict_types=1);

use App\Models\Client;
use App\Models\Project;
use App\Models\User;

describe('Project show page props', function () {
    beforeEach(function () {
        $this->withoutVite();
        seedMinimalWorld();
    });

    // Regression for #87: ProjectController@show passed a bare ProjectResource (Inertia wraps as
    // { data: {...} }). After ->resolve() the show page receives the prop flat.
    test('show page exposes the project prop unwrapped', function () {
        $this->actingAs(User::factory()->superAdmin()->create());

        $client = Client::factory()->create();
        $project = Project::factory()->create([
            'client_id' => $client->id,
            'name' => 'Acme Redesign',
        ]);

        $this->get(route('projects.show', $project))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('dashboard/projects/show')
                // Top-level prop is flat (would be under `project.data` if wrapped).
                ->where('project.name', 'Acme Redesign')
            );
    });
});
