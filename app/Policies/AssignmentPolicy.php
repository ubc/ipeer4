<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\User;
use App\Traits\CanCourse;

use Illuminate\Auth\Access\Response;

class AssignmentPolicy
{
    use CanCourse;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Course $course): bool
    {
        return (
            $this->canCourse('courseId.viewPublishedAssignment', $user, $course)
            ||
            $this->canCourse('courseId.viewUnpublishedAssignment', $user, $course)
        );
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(
        User $user,
        Course $course,
        Assignment $assignment
    ): bool {
        if ($this->canCourse('courseId.viewUnpublishedAssignment',
                             $user, $course))
            return true;
        if ($assignment->is_published)
            return $this->canCourse('courseId.viewPublishedAssignment',
                                    $user, $course);
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Course $course): bool
    {
        return $this->canCourse('courseId.editAssignment', $user, $course);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Course $course): bool
    {
        return $this->canCourse('courseId.editAssignment', $user, $course);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Course $course): bool
    {
        return $this->canCourse('courseId.editAssignment', $user, $course);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Assignment $assignment): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Assignment $assignment): bool
    {
        //
    }
}
