<?php

namespace App\Http\Controllers\Api;
use App\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\OrderItems;
use Illuminate\Support\Str;
use App\Models\Payments;
use App\Http\Requests\OrderRequest;
use App\Http\Controllers\Api\PaymentController;
use App\Models\CartItems;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Http;
use App\Models\Addresses;
class OrderController extends Controller
{
    protected $order;

    public function __construct()
    {
        $this->order = new Order();
    }

    public function index()
    {
        $userId = auth()->id();

        $role = auth()->user()->role->RoleName;

        $order = $this->order->getOrder($role == 'Admin' ? null : $userId);

        return response()->json(['message' => 'Success', 'data' => $order], 200);
    }

    public function store(OrderRequest $request)
    {
        $userId = auth()->id();
        $cart = (new Cart())->getCartByUserID($userId);
        $codeOrder = (string) Str::uuid();
        $address = (new Addresses())->getDistrictID($userId);

        $dataOrder = [
            'UserID' => $userId,
            'AddressID' => $address->AddressID,
            'CartID' => $cart->CartID,
            'OrderCode' => $codeOrder,
        ];

        $orderID = $this->order->createOrder($dataOrder);

        $cartItems = (new CartItems())->getCartItem($cart->CartID);

        foreach ($cartItems as $cartItem) {
            $this->createOrderItem($orderID, $cartItem);
        }

        if ($request->PaymentMethodID == 1) {
            $this->processPayment($cartItems, $orderID, $request, $cart);

            return response()->json(['message' => 'Order created successfully, waiting for delivery.'], 201);
        } else {
            return (new PaymentController())->addPayment($userId, $request->TotalAmount, $request);
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

    private function processPayment($cartItems, $orderID, $request, $cart)
    {
        $paymentData = [
            'OrderID' => $orderID,
            'PaymentMethodID' => $request->PaymentMethodID,
            'PaymentStatusID' => 1,
            'Amount' => $request->TotalAmount,
            'TransactionID' => null,
            'BankCode' => null,
            'CardType' => null,
            'OrderInfo' => null,
            'ResponseCode' => null,
        ];

        (new Payments())->createPayment($paymentData);

        (new CartItems())->deleteCartItemByCartID($cart->CartID);

        foreach ($cartItems as $cartItem) {
            $variant = (new ProductVariant())->getVariantByIDAdmin($cartItem->VariantID);

            (new ProductVariant())->updateQuantity($cartItem->VariantID, $variant->Quantity - $cartItem->Quantity);
        }
    }

    public function getOrderById($id)
    {
        $userId = auth()->id();

        $role = auth()->user()->role->RoleName;

        $order = $this->order->getOrderById($id, $role == 'Admin' ? null : $userId);

        return response()->json(['message' => 'Success', 'data' => $order], 200);
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $userId = auth()->id();

        $role = auth()->user()->role->RoleName;

        $order = $this->order->updateOrderStatus($id, $request->OrderStatusID, $role == 'Admin' ? null : $userId);

        return response()->json(['message' => 'Success', 'data' => $order], 200);
    }

}
