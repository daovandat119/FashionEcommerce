<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payments;
use App\Models\Cart;
use App\Models\CartItems;
use App\Models\Addresses;
use Illuminate\Support\Str;
use App\Models\OrderItems;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\AddressController;
use App\Models\Coupon;
use Illuminate\Support\Facades\Log;

class ZaloPayController extends Controller
{
    public function addPayment($userId, Request $request)
    {
        $config = [
            "app_id" => env('ZALOPAY_APP_ID'),
            "key1" => env('ZALOPAY_KEY1'),
            "key2" => env('ZALOPAY_KEY2'),
            "endpoint" => env('ZALOPAY_ENDPOINT'),
        ];

        $embeddata = json_encode([
            'redirecturl' => env('ZALOPAY_REDIRECT_URL'),
            "cancel_url" => env('ZALOPAY_CANCEL_URL'),
            'UserID' => $userId,
            'CouponID' => $request->CouponID,
            'Discount' => $request->Discount,
        ]);

        $items = '[]';

        $transID = rand(0,10000000000000);
        $order = [
            "app_id" => $config["app_id"],
            "app_time" => round(microtime(true) * 1000),
            "app_trans_id" => date("ymd") . "_" . $transID,
            "app_user" => "user123",
            "item" => $items,
            "embed_data" => $embeddata,
            "amount" => (int) $request->TotalAmount,
            "description" => "Lazada - Payment for the order #$transID",
            "bank_code" => "" ,
            "callback_url" => env('ZALOPAY_RETURN_URL'),
        ];

        $data = $order["app_id"] . "|" . $order["app_trans_id"] . "|" . $order["app_user"] . "|" . $order["amount"]
            . "|" . $order["app_time"] . "|" . $order["embed_data"] . "|" . $order["item"];
        $order["mac"] = hash_hmac("sha256", $data, $config["key1"]);

        $context = stream_context_create([
            "http" => [
                "header" => "Content-type: application/x-www-form-urlencoded\r\n",
                "method" => "POST",
                "content" => http_build_query($order)
            ]
        ]);

        $resp = file_get_contents($config["endpoint"], false, $context);
        $result = json_decode($resp, true);

        if ($result['return_code'] == 1) {
            return response()->json(['vnpay_url' => $result['order_url']]);
        } else {
            return response()->json([
                'message' => $result['return_message'] ?? 'Transaction failed',
                'code' => $result['return_code'] ?? 0
            ]);
        }
    }

    public function paymentCallback(Request $request)
    {
        $result = [];

        try {
            $key2 = env('ZALOPAY_KEY2');
            $postdata = file_get_contents('php://input');
            $postdatajson = json_decode($postdata, true);
            $mac = hash_hmac("sha256", $postdatajson["data"], $key2);

            $requestmac = $postdatajson["mac"];

            if (strcmp($mac, $requestmac) != 0) {
                $result["return_code"] = -1;
                $result["return_message"] = "mac not equal";
            } else {
                $data = json_decode($postdatajson["data"], true);

                $embedData = json_decode($data['embed_data'], true);

                $userID = $embedData['UserID'] ?? null;

                $cart = (new Cart())->getCartByUserID($embedData['UserID']);

                $codeOrder = (string) Str::uuid();

                $address = (new Addresses())->getDistrictID($embedData['UserID']);

                if ($embedData['CouponID'] != null) {
                    (new CouponController())->updateDiscount($embedData['CouponID']);
                    $discount = ($embedData['Discount'] == null) ? 0 : $embedData['Discount'];
                }
                Log::info('UserID from embed_data:');
                $shippingFee = (new AddressController())->getShippingFee($request, $embedData['UserID']);
                $totalShippingFee = $shippingFee->original['data']['total'];

                $dataOrder = [
                    'UserID' => $embedData['UserID'],
                    'AddressID' => $address->AddressID,
                    'CartID' => $cart->CartID,
                    'OrderCode' => $codeOrder,
                    'ShippingFee' => $totalShippingFee,
                    'Discount' => $discount ?? 0
                ];

                $orderID = (new Order())->createOrder($dataOrder);

                $cartItems = (new CartItems())->getCartItem($cart->CartID, 'ACTIVE');

                foreach ($cartItems as $cartItem) {
                    $this->createOrderItem($orderID, $cartItem);
                }

                $paymentData = [
                    'OrderID' => $orderID,
                    'PaymentMethodID' => 3,
                    'PaymentStatusID' => 2,
                    'Amount' => $data['amount'],
                    'TransactionID' => $data['zp_trans_id'],
                    'BankCode' => $data['channel']?? 'VISA',
                    'CardType' => $data['card_type'] ?? null,
                    'OrderInfo' => $data['order_info'] ?? 'No Order Info',
                    'ResponseCode' => $data['response_code'] ?? null,
                ];

                $this->processPayment($paymentData, $cartItems, $cart);

            }
        } catch (Exception $e) {
            $result["return_code"] = 0;
            $result["return_message"] = $e->getMessage();
        }

    }

    private function createOrderItem($orderID, $cartItem)
    {
        $orderItemData = [
            'OrderID' => $orderID,
            'ProductID' => $cartItem->ProductID,
            'VariantID' => $cartItem->VariantID,
            'Quantity' => $cartItem->Quantity,
        ];
        (new OrderItems())->createOrderItem($orderItemData);
    }

    private function processPayment($paymentData, $cartItems, $cart)
    {

        (new Payments())->createPayment($paymentData);

        (new CartItems())->deleteCartItemByCartID($cart->CartID, 'ACTIVE');

        foreach ($cartItems as $cartItem) {
            $variant = (new ProductVariant())->getVariantByIDAdmin($cartItem->VariantID);

            (new ProductVariant())->updateQuantity($cartItem->VariantID, $variant->Quantity - $cartItem->Quantity);
        }
    }

}
