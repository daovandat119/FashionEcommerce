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

class PaymentController extends Controller
{

    public function addPayment($userId, Request $request) {

        $vnp_TmnCode = env('VNP_TMN_CODE');
        $vnp_HashSecret = env('VNP_HASH_SECRET');
        $vnp_Url = env('VNP_URL');
        $vnp_ReturnUrl = env('VNP_RETURN_URL');

        $data = [
            'vnp_TxnRef' => time(),
            'UserID' => $userId,
            'CouponID' => $request->CouponID,
        ];

        $jsonData = json_encode($data);
        $base64Data = base64_encode($jsonData);
        $vnp_TxnRef = $base64Data;
        $vnp_OrderInfo = "Thanh toán cho đơn hàng";
        $vnp_OrderType = "billpayment";
        $vnp_Amount = $request->TotalAmount * 100;
        $vnp_Locale = 'vn';
        $vnp_IpAddr = $request->ip();

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_ReturnUrl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        if (isset($request->bankCode) && $request->bankCode != "") {
            $inputData['vnp_BankCode'] = $request->bankCode;
        }

        ksort($inputData);
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            $hashdata .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        $hashdata = rtrim($hashdata, '&');

        $vnp_Url = $vnp_Url . "?" . http_build_query($inputData);
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= '&vnp_SecureHash=' . $vnpSecureHash;
        }

        return response()->json(['vnpay_url' => $vnp_Url]);
    }

    public function vnpayReturn(Request $request) {

        $vnp_SecureHash = $request->vnp_SecureHash;
        $inputData = $request->all();
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $hashData = "";
        foreach ($inputData as $key => $value) {
            $hashData .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        $hashData = rtrim($hashData, '&');

        $secureHash = hash_hmac('sha512', $hashData, env('VNP_HASH_SECRET'));

        if ($secureHash === $vnp_SecureHash) {
            if ($request->vnp_ResponseCode == '00') {
                $data = json_decode(base64_decode($request->vnp_TxnRef), true);

                $cart = (new Cart())->getCartByUserID($data['UserID']);

                $codeOrder = (string) Str::uuid();

                $address = (new Addresses())->getDistrictID($data['UserID']);

                (new CouponController())->updateDiscount($request->CouponID);

                $dataOrder = [
                    'UserID' => $data['UserID'],
                    'AddressID' => $address->AddressID,
                    'CartID' => $cart->CartID,
                    'OrderCode' => $codeOrder,
                ];

                $orderID = (new Order())->createOrder($dataOrder);

                $cartItems = (new CartItems())->getCartItem($cart->CartID);

                foreach ($cartItems as $cartItem) {
                    $this->createOrderItem($orderID, $cartItem);
                }

                $paymentData = [
                    'OrderID' => $orderID,
                    'PaymentMethodID' => 2,
                    'PaymentStatusID' => 2,
                    'Amount' => $request->vnp_Amount / 100,
                    'TransactionID' => $data['vnp_TxnRef'],
                    'BankCode' => $request->vnp_BankCode,
                    'CardType' => $request->vnp_CardType,
                    'OrderInfo' => $request->vnp_OrderInfo,
                    'ResponseCode' => $request->vnp_ResponseCode,
                ];

                $this->processPayment($paymentData, $cartItems, $cart);

                return redirect()->to("http://localhost:5173/shop_order_complete/$orderID");
            } else {
                return response()->json(['message' => 'Thanh toán không thành công'], 400);
            }
        } else {
            return response()->json(['message' => 'Mã bảo mật không hợp lệ'], 400);
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

        (new CartItems())->deleteCartItemByCartID($cart->CartID);

        foreach ($cartItems as $cartItem) {
            $variant = (new ProductVariant())->getVariantByIDAdmin($cartItem->VariantID);

            (new ProductVariant())->updateQuantity($cartItem->VariantID, $variant->Quantity - $cartItem->Quantity);
        }
    }

}
