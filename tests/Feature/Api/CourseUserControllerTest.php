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

use Database\Seeders\OneCourseWithUsersSeeder;

use Tests\Feature\Api\AbstractApiTestCase;

class CourseUserControllerTest extends AbstractApiTestCase
{
    private int $perPage = 15;
    private Course $course;
    private User $instructor;
    private User $student;

    private function login()
    {
        $this->seed(OneCourseWithUsersSeeder::class);
        // get the seeded course
        $this->course = Course::first();
        // get one instructor in the course
        $instructorRole = Role::getTemplate('instructor')
                              ->getCourseRole($this->course->id);
        $user = User::role($instructorRole->name)->first();
        Sanctum::actingAs($user, ['*']);
        $this->instructor = $user;
        // get a student in the course
        $studentRole = Role::getTemplate('student')
                           ->getCourseRole($this->course->id);
        $this->student = User::role($studentRole->name)->first();
    }

    private function getUrl(Course $course, User $user = null): string
    {
        $ret = '/api/course/' . $course->id . '/user';
        if ($user) $ret .= '/' . $user->id;
        return $ret;
    }

    public function test_block_student_access()
    {
        $this->login();
        Sanctum::actingAs($this->student, ['*']);
        // can't get list of enroled users
        $resp = $this->getJson($this->getUrl($this->course));
        $resp->assertStatus(Status::HTTP_FORBIDDEN);
        // can't create enrolment
        $user = User::factory()->create();
        $url = $this->getUrl($this->course);
        $studentRole = Role::getTemplate('student')
                           ->getCourseRole($this->course->id);
        $resp = $this->postJson($url, ['userIds' => $user->id,
                                       'roleId' => $studentRole->id]);
        $resp->assertStatus(Status::HTTP_FORBIDDEN);
        // can't drop instructor
        $url = $this->getUrl($this->course, $this->instructor);
        $resp = $this->deleteJson($url);
        $resp->assertStatus(Status::HTTP_FORBIDDEN);
    }

    public function test_index()
    {
        $this->login();

        $instructorRole = Role::getTemplate('instructor')
                              ->getCourseRole($this->course->id);
        $resp = $this->getJson($this->getUrl($this->course));
        //$resp->dump();
        $resp->assertStatus(Status::HTTP_OK);
        $resp->assertJson(fn (AssertableJson $json) =>
            $json->where('total', 3)
                 ->where('per_page', $this->perPage)
                 ->hasAll([
                     'first_page_url',
                     'prev_page_url',
                     'next_page_url',
                     'last_page_url'
                 ])
                 ->has('data', 3, fn (AssertableJson $json) =>
                     $json->where('user_id', $this->instructor->id)
                          ->where('name', $this->instructor->name)
                          ->where('role_id', $instructorRole->id)
                          ->etc()
                 )
                 ->etc()
        );

    }

    public function test_store()
    {
        $this->login();

        $users = User::factory()->count(3)->create();
        $url = $this->getUrl($this->course);
        $studentRole = Role::getTemplate('student')
                           ->getCourseRole($this->course->id);
        // test insert invalid user
        $resp = $this->postJson($url, ['userIds' => 9999999999999999,
                                       'roleId' => $studentRole->id]);
        $resp->assertStatus(Status::HTTP_UNPROCESSABLE_ENTITY);
        // test insert invalid role
        $resp = $this->postJson($url, ['userIds' => $users[0]->id,
                                       'roleId' => 999999999999999]);
        $resp->assertStatus(Status::HTTP_UNPROCESSABLE_ENTITY);
        // test insert single id
        $resp = $this->postJson($url, ['userIds' => $users[0]->id,
                                       'roleId' => $studentRole->id]);
        $resp->assertStatus(Status::HTTP_NO_CONTENT);
        $this->assertDatabaseHas('course_user',
            ['course_id' => $this->course->id,
             'user_id' => $users[0]->id]);
        // test insert array of ids
        $resp = $this->postJson($url,
            ['userIds' => [$users[1]->id, $users[2]->id],
             'roleId' => $studentRole->id]);
        $resp->assertStatus(Status::HTTP_NO_CONTENT);
        $this->assertDatabaseHas('course_user',
            ['course_id' => $this->course->id,
             'user_id' => $users[1]->id]);
        $this->assertDatabaseHas('course_user',
            ['course_id' => $this->course->id,
             'user_id' => $users[2]->id]);
        // test insert empty
        $resp = $this->postJson($url, []);
        $resp->assertStatus(Status::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_show()
    {
        $this->login();

        // Create a course with some users
        $course = Course::factory()->create();
        #$users = User::factory()->count(3)->hasAttached($course)->create();
        $users = User::factory()->count(3)->create();
        $course = Course::factory()->hasAttached($users)->create();
        $url = $this->getUrl($course, $users[0]);
        $resp = $this->getJson($url);
        $resp->assertStatus(Status::HTTP_NOT_IMPLEMENTED);
    }

    public function test_update()
    {
        $this->login();

        // Create a course with some users
        $course = Course::factory()->create();
        #$users = User::factory()->count(3)->hasAttached($course)->create();
        $users = User::factory()->count(3)->create();
        $course = Course::factory()->hasAttached($users)->create();
        $url = $this->getUrl($course, $users[0]);
        $resp = $this->putJson($url, []);
        $resp->assertStatus(Status::HTTP_NOT_IMPLEMENTED);
    }

    public function test_delete()
    {
        $this->login();

        // add some users we can delete to the course
        $users = User::factory()->count(3)->create();
        $studentRole = Role::getTemplate('student')
                           ->getCourseRole($this->course->id);
        $this->course->users()->attach($users, ['role_id' => $studentRole->id]);
        // check that the new user is enroled
        $this->assertDatabaseHas('course_user',
            ['course_id' => $this->course->id,
             'user_id' => $users[0]->id]);
        // delete the first user from the course
        $url = $this->getUrl($this->course, $users[0]);
        $resp = $this->deleteJson($url);
        $resp->assertStatus(Status::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('course_user',
            ['course_id' => $this->course->id,
             'user_id' => $users[0]->id]);
    }

}
