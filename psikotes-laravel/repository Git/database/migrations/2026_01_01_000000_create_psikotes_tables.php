<?php

use App\Support\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(Schema::SQL);
    }

    public function down(): void
    {
        foreach (['settings', 'logs', 'results', 'sessions', 'users'] as $t) {
            DB::unprepared("DROP TABLE IF EXISTS {$t}");
        }
    }
};
