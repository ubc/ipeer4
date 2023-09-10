<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Symfony\Component\HttpFoundation\Response as Status;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\AbstractApiController;
use App\Models\Course;
use App\Models\Role;


class CourseRoleController extends AbstractApiController
{
    /**
     * List all course roles.
     *
     * Not using a paginated request since there should only be a small
     * number of course roles that can all be sent in one request.
     */
    public function index(Request $request, Course $course)
    {
        $this->authorize('viewAny', [CourseRole::class, $course]);
        return Role::getAllCourseRoles($course->id);
    }

    /**
     * Can implement later for custom roles
     */
    public function show(Request $request, Course $course, Role $role)
    {
        abort(Status::HTTP_NOT_IMPLEMENTED, 'Not implemented');
    }

    public function store(Request $request, Course $course)
    {
        abort(Status::HTTP_NOT_IMPLEMENTED, 'Not implemented');
    }

    /**
     * Can implement later for custom roles
     */
    public function update(Request $request, Course $course, Role $role)
    {
        abort(Status::HTTP_NOT_IMPLEMENTED, 'Not implemented');
    }

    /**
     * Can implement later for custom roles
     */
    public function destroy(Request $request, Course $course, Role $role)
    {
        abort(Status::HTTP_NOT_IMPLEMENTED, 'Not implemented');
    }
}
