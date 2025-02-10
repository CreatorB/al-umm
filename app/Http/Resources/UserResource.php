<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
   public function toArray($request)
   {
       return [
           'id' => $this->id,
           'uuid' => $this->uuid,
           'name' => $this->name,
           'email' => $this->email,
           'email_verified_at' => $this->email_verified_at,
           'photo' => $this->photo,
           'gender' => $this->gender,
           'phone' => $this->phone,
           'nip' => $this->nip,
           'working_days' => $this->working_days,
           'jumlah_cuti' => $this->jumlah_cuti,
           'lokasi_kerja' => $this->lokasi_kerja,
           'tgl_mulai' => $this->tgl_mulai,
           'tgl_berhenti' => $this->tgl_berhenti,
           'tempat_lahir' => $this->tempat_lahir,
           'tanggal_lahir' => $this->tanggal_lahir,
           'pendidikan' => $this->pendidikan,
           'gelar' => $this->gelar,
           'jurusan' => $this->jurusan,
           'sekolah_universitas' => $this->sekolah_universitas,
           'tahun_lulus_1' => $this->tahun_lulus_1,
           'pendidikan_2' => $this->pendidikan_2,
           'jurusan_pendidikan_2' => $this->jurusan_pendidikan_2,
           'sekolah_universitas_2' => $this->sekolah_universitas_2,
           'tahun_lulus_2' => $this->tahun_lulus_2,
           'alamat' => $this->alamat,
           'alamat_email' => $this->alamat_email,
           'type_pegawai' => $this->type_pegawai,
           'status_pegawai' => $this->status_pegawai,
           'ktp_id' => $this->ktp_id,
           'keterangan' => $this->keterangan,
           'no_rek' => $this->no_rek,
           'special_adjustment_sa' => $this->special_adjustment_sa,
           'sa_date_start_acting' => $this->sa_date_start_acting,
           'kontrak_mulai_1' => $this->kontrak_mulai_1,
           'kontrak_selesai_1' => $this->kontrak_selesai_1,
           'kontrak_mulai_2' => $this->kontrak_mulai_2,
           'kontrak_selesai_2' => $this->kontrak_selesai_2,
           'gaji_pokok' => $this->gaji_pokok,
           'ptt' => $this->ptt,
           't_jabatan' => $this->t_jabatan,
           't_kehadiran' => $this->t_kehadiran,
           't_anak' => $this->t_anak,
           'bonus_sanad' => $this->bonus_sanad,
           'diniyyah' => $this->diniyyah,
           'status' => $this->status,
           'created_at' => $this->created_at,
           'updated_at' => $this->updated_at,
           'photo_url' => $this->photo_url,
           'schedule' => $this->schedule,
           'department' => $this->department,
           'part' => $this->part
       ];
   }
}