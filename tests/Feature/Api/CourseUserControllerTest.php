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

    private function login()
    {
        $this->seed(OneCourseWithUsersSeeder::class);
        // get the seeded course
        $this->course = Course::first();
        // get one instructor in the course
        $instructorRole = Role::findByName('instructor')
                              ->getCourseRole($this->course->id);
        $user = User::role($instructorRole->name)->first();
        Sanctum::actingAs($user, ['*']);
        $this->instructor = $user;
    }

    private function getUrl(Course $course, User $user = null): string
    {
        $ret = '/api/course/' . $course->id . '/user';
        if ($user) $ret .= '/' . $user->id;
        return $ret;
    }

    public function test_index()
    {
        $this->login();

        $resp = $this->getJson($this->getUrl($this->course));
        $resp->dump();
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
                 $json->where('id', $this->instructor->id)
                      ->where('name', $this->instructor->name)
                      ->etc()
                 )
                 ->etc()
        );

    }

    public function test_store()
    {
        $this->login();

        $course = Course::factory()->create();
        $users = User::factory()->count(3)->create();
        $url = $this->getUrl($course);
        // test insert single id
        $resp = $this->postJson($url, ['userIds' => $users[0]->id]);
        $resp->assertStatus(Status::HTTP_NO_CONTENT);
        $this->assertDatabaseHas('course_user',
            ['course_id' => $course->id,
             'user_id' => $users[0]->id]);
        // test insert array of ids
        $resp = $this->postJson($url,
            ['userIds' => [$users[1]->id, $users[2]->id]]);
        $resp->assertStatus(Status::HTTP_NO_CONTENT);
        $this->assertDatabaseHas('course_user',
            ['course_id' => $course->id,
             'user_id' => $users[1]->id]);
        $this->assertDatabaseHas('course_user',
            ['course_id' => $course->id,
             'user_id' => $users[2]->id]);
        // test insert invalid user
        $resp = $this->postJson($url, ['userIds' => 9999999999999999]);
        $resp->assertStatus(Status::HTTP_UNPROCESSABLE_ENTITY);
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

        // Create a course with some users
        $course = Course::factory()->create();
        #$users = User::factory()->count(3)->hasAttached($course)->create();
        $users = User::factory()->count(3)->create();
        $course = Course::factory()->hasAttached($users)->create();
        $this->assertDatabaseHas('course_user',
            ['course_id' => $course->id,
             'user_id' => $users[0]->id]);
        // delete the first user from the course
        $url = $this->getUrl($course, $users[0]);
        $resp = $this->deleteJson($url);
        $resp->assertStatus(Status::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('course_user',
            ['course_id' => $course->id,
             'user_id' => $users[0]->id]);
    }

}
