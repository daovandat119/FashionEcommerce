<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Order API</title>
</head>
<body>
    <h1>Đặt hàng</h1>
    <form id="orderForm">
        <label for="PaymentMethodID">Phương thức thanh toán:</label>
        <input type="number" id="PaymentMethodID" name="PaymentMethodID" required>
        <br>
        <label for="TotalAmount">Tổng số tiền:</label>
        <input type="number" id="TotalAmount" name="TotalAmount" required>
        <br>
        <button type="submit">Gửi</button>
    </form>

    <div id="response"></div>

    <script>
        document.getElementById('orderForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const paymentMethodID = document.getElementById('PaymentMethodID').value;
            const totalAmount = document.getElementById('TotalAmount').value;

            fetch('http://127.0.0.1:8000/api/order', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    PaymentMethodID: paymentMethodID,
                    TotalAmount: totalAmount
                }),
            })

        });
    </script>
</body>
</html>
