<?php

namespace App\Imports;

use App\Models\Pekerjaan;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KinerjaImport implements ToModel, WithHeadingRow
{
    protected $fileName;
    protected $tanggal;

    public function __construct($fileName, $tanggal)
    {
        $this->fileName = $fileName;
        $this->tanggal = $tanggal;
    }

    public function model(array $row): Model|array|null
    {
        $nama = $row['nama_pegawai'] ?? $row['nama'] ?? null;
        $pekerjaanInput = $row['pekerjaan'] ?? $row['daftar_pekerjaan'] ?? '';

        if (!$nama) return null;

        $user = User::firstOrCreate(
            ['name' => $nama],
            ['username' => strtolower(str_replace(' ', '', $nama)), 'password' => bcrypt('password123')]
        );

        $newTasksArray = array_filter(array_map('trim', explode(',', $pekerjaanInput)));

        $existingPekerjaan = Pekerjaan::where('user_id', $user->id)
            ->whereDate('tanggal', $this->tanggal)
            ->first();

        if ($existingPekerjaan) {
            // Jika sudah ada data di hari yang sama: Gabungkan & hapus duplikat
            $oldTasksArray = array_filter(array_map('trim', explode(',', $existingPekerjaan->daftar_pekerjaan)));
            $mergedTasks = array_unique(array_merge($oldTasksArray, $newTasksArray));

            $existingPekerjaan->update([
                'daftar_pekerjaan' => implode(', ', $mergedTasks),
                'total_pekerjaan'  => count($mergedTasks),
                'file_name'        => $this->fileName,
            ]);

            return null;
        }

        return new Pekerjaan([
            'user_id'          => $user->id,
            'daftar_pekerjaan' => implode(', ', $newTasksArray),
            'total_pekerjaan'  => count($newTasksArray),
            'file_name'        => $this->fileName,
            'tanggal'          => $this->tanggal,
        ]);
    }
}