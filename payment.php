<?php
session_start();
require_once __DIR__ . '/include/config.php';
require_once __DIR__ . '/include/functions.php';

if (empty($_SESSION['pending_booking'])) {
    header('Location: index.php');
    exit;
}

$b = $_SESSION['pending_booking'];
unset($_SESSION['pending_booking']);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thanh toán - <?= $b['booking_code'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; font-family: 'Poppins', sans-serif; }
        .card { border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); }
        .qr-img { max-width: 300px; border: 5px solid #e74c3c; border-radius: 15px; }
        .countdown { font-size: 3rem; font-weight: bold; color: #e74c3c; }
        .header-bg { background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; padding: 50px 0; text-align: center; }
    </style>
</head>
<body>
    <div class="header-bg">
        <div class="container">
            <h1 class="display-4"><i class="fas fa-check-circle"></i> ĐẶT TOUR THÀNH CÔNG!</h1>
            <h2>Mã đặt chỗ: <strong><?= $b['booking_code'] ?></strong></h2>
        </div>
    </div>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body p-5">
                        <h3 class="text-center mb-4"><?= htmlspecialchars($b['tour_title']) ?></h3>
                        <p class="text-center text-muted"><i class="fas fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($b['start_date'])) ?></p>

                        <div class="text-center my-5">
                            <h2 class="text-danger fw-bold"><?= format_price($b['total_price']) ?></h2>
                            <?php if ($b['discount'] > 0): ?>
                                <p class="text-decoration-line-through">Giá gốc: <?= format_price($b['original_price']) ?></p>
                                <p class="text-success fw-bold">Tiết kiệm: <?= format_price($b['discount']) ?></p>
                            <?php endif; ?>
                        </div>

                        <?php if ($b['payment_method'] === 'bank_transfer'): ?>
                            <div class="text-center my-5">
                                <p class="fw-bold fs-4">Quét QR thanh toán ngay</p>
                                <img src="<?= $b['qr_code'] ?>" class="qr-img" alt="QR Code">
                                <p class="mt-3 text-danger fw-bold">Nội dung chuyển khoản: <?= $b['booking_code'] ?></p>
                            </div>

                            <div class="alert alert-warning text-center">
                                <i class="fas fa-clock"></i> Thời gian giữ chỗ: <span class="countdown" id="countdown">30:00</span>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info text-center p-5">
                                <i class="fas fa-building fa-3x mb-3"></i>
                                <h4>THANH TOÁN TẠI VĂN PHÒNG</h4>
                                <p>Vui lòng đến thanh toán trong 24h</p>
                            </div>
                        <?php endif; ?>

                        <div class="text-center mt-5">
                            <a href="index.php" class="btn btn-outline-secondary btn-lg px-5">Về trang chủ</a>
                            <a href="mailto:<?= $b['customer_email'] ?>" class="btn btn-success btn-lg px-5 ms-3">Gửi lại email</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let time = 1800;
        const cd = document.getElementById('countdown');
        if (cd) {
            const timer = setInterval(() => {
                time--;
                let m = String(Math.floor(time / 60)).padStart(2, '0');
                let s = String(time % 60).padStart(2, '0');
                cd.textContent = m + ':' + s;
                if (time <= 0) clearInterval(timer);
            }, 1000);
        }
    </script>
</body>
</html>