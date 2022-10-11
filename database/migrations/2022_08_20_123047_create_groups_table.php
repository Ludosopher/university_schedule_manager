<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faculties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('study_degrees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('study_programs', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->unsignedBigInteger('faculty_id');
            $table->unsignedBigInteger('study_degree_id');
            $table->foreign('faculty_id')->references('id')->on('faculties')->onDelete('cascade');
            $table->foreign('study_degree_id')->references('id')->on('study_degrees')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('study_orientations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('study_program_id');
            $table->foreign('study_program_id')->references('id')->on('study_programs')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('study_forms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('faculty_id');
            $table->unsignedBigInteger('study_program_id');
            $table->unsignedBigInteger('study_orientation_id');
            $table->unsignedBigInteger('study_degree_id');
            $table->unsignedBigInteger('study_form_id');
            $table->unsignedBigInteger('course');
            $table->unsignedBigInteger('size')->nullable();
            $table->foreign('faculty_id')->references('id')->on('faculties')->onDelete('cascade');
            $table->foreign('study_program_id')->references('id')->on('study_programs')->onDelete('cascade');
            $table->foreign('study_orientation_id')->references('id')->on('study_orientations')->onDelete('cascade');
            $table->foreign('study_degree_id')->references('id')->on('study_degrees')->onDelete('cascade');
            $table->foreign('study_form_id')->references('id')->on('study_forms')->onDelete('cascade');
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
        Schema::dropIfExists('faculties');
        Schema::dropIfExists('study_degrees');
        Schema::dropIfExists('study_programs');
        Schema::dropIfExists('study_orientations');
        Schema::dropIfExists('study_forms');
        Schema::dropIfExists('groups');
    }
}
