<!DOCTYPE html>
<html>
<head>
    <title>Xác minh tài khoản</title>
</head>
<body>
    <h2>Chào {{ $Username }},</h2>
    <p>Cảm ơn bạn đã đăng ký. Dưới đây là mã xác minh của bạn:</p>
    <h3>{{ $verificationCode }}</h3>
    <p>Mã xác minh này sẽ hết hạn sau 5 phút.</p>
    <p>Trân trọng,<br>Đội ngũ hỗ trợ</p>
</body>
</html>
