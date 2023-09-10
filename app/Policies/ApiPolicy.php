<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\Permission;
use App\Models\User;

class ApiPolicy
{
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
