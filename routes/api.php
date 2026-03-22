<?php

// 1. Namespace HAPUS SAJA jika ini file routes/api.php
// 2. Import class di sini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApprovalMasterController;
use App\Models\ApprovalLog;

// 3. Logic Route
Route::prefix('approval-master')->group(function () {
    Route::get('/', [ApprovalMasterController::class, 'index']);
    Route::post('/store', [ApprovalMasterController::class, 'store']);
    Route::get('/show/{id}', [ApprovalMasterController::class, 'show']);
    Route::put('/update/{id}', [ApprovalMasterController::class, 'update']);
    Route::delete('/delete/{id}', [ApprovalMasterController::class, 'destroy']);

    // Endpoint History
    Route::get('logs', function (Request $request) {
        $query = $request->query('search');

        $logs = ApprovalLog::when($query, function ($q) use ($query) {
            return $q->where('admin_name', 'like', "%{$query}%")
                     ->orWhere('form_name', 'like', "%{$query}%")
                     ->orWhere('departemen', 'like', "%{$query}%");
        })
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json([
            'success' => true,
            'data'    => $logs
        ]);
    });
});