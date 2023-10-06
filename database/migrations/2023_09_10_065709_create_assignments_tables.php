<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->comment('Assignment you give to students in a course');

            $table->id();
            $table->text('name');
            $table->text('desc')->nullable();
            $table->boolean('has_self_eval')->default(false);
            $table->boolean('is_published')->default(false);
            $table->timestampTz('due');
            $table->timestampTz('open_from');
            $table->timestampTz('open_until')->nullable()
                  ->comment('defaults to due date if empty, allows late submissions if set');
            $table->timestampTz('results_from')->nullable()
                  ->comment('defaults to due date if empty');
            $table->timestampTz('results_until')->nullable()
                  ->comment('will allow access to results forever if empty');

            $table->unsignedBigInteger('course_id');
            $table->foreign('course_id')
                  ->references('id')->on('courses')->onDelete('cascade');

            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent()
                                             ->useCurrentOnUpdate();
        });
        Schema::create('assignment_questions', function (Blueprint $table) {
            $table->comment('Questions associated with an assignment');

            $table->id();
            $table->enum('type', ['sentence', 'paragraph'])
                  ->default('sentence');
            $table->boolean('is_required')->default(true);
            $table->text('prompt');
            $table->text('instruction')->nullable();

            $table->unsignedBigInteger('assignment_id');
            $table->foreign('assignment_id')
                  ->references('id')->on('assignments')->onDelete('cascade');

            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent()
                                             ->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_questions');
        Schema::dropIfExists('assignments');
    }
};
