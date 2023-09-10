<?php
namespace Tests\Feature\Api;

use App\Models\Course;
use App\Models\Role;
use App\Models\User;

use Database\Seeders\OneCourseWithUsersSeeder;

use Laravel\Sanctum\Sanctum;

use Tests\TestCase;
use Tests\Feature\Api\AbstractApiTestCase;

class AbstractApiCourseTestCase extends AbstractApiTestCase
{
    protected Course $course;
    protected Role $courseInstructorRole;
    protected Role $courseStudentRole;
    protected User $instructor;
    protected User $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(OneCourseWithUsersSeeder::class);
        $this->course = Course::first();
        $this->courseInstructorRole = Role::getTemplate('instructor')
                                          ->getCourseRole($this->course->id);
        $this->instructor = User::role($this->courseInstructorRole->name)
                                ->first();
        $this->courseStudentRole = Role::getTemplate('student')
                                       ->getCourseRole($this->course->id);
        $this->student = User::role($this->courseStudentRole->name)
                             ->first();
    }

    protected function login(User $user): void
    {
        Sanctum::actingAs($user, ['*']);
    }

    protected function loginInstructor(): void
    {
        $this->login($this->instructor);
    }

    protected function loginStudent(): void
    {
        $this->login($this->student);
    }
}
