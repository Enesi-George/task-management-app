<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use App\Models\UserList;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users and tasks
        $regularUser = User::where('email', 'user@gmail.com')->first();
        $tasks = Task::all();

        $regularUserLists = [
            [
                'user_id' => $regularUser->id,
                'task_id' => $tasks[0]->id, 
                'title' => 'Homepage Wireframes',
                'description' => 'Create detailed wireframes for the homepage including header, hero section, features, and footer.',
            ],
            [
                'user_id' => $regularUser->id,
                'task_id' => $tasks[0]->id, 
                'title' => 'Color Palette Selection',
                'description' => 'Choose appropriate colors that align with brand guidelines and ensure good accessibility.',
            ],
            [
                'user_id' => $regularUser->id,
                'task_id' => $tasks[1]->id,
                'title' => 'Query Performance Analysis',
                'description' => 'Identify slow-running queries and analyze their execution plans.',
            ],
            [
                'user_id' => $regularUser->id,
                'task_id' => $tasks[2]->id, 
                'title' => 'Endpoint Inventory',
                'description' => 'Create a complete list of all API endpoints with their methods and purposes.',
            ],
            [
                'user_id' => $regularUser->id,
                'task_id' => $tasks[2]->id, 
                'title' => 'Response Examples',
                'description' => 'Document example responses for each API endpoint including success and error cases.',
            ],
        ];

        // Create all lists for regular user
        foreach ($regularUserLists as $list) {
            UserList::create($list);
        }

    }
}
