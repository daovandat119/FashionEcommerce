<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users'; // Tên bảng
    protected $primaryKey = 'UserID'; // Khóa chính

    protected $fillable = [
        'RoleID',
        'Username',
        'Email',
        'Password',
        'Image',
        'IsActive',
        'CodeId',
        'CodeExpired',
    ];

    protected $hidden = [
        'Password', // Ẩn mật khẩu trong JSON
        'CodeId',
        'CodeExpired',
    ];

    // Mối quan hệ với Role
    public function role()
    {
        return $this->belongsTo(Role::class, 'RoleID', 'RoleID');
    }

}
