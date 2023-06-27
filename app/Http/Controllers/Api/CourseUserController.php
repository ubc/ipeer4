<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Symfony\Component\HttpFoundation\Response as Status;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\AbstractApiController;
use App\Http\Requests\Paginated\CourseUserPaginatedRequest;
use App\Models\Course;
use App\Models\User;
use App\Rules\BoolStr;


class CourseUserController extends AbstractApiController
{
    public function index(CourseUserPaginatedRequest $request, Course $course)
    {
        return $this->paginatedIndex($request, $course->users(), ['name']);
    }

    /**
     * Can't think of a use case for this, so not implemented for now.
     */
    public function show(Request $request, Course $course, User $user)
    {
        abort(Status::HTTP_NOT_IMPLEMENTED, 'Not implemented');
    }

    public function store(Request $request, Course $course)
    {
        $data = $request->validate([
            'userIds' => 'required|exists:App\Models\User,id',
        ]);
        $course->users()->attach($data['userIds']);
        return response()->noContent();
    }

    /**
     * Can't think of a use case for editing an enrolment, since we only
     * want to remove or add users to a course, so not implemented.
     */
    public function update(Request $request, Course $course, User $user)
    {
        abort(Status::HTTP_NOT_IMPLEMENTED, 'Not implemented');
    }

    /**
     * Delete specified enrolment.
     */
    public function destroy(Request $request, Course $course, User $user)
    {
        $courseName = $course->name;
        $userName = $user->username;
        $hasEntry = $course->users()->where('user_id', $user->id)->exists();
        if ($hasEntry) {
            if ($course->users()->detach($user->id))
                return response()->noContent(); // HTTP 204
            abort(Status::HTTP_CONFLICT, "User '$userName' is already being removed from course '$courseName'");
        }
        abort(Status::HTTP_NOT_FOUND, "User '$userName' is not found in course '$courseName'");
    }
}
