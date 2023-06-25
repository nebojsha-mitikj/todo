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
        Schema::table('users', function (Blueprint $table) {
            $table->date('last_daily_goal_reach_date')
                ->after('email')
                ->nullable();
            $table->unsignedInteger('daily_goal_reach_counter')
                ->after('email')
                ->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('last_daily_goal_reach_date');
            $table->dropColumn('daily_goal_reach_counter');
        });
    }
};
