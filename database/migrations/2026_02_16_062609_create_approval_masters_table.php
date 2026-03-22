<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create('approval_masters', function (Blueprint $table) {
            $table->id();
            $table->string('form_name'); // Nama Module (Misal: Employee Master)
            $table->string('form_type'); // Misal: CUTI, IZIN
            $table->integer('order_number'); // Urutan (1, 2, 3)
            $table->integer('status'); // Status (Sinkron dengan order_number)

            // Target yang akan diapprove (Opsional/Bisa null untuk 'All')
            $table->string('dept_id')->nullable();
            $table->string('departemen');
            $table->string('sub_dept_id')->nullable();
            $table->string('sub_departemen')->nullable();
            $table->string('grade_id')->nullable();
            $table->string('grade')->nullable();
            $table->string('position_id')->nullable();

            // Siapa yang approve
            $table->string('absen_id')->nullable(); // ID Karyawan Approver
            $table->string('name')->nullable(); // Nama Karyawan Approver

            $table->string('created_by')->nullable(); // ID Karyawan yang membuat aturan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_masters');
    }
};
