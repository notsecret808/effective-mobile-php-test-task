<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Task;
use App\Enums\Status;

pest()->use(RefreshDatabase::class);

beforeEach(function () {
    $this->tasks = Task::factory()->count(5)->create();
});

test('retrieve all tasks', function () {
    $response = $this->getJson('/api/tasks');

    $response->assertOk();
    $response->assertJsonCount(5);

    foreach ($this->tasks as $task) {
        $response->assertJsonFragment($task->toArray());
    }
});


describe('create task', function () {
    test('data is valid', function () {
        $expected = [
            'title' => 'new',
            'description' => 'new',
            'status' => Status::Active
        ];

        $response = $this->post('/api/tasks', [
            'title' => 'new',
            'description' => 'new',
            'status' => Status::Active->value
        ]);

        $response->assertCreated();

        $latestTask = Task::find(6);

        $this->assertEquals($expected, $latestTask->only(array_keys($expected)));

        $this->assertDatabaseCount('tasks', 6);
    });

    test('data is not valid', function () {
        $response = $this->post('/api/tasks', [
            'title' => 'new',
            'description' => 'new',
            'status' => 'bull'
        ]);

        $response->assertBadRequest();

        $this->assertDatabaseCount('tasks', 5);
    });
});

describe('update task by id', function () {
    test('data is valid', function () {
        $changedAttributes = [
            'title' => 'changed'
        ];

        $response = $this->put('/api/tasks/2', $changedAttributes);

        $response->assertOk();

        $latestTask = Task::find(2);

        $this->assertEquals($changedAttributes, $latestTask->only(array_keys($changedAttributes)));

        $this->assertDatabaseCount('tasks', 5);
    });

    test('data is not valid', function () {
        $response = $this->put('/api/tasks/2', [
            'title' => 'new',
            'description' => 'new',
            'status' => 'bull'
        ]);

        $response->assertBadRequest();

        $targetTask = Task::find(2);
        expect($targetTask->title)->not->toBe('new');

        $this->assertDatabaseCount('tasks', 5);
    });
});

describe('retrieve task by id', function () {
    test('task is existed', function () {
        $response = $this->getJson('/api/tasks/2');

        $response->assertOk();

        $response->assertJson($this->tasks->find(2)->toArray());
    });

     test('task is not existed', function () {
        $response = $this->getJson('/api/tasks/21');

        $response->assertNotFound();
    });
});

describe('drop task by id', function () {
    test('task is existed', function () {
        $response = $this->delete('/api/tasks/2');

        $response->assertOk();

        $this->assertDatabaseCount('tasks', 4);
    });

     test('task is not existed', function () {
        $response = $this->getJson('/api/tasks/21');

        $response->assertNotFound();
        $this->assertDatabaseCount('tasks', 5);
    });
});