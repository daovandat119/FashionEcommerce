<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles'; // Tên bảng
    protected $primaryKey = 'RoleID'; // Khóa chính

    protected $fillable = [
        'RoleName',
    ];

    // Mối quan hệ với User
    public function users()
    {
        return $this->hasMany(User::class, 'RoleID', 'RoleID');
    }
}