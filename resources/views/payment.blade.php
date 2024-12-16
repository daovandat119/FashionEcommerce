<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Order API</title>
</head>
<body>
    <h1>Đặt hàng</h1>
    <form id="orderForm" method="POST" action="{{ route('payment.process') }}">
        @csrf
        <label for="PaymentMethodID">Phương thức thanh toán:</label>
        <input type="number" id="PaymentMethodID" name="PaymentMethodID" required>
        <br>
        <label for="TotalAmount">Tổng số tiền:</label>
        <input type="number" id="TotalAmount" name="TotalAmount" required>
        <br>
        <button type="submit">Gửi</button>
    </form>

</body>
</html>
