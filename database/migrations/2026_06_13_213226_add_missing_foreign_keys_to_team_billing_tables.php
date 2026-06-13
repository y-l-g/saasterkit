<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        $this->ensureNoOrphans();

        Schema::table('subscriptions', function (Blueprint $table): void {
            $table->foreign('team_id', 'subscriptions_team_id_fk')
                ->references('id')
                ->on('teams')
                ->restrictOnDelete();
        });

        Schema::table('subscription_items', function (Blueprint $table): void {
            $table->foreign('subscription_id', 'subscription_items_subscription_id_fk')
                ->references('id')
                ->on('subscriptions')
                ->cascadeOnDelete();
        });

        Schema::table('team_user', function (Blueprint $table): void {
            $table->foreign('team_id', 'team_user_team_id_fk')
                ->references('id')
                ->on('teams')
                ->cascadeOnDelete();

            $table->foreign('user_id', 'team_user_user_id_fk')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('team_user', function (Blueprint $table): void {
            $table->dropForeign('team_user_user_id_fk');
            $table->dropForeign('team_user_team_id_fk');
        });

        Schema::table('subscription_items', function (Blueprint $table): void {
            $table->dropForeign('subscription_items_subscription_id_fk');
        });

        Schema::table('subscriptions', function (Blueprint $table): void {
            $table->dropForeign('subscriptions_team_id_fk');
        });
    }

    private function ensureNoOrphans(): void
    {
        $orphans = [
            'subscriptions.team_id' => DB::table('subscriptions')
                ->leftJoin('teams', 'subscriptions.team_id', '=', 'teams.id')
                ->whereNull('teams.id')
                ->count(),
            'subscription_items.subscription_id' => DB::table('subscription_items')
                ->leftJoin('subscriptions', 'subscription_items.subscription_id', '=', 'subscriptions.id')
                ->whereNull('subscriptions.id')
                ->count(),
            'team_user.team_id' => DB::table('team_user')
                ->leftJoin('teams', 'team_user.team_id', '=', 'teams.id')
                ->whereNull('teams.id')
                ->count(),
            'team_user.user_id' => DB::table('team_user')
                ->leftJoin('users', 'team_user.user_id', '=', 'users.id')
                ->whereNull('users.id')
                ->count(),
        ];

        $dirtyOrphans = array_filter($orphans, fn (int $count): bool => $count > 0);

        if ($dirtyOrphans !== []) {
            throw new RuntimeException(
                'Cannot add team billing foreign keys while orphaned rows exist: '.
                json_encode($dirtyOrphans, JSON_THROW_ON_ERROR)
            );
        }
    }
};
