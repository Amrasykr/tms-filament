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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->nullable()->constrained('classes')->onDelete('set null');
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->onDelete('set null');
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->onDelete('set null');
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->onDelete('set null');
            $table->foreignId('schedule_time_id')->nullable()->constrained('schedules_times')->onDelete('set null'); 
            $table->boolean('is_repeating')->default(false);
            $table->unsignedInteger('number_of_sessions')->nullable();
            $table->decimal('attendance_weight', 5, 2)->default(25); 
            $table->decimal('task_weight', 5, 2)->default(25); 
            $table->decimal('midterm_weight', 5, 2)->default(25); 
            $table->decimal('final_exam_weight', 5, 2)->default(25); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
