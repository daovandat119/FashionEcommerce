<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Addresses;
use App\Http\Requests\AddressRequest;
use Illuminate\Support\Facades\Http;

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

    public function store(Request $request)
    {
        $userId = auth()->id();

        $address = $this->repoAddress->checkAddressByUserID($userId);

        $data = [
            'UserID' => $userId,
            'UserName' => $request->input('UserName'),
            'Address' => $request->input('Address'),
            'PhoneNumber' => $request->input('PhoneNumber'),
            'DistrictID' => $request->input('DistrictID'),
            'WardCode' => $request->input('WardCode'),
            'IsDefault' => $address ? 0 : 1,
            'Status' => 'ACTIVE',
        ];

        $this->repoAddress->addAddress($data);

        return response()->json(['message' => 'Success', 'data' => $data], 201);
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
            'DistrictID' => $request->input('DistrictID'),
            'WardCode' => $request->input('WardCode'),
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

        $addressInUse = $this->repoAddress->checkAddressInUse($id, $userId);

        if ($addressInUse) {
            return response()->json(['message' => 'Address is in use'], 400);
        }

        $address = $this->repoAddress->getAddressByID($id, $userId);

        if (!$address) {
            return response()->json(['message' => 'Address not found'], 404);
        }

        $this->repoAddress->deleteAddress($id, $userId);

        return response()->json(['message' => 'Delete address successfully'], 200);
    }

    public function checkAddress(Request $request){

        $userId = auth()->id();

        $address = $this->repoAddress->checkAddressByUserID($userId);

        return response()->json(['message' => 'Success', 'data' => $address ? "true" : "false"], 200);
    }

    public function getProvinces()
    {
        $response = Http::withHeaders([
            'token' => env('GHN_TOKEN'),
        ])->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/province');

        $data = $response->json();

        if ($response->successful()) {
            $provinces = array_map(function($province) {
                return [
                    'ProvinceID' => $province['ProvinceID'],
                    'ProvinceName' => $province['ProvinceName'],
                ];
            }, $data['data']);

            return response()->json(['message' => 'Success', 'data' => $provinces], 200);
        }

        return response()->json(['message' => 'Failed to fetch provinces'], 500);
    }

    public function getDistricts(Request $request)
    {
        $response = Http::withHeaders([
            'token' => env('GHN_TOKEN'),
        ])->post('https://online-gateway.ghn.vn/shiip/public-api/master-data/district', [
            'province_id' => $request->input('province_id'),
        ]);

        if ($response->successful()) {
            $data = array_map(function($district) {
                return [
                    'DistrictID' => $district['DistrictID'],
                    'DistrictName' => $district['DistrictName'],
                ];
            }, $response->json()['data']);

            return response()->json(['message' => 'Success', 'data' => $data], 200);
        }

        return response()->json(['message' => 'Failed to fetch districts'], 500);
    }

    public function getWards(Request $request)
    {
        $response = Http::withHeaders([
            'token' => env('GHN_TOKEN'),
        ])->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/ward', [
            'district_id' => $request->input('district_id'),
        ]);

        if ($response->successful()) {
            $data = array_map(function($ward) {
                return [
                    'DistrictID' => $ward['DistrictID'],
                    'WardCode' => $ward['WardCode'],
                ];

            }, $response->json()['data']);

            return response()->json(['message' => 'Success', 'data' => $data], 200);
        }

        return response()->json(['message' => 'Failed to fetch wards'], 500);
    }


    public function getShippingFee(Request $request)
    {
        $userId = auth()->id();

        $addressUser = $this->repoAddress->getDistrictID($userId);

        $addressAdmin = $this->repoAddress->getAddressByID(1, 1);

        $response = Http::withHeaders([
            'token' => env('GHN_TOKEN'),
            'shop_id' => env('GHN_SHOP_ID'),
        ])->post('https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/fee', [
            'service_id' => 53321,
            'insurance_value' => 500000,
            'coupon' => null,
            'from_district_id' => (int) $addressAdmin->DistrictID,
            'to_district_id' => (int) $addressUser->DistrictID,
            'to_ward_code' => $addressUser->WardCode,
            'height' => 15,
            'length' => 25,
            'weight' => 1000,
            'width' => 15,
        ]);

        if ($response->successful()) {
            $data = [
                'total' => $response->json()['data']['total'],
            ];

            return response()->json(['message' => 'Success', 'data' => $data], 200);
        }

        return response()->json(['message' => 'Failed to fetch wards'], 500);
    }
}
