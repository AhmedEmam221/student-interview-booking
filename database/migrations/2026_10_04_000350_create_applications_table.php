<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_number')->unique();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->string('major'); // CS, BIS, Language and Translation
            $table->string('academic_year'); // 1st, 2nd, 3rd, 4th
            $table->unsignedBigInteger('track_id');
            $table->foreign('track_id')->references('id')->on('tracks')->onDelete('restrict');
            
            $table->unsignedBigInteger('schedule_slot_id')->nullable();
            $table->foreign('schedule_slot_id')->references('id')->on('schedule_slots')->onDelete('set null');
            
            $table->unsignedBigInteger('interview_board_member_id')->nullable();
            $table->foreign('interview_board_member_id')->references('id')->on('board_members')->onDelete('set null');
            
            $table->unsignedBigInteger('referral_board_member_id')->nullable();
            $table->foreign('referral_board_member_id')->references('id')->on('board_members')->onDelete('set null');
            
            $table->boolean('joined_activity_before')->default(false);
            $table->string('activity_name')->nullable();
            $table->text('hobbies')->nullable();
            $table->text('comments')->nullable();
            
            $table->enum('status', ['Pending', 'Accepted', 'Waiting', 'Rejected'])->default('Pending');
            
            $table->timestamps();
            
            $table->index(['track_id', 'status']);
            $table->index(['schedule_slot_id']);
            $table->index(['interview_board_member_id']);
            $table->index(['referral_board_member_id']);
            $table->index(['status']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('applications');
    }
}