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
        Schema::create('customers', function (Blueprint $table) {
            $table->id(); // Tự động tạo khóa chính
            $table->text('type');
            $table->text('code');
            $table->text('name');
            $table->text('gender');
            $table->date('birthday')->nullable();
            $table->json('phones')->nullable(); // Sử dụng JSON để lưu trữ mảng
            $table->json('emails')->nullable(); // Sử dụng JSON để lưu trữ mảng
            $table->text('address')->nullable();
            $table->text('id1OFFICE')->nullable();
            $table->json('FullData')->nullable(); // Sử dụng JSON để lưu trữ dữ liệu bổ sung
            $table->timestamps(); // Tự động thêm created_at và updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
