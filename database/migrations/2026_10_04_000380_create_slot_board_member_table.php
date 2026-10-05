<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSlotBoardMemberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('slot_board_member', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('schedule_slot_id');
            $table->foreign('schedule_slot_id')->references('id')->on('schedule_slots')->onDelete('cascade');
            $table->unsignedBigInteger('board_member_id');
            $table->foreign('board_member_id')->references('id')->on('board_members')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['schedule_slot_id', 'board_member_id']);
            $table->index(['board_member_id', 'schedule_slot_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('slot_board_member');
    }
}