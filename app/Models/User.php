<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users'; // Tên bảng
    protected $primaryKey = 'UserID'; // Khóa chính

    protected $fillable = [
        'RoleID',  // Thêm RoleID để xác định vai trò của người dùng
        'Username',
        'Email',
        'Password',
        'Image',
        'IsActive',
        'CodeId',
        'CodeExpired',
    ];

    // Thêm quan hệ Role
    public function role()
    {
        return $this->belongsTo(Role::class, 'RoleID', 'RoleID');
    }
}
