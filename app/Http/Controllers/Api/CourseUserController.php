<?php

namespace App\Http\Controllers\Api;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

use Symfony\Component\HttpFoundation\Response as Status;

use App\Http\Controllers\Controller;
use App\Http\Requests\Paginated\CourseUserPaginatedRequest;
use App\Models\Course;
use App\Models\CourseUser;
use App\Models\User;
use App\Rules\BoolStr;
use App\Traits\Controller\PaginatedIndex;

class CourseUserController extends Controller
{
    use PaginatedIndex;

    public function index(CourseUserPaginatedRequest $request, Course $course)
    {
        $this->authorize('viewAny', [CourseUser::class, $course]);
        # Using eager loading's with() doesn't allow sorting on the relationship
        # fields, so we have to do a join
        $query = CourseUser::select([
                               'course_user.*',
                               'users.name',
                               'users.username',
                               'users.email',
                               'roles.display_name AS role_name'
                           ])
                           ->join('users', 'users.id', '=', 'course_user.user_id')
                           ->join('roles', 'roles.id', '=', 'course_user.role_id');
        return $this->paginatedIndex($request, $query, ['name']);
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
        $this->authorize('create', [CourseUser::class, $course]);
        $data = $request->validate([
            'userIds' => 'required|exists:App\Models\User,id',
            'roleId' => ['required',
                // make sure the role exists and is for the right course
                Rule::exists('App\Models\Role', 'id')->where(
                    function (Builder $query) use ($course) {
                        return $query->where('course_id', $course->id);
                    }),
            ],
        ]);
        $course->users()->attach($data['userIds'],
                                 ['role_id' => $data['roleId']]);
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
        $this->authorize('delete', [CourseUser::class, $course]);
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
