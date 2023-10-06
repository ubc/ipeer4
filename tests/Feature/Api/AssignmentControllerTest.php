<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\AssertableJson;

use Symfony\Component\HttpFoundation\Response as Status;

use Laravel\Sanctum\Sanctum;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Role;
use App\Models\User;

use Database\Seeders\OneCourseWithUsersSeeder;

use Tests\Feature\Api\AbstractApiCourseTestCase;
use Tests\Feature\Traits\CreateAssignments;
use Tests\Feature\Traits\CreateSecondCourseWithUsers;

class AssignmentControllerTest extends AbstractApiCourseTestCase
{
    use CreateAssignments, CreateSecondCourseWithUsers;

    private function getUrl(int $courseId, int $assignmentId = 0): string
    {
        $url = '/api/course/' . $this->course->id . '/assignment';
        if ($assignmentId) {
            $url .= '/' . $assignmentId;
        }
        return $url;
    }

    public function test_index(): void
    {
        $url = $this->getUrl($this->course->id);
        // block unlogged in access
        $resp = $this->getJson($url);
        $resp->assertStatus(Status::HTTP_UNAUTHORIZED);
        // students can only see published assignments
        $this->loginStudent();
        $resp = $this->getJson($url);
        #$resp->dump();
        $resp->assertStatus(Status::HTTP_OK);
        $resp->assertJson(fn (AssertableJson $json) =>
            $json->where('total', 1)
                 ->hasAll([
                     'per_page',
                     'first_page_url',
                     'prev_page_url',
                     'next_page_url',
                     'last_page_url'
                 ])
                 ->has('data', 1, fn (AssertableJson $json) =>
                 $json->where('id', $this->assignmentPublished->id)
                      ->where('name', $this->assignmentPublished->name)
                      ->where('desc', $this->assignmentPublished->desc)
                      ->where('due', $this->assignmentPublished->due->toJson())
                      ->where('open_from',
                              $this->assignmentPublished->open_from->toJson())
                      ->where('course_id', $this->assignmentPublished->course_id)
                      ->etc()
                 )
                 ->etc()
        );
        // instructors can see both published and unpublished assingments
        $this->loginInstructor();
        $resp = $this->getJson($url);
        #$resp->dump();
        $resp->assertStatus(Status::HTTP_OK);
        $resp->assertJson(fn (AssertableJson $json) =>
            $json->where('total', 2)
                 ->hasAll([
                     'per_page',
                     'first_page_url',
                     'prev_page_url',
                     'next_page_url',
                     'last_page_url'
                 ])
                 ->has('data', 2, fn (AssertableJson $json) =>
                 $json->where('id', $this->assignmentUnpublished->id)
                      ->where('name', $this->assignmentUnpublished->name)
                      ->where('desc', $this->assignmentUnpublished->desc)
                      ->where('due',
                              $this->assignmentUnpublished->due->toJson())
                      ->where('open_from',
                              $this->assignmentUnpublished->open_from->toJson())
                      ->where('course_id',
                              $this->assignmentUnpublished->course_id)
                      ->etc()
                 )
                 ->etc()
        );
    }

    public function test_show_published_assignment(): void
    {
        $url = $this->getUrl($this->course->id, $this->assignmentPublished->id);
        // block unlogged in access
        $resp = $this->getJson($url);
        $resp->assertStatus(Status::HTTP_UNAUTHORIZED);
        // students can see published assignments
        $this->loginStudent();
        $resp = $this->getJson($url);
        $resp->assertStatus(Status::HTTP_OK);
        $resp->assertJson(fn (AssertableJson $json) =>
            $json->where('id', $this->assignmentPublished->id)
                 ->where('name', $this->assignmentPublished->name)
                 ->where('desc', $this->assignmentPublished->desc)
                 ->where('due', $this->assignmentPublished->due->toJson())
                 ->where('open_from',
                     $this->assignmentPublished->open_from->toJson())
                 ->where('course_id', $this->assignmentPublished->course_id)
                 ->etc()
        );
        // users from another course can't see published assignment
        $this->login($this->course2Student);
        $resp = $this->getJson($url);
        $resp->assertStatus(Status::HTTP_FORBIDDEN);
        $this->login($this->course2Instructor);
        $resp = $this->getJson($url);
        $resp->assertStatus(Status::HTTP_FORBIDDEN);
    }

    public function test_show_unpublished_assignment(): void
    {
        // change url to unpublished assignment
        $url = $this->getUrl($this->course->id,
                             $this->assignmentUnpublished->id);
        // students cannot see unpublished assignments
        $this->loginStudent();
        $resp = $this->getJson($url);
        $resp->assertStatus(Status::HTTP_FORBIDDEN);
        // users from another course can't see unpublished assignment
        $this->login($this->course2Student);
        $resp = $this->getJson($url);
        $resp->assertStatus(Status::HTTP_FORBIDDEN);
        $this->login($this->course2Instructor);
        $resp = $this->getJson($url);
        $resp->assertStatus(Status::HTTP_FORBIDDEN);
        // instructor can see unpublished assingment
        $this->loginInstructor();
        $resp = $this->getJson($url);
        $resp->assertStatus(Status::HTTP_OK);
        $resp->assertJson(fn (AssertableJson $json) =>
            $json->where('id', $this->assignmentUnpublished->id)
                 ->where('name', $this->assignmentUnpublished->name)
                 ->where('desc', $this->assignmentUnpublished->desc)
                 ->where('due', $this->assignmentUnpublished->due->toJson())
                 ->where('open_from',
                     $this->assignmentUnpublished->open_from->toJson())
                 ->where('course_id', $this->assignmentUnpublished->course_id)
                 ->etc()
        );
    }

    public function test_store(): void
    {
        $url = $this->getUrl($this->course->id);
        $newAssignment = Assignment::factory()->make([
            'course_id' => $this->course->id
        ]);
        $newAssignmentParams = [
            'name' => $newAssignment->name,
            'due' => $newAssignment->due->toJson(),
            'open_from' => $newAssignment->open_from->toJson(),
            'course_id' => $newAssignment->course_id,
        ];

        // block unlogged in access
        $resp = $this->postJson($url, $newAssignmentParams);
        $resp->assertStatus(Status::HTTP_UNAUTHORIZED);
        // block student access
        $this->loginStudent();
        $resp = $this->postJson($url, $newAssignmentParams);
        $resp->assertStatus(Status::HTTP_FORBIDDEN);
        // instructor should work
        $this->loginInstructor();
        $resp = $this->postJson($url, $newAssignmentParams);
        $resp->assertStatus(Status::HTTP_CREATED);
        $resp->assertJson(fn (AssertableJson $json) =>
            $json->where('name', $newAssignment->name)
                 ->where('desc', null)
                 ->where('due', $newAssignment->due->toJson())
                 ->where('open_from', $newAssignment->open_from->toJson())
                 ->where('course_id', $newAssignment->course_id)
                 # values not sent should've been populated with defaults
                 ->where('has_self_eval', 0)
                 ->where('is_published', 0)
                 ->etc()
        );
    }

    public function test_store_must_have_required_params(): void
    {
        $url = $this->getUrl($this->course->id);
        $newAssignment = Assignment::factory()->make([
            'course_id' => $this->course->id
        ]);
        $paramsBase = [
            'name' => $newAssignment->name,
            'due' => $newAssignment->due->toJson(),
            'open_from' => $newAssignment->open_from->toJson(),
            'course_id' => $newAssignment->course_id,
        ];

        $this->loginInstructor();
        $checkParam = function(string $paramName) use($paramsBase, $url) {
            $params = $paramsBase;
            unset($params[$paramName]);
            $resp = $this->postJson($url, $params);
            $resp->assertStatus(Status::HTTP_UNPROCESSABLE_ENTITY);
        };

        $checkParam('name');
        $checkParam('due');
        $checkParam('open_from');
        $checkParam('course_id');
    }

    public function test_store_bool_params(): void
    {
        $url = $this->getUrl($this->course->id);
        $newAssignment = Assignment::factory()->make([
            'course_id' => $this->course->id
        ]);
        # check that invalid bool values are rejected, this is
        # has_self_eval and is_published
        $params = [
            'name' => $newAssignment->name,
            'due' => $newAssignment->due->toJson(),
            'open_from' => $newAssignment->open_from->toJson(),
            'course_id' => $newAssignment->course_id,
            'has_self_eval' => 'invalid',
            'is_published' => '0'
        ];
        $this->loginInstructor();
        $resp = $this->postJson($url, $params);
        $resp->assertStatus(Status::HTTP_UNPROCESSABLE_ENTITY);
        $params['has_self_eval'] = '0';
        $params['is_published'] = 'invalid';
        $resp = $this->postJson($url, $params);
        $resp->assertStatus(Status::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_store_datetime_validation(): void
    {
        $url = $this->getUrl($this->course->id);
        $newAssignment = Assignment::factory()->make([
            'course_id' => $this->course->id,
        ]);
        $paramsBase = [
            'name' => $newAssignment->name,
            'due' => $newAssignment->due->toJson(),
            'open_from' => $newAssignment->open_from->toJson(),
            'course_id' => $newAssignment->course_id,
        ];
        $this->loginInstructor();
        // cannot open the assignment after the due date
        $params = $paramsBase;
        $params['open_from'] = $newAssignment->due->addSecond()->toJson();
        $resp = $this->postJson($url, $params);
        $resp->assertStatus(Status::HTTP_UNPROCESSABLE_ENTITY);
        // open_until cannot be before due
        $params = $paramsBase;
        $params['open_until'] = $newAssignment->due->subSecond()->toJson();
        $resp = $this->postJson($url, $params);
        $resp->assertStatus(Status::HTTP_UNPROCESSABLE_ENTITY);
        // open_until can be same as due
        $params['open_until'] = $newAssignment->due->toJson();
        $resp = $this->postJson($url, $params);
        $resp->assertStatus(Status::HTTP_CREATED);

        // results_from cannot be before due
        $params = $paramsBase;
        $params['results_from'] = $newAssignment->due->subSecond()->toJson();
        $resp = $this->postJson($url, $params);
        $resp->assertStatus(Status::HTTP_UNPROCESSABLE_ENTITY);
        // results_from can be same as due
        $params['results_from'] = $newAssignment->due->toJson();
        $resp = $this->postJson($url, $params);
        $resp->assertStatus(Status::HTTP_CREATED);

        // results_until needs to be after results_from
        $newAssignment->results_from = $newAssignment->due;
        $params = $paramsBase;
        $params['results_until'] =
            $newAssignment->results_from->subSecond()->toJson();
        $resp = $this->postJson($url, $params);
        $resp->assertStatus(Status::HTTP_CREATED);
    }

    public function test_update(): void
    {
        $url = $this->getUrl($this->course->id, $this->assignmentPublished->id);
        $newAssignment = Assignment::factory()->make([
            'course_id' => $this->course->id,
        ]);
        $params = ['name' => $newAssignment->name];

        // block unlogged in access
        $resp = $this->putJson($url, $params);
        $resp->assertStatus(Status::HTTP_UNAUTHORIZED);
        // block student access
        $this->loginStudent();
        $resp = $this->putJson($url, $params);
        $resp->assertStatus(Status::HTTP_FORBIDDEN);
        // instructor should work
        $this->loginInstructor();
        $resp = $this->putJson($url, $params);
        $resp->assertStatus(Status::HTTP_OK);
        $resp->assertJson(fn (AssertableJson $json) =>
            $json->where('name', $newAssignment->name)
                 ->where('id', $this->assignmentPublished->id)
                 ->where('desc', $this->assignmentPublished->desc)
                 ->where('due', $this->assignmentPublished->due->toJson())
                 ->where('open_from',
                     $this->assignmentPublished->open_from->toJson())
                 ->where('course_id', $this->assignmentPublished->course_id)
                 ->where('has_self_eval',
                     $this->assignmentPublished->has_self_eval)
                 ->where('is_published',
                     $this->assignmentPublished->is_published)
                 ->etc()
        );
        // test a more complicated save
        $newAssignment = Assignment::factory()->make([
            'course_id' => $this->course->id,
            'desc' => fake()->paragraphs(2, true),
            'has_self_eval' => !$this->assignmentPublished->has_self_eval,
            'is_published' => !$this->assignmentPublished->is_published,
            'due' => $this->assignmentPublished->due->addDay()->toJson(),
            'open_from' =>
                $this->assignmentPublished->open_from->subDay()->toJson(),
            'open_until' =>
                $this->assignmentPublished->due->addDay()->toJson(),
            'results_from' =>
                $this->assignmentPublished->due->addWeek()->toJson(),
            'results_until' =>
                $this->assignmentPublished->due->addWeek(2)->toJson(),
        ]);

        $params = [
            'name' => $newAssignment->name,
            'desc' => $newAssignment->desc,
            'has_self_eval' => $newAssignment->has_self_eval,
            'is_published' => $newAssignment->is_published,
            'due' => $newAssignment->due->toJson(),
            'open_from' => $newAssignment->open_from->toJson(),
            'open_until' => $newAssignment->open_until->toJson(),
            'results_from' => $newAssignment->results_from->toJson(),
            'results_until' => $newAssignment->results_until->toJson(),
            # should ignore unfiltered params
            'id' => 999999,
            'course_id' => 9999,
        ];
        $resp = $this->putJson($url, $params);
        $resp->assertStatus(Status::HTTP_OK);
        $resp->assertJson(fn (AssertableJson $json) =>
            $json->where('name', $newAssignment->name)
                 ->where('id', $this->assignmentPublished->id)
                 ->where('course_id', $this->assignmentPublished->course_id)
                 ->where('desc', $newAssignment->desc)
                 ->where('due', $newAssignment->due->toJson())
                 ->where('open_from',
                     $newAssignment->open_from->toJson())
                 ->where('open_until',
                     $newAssignment->open_until->toJson())
                 ->where('results_from',
                     $newAssignment->results_from->toJson())
                 ->where('results_until',
                     $newAssignment->results_until->toJson())
                 ->where('has_self_eval',
                     $newAssignment->has_self_eval)
                 ->where('is_published',
                     $newAssignment->is_published)
                 ->etc()
        );
    }

    public function test_destory(): void
    {
        $newAssignment = Assignment::factory()->create([
            'course_id' => $this->course->id,
        ]);
        $url = $this->getUrl($this->course->id, $newAssignment->id);

        // block unlogged in access
        $resp = $this->deleteJson($url);
        $resp->assertStatus(Status::HTTP_UNAUTHORIZED);
        // block student access
        $this->loginStudent();
        $resp = $this->deleteJson($url);
        $resp->assertStatus(Status::HTTP_FORBIDDEN);
        // instructor should work
        $this->loginInstructor();
        $resp = $this->deleteJson($url);
        $resp->assertStatus(Status::HTTP_NO_CONTENT);
    }
}
