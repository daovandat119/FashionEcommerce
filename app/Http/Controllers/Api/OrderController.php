<?php

namespace App\Http\Controllers\Api;
use App\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CartItems;
class OrderController extends Controller
{
    protected $order;

    public function __construct()
    {
        $this->order = new Order();
    }

    public function index()
    {
        $order = $this->order->getOrder();
        return response()->json(['message' => 'Success', 'data' => $order], 200);
    }

    public function create(OrderRequest $request)
    {
        $userId = 4;

        $cartItems = (new CartItems())->getCartByUserID($userId);

        

        $order = $this->order->createOrder($request);
        return response()->json(['message' => 'Success', 'data' => $order], 200);
    }
}
