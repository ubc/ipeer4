<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Course;
use App\Models\Role;
use App\Models\User;

use Spatie\Permission\Models\Permission;

class OneCourseWithUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::factory()->count(3)->create();
        $course = Course::factory()->hasAttached($users)->create();

        $roleInstructor = Role::getTemplate('instructor');
        $roleStudent = Role::getTemplate('student');

        $courseRoleInstructor = $roleInstructor->getCourseRole($course->id);
        $courseRoleStudent = $roleStudent->getCourseRole($course->id);

        $users[0]->assignRole($courseRoleInstructor);
        $users[0]->courses()->updateExistingPivot($course->id, [
            'role_id' => $courseRoleInstructor->id
        ]);
        $users[1]->assignRole($courseRoleStudent);
        $users[1]->courses()->updateExistingPivot($course->id, [
            'role_id' => $courseRoleStudent->id
        ]);
        $users[2]->assignRole($courseRoleStudent);
        $users[2]->courses()->updateExistingPivot($course->id, [
            'role_id' => $courseRoleStudent->id
        ]);
    }
}
