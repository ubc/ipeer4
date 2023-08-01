<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

use Symfony\Component\HttpFoundation\Response as Status;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\AbstractApiController;
use App\Http\Requests\Paginated\CoursePaginatedRequest;
use App\Models\Course;


class CourseController extends AbstractApiController
{
    public function __construct()
    {
        // tell Laravel to use UserPolicy to protect access to these methods
        $this->authorizeResource(Course::class, 'course');
    }

    /**
     * Get a list of courses.
     */
    public function index(CoursePaginatedRequest $request)
    {
        $user = auth()->user();
        if ($user->can('user.admin'))
            return $this->paginatedIndex($request, Course::query(), ['name']);
        // regular users can only get their enroled courses
        return $this->paginatedIndex($request, $user->courses(), ['name']);

    }

    /**
     * Get the specified course.
     */
    public function show(Course $course)
    {
        return $course;
    }

    /**
     * Store a new user.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
        ]);
        $course = new Course;
        $course->name = $data['name'];
        $course->save();
        return $course;
    }

    /**
     * Update the specified course.
     */
    public function update(Request $request, Course $course)
    {
        $courseInfo = $request->validate([
            'name' => ['nullable', 'string'],
        ]);
        // TODO: This means that courses can't empty out fields, e.g. name/email
        // not sure if this is a concern
        if (isset($courseInfo['name']) && $courseInfo['name'] != $course->name) {
            $course->name = $courseInfo['name'];
        }
        $course->save();
        return $course;
    }

    /**
     * Delete specified course.
     */
    public function destroy(string $id)
    {
        $course = Course::find($id);
        if ($course) {
            if ($course->delete()) return response()->noContent(); // HTTP 204
            abort(Status::HTTP_CONFLICT, 'Course in the process of being deleted or already deleted');
        }
        abort(Status::HTTP_NOT_FOUND, 'Course already deleted / does not exist');
    }
}
