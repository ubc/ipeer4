<?php
namespace Tests\Feature\Traits;

use App\Models\Course;
use App\Models\Role;
use App\Models\User;

trait CreateSecondCourseWithUsers
{
    protected Course $course2;
    protected Role $course2InstructorRole;
    protected Role $course2StudentRole;
    protected User $course2Instructor;
    protected User $course2Student;

    protected function createSecondCourseWithUsers()
    {
        $users = User::factory()->count(2)->create();
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

        $this->course2 = $course;
        $this->course2InstructorRole = $courseRoleInstructor;
        $this->course2Instructor = $users[0];
        $this->course2StudentRole = $courseRoleStudent;
        $this->course2Student = $users[1];
    }

}
