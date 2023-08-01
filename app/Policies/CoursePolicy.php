<?php

namespace App\Policies;

use Illuminate\Support\Facades\Log;

use App\Models\Course;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CoursePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // anybody can view at least their enroled courses
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Course $course): bool
    {
        if ($user->can('course.admin') ||
            $user->courses()->where('course_id', $course->id)->exists())
            return true;
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->can('course.admin')) return true;
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Course $course): bool
    {
        $courseRole = $this->getCourseRole($user, $course);
        if ($user->can('course.admin')) return true;
        if ($courseRole &&
            $courseRole->hasPermissionTo('courseId.'. $course->id .'.manageInfo')) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Course $course): bool
    {
        if ($user->can('course.admin')) return true;
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Course $course): bool
    {
        if ($user->can('course.admin')) return true;
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Course $course): bool
    {
        if ($user->can('course.admin')) return true;
        return false;
    }

    // HELPERS
    protected function getCourseRole(User $user, Course $course): ?Role
    {
        return $user->courseRoles()->wherePivot('course_id', $course->id)
                                   ->first();
    }
}
