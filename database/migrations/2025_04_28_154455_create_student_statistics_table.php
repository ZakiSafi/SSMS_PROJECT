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
        Schema::create('student_statistics', function (Blueprint $table) {
            $table->id();
            $table->year('academic_year');
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->string('classroom')->default('first');
            $table->enum('shift', ['day', 'night'])->default('day');
            $table->enum('season', ['spring', 'autumn', 'summer', 'winter'])->default('spring');
            $table->tinyInteger('semester_number')->default(1); // 1 for first semester, 2 for second semester
            $table->integer('male_total')->default(0); // Total number of male students
            $table->integer('female_total')->default(0); // Total number of female students
            $table->enum('student_type', ['new', 'current', 'graduated'])->default('new');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_statistics');
    }
};
