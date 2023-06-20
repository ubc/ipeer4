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
use App\Models\User;

use Tests\TestCase;

class CourseControllerTest extends TestCase
{
    use RefreshDatabase;

    private string $url = '/api/course';
    private int $perPage = 15;

    private function login()
    {
        // create a user and login as that user
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);
    }

    public function test_deny_access_to_guest_users(): void
    {
        // create a course so there's at least one in the database
        $course = Course::factory()->create();
        $urlForCourse = $this->url .'/'. $course->id;
        // GET all
        $resp = $this->getJson($this->url);
        $resp->assertStatus(Status::HTTP_UNAUTHORIZED);
        // GET single course
        $resp = $this->getJson($urlForCourse);
        $resp->assertStatus(Status::HTTP_UNAUTHORIZED);
        // POST
        $params = ['name' => $course->name . '1'];
        $resp = $this->postJson($this->url, $params);
        $resp->assertStatus(Status::HTTP_UNAUTHORIZED);
        // PUT / PATCH
        $params = ['name' => $course->name . '1'];
        $resp = $this->putJson($urlForCourse, $params);
        $resp->assertStatus(Status::HTTP_UNAUTHORIZED);
        $resp = $this->patchJson($urlForCourse, $params);
        $resp->assertStatus(Status::HTTP_UNAUTHORIZED);
        // DELETE
        $resp = $this->deleteJson($urlForCourse);
        $resp->assertStatus(Status::HTTP_UNAUTHORIZED);
    }

    public function test_get_users()
    {
        $this->login();

        // GET list
        $course = Course::factory()->create();
        $resp = $this->getJson($this->url);
        $resp->assertStatus(Status::HTTP_OK);
        $resp->assertJson(fn (AssertableJson $json) =>
            $json->where('total', 1)
                 ->where('per_page', $this->perPage)
                 ->hasAll([
                     'first_page_url',
                     'prev_page_url',
                     'next_page_url',
                     'last_page_url'
                 ])
                 ->has('data', 1, fn (AssertableJson $json) =>
                 $json->where('id', $course->id)
                      ->where('name', $course->name)
                      ->etc()
                 )
                 ->etc()
        );

        // create a second course
        $course2 = Course::factory()->create();
        $urlForCourse2 = $this->url .'/'. $course2->id;
        // GET single course
        $resp = $this->getJson($urlForCourse2);
        $resp->assertStatus(Status::HTTP_OK);
        $resp->assertJson(fn (AssertableJson $json) =>
            $json->where('id', $course2->id)
                 ->where('name', $course2->name)
                 ->etc()
        );
    }

    public function test_get_users_paginated()
    {
        $this->login();
        // create enough courses to trigger the pagination, default per page
        // limit in laravel is 15
        $totalCourses = $this->perPage + 1;
        $courses = Course::factory()->count($totalCourses)->create();

        // GET the first page of courses
        $resp = $this->getJson($this->url);
        $resp->assertStatus(Status::HTTP_OK);
        //$resp->dump();
        $resp->assertJson(fn (AssertableJson $json) =>
            $json->where('total', $totalCourses)
                 ->where('per_page', $this->perPage)
                 ->has('data', $this->perPage, fn (AssertableJson $json) =>
                 $json->where('id', $courses[0]->id)
                      ->where('name', $courses[0]->name)
                      ->etc()
                 )
                 ->etc()
        );
        $nextPageUrl = $resp['next_page_url'];

        // get the next page of courses, which should only have 1
        $resp = $this->getJson($nextPageUrl);
        $resp->assertStatus(Status::HTTP_OK);
        //$resp->dump();
        $resp->assertJson(fn (AssertableJson $json) =>
            $json->where('total', $totalCourses)
                 ->where('per_page', $this->perPage)
                 ->has('data', 1, fn (AssertableJson $json) =>
                 $json->where('id', $courses[$totalCourses-1]->id)
                      ->where('name', $courses[$totalCourses-1]->name)
                      ->etc()
                 )
                 ->etc()
        );

    }

    /**
     * Test that we can sort by different columns when getting courses
     */
    public function test_get_courses_with_sorting()
    {
        $this->login();
        // create enough courses to trigger the pagination, default per page
        // limit in laravel is 15
        $totalCourses = $this->perPage + 1;
        $courses = Course::factory()->count($totalCourses)->create();

        $sortableFields = ['id', 'name', 'created_at', 'updated_at'];
        foreach ($sortableFields as $sortableField) {
            //fwrite(STDERR, "\n--- $sortableField ---\n");
            // sortBy leaves the old index keys in place, we need to use values()
            // to generate a new consecutively numbered index
            $courses = $courses->sortBy($sortableField)->values();

            // GET the first page of courses
            $resp = $this->getJson($this->url . "?sort_by=$sortableField");
            $resp->assertStatus(Status::HTTP_OK);
            //$resp->dump();
            $resp->assertJson(fn (AssertableJson $json) =>
                $json->where('total', $totalCourses)
                     ->where('per_page', $this->perPage)
                     ->has('data', $this->perPage, fn (AssertableJson $json) =>
                     $json->where('id', $courses[0]->id)
                          ->where('name', $courses[0]->name)
                          ->etc()
                     )
                     ->etc()
            );
            $nextPageUrl = $resp['next_page_url'];
            // get the next page of courses, which should only have 1
            $resp = $this->getJson($nextPageUrl);
            $resp->assertStatus(Status::HTTP_OK);
            //$resp->dump();
            $resp->assertJson(fn (AssertableJson $json) =>
                $json->where('total', $totalCourses)
                     ->where('per_page', $this->perPage)
                     ->has('data', 1, fn (AssertableJson $json) =>
                     $json->where('id', $courses[$totalCourses-1]->id)
                          ->where('name', $courses[$totalCourses-1]->name)
                          ->etc()
                     )
                     ->etc()
            );
        }
    }

    public function test_create_course()
    {
        $this->login();
        // this course is just generated and NOT stored in the database
        $course2 = Course::factory()->make();
        // POST without optional params
        $params = ['name' => $course2->name,];
        $resp = $this->postJson($this->url, $params);
        $resp->assertStatus(Status::HTTP_CREATED);
        $resp->assertJson(fn (AssertableJson $json) =>
            $json->has('id')
                 ->where('name', $course2->name)
                 ->missing('password')
                 ->etc()
        );
        // this course is just generated and NOT stored in the database
        $course3 = Course::factory()->make();
        // POST with optional params
        $params = [ 'name' => $course3->name, ];
        $resp = $this->postJson($this->url, $params);
        $resp->assertStatus(Status::HTTP_CREATED);
        $resp->assertJson(fn (AssertableJson $json) =>
            $json->has('id')
                 ->where('name', $course3->name)
                 ->etc()
        );
    }

    public function test_update_course()
    {
        $this->login();

        // create a course so there's at least one in the database
        $course = Course::factory()->create();
        $urlForCourse = $this->url .'/'. $course->id;
        // check PUT / PATCH both work
        $expectedName = $course->name . 'EDIT';
        $params = ['name' => $expectedName];
        $resp = $this->putJson($urlForCourse, $params);
        $resp->assertStatus(Status::HTTP_OK);
        $resp->assertJson(fn (AssertableJson $json) =>
            $json->has('id')
                 ->where('name', $expectedName)
                 ->etc()
        );
        $expectedName = $course->name . 'ANOTHEREDIT';
        $params = ['name' => $expectedName];
        $resp = $this->patchJson($urlForCourse, $params);
        $resp->assertStatus(Status::HTTP_OK);
        $resp->assertJson(fn (AssertableJson $json) =>
            $json->has('id')
                 ->where('name', $expectedName)
                 ->etc()
        );
        // check that all params can be changed
        $params = [
            'name' => $course->name . '1',
        ];
        $resp = $this->putJson($urlForCourse, $params);
        $resp->assertStatus(Status::HTTP_OK);
        $resp->assertJson(fn (AssertableJson $json) =>
            $json->has('id')
                 ->where('name', $course->name . '1')
                 ->etc()
        );
        // TODO: check that password is changed & that only logged in course can
        // change their own password
    }

    public function test_delete_course()
    {
        $this->login();

        // create a course to delete
        $course2 = Course::factory()->create();
        $urlForCourse = $this->url .'/'. $course2->id;
        // delete the course
        $resp = $this->deleteJson($urlForCourse);
        $resp->assertStatus(Status::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('courses',
                                    ['id' => $course2->id]);
    }

}
