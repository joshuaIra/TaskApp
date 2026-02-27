<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@taskapp.local',
        ]);

        // Create manager user
        User::factory()->manager()->create([
            'name' => 'Manager User',
            'email' => 'manager@taskapp.local',
        ]);

        // Create regular member user
        User::factory()->create([
            'name' => 'Member User',
            'email' => 'member@taskapp.local',
        ]);

        // Create additional test members
        $members = User::factory(5)->create();

        // Create some tasks
        $allUsers = User::all();

        foreach (range(1, 10) as $i) {
            $creator = $allUsers->random();
            $task = \App\Models\Task::create([
                'title' => "Sample Task $i",
                'description' => "This is sample task number $i",
                'priority' => ['low','medium','high'][array_rand(['low','medium','high'])],
                'status' => ['pending','in_progress','completed'][array_rand(['pending','in_progress','completed'])],
                'due_at' => now()->addDays(rand(1,30)),
                'creator_id' => $creator->id,
            ]);
            // assign random assignees
            $assignees = $allUsers->random(rand(1,3))->pluck('id')->toArray();
            $task->assignees()->sync($assignees);
        }
    }
}
