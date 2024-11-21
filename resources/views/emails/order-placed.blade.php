<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation</title>
</head>
<body>
    <h1>Thank you for your order, {{ $order['UserName'] }}</h1>
    <p>Your Order Code: <strong>{{ $order['OrderCode'] }}</strong></p>
    <p>We will notify you once your order is shipped.</p>
    <p>Total Amount: <strong>{{ $order['TotalAmount'] }} VND</strong></p>
    <p>Thank you for shopping with us!</p>
</body>
</html>
