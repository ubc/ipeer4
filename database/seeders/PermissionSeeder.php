<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Role;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        // Two tiers of roles and permissions: System or course-specific. Added
        // a boolean column 'is_system' to indicate system role/permissions.
        // Course-specific permissions require a user to be enroled in the
        // course they're performing actions in.

        // System roles are assigned using laravel-permission's methods.
        // Course roles are assigned in the course_user table, we're not
        // using the laravel-permissions system for that.

        // --- SYSTEM ROLES --- 
        $roleAdmin = Role::create([
            'name' => 'admin',
            'desc' => 'System Role Admin',
            'is_system' => true
        ]);
        // system permissions, can only be assigned to system roles
        // while we don't need to explicitly assign them to admins since we're
        // allowing admins to do anything, they still need to be created so
        // we can check against them
        $courseAdmin = Permission::create([
            'name' => 'course.admin',
            'desc' => 'Can admin all courses',
        ]);
        $userAdmin = Permission::create([
            'name' => 'user.admin',
            'desc' => 'Can admin all users',
        ]);

        $roleAdmin->syncPermissions([$courseAdmin, $userAdmin]);

        // --- COURSE ROLES ----
        $roleInstructor = Role::create([
            'name' => 'instructor',
            'desc' => 'Default settings for a course instructor',
            'is_template' => true
        ]);
        $roleStudent = Role::create([
            'name' => 'student',
            'desc' => 'Default settings for a course student',
            'is_template' => true
        ]);
        // create course template permissions. These are not used directly. We
        // create a copy, replace 'courseId' with 'courseId.id' to lock it to
        // down to a specific course, then use the copy.
        $manageCourseInfo = Permission::create([
            'name' => 'courseId.manageInfo',
            'desc' => 'Can edit course info',
            'is_template' => true
        ]);
        $manageEnrolment = Permission::create([
            'name' => 'courseId.manageEnrolment',
            'desc' => 'Can see and modify user enrolments in their courses',
            'is_template' => true
        ]);
        // assign permissions, note admin's access to everything is handled
        // separately, so we don't need to add every permission to it
        $roleInstructor->syncPermissions([
            $manageCourseInfo,
            $manageEnrolment,
        ]);
        $roleStudent->syncPermissions([
        ]);
    }
}
