<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Addresses;
use App\Http\Requests\AddressRequest;

class AddressController extends Controller
{
    protected $repoAddress;

    public function __construct()
    {
        $this->repoAddress = new Addresses();
    }

    public function index()
    {
        $userId = auth()->id();

        $address = $this->repoAddress->getAddressByUserID($userId);
        return response()->json(['message' => 'Success', 'data' => $address], 200);
    }

    public function store(AddressRequest $request)
    {
        $userId = auth()->id();

        $address = $this->repoAddress->getAddressByUserID($userId);

        $data = [
            'UserID' => $userId,
            'UserName' => $request->input('UserName'),
            'Address' => $request->input('Address'),
            'PhoneNumber' => $request->input('PhoneNumber'),
            'IsDefault' => $address ? 0 : 1,
        ];

        $this->repoAddress->addAddress($data);

        return response()->json(['message' => 'Success', 'data' => $data], 200);
    }

    public function edit($id)
    {
        $userId = auth()->id();

        $address = $this->repoAddress->getAddressByID($id, $userId);

        if (!$address) {
            return response()->json(['message' => 'Address not found'], 404);
        }

        return response()->json(['message' => 'Success', 'data' => $address], 200);
    }

    public function update(AddressRequest $request, $id)
    {
        $userId = auth()->id();

        $address = $this->repoAddress->getAddressByID($id, $userId);

        if (!$address) {
            return response()->json(['message' => 'Address not found'], 404);
        }

        $data = [
            'UserID' => $request->input('UserID'),
            'UserName' => $request->input('UserName'),
            'Address' => $request->input('Address'),
            'PhoneNumber' => $request->input('PhoneNumber'),
            'IsDefault' => $request->input('IsDefault'),
        ];

        $this->repoAddress->updateAddress($id, $userId, $data);

        return response()->json(['message' => 'Success', 'data' => $data], 200);
    }

    public function setDefaultAddress($id)
    {
        $userId = auth()->id();

        $address = $this->repoAddress->getAddressByID($id, $userId);

        if (!$address) {
            return response()->json(['message' => 'Address not found'], 404);
        }

        $this->repoAddress->setDefaultAddress($id, $userId);

        return response()->json(['message' => 'Success', 'data' => $address], 200);
    }

    public function delete($id)
    {
        $userId = auth()->id();

        $address = $this->repoAddress->getAddressByID($id, $userId);

        if (!$address) {
            return response()->json(['message' => 'Address not found'], 404);
        }

        $this->repoAddress->deleteAddress($id, $userId);

        return response()->json(['message' => 'Success', 'data' => $address], 200);
    }

}
