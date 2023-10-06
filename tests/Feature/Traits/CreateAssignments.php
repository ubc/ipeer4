<?php
namespace Tests\Feature\Traits;

use App\Models\Assignment;
use App\Models\Course;

trait CreateAssignments
{
    public $assignments = [];
    public $assignmentUnpublished;
    public $assignmentPublished;

    public function createAssignments(Course $course) {
        $this->assignmentUnpublished = Assignment::factory()->create([
            'course_id' => $course->id
        ]);
        $this->assignmentUnpublished->refresh();
        $this->assignmentPublished = Assignment::factory()->create([
            'course_id' => $course->id,
            'is_published' => true
        ]);
        $this->assignmentPublished->refresh();
        $this->assignments = [$this->assignmentUnpublished,
                              $this->assignmentPublished];
    }
}

