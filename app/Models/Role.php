<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';

    public $timestamps = true;

    protected $primaryKey = 'RoleID';

    protected $fillable = [
        'RoleName',
        'created_at',
        'updated_at',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'RoleID', 'RoleID');
    }
}
