<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalLog extends Model
{
    protected $table = 'approval_logs';
    protected $guarded = []; // Agar semua field bisa diisi otomatis oleh Master tadi
}