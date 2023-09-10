<?php

namespace Tests\Feature\Api;

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

use Tests\Feature\Api\AbstractApiCourseTestCase;

class CourseRoleControllerTest extends AbstractApiCourseTestCase
{
    public function test_index()
    {
        $url = '/api/course/' . $this->course->id . '/role';
        // block unlogged in access
        $resp = $this->getJson($url);
        $resp->assertStatus(Status::HTTP_UNAUTHORIZED);
        // block student access
        $this->loginStudent();
        $resp = $this->getJson($url);
        $resp->assertStatus(Status::HTTP_FORBIDDEN);
        // allow instructor access
        $this->loginInstructor();
        $resp = $this->getJson($url);
        $resp->assertStatus(Status::HTTP_OK);
        $resp->assertJson(fn (AssertableJson $json) =>
            $json->has(2)
                 ->first(fn (AssertableJson $json) =>
                     $json->where('id', $this->courseInstructorRole->id)
                          ->where('name', $this->courseInstructorRole->name)
                          ->etc()
                 )
                 ->has('1', fn (AssertableJson $json) =>
                     $json->where('id', $this->courseStudentRole->id)
                          ->where('name', $this->courseStudentRole->name)
                          ->etc()
                 )
        );
    }
}
