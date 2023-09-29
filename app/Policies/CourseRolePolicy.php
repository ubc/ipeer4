<?php

namespace App\Policies;

use App\Models\CourseRole;
use App\Models\Course;
use App\Models\Permission;
use App\Models\User;
use App\Traits\CanCourse;

use Illuminate\Auth\Access\Response;

class CourseRolePolicy
{
    use CanCourse;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Course $course): bool
    {
        return $this->canCourse('courseId.manageEnrolment', $user, $course);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CourseRole $courseRole): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CourseRole $courseRole): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CourseRole $courseRole): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CourseRole $courseRole): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CourseRole $courseRole): bool
    {
        //
    }
}
