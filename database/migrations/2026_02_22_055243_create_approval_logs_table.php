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
        Schema::create('approval_logs', function (Blueprint $table) {
            $table->id();

            $table->string('form_name'); // Nama Module (Misa
            $table->string('form_type'); // Misal: CUTI, IZIN
            $table->string('admin_name'); // Nama Admin yang melakukan perubahan
            $table->string('departemen'); // Departemen yang terkait
            $table->string('sub_departemen'); // Sub Departemen yang terkait

            $table->string('action'); // Aksi yang dilakukan (CREATE, UPDATE, DELETE)

            $table->longText('old_value')->nullable(); // Nilai sebelum perubahan (untuk UPDATE dan DELETE)
            $table->longText('new_value')->nullable(); // Nilai setelah perubahan (untuk CREATE dan UPDATE)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_logs');
    }
};
