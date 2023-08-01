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
        $existingPermission = self::getPermission(
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
        return str_replace('courseId', "courseId.$courseId", $this->name);
    }
}
