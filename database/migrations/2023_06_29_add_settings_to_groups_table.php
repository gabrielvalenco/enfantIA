<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->boolean('competitive_mode')->default(false)->after('created_by');
            $table->boolean('allow_member_invite')->default(false)->after('competitive_mode');
            $table->boolean('allow_member_tasks')->default(false)->after('allow_member_invite');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn(['competitive_mode', 'allow_member_invite', 'allow_member_tasks']);
        });
    }
};
