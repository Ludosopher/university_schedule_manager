<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReplacementRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('replacement_request_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('replacement_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('replaceable_lesson_id');
            $table->unsignedBigInteger('replacing_lesson_id');
            $table->boolean('is_regular')->default(false);
            $table->date('replaceable_date')->nullable();
            $table->date('replacing_date')->nullable();
            $table->boolean('is_sent')->default(false);
            $table->boolean('is_agreed')->default(false);
            $table->boolean('is_permitted')->default(false);
            $table->boolean('is_cancelled')->default(false);
            $table->boolean('is_declined')->default(false);
            $table->boolean('is_not_permitted')->default(false);
            $table->unsignedBigInteger('status_id')->default(1);
            $table->unsignedBigInteger('initiator_id');
            $table->foreign('replaceable_lesson_id')->references('id')->on('lessons')->onDelete('cascade');
            $table->foreign('replacing_lesson_id')->references('id')->on('lessons')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('replacement_request_statuses')->onDelete('cascade');
            $table->foreign('initiator_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('replacement_request_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('replacement_request_id')->nullable();
            $table->unsignedBigInteger('author_id');
            $table->text('body');
            $table->foreign('replacement_request_id')->references('id')->on('replacement_requests')->onDelete('cascade');
            $table->foreign('author_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('replacement_request_statuses');
        Schema::dropIfExists('replacement_requests');
        Schema::dropIfExists('messages');
    
    }
}
