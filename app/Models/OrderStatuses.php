<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatuses extends Model
{
    use HasFactory;

    protected $table = 'order_statuses';

    protected $primaryKey = 'OrderStatusID';

    public $timestamps = true;

    protected $fillable = [
        'StatusName',
        'created_at',
        'updated_at',
    ];
}
