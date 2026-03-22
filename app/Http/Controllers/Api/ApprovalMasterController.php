<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApprovalMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ApprovalMasterController extends Controller
{
    public function index()
    {
        // Tambahkan pengurutan berdasarkan form_type juga agar lebih rapi
        $data = ApprovalMaster::orderBy('form_name')
            ->orderBy('form_type')
            ->orderBy('order_number')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'List Master Approval',
            'data'    => $data
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'form_name'    => 'required|string',
            'form_type'    => 'nullable|string',
            'order_number' => 'required|numeric',
            'status'       => 'required',
            'absen_id'     => 'nullable',
            'name'         => 'nullable',
            'dept_id'      => 'nullable',
            'departemen'   => 'nullable',
            'sub_dept_id'  => 'nullable',
            'sub_departemen' => 'nullable',
            'grade_id'     => 'nullable',
            'grade'        => 'nullable',
            'position_id'  => 'nullable',
            'created_by'   => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            // PERBAIKAN: Cek duplikat yang lebih cerdas (menangani null)
            $exists = ApprovalMaster::where('form_name', $request->form_name)
                ->where('form_type', $request->form_type)
                ->where('order_number', $request->order_number)
                ->where('dept_id', $request->dept_id)
                ->where('absen_id', $request->absen_id)
                ->first();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => "Urutan {$request->order_number} untuk form ini sudah terdaftar."
                ], 400);
            }

            // Gunakan $request->only atau $request->all() agar lebih ringkas jika fillable sudah diset
            $approval = ApprovalMaster::create($request->all());

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Berhasil disimpan!', 'data' => $approval], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $approval = ApprovalMaster::find($id);
        if (!$approval) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'form_name'    => 'required|string',
            'order_number' => 'required|numeric',
            'status'       => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            // PERBAIKAN: Cek apakah urutan baru yang diinput sudah dipakai oleh data LAIN
            $duplicate = ApprovalMaster::where('form_name', $request->form_name)
                ->where('order_number', $request->order_number)
                ->where('dept_id', $request->dept_id)
                ->where('absen_id', $request->absen_id)
                ->where('id', '!=', $id) // Kecualikan data diri sendiri
                ->exists();

            if ($duplicate) {
                return response()->json(['success' => false, 'message' => 'Urutan baru sudah digunakan data lain.'], 400);
            }

            $approval->update($request->all());

            return response()->json(['success' => true, 'message' => 'Berhasil diperbarui!', 'data' => $approval]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }
    public function show($id)
    {
        $data = ApprovalMaster::find($id); // Sesuaikan dengan nama Model kamu
        if ($data) {
            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        }
        return response()->json(['message' => 'Data tidak ditemukan'], 404);
    }

    public function destroy($id)
    {
        try {
            $approval = ApprovalMaster::find($id);
            if (!$approval) {
                return response()->json(['success' => false, 'message' => 'Data sudah tidak ada'], 404);
            }
            $approval->delete();
            return response()->json(['success' => true, 'message' => 'Konfigurasi berhasil dihapus!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data'], 500);
        }
    }
}
