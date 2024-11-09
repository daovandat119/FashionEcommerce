<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ShippingController extends Controller
{
    protected $yourAddress = '123 Đường Lê Lợi, Quận 1, TP. Hồ Chí Minh, Việt Nam';

    public function calculateShipping(Request $request)
    {
        $userAddress = $request->input('address');

        if (empty($userAddress)) {
            return response()->json(['message' => 'Address is required'], 400);
        }

        $apiKey = env('GOOGLE_MAPS_API_KEY');
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . urlencode($this->yourAddress) . "&destinations=" . urlencode($userAddress) . "&key=" . $apiKey;

        $response = Http::get($url);
        $data = $response->json();

        if (isset($data['error_message'])) {
            return response()->json(['message' => $data['error_message']], 400);
        }

        if (isset($data['rows'][0]['elements'][0]['status']) && $data['rows'][0]['elements'][0]['status'] == 'OK') {
            $distance = $data['rows'][0]['elements'][0]['distance']['value']; // distance in meters
            $shippingCost = $this->calculateShippingCost($distance);
            return response()->json(['shipping_cost' => $shippingCost], 200);
        } else {
            return response()->json(['message' => 'Unable to calculate distance'], 400);
        }
    }

    private function calculateShippingCost($distance)
    {
        $baseCost = 5000; // Base cost in your currency
        $costPerKm = 2000; // Cost per kilometer

        $distanceInKm = $distance / 1000; // Convert meters to kilometers

        return $baseCost + ($distanceInKm * $costPerKm);
    }
}
