<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
        });

        $usedSlugs = [];

        DB::table('teams')
            ->select(['id', 'name'])
            ->orderBy('id')
            ->each(function (object $team) use (&$usedSlugs): void {
                $baseSlug = Str::slug((string) $team->name) ?: 'team';
                $slug = $baseSlug;
                $suffix = 1;

                while (in_array($slug, $usedSlugs, true)) {
                    $slug = $baseSlug.'-'.$suffix;
                    $suffix++;
                }

                $usedSlugs[] = $slug;

                DB::table('teams')
                    ->where('id', $team->id)
                    ->update(['slug' => $slug]);
            });

        Schema::table('teams', function (Blueprint $table) {
            $table->unique('slug');
        });

        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE teams ALTER COLUMN slug SET NOT NULL');
        }

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE teams MODIFY slug VARCHAR(255) NOT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
