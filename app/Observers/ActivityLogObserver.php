<?php

namespace App\Observers;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLogObserver
{
    public function created(Model $model): void
    {
        $this->log('created', $model, null, $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        $changes = $model->getChanges();
        unset($changes['updated_at']); // jangan catat timestamp otomatis

        if (empty($changes)) {
            return; // tidak ada perubahan nyata
        }

        $original = array_intersect_key($model->getOriginal(), $changes);

        $this->log('updated', $model, $original, $changes);
    }

    public function deleted(Model $model): void
    {
        $this->log('deleted', $model, $model->getAttributes(), null);
    }

    private function log(string $aksi, Model $model, ?array $dataLama, ?array $dataBaru): void
    {
        $user      = Auth::user();
        $namaModel = class_basename($model);
        $label     = $this->getDisplayLabel($model);

        $deskripsi = match($aksi) {
            'created' => "{$this->aktorNama($user)} menambahkan {$namaModel}: {$label}",
            'deleted' => "{$this->aktorNama($user)} menghapus {$namaModel}: {$label}",
            'updated' => "{$this->aktorNama($user)} mengubah {$namaModel}: {$label}",
            default   => "{$this->aktorNama($user)} melakukan {$aksi} pada {$namaModel}",
        };

        ActivityLog::create([
            'user_id'    => $user?->id,
            'aksi'       => $aksi,
            'model_type' => get_class($model),
            'model_id'   => $model->getKey(),
            'deskripsi'  => $deskripsi,
            'data_lama'  => $dataLama,
            'data_baru'  => $dataBaru,
            'ip_address' => request()->ip(),
        ]);
    }

    private function aktorNama($user): string
    {
        return $user ? $user->name : 'Sistem';
    }

    /**
     * Ambil label yang mudah dibaca dari model (nama barang, nama lokasi, dll)
     */
    private function getDisplayLabel(Model $model): string
    {
        foreach (['nama_barang', 'nama', 'judul', 'name'] as $field) {
            if (isset($model->{$field})) {
                return $model->{$field};
            }
        }
        return "#{$model->getKey()}";
    }
}