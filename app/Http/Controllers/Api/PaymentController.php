<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Payments;


class PaymentController extends Controller
{
    protected $payment;

    public function __construct()
    {
        $this->payment = new Payments();
    }

    public function createPayment($orderCode, $totalAmount, Request $request) {
        $vnp_TmnCode = env('VNP_TMN_CODE');
        $vnp_HashSecret = env('VNP_HASH_SECRET');
        $vnp_Url = env('VNP_URL');
        $vnp_ReturnUrl = env('VNP_RETURN_URL');

       $order = (object)[
          "code" => 1000,
          "total" => 100000,
          "bankCode" => 'NCB',
          "type" => "billpayment",
          "info" => "Thanh toán đơn hàng"
       ];

        $vnp_TxnRef = 10000;
        $vnp_OrderInfo = "ok";
        $vnp_OrderType = "BNC";
        $vnp_Amount = 10000 * 100;
        $vnp_Locale = 'vn';
        $vnp_BankCode = "NCB";
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
         if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
         }

         if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
             $inputData['vnp_Bill_State'] = $vnp_Bill_State;
         }

         ksort($inputData);

         $query = "";
         $i = 0;
         $hashdata = "";

         foreach ($inputData as $key => $value) {
             if ($i == 1) {
                 $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
             } else {
                 $hashdata .= urlencode($key) . "=" . urlencode($value);
                 $i = 1;
             }
             $query .= urlencode($key) . "=" . urlencode($value) . '&';
         }

         $vnp_Url = $vnp_Url . "?" . $query;

         if (isset($vnp_HashSecret)) {
             $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
             $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
         }

          return redirect($vnp_Url);
    }

    public function vnpayReturn(Request $request)
       {
           $vnp_SecureHash = $request->vnp_SecureHash;
           $inputData = $request->all();

           unset($inputData['vnp_SecureHash']);
           ksort($inputData);
           $hashData = "";
           foreach ($inputData as $key => $value) {
               $hashData .= urlencode($key) . "=" . urlencode($value) . '&';
           }
           $hashData = rtrim($hashData, '&');

           $secureHash = hash_hmac('sha512', $hashData, config('vnpay.vnp_HashSecret'));

           if ($secureHash === $vnp_SecureHash) {
               if ($request->vnp_ResponseCode == '00') {
                   $transactionID = $request->vnp_TransactionNo;

                   $paymentData = [
                       'OrderID' => $orderCode,
                       'PaymentMethodID' => 2,
                       'PaymentStatusID' => 2,
                       'Amount' => $totalAmount,
                       'TransactionID' => $transactionID,
                   ];

                   (new Payments())->createPayment($paymentData);

                   return 'Thanh toán thành công';
               } else {
                   return 'Thanh toán không thành công';
               }
           } else {
               return 'Thanh toán không thành công';
           }
       }
}
