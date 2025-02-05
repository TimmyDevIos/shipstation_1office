<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Mã đơn hàng bán
            $table->date('date_sign'); // Ngày bán
            $table->string('customer_code'); // Khách hàng
            $table->string('customerUsername')->nullable(); // Tên khách hàng
            $table->string('customerEmail')->nullable(); // Email khách hàng
            $table->json('Address_Bill')->nullable(); // thông tin người mua
            $table->json('Address_Ship')->nullable(); // Thông tin giao hàng
            $table->string('currency_unit')->nullable(); // Đơn vị tiền
            $table->decimal('rate', 8, 2)->nullable(); // Phí ship từ Shipstation
            $table->decimal('fee', 8, 2)->nullable(); // Phí
            $table->decimal('sale', 8, 2)->nullable(); // Chiết khấu
            $table->decimal('vat', 8, 2)->nullable(); // Tiền thuế
            $table->decimal('amountPaid', 8, 2)->nullable(); // Tổng số tiền đã thanh toán cho Đơn hàng.
            $table->decimal('shippingAmount', 8, 2)->nullable(); // Số tiền vận chuyển do khách hàng thanh toán nếu có.
            $table->decimal('total_price', 8, 2)->nullable(); // Đã chi
            $table->string('orderStatus'); // trạng thái đơn hàng
            $table->longText('desc')->nullable(); // Mô tả
            $table->json('detail')->nullable(); // Nội dung đơn hàng
            $table->json('weight')->nullable(); // Trọng lượng của đơn đặt hàng.
            $table->json('dimensions')->nullable(); // Kích thước của đơn đặt hàng.
            $table->json('payment')->nullable(); // Các đợt thanh toán
            $table->json('income')->nullable(); // Hoa hồng
            $table->string('customerNotes')->nullable(); // customer Notes
            $table->string('internalNotes')->nullable(); // internal Notes
            $table->json('advanced_options_ShipSation')->nullable(); // Cấu hình nâng cao
            $table->string('warehouse_status')->nullable(); // Tình trạng xuất kho
            $table->string('idthirdparty')->nullable(); // Id bên thứ 3
            $table->string('orderKeyShipSation')->nullable(); // Order Key dành riêng cho Shipsation
            $table->string('id1OFFICE')->nullable(); // id 1Office

            $table->json('FullData')->nullable(); // Full data nhận từ reques
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
