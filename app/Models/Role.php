<?php
namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Spatie\Permission\Models\Role as SpatieRole;

use App\Models\Course;

class Role extends SpatieRole
{
    // You might set a public property like guard_name or connection, or override other Eloquent Model methods/properties

    // DEFINE RELATIONSHIP
 
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
    
    // PUBLIC HELPER METHODS

    public function getCourseRole(int $courseId): self
    {
        if ($this->is_system) throw new UnexpectedValueException(
            'Cannot use a system role as a course role');
        // non-template and non-system role, just use directly
        if (!$this->is_template) return $this;
        // look for an existing one
        $existingRole = self::findByParam(
            ['name' => $this->getCourseSpecificName($courseId)]);
        if ($existingRole) return $existingRole;
        // need to create a new one
        $newRole = $this->replicate()->fill([
            'name' => $this->getCourseSpecificName($courseId),
            'is_template' => false,
            'course_id' => $courseId,
        ]);
        // assign the new role the same permissions as the template
        $permissions = $this->permissions()->get();
        DB::transaction(function () use ($newRole, $permissions, $courseId) {
            $newPermissions = [];
            foreach ($permissions as $permission) {
                $newPermissions[] = $permission->getCoursePermission($courseId);
            }
            $newRole->save();
            $newRole->syncPermissions($newPermissions);
        });
        return $newRole;
    }

    // PUBLIC STATIC HELPER METHODS

    public static function getTemplate(string $roleName): self
    {
        $role = self::where('name', $roleName)
                    ->where('is_template', true)->firstOrFail();
        return $role;
    }

    public static function getSystem(string $roleName): self
    {
        $role = self::where('name', $roleName)
                    ->where('is_system', true)->firstOrFail();
        return $role;
    }

    // PRIVATE HELPER METHODS
    private function getCourseSpecificName(int $courseId): string
    {
        return "courseId.$courseId." . $this->name;
    }

}
