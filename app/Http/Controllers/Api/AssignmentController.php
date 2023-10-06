<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Controller;
use App\Http\Requests\Paginated\AssignmentPaginatedRequest;
use App\Http\Requests\StoreAssignmentRequest;
use App\Http\Requests\UpdateAssignmentRequest;
use App\Models\Assignment;
use App\Models\Course;
use App\Rules\BoolStr;
use App\Traits\CanCourse;
use App\Traits\Controller\PaginatedIndex;

class AssignmentController extends Controller
{
    use PaginatedIndex, CanCourse;

    public function index(AssignmentPaginatedRequest $request, Course $course)
    {
        $this->authorize('viewAny', [Assignment::class, $course]);
        $user = auth()->user();
        $query = null;
        if ($this->canCourse('courseId.viewUnpublishedAssignment',
                             auth()->user(), $course)
        ) {
            $query = $course->assignments();
        }
        else {
            // user can only see published assignments
            $query = $course->assignments()->where('is_published', true);
        }

        return $this->paginatedIndex($request, $query, ['name', 'desc']);
    }

    public function show(Course $course, Assignment $assignment)
    {
        $this->authorize('view', [Assignment::class, $course, $assignment]);
        return $assignment;
    }

    public function store(StoreAssignmentRequest $request, Course $course)
    {
        $assignment = new Assignment($request->validated());
        $assignment->save();
        $assignment->refresh();
        return $assignment;
    }

    public function update(
        UpdateAssignmentRequest $request,
        Course $course,
        Assignment $assignment
    ) {
        $assignment->update($request->validated());
        $assignment->save();
        return $assignment;
    }

    public function destroy(Course $course, Assignment $assignment)
    {
        $this->authorize('delete', [Assignment::class, $course]);
        $assignment->delete();
        return response()->noContent();
    }

}
