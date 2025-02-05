<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Customer extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $table = 'customers'; // Tên bảng 

    protected $fillable = [
        'type', 'code', 'name', 'gender', 'birthday', 'phones', 'emails', 'address', 'id1OFFICE','idthirdparty', 'FullData'
    ]; 

    protected $casts = [
        'phones' => 'array', 
        'emails' => 'array', 
        'FullData' => 'array', 
    ];
}
