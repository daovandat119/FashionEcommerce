<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Addresses extends Model
{
    use HasFactory;

    protected $table = 'addresses';
    protected $primaryKey = 'AddressID';

    public function getAddress($id)
    {
        return DB::table($this->table)->where('AddressID', $id)->first();
    }


    public function getAddressByUserID($id)
    {
        return DB::table($this->table)->where('UserID', $id)->get();
    }

    public function addAddress($data)
    {
        return DB::table($this->table)->insert([
            'UserID' => $data['UserID'],
            'UserName' => $data['UserName'],
            'Address' => $data['Address'],
            'PhoneNumber' => $data['PhoneNumber'],
            'IsDefault' => $data['IsDefault'],
        ]);
    }

    public function getAddressByID($id, $userId)
    {
        return DB::table('addresses')
            ->where('AddressID', $id)
            ->where('UserID', $userId)
            ->first();
    }

    public function updateAddress($id, $userId, $data)
    {
        return DB::table($this->table)
        ->where('AddressID', $id)
        ->where('UserID', $userId)
        ->update([
            'UserName' => $data['UserName'],
            'Address' => $data['Address'],
            'PhoneNumber' => $data['PhoneNumber'],
        ]);
    }

    public function setDefaultAddress($id, $userId)
    {
        DB::table($this->table)
            ->where('UserID', $userId)
            ->update(['IsDefault' => 0]);

        return DB::table($this->table)
            ->where('AddressID', $id)
            ->where('UserID', $userId)
            ->update(['IsDefault' => 1]);
    }



    public function deleteAddress($id, $userId)
    {
        return DB::table($this->table)
        ->where('AddressID', $id)
        ->where('UserID', $userId)
        ->delete();
    }

}
