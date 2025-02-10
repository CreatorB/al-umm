<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysUsersDepartmentsParts extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('jabatan_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('bagian_id')->references('id')->on('parts')->onDelete('set null');
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->foreign('head_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['jabatan_id']);
            $table->dropForeign(['bagian_id']);
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['head_id']);
        });
    }
}
