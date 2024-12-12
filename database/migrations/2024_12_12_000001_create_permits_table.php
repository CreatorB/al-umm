<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermitsTable extends Migration
{
    public function up()
    {
        Schema::create('permits', function (Blueprint $table) {
            $table->id();
            $table->string('name')->notNullable();
            $table->text('description')->nullable();
            $table->integer('max_days')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('permits');
    }
}