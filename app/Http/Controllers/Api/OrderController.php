<?php

namespace App\Http\Controllers\Api;
use App\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\OrderItems;
use Illuminate\Support\Str;
use App\Models\Payments;

class OrderController extends Controller
{
    protected $order;

    public function __construct()
    {
        $this->order = new Order();
    }

    public function index()
    {
        $userId = 4;
        $order = $this->order->getOrder($userId);
        return response()->json(['message' => 'Success', 'data' => $order], 200);
    }

    public function store(Request $request)
    {
        $userId = 4;

        $cart = (new Cart())->getCartByUserID($userId);

        $codeOrder = Str::random(10);

        $dataOrder = [
            'UserID' => $userId,
            'AddressID' => $request->AddressID,
            'CartID' => $cart->CartID,
            'OrderStatusID' => $request->OrderStatusID,
            'OrderCode' => $codeOrder,
        ];

        $orderID = $this->order->createOrder($dataOrder);

        foreach ($request->products as $product) {
            $orderItemData = [
                'OrderID' => $orderID,
                'ProductID' => $product['ProductID'],
                'VariantID' => $product['VariantID'],
                'Quantity' => $product['Quantity'],
            ];

            (new OrderItems())->createOrderItem($orderItemData);
        }

        $paymentData = [
            'OrderID' => $orderID,
            'PaymentMethodID' => $request->PaymentMethodID,
            'PaymentStatusID' => $request->PaymentStatusID,
        ];

        (new Payments())->createPayment($paymentData);

        return response()->json(['message' => 'Success', 'data' => $orderItemData], 200);
    }


    public function getOrderById($id)
    {
        $userId = 4;
        $order = $this->order->getOrderById($userId, $id);
        return response()->json(['message' => 'Success', 'data' => $order], 200);
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $userId = 4;
        $order = $this->order->updateOrderStatus($userId, $id, $request->OrderStatusID);
        return response()->json(['message' => 'Success', 'data' => $order], 200);
    }

}
