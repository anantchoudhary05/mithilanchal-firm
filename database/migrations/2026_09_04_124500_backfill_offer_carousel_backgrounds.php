<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('homepage_sections')
            ->where('type', 'offer')
            ->where(function ($query): void {
                $query->whereNull('background_image')->orWhere('background_image', '');
            })
            ->update([
                'background_image' => 'assests/img/hq-roast-bihar.jpg',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        //
    }
};
