<?php
namespace Database\Seeders;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $departments = ['Engineering', 'Marketing', 'HR', 'Finance', 'Operations'];
        $positions = ['Manager', 'Staff', 'Senior Staff', 'Supervisor', 'Director'];

        for ($i = 1; $i <= 50; $i++) {
            Employee::create([
                'name'       => fake()->name(),
                'email'      => fake()->unique()->safeEmail(),
                'phone'      => fake()->phoneNumber(),
                'position'   => $positions[array_rand($positions)],
                'department' => $departments[array_rand($departments)],
                'join_date'  => fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
            ]);
        }
    }
}