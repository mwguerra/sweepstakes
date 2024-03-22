<?php

use App\Models\Sweepstakes;
use App\Models\User;
use Illuminate\Http\UploadedFile;

it('displays the sweepstakes index page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('sweepstakes.index'));
    $response->assertInertia(fn ($page) => $page->component('Sweepstakes/Index')->has('sweepstakes'));
});

it('displays a specific sweepstakes', function () {
    $sweepstakes = Sweepstakes::factory()->create();
    $response = $this->get(route('sweepstakes.show', $sweepstakes));
    $response->assertInertia(fn ($page) => $page->component('Sweepstakes/Show')->has('sweepstakes')->has('totalParticipants'));
});

it('loads the edit page for a sweepstakes', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $sweepstakes = Sweepstakes::factory()->create();
    $response = $this->get(route('sweepstakes.edit', $sweepstakes));
    $response->assertInertia(fn ($page) => $page->component('Sweepstakes/Edit')->has('sweepstakes')->has('timezones'));
});

it('loads the create sweepstakes page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('sweepstakes.create'));
    $response->assertInertia(fn ($page) => $page->component('Sweepstakes/Edit')->has('timezones'));
});

it('stores a new sweepstakes', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $data = Sweepstakes::factory()->make()->toArray();

    unset($data['files']);

    $data['newFiles'] = [
        UploadedFile::fake()->image('prize1.jpg'),
        UploadedFile::fake()->image('prize2.jpg'),
    ];

    $this->post(route('sweepstakes.store'), $data);

    $this->assertDatabaseHas('sweepstakes', ['title' => $data['title']]);
    foreach ($data['newFiles'] as $file) {
        $this->assertDatabaseHas('files', ['original_name' => $file->name]);
    }
});

it('updates a sweepstakes', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $sweepstakes = Sweepstakes::factory()->create();
    $updatedData = [
        'title' => 'Updated Title',
    ];

    $this->put(route('sweepstakes.update', $sweepstakes), $updatedData)
        ->assertRedirect(route('sweepstakes.index'));

    $this->assertDatabaseHas('sweepstakes', ['title' => 'Updated Title']);
});

it('deletes a sweepstakes', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $sweepstakes = Sweepstakes::factory()->create();

    $this->delete(route('sweepstakes.destroy', $sweepstakes->slug))
        ->assertRedirect(route('sweepstakes.index'));

    $this->assertModelMissing($sweepstakes);
});
