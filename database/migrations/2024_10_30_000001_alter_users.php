<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('api_token', 80)->after('password')
                ->unique()
                ->nullable()
                ->default(null);
            $table->string('nip')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('bagian')->nullable();
            $table->string('lokasi_kerja')->nullable();
            $table->date('tgl_mulai')->nullable();
            $table->date('tgl_berhenti')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('pendidikan')->nullable();
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
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'api_token',
                'nip',
                'jabatan',
                'bagian',
                'lokasi_kerja',
                'tgl_mulai',
                'tgl_berhenti',
                'tempat_lahir',
                'tanggal_lahir',
                'pendidikan',
                'jurusan',
                'sekolah_universitas',
                'tahun_lulus_1',
                'pendidikan_2',
                'jurusan_pendidikan_2',
                'sekolah_universitas_2',
                'tahun_lulus_2',
                'alamat',
                'alamat_email',
                'type_pegawai',
                'status_pegawai',
                'ktp_id',
                'keterangan',
                'no_rek',
                'special_adjustment_sa',
                'sa_date_start_acting',
                'kontrak_mulai_1',
                'kontrak_selesai_1',
                'kontrak_mulai_2',
                'kontrak_selesai_2',
                'gaji_pokok',
                'ptt',
                't_jabatan',
                't_kehadiran',
                't_anak',
                'bonus_sanad',
                'diniyyah'
            ]);
        });
    }
};