<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('photo')->nullable();
            $table->string('gender')->nullable();
            $table->string('phone')->nullable();
            $table->string('api_token', 80)->unique()->nullable()->default(null);
            $table->string('nip')->nullable();
            $table->foreignId('schedule_id')->nullable()->constrained('schedules')->onDelete('set null');
            $table->integer('working_days')->nullable();
            $table->integer('jumlah_cuti')->nullable();
            $table->foreignId('jabatan_id')->nullable();
            $table->foreignId('bagian_id')->nullable();
            $table->string('lokasi_kerja')->nullable();
            $table->date('tgl_mulai')->nullable();
            $table->date('tgl_berhenti')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('gelar')->nullable();
            $table->string('jurusan')->nullable();
            $table->string('sekolah_universitas')->nullable();
            $table->integer('tahun_lulus_1')->nullable();
            $table->string('pendidikan_2')->nullable();
            $table->string('jurusan_pendidikan_2')->nullable();
            $table->string('sekolah_universitas_2')->nullable();
            $table->integer('tahun_lulus_2')->nullable();
            $table->text('alamat')->nullable();
            $table->string('alamat_email')->nullable();
            $table->string('type_pegawai')->nullable();
            $table->string('status_pegawai')->nullable();
            $table->string('ktp_id')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('no_rek')->nullable();
            $table->decimal('special_adjustment_sa', 8, 2)->nullable();
            $table->date('sa_date_start_acting')->nullable();
            $table->date('kontrak_mulai_1')->nullable();
            $table->date('kontrak_selesai_1')->nullable();
            $table->date('kontrak_mulai_2')->nullable();
            $table->date('kontrak_selesai_2')->nullable();
            $table->integer('gaji_pokok')->nullable();
            $table->string('ptt')->nullable();
            $table->string('t_jabatan')->nullable();
            $table->string('t_kehadiran')->nullable();
            $table->string('t_anak')->nullable();
            $table->string('bonus_sanad')->nullable();
            $table->string('diniyyah')->nullable();
            $table->enum('status', [
                'active',
                'inactive'
            ])->default('inactive');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
