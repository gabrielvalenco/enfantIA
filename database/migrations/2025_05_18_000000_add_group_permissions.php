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
        Schema::table('groups', function (Blueprint $table) {
            $table->boolean('member_can_manage_tasks')->default(false);
            $table->boolean('member_can_delegate_tasks')->default(false);
            $table->boolean('member_can_invite')->default(false);
            $table->string('group_theme')->default('default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn([
                'member_can_manage_tasks',
                'member_can_delegate_tasks',
                'member_can_invite',
                'group_theme'
            ]);
        });
    }
};
