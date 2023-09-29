<?php

namespace App\Traits;

use App\Models\Course;
use App\Models\Permission;
use App\Models\User;

trait CanCourse
{
    /**
     * Convert course permission template into one for the given course,
     * then return true if the given user has that course permission. Used
     * by policies and controllers.
     */
    public static function canCourse(
        string $permName,
        User $user,
        Course $course
    ): bool
    {
        $perm = Permission::addCourseId($permName, $course->id);
        if ($user->can($perm)) return true;
        return false;
    }

}
