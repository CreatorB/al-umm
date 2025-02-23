<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddTitleToSchedulesTable extends Migration
{
    public function up()
    {
        // Cek apakah kolom title sudah ada
        if (!Schema::hasColumn('schedules', 'title')) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->string('title')->after('id')->nullable();
            });

            // Set default title untuk data yang sudah ada
            DB::table('schedules')->whereNull('title')->update([
                'title' => DB::raw("CONCAT('Schedule ', id)")
            ]);
        }

        // Update existing titles yang masih null
        DB::table('schedules')->whereNull('title')->update([
            'title' => DB::raw("CONCAT('Schedule ', id)")
        ]);
    }

    public function down()
    {
        if (Schema::hasColumn('schedules', 'title')) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->dropColumn('title');
            });
        }
    }
}