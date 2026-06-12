<?php

declare(strict_types=1);

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
        Schema::table('team_invitations', function (Blueprint $table) {
            $table->dropUnique('team_invitations_team_id_email_unique');
            $table->index(['team_id', 'email']);
        });

        Schema::table('team_invitations', function (Blueprint $table) {
            $table->timestamp('accepted_at')->nullable()->after('role')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_invitations', function (Blueprint $table) {
            $table->dropIndex('team_invitations_accepted_at_index');
            $table->dropIndex('team_invitations_team_id_email_index');
            $table->dropColumn('accepted_at');
        });

        Schema::table('team_invitations', function (Blueprint $table) {
            $table->unique(['team_id', 'email']);
        });
    }
};
