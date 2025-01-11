<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(); 
            $table->date('attendance_date'); 
            $table->timestamp('check_in')->nullable(); 
            $table->timestamp('check_out')->nullable(); 
            $table->string('check_in_location')->nullable(); 
            $table->string('check_out_location')->nullable(); 
            $table->enum('status', [
                'hadir', 'sakit', 'izin', 'tugas_luar', 'cuti', 'alpha'
            ])->default('hadir');
            $table->timestamp('over_time_in')->nullable(); 
            $table->timestamp('over_time_out')->nullable(); 
            $table->foreignId('permit_id')->nullable()->constrained(); 
            $table->text('notes')->nullable(); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}