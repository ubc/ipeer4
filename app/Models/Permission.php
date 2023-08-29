<?php
namespace App\Models;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    // You might set a public property like guard_name or connection, or override other Eloquent Model methods/properties
    public function getCoursePermission(int $courseId): self
    {
        // non-template permissions can be used directly
        if (!$this->is_template) return $this;
        // look for existing
        $existingPermission = static::getPermission(
            ['name' => $this->getNameWithCourseId($courseId)]);
        // need to create new
        if ($existingPermission) return $existingPermission;
        $newPermission = $this->replicate()->fill([
            'name' => $this->getNameWithCourseId($courseId),
            'is_template' => false,
        ]);
        $newPermission->save();
        return $newPermission;
    }

    private function getNameWithCourseId(int $courseId): string
    {
        return static::addCourseId($this->name, $courseId);
    }

    /**
     * Helper that looks for 'courseId' in the string and replace it with
     * 'courseId.1', where 1 is the courseId given.
     */
    public static function addCourseId(string $name, int $courseId): string
    {
        return str_replace('courseId', "courseId.$courseId", $name);
    }
}
