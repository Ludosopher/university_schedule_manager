<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeachersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('academic_degrees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name');
            $table->timestamps();
        });

        Schema::create('lesson_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_notation');
            $table->timestamps();
        });

        Schema::create('week_days', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_notation');
            $table->timestamps();
        });

        Schema::create('weekly_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('faculty_id');
            $table->foreign('faculty_id')->references('id')->on('faculties')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('professional_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name');
            $table->unsignedInteger('level');
            $table->timestamps();
        });

        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('level');
            $table->timestamps();
        });

        Schema::create('class_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->time('start');
            $table->time('end');
            $table->timestamps();
        });

        Schema::create('study_periods', function (Blueprint $table) {
            $table->id();
            $table->string('year');
            $table->string('season');
            $table->date('start');
            $table->date('micro_end');
            $table->date('end');
            $table->date('session_start');
            $table->date('session_end');
            $table->timestamps();
        });

        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('patronymic');
            $table->enum('gender', ['male', 'female', 'not_specified']);
            $table->unsignedInteger('birth_year');
            $table->string('phone');
            $table->string('email');
            $table->unsignedBigInteger('faculty_id');
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('professional_level_id');
            $table->unsignedBigInteger('position_id');
            $table->unsignedBigInteger('academic_degree_id')->nullable();
            $table->foreign('faculty_id')->references('id')->on('faculties')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->foreign('professional_level_id')->references('id')->on('professional_levels')->onDelete('cascade');
            $table->foreign('position_id')->references('id')->on('positions')->onDelete('cascade');
            $table->foreign('academic_degree_id')->references('id')->on('academic_degrees')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('lesson_rooms', function(Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->unsignedInteger('capacity');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('lesson_type_id');
            $table->unsignedBigInteger('week_day_id');
            $table->unsignedBigInteger('weekly_period_id');
            $table->unsignedBigInteger('class_period_id');
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('lesson_room_id');
            $table->unsignedBigInteger('study_period_id')->nullable();
            $table->boolean('in_micro_pereiod')->nullable();
            $table->date('date')->nullable();
            $table->foreign('lesson_type_id')->references('id')->on('lesson_types')->onDelete('cascade');
            $table->foreign('week_day_id')->references('id')->on('week_days')->onDelete('cascade');
            $table->foreign('weekly_period_id')->references('id')->on('weekly_periods')->onDelete('cascade');
            $table->foreign('class_period_id')->references('id')->on('class_periods')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
            $table->foreign('lesson_room_id')->references('id')->on('lesson_rooms')->onDelete('cascade');
            $table->foreign('study_period_id')->references('id')->on('study_periods')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('teacher_user', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('teacher_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
        });

        Schema::create('group_user', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('group_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
        });

        Schema::create('group_lesson', function (Blueprint $table) {
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('lesson_id');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('academic_degrees');
        Schema::dropIfExists('lesson_types');
        Schema::dropIfExists('week_days');
        Schema::dropIfExists('weekly_periods');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('professional_levels');
        Schema::dropIfExists('positions');
        Schema::dropIfExists('class_periods');
        Schema::dropIfExists('study_periods');
        Schema::dropIfExists('teachers');
        Schema::dropIfExists('lesson_rooms');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('teacher_user');
        Schema::dropIfExists('group_user');
        Schema::dropIfExists('group_lesson');
    }
}
