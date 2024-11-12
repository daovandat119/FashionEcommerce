<!DOCTYPE html>
<html>
<head>
    <title>Thanh toán VNPAY</title>
</head>
<body>
    <h2>Thanh toán đơn hàng</h2>
    <form action="{{ route('pay', ['totalAmount' => 100000, 'userId' => 1]) }}" method="get">
        <input type="number" name="amount" placeholder="Nhập số tiền" required>
        <button type="submit">Thanh toán với VNPAY</button>
    </form>
</body>
</html>
