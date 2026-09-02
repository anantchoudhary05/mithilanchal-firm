<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! DB::table('moonshine_user_roles')->where('name', 'Author')->exists()) {
            DB::table('moonshine_user_roles')->insert([
                'name' => 'Author',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('moonshine_users', function (Blueprint $table): void {
            if (! Schema::hasColumn('moonshine_users', 'bio')) {
                $table->text('bio')->nullable()->after('avatar');
            }
        });

        Schema::table('blogs', function (Blueprint $table): void {
            if (! Schema::hasColumn('blogs', 'author_id')) {
                $table->foreignId('author_id')
                    ->nullable()
                    ->after('author_profile')
                    ->constrained('moonshine_users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table): void {
            if (Schema::hasColumn('blogs', 'author_id')) {
                $table->dropConstrainedForeignId('author_id');
            }
        });

        Schema::table('moonshine_users', function (Blueprint $table): void {
            if (Schema::hasColumn('moonshine_users', 'bio')) {
                $table->dropColumn('bio');
            }
        });

        DB::table('moonshine_user_roles')->where('name', 'Author')->delete();
    }
};
