<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    { // Get the admin user to create tasks
        $adminUser = User::where('role', 'admin')->first();

        $tasks = [
            [
                'title' => 'Website Redesign Project',
                'description' => 'Complete redesign of the company website with modern UI/UX principles. This includes wireframing, prototyping, and implementation of responsive design.',
                'created_by' => $adminUser->id,
            ],
            [
                'title' => 'Database Optimization',
                'description' => 'Optimize database queries and improve performance. This involves analyzing slow queries, adding proper indexes, and restructuring tables where necessary.',
                'created_by' => $adminUser->id,
            ],
            [
                'title' => 'API Documentation',
                'description' => 'Create comprehensive API documentation for all endpoints. Include examples, parameter descriptions, and response formats.',
                'created_by' => $adminUser->id,
            ],
            [
                'title' => 'User Authentication System',
                'description' => 'Implement secure user authentication with role-based access control. Include features like password reset, email verification, and two-factor authentication.',
                'created_by' => $adminUser->id,
            ],
            [
                'title' => 'Mobile App Development',
                'description' => 'Develop a mobile application for iOS and Android platforms. The app should sync with the web platform and provide offline capabilities.',
                'created_by' => $adminUser->id,
            ],
            [
                'title' => 'Security Audit',
                'description' => 'Conduct a comprehensive security audit of the application. Check for vulnerabilities, implement security best practices, and ensure data protection.',
                'created_by' => $adminUser->id,
            ],
        ];

        // Create all the tasks
        foreach ($tasks as $task) {
            Task::create($task);
        }
    
    }
}
