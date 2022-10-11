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
            $table->unsignedInteger('number');
            $table->time('start');
            $table->time('end');
            $table->timestamps();
        });
        
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('patronymic');
            $table->enum('gender', ['мужчина', 'женщина', 'не указано']);
            $table->unsignedInteger('birth_year');
            $table->string('phone');
            $table->string('email');
            $table->unsignedBigInteger('faculty_id');
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('professional_level_id');
            $table->unsignedBigInteger('position_id');
            $table->foreign('faculty_id')->references('id')->on('faculties')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->foreign('professional_level_id')->references('id')->on('professional_levels')->onDelete('cascade');
            $table->foreign('position_id')->references('id')->on('positions')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('week_day_id');
            $table->unsignedBigInteger('weekly_period_id');
            $table->unsignedBigInteger('class_period_id');
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('teacher_id');
            $table->foreign('week_day_id')->references('id')->on('week_days')->onDelete('cascade');
            $table->foreign('weekly_period_id')->references('id')->on('weekly_periods')->onDelete('cascade');
            $table->foreign('class_period_id')->references('id')->on('class_periods')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('week_days');
        Schema::dropIfExists('weekly_periods');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('professional_levels');
        Schema::dropIfExists('positions');
        Schema::dropIfExists('class_periods');
        Schema::dropIfExists('teachers');
        Schema::dropIfExists('lessons');
    }
}
