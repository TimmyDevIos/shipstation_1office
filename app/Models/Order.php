<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Order extends Model implements AuditableContract
{
    use HasFactory, Auditable;
    // Tạm ngừng ghi log audit
    public static $auditDisabled = true;

    protected $table = 'orders'; // Tên bảng

    protected $fillable = [
        'code', 'date_sign', 'customer_code', 'customerUsername', 'customerEmail', 'Address_Bill',
        'Address_Ship', 'currency_unit', 'fee', 'sale', 'vat', 'amountPaid', 'shippingAmount',
        'total_price', 'orderStatus', 'desc', 'detail', 'weight', 'dimensions',
        'customerNotes','internalNotes',
        'payment', 'income', 'advanced_options_ShipSation', 'warehouse_status', 'idthirdparty', 'orderKeyShipSation', 'id1OFFICE', 'FullData',
    ];

    protected $casts = [
        'Address_Bill' => 'array',
        'Address_Ship' => 'array',
        'detail' => 'array',
        'weight' => 'array',
        'dimensions' => 'array',
        'payment' => 'array',
        'income' => 'array',
        'advanced_options_ShipSation' => 'array',
        'FullData' => 'array',
    ];

}
