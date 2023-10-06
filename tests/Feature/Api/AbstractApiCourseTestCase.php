<?php
namespace Tests\Feature\Api;

use App\Models\Course;
use App\Models\Role;
use App\Models\User;

use Database\Seeders\OneCourseWithUsersSeeder;

use Laravel\Sanctum\Sanctum;

use Tests\TestCase;
use Tests\Feature\Api\AbstractApiTestCase;
use Tests\Feature\Traits\CreateSecondCourseWithUsers;

abstract class AbstractApiCourseTestCase extends AbstractApiTestCase
{
    protected Course $course;
    protected Role $courseInstructorRole;
    protected Role $courseStudentRole;
    protected User $instructor;
    protected User $student;

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

    /**
     * We're copying how Illuminate\Foundation\Testing\TestCase.php initializes
     * traits such as RefreshDatabase.
     */
    protected function setUpTraits()
    {
        $uses = parent::setUpTraits();

        // later traits we might include can require a course already exists
        $this->createCourseWithUsers();

        if (isset($uses[CreateSecondCourseWithUsers::class])) {
            $this->createSecondCourseWithUsers();
        }

        return $uses;
    }

    private function createCourseWithUsers()
    {
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
}
