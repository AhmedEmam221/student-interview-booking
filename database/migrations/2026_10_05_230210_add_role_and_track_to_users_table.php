<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoleAndTrackToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'highboard', 'board'])->default('admin')->after('is_admin');
            $table->unsignedBigInteger('track_id')->nullable()->after('role');
            $table->foreign('track_id')->references('id')->on('tracks')->onDelete('set null');
            $table->boolean('is_active')->default(true)->after('track_id');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['track_id']);
            $table->dropColumn(['role', 'track_id', 'is_active']);
        });
    }
}