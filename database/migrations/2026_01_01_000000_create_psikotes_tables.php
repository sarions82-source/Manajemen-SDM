<?php

use App\Support\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach (Schema::MYSQL as $sql) {
            DB::unprepared($sql);
        }
    }

    public function down(): void
    {
        foreach (['iq_files', 'links', 'alumni_sessions', 'alumni', 'attempts', 'settings', 'logs', 'results', 'sessions', 'users'] as $t) {
            DB::unprepared("DROP TABLE IF EXISTS {$t}");
        }
    }
};
