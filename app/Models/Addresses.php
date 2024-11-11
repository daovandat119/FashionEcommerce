<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Addresses extends Model
{
    use HasFactory;

    protected $table = 'addresses';

    protected $primaryKey = 'AddressID';

    public $timestamps = true;

    protected $fillable = [
        'UserID',
        'UserName',
        'Address',
        'PhoneNumber',
        'DistrictID',
        'WardCode',
        'IsDefault',
        'Status',
        'created_at',
        'updated_at',
    ];

    public function getAddress($id)
    {
        return Addresses::where('AddressID', $id)->where('Status', 'ACTIVE')->first();
    }

    public function getAddressByUserID($id)
    {
        return Addresses::where('UserID', $id)->where('Status', 'ACTIVE')->get();
    }

    public function checkAddressByUserID($id)
    {
        return Addresses::where('UserID', $id)->where('Status', 'ACTIVE')->first();
    }

    public function addAddress($data)
    {
        return Addresses::create([
            'UserID' => $data['UserID'],
            'UserName' => $data['UserName'],
            'Address' => $data['Address'],
            'PhoneNumber' => $data['PhoneNumber'],
            'DistrictID' => $data['DistrictID'],
            'WardCode' => $data['WardCode'],
            'IsDefault' => $data['IsDefault'],
            'Status' => $data['Status'],
        ]);
    }

    public function getAddressByID($id, $userId)
    {
        return Addresses::where('AddressID', $id)
            ->where('UserID', $userId)
            ->where('Status', 'ACTIVE')
            ->first();
    }

    public function updateAddress($id, $userId, $data)
    {
        return Addresses::where('AddressID', $id)
        ->where('UserID', $userId)
        ->where('Status', 'ACTIVE')
        ->update([
            'UserName' => $data['UserName'],
            'Address' => $data['Address'],
            'PhoneNumber' => $data['PhoneNumber'],
            'DistrictID' => $data['DistrictID'],
            'WardCode' => $data['WardCode'],
        ]);
    }

    public function setDefaultAddress($id, $userId)
    {
        Addresses::where('UserID', $userId)
            ->where('Status', 'ACTIVE')
            ->update(['IsDefault' => 0]);

        return Addresses::where('AddressID', $id)
            ->where('UserID', $userId)
            ->where('Status', 'ACTIVE')
            ->update(['IsDefault' => 1]);
    }

    public function deleteAddress($id, $userId)
    {
        return Addresses::where('AddressID', $id)
            ->where('UserID', $userId)
            ->where('Status', 'ACTIVE')
            ->update(['Status' => 'INACTIVE']);
    }

    public function getDistrictID($id)
    {
        return Addresses::where('UserID', $id)
        ->where('UserID', $id)
        ->where('IsDefault', 1)
        ->where('Status', 'ACTIVE')
        ->first();
    }

    public function checkAddressInUse($id, $userId)
    {
        return Addresses::where('AddressID', $id)->where('UserID', $userId)->where('IsDefault', 1)->first();
    }
}
