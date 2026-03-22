<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ApprovalLog;

class ApprovalMaster extends Model
{
    protected $table = 'approval_masters';

    protected $fillable = [
        'form_name',
        'form_type',
        'order_number',
        'status',
        'dept_id',
        'departemen',
        'sub_dept_id',
        'sub_departemen',
        'grade_id',
        'grade',
        'position_id',
        'absen_id',
        'name',
        'created_by',
    ];

    // --- LOGIKA HISTORY DIMULAI DI SINI ---
    protected static function booted()
    {
        // 1. Catat saat TAMBAH data
        static::created(function ($model) {
            ApprovalLog::create([
                'form_name'  => $model->form_name,
                'form_type'  => $model->form_type,
                'action'     => 'CREATE',
                'admin_name' => request()->admin_name ?? $model->created_by,
                'departemen' => $model->departemen,
                'sub_departemen' => $model->sub_departemen,
                'new_value'  => json_encode($model->getAttributes()),
            ]);
        });

        // 2. Catat saat UBAH data (Ganti Grade, Order No, dll)
        static::updated(function ($model) {
            $changes = $model->getChanges();
            
            // Abaikan jika hanya waktu update yang berubah
            if (count($changes) <= 1 && isset($changes['updated_at'])) return;

            // Ambil data asli sebelum diubah untuk kolom yang berubah saja
            $oldValues = array_intersect_key($model->getOriginal(), $changes);

            ApprovalLog::create([
                'form_name'  => $model->form_name,
                'form_type'  => $model->form_type,
                'action'     => 'UPDATE',
                'admin_name' => request()->admin_name ?? 'Admin',
                'departemen' => $model->departemen,
                'sub_departemen' => $model->sub_departemen,
                'old_value'  => json_encode($oldValues),
                'new_value'  => json_encode($changes),
            ]);
        });

        // 3. Catat saat HAPUS data
        static::deleted(function ($model) {
            ApprovalLog::create([
                'form_name'  => $model->form_name,
                'form_type'  => $model->form_type,
                'action'     => 'DELETE',
                'admin_name' => request()->admin_name ?? 'Admin',
                'departemen' => $model->departemen,
                'sub_departemen' => $model->sub_departemen,
                'old_value'  => json_encode($model->getAttributes()),
                'new_value'  => json_encode(['status' => 'DELETED']),
            ]);
        });
    }
}

