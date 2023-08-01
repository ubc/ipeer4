<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\AssertableJson;

use Symfony\Component\HttpFoundation\Response as Status;

use Laravel\Sanctum\Sanctum;

use App\Models\Course;
use App\Models\Role;
use App\Models\User;

use Database\Seeders\PermissionTestSeeder;

use Tests\Feature\Api\AbstractApiTestCase;

/**
 * A basic test that policies are being executed and roles are being enforced.
 */
class PermissionTest extends AbstractApiTestCase
{
    private int $perPage = 15;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionTestSeeder::class);
    }

    private function getAdmin(): User
    {
        return $admin = User::role('admin')->first();
    }

    private function getInstructor(Course $course): User
    {
        $instructorRole = Role::getTemplate('instructor');
        $courseInstructorRole = $instructorRole->getCourseRole($course->id);
        $instructor = $course->users()
                             ->where('role_id',$courseInstructorRole->id)
                             ->first();
        return $instructor;
    }

    private function getStudent(Course $course): User
    {
        $studentRole = Role::getTemplate('student');
        $courseStudentRole = $studentRole->getCourseRole($course->id);
        $student = $course->users()
                          ->where('role_id',$courseStudentRole->id)
                          ->first();
        return $student;
    }

    public function test_admin_get_users_and_courses_list()
    {
        $course = Course::first();
        // create an additional course so there's more than one to list
        Course::factory()->create();
        $admin = $this->getAdmin();
        Sanctum::actingAs($admin, ['*']);
        $resp = $this->getJson('/api/user');
        $resp->assertStatus(Status::HTTP_OK);
        $resp = $this->getJson('/api/course');
        $resp->assertStatus(Status::HTTP_OK);
        $resp->assertJson(fn (AssertableJson $json) =>
            $json->has('data', 2)
                 ->has('data.0', fn (AssertableJson $json) =>
                     $json->where('id', $course->id)
                          ->where('name', $course->name)
                          ->etc()
                 )->etc()
        );
    }
    
    public function test_instructor_and_student_get_users_and_courses_list()
    {
        $course = Course::first();
        // create an additional course that they shouldn't be able to see
        Course::factory()->create();

        $instructor = $this->getInstructor($course);
        Sanctum::actingAs($instructor, ['*']);
        $resp = $this->getJson('/api/user');
        $resp->assertStatus(Status::HTTP_FORBIDDEN);
        $resp = $this->getJson('/api/course');
        $resp->assertStatus(Status::HTTP_OK);
        $resp->assertJson(fn (AssertableJson $json) =>
            $json->has('data', 1) // can't see the other course
                 ->has('data.0', fn (AssertableJson $json) =>
                     $json->where('id', $course->id)
                          ->where('name', $course->name)
                          ->etc()
                 )->etc()
        );

        $student = $this->getStudent($course);
        Sanctum::actingAs($student, ['*']);
        $resp = $this->getJson('/api/user');
        $resp->assertStatus(Status::HTTP_FORBIDDEN);
        $resp = $this->getJson('/api/course');
        $resp->assertStatus(Status::HTTP_OK);
        $resp->assertJson(fn (AssertableJson $json) =>
            $json->has('data', 1) // can't see the other course
                 ->has('data.0', fn (AssertableJson $json) =>
                     $json->where('id', $course->id)
                          ->where('name', $course->name)
                          ->etc()
                 )->etc()
        );
    }

    public function test_instructor_can_edit_enroled_course()
    {
        $course = Course::first();
        $instructor = $this->getInstructor($course);
        Sanctum::actingAs($instructor, ['*']);
        $editCourse = ['name' => 'SomeNewName'];
        $resp = $this->putJson('/api/course/' . $course->id, $editCourse);
        $resp->assertStatus(Status::HTTP_OK);

        $forbiddenCourse = Course::factory()->create();
        $resp = $this->putJson('/api/course/' . $forbiddenCourse->id, $editCourse);
        $resp->assertStatus(Status::HTTP_FORBIDDEN);
    }

    public function test_student_can_only_get_self()
    {
        $course = Course::first();
        $student = $this->getStudent($course);
        Sanctum::actingAs($student, ['*']);
        $resp = $this->getJson('/api/user/'. $student->id);
        $resp->assertStatus(Status::HTTP_OK);

        $instructor = $this->getInstructor($course);
        $resp = $this->getJson('/api/user/'. $instructor->id);
        $resp->assertStatus(Status::HTTP_FORBIDDEN);
    }

    public function test_student_can_only_get_enroled_course()
    {
        $course = Course::first();
        $student = $this->getStudent($course);
        Sanctum::actingAs($student, ['*']);
        $resp = $this->getJson('/api/course/'. $course->id);
        $resp->assertStatus(Status::HTTP_OK);

        $inaccessibleCourse = Course::factory()->create();
        $resp = $this->getJson('/api/course/'. $inaccessibleCourse->id);
        $resp->assertStatus(Status::HTTP_FORBIDDEN);
    }

    public function test_only_admin_can_create_user()
    {
        $course = Course::first();
        $newUser = User::factory()->make()->attributesToArray();
        // password gets skipped when converting to array, so need to reset it
        $newUser['password'] = 'password';
        $student = $this->getStudent($course);
        Sanctum::actingAs($student, ['*']);
        $resp = $this->postJson('/api/user/', $newUser);
        $resp->assertStatus(Status::HTTP_FORBIDDEN);

        $instructor = $this->getInstructor($course);
        Sanctum::actingAs($instructor, ['*']);
        $resp = $this->postJson('/api/user/', $newUser);
        $resp->assertStatus(Status::HTTP_FORBIDDEN);

        $admin = $this->getAdmin();
        Sanctum::actingAs($admin, ['*']);
        $resp = $this->postJson('/api/user/', $newUser);
        $resp->assertStatus(Status::HTTP_CREATED);
    }

    public function test_only_admin_can_create_course()
    {
        $course = Course::first();
        $newCourse = Course::factory()->make()->attributesToArray();
        $student = $this->getStudent($course);
        Sanctum::actingAs($student, ['*']);
        $resp = $this->postJson('/api/course/', $newCourse);
        $resp->assertStatus(Status::HTTP_FORBIDDEN);

        $instructor = $this->getInstructor($course);
        Sanctum::actingAs($instructor, ['*']);
        $resp = $this->postJson('/api/course/', $newCourse);
        $resp->assertStatus(Status::HTTP_FORBIDDEN);

        $admin = $this->getAdmin();
        Sanctum::actingAs($admin, ['*']);
        $resp = $this->postJson('/api/course/', $newCourse);
        $resp->assertStatus(Status::HTTP_CREATED);
    }

    public function test_only_admin_can_delete_user()
    {
        $course = Course::first();
        $userToDelete = User::factory()->create();
        $student = $this->getStudent($course);
        Sanctum::actingAs($student, ['*']);
        $resp = $this->deleteJson('/api/user/' . $userToDelete->id);
        $resp->assertStatus(Status::HTTP_FORBIDDEN);

        $instructor = $this->getInstructor($course);
        Sanctum::actingAs($instructor, ['*']);
        $resp = $this->deleteJson('/api/user/' . $userToDelete->id);
        $resp->assertStatus(Status::HTTP_FORBIDDEN);

        $admin = $this->getAdmin();
        Sanctum::actingAs($admin, ['*']);
        $resp = $this->deleteJson('/api/user/' . $userToDelete->id);
        $resp->assertStatus(Status::HTTP_NO_CONTENT);
    }

    public function test_only_admin_can_delete_course()
    {
        $course = Course::first();
        $student = $this->getStudent($course);
        Sanctum::actingAs($student, ['*']);
        $resp = $this->deleteJson('/api/course/' . $course->id);
        $resp->assertStatus(Status::HTTP_FORBIDDEN);

        $instructor = $this->getInstructor($course);
        Sanctum::actingAs($instructor, ['*']);
        $resp = $this->deleteJson('/api/course/' . $course->id);
        $resp->assertStatus(Status::HTTP_FORBIDDEN);

        $admin = $this->getAdmin();
        Sanctum::actingAs($admin, ['*']);
        $resp = $this->deleteJson('/api/course/' . $course->id);
        $resp->assertStatus(Status::HTTP_NO_CONTENT);
    }
}
