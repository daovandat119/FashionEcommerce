<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes; // Thêm SoftDeletes

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
        'Password', 
        'CodeId',
        'CodeExpired',
    ];

    // Mối quan hệ với Role
    public function role()
    {
        return $this->belongsTo(Role::class, 'RoleID', 'RoleID');
    }
}