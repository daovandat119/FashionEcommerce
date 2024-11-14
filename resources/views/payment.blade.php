<!DOCTYPE html>
<html>
<head>
    <title>Thanh toán VNPAY</title>
</head>
<body>
    <h2>Thanh toán đơn hàng</h2>
    <form action="{{ route('pay') }}" method="post">
        @csrf
        <input type="number" name="totalAmount" value="100000">
        <input type="number" name="userId" value="1">
        <button type="submit">Thanh toán với VNPAY</button>
    </form>
</body>
</html>
