<?php

namespace Tests\Feature;

use App\Enum\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{

    use RefreshDatabase;

    private function taskData(): array
    {
        $taskData = Task::factory()->raw();

        unset($taskData['user_id']);

        return $taskData;
    }

    public function test_todo_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/todo');

        $response->assertOk();
    }

    public function test_task_can_be_created(): void
    {
        $user = User::factory()->create();

        $expectedCount = Task::count() + 1;

        $response = $this
            ->actingAs($user)
            ->post('/todo', array_merge($this->taskData(), ['for_today' => true]));

        $response->assertOk();

        $response->assertJsonStructure(['task']);

        $this->assertDatabaseCount('tasks', $expectedCount);
    }

    public function test_task_can_be_updated(): void
    {
        $user = User::factory()
            ->has(Task::factory())
            ->create();

        $task = Task::where('user_id', $user->id)->first();

        $response = $this
            ->actingAs($user)
            ->put('/todo/' . $task->id, $this->taskData());

        $response->assertOk();

        $response->assertJsonStructure(['task']);
    }

    public function test_task_status_can_be_updated(): void
    {
        $user = User::factory()
            ->has(Task::factory())
            ->create();

        $task = Task::where('user_id', $user->id)->first();

        $response = $this
            ->actingAs($user)
            ->put('/todo/status/' . $task->id, ['status' => TaskStatus::Finished->value]);

        $response->assertOk();

        $response->assertJsonStructure(['task', 'goalReached']);
    }

    public function test_task_can_be_deleted(): void
    {
        $user = User::factory()
            ->has(Task::factory())
            ->create();

        $task = Task::where('user_id', $user->id)->first();

        $response = $this
            ->actingAs($user)
            ->delete('/todo/' . $task->id);

        $response->assertOk();

        $response->assertJsonStructure(['taskDestroyed']);
    }

}
