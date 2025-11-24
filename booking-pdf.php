<?php
require_once 'include/config.php';
require_once 'include/functions.php';

// Bắt buộc đăng nhập
if (!isset($_SESSION['user_id'])) {
    die('Bạn cần đăng nhập để tải vé!');
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Vé không tồn tại!');
}

$booking_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// LẤY THÔNG TIN BOOKING CHI TIẾT
$stmt = $pdo->prepare("
    SELECT b.*, t.title AS tour_title, t.image, t.hotel, t.duration, t.days, t.nights, t.airline,
           td.start_date, td.end_date, td.departure_code, td.price_adult, td.price_child, td.price_infant
    FROM bookings b
    JOIN tours t ON b.tour_id = t.id
    JOIN tour_departures td ON b.departure_id = td.id
    WHERE b.id = ? AND b.user_id = ?
");
$stmt->execute([$booking_id, $user_id]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    die('Vé không tồn tại hoặc bạn không có quyền xem!');
}

// Lấy voucher nếu có
$voucher = '';
if ($booking['voucher_id']) {
    $stmt = $pdo->prepare("SELECT ma_voucher, giam_gia FROM vouchers WHERE id = ?");
    $stmt->execute([$booking['voucher_id']]);
    $v = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($v) {
        $voucher = "Mã giảm giá: <strong>{$v['ma_voucher']}</strong> (-{$v['giam_gia']}% - " . format_price($booking['discount_amount']) . ')';
    }
}

// Tính tổng trước giảm (nếu có voucher)
$total_before_discount = $booking['total_price'] + $booking['discount_amount'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Vé Tour - <?= $booking['booking_code'] ?></title>
    <meta name="author" content="Travela">
    <style>
        body { 
            font-family: 'DejaVu Sans', 'Arial', sans-serif; 
            margin: 0; 
            padding: 20px; 
            background: #f0f2f5; 
            color: #333; 
        }
        .ticket { 
            max-width: 900px; 
            margin: auto; 
            background: white; 
            border-radius: 20px; 
            overflow: hidden; 
            box-shadow: 0 20px 60px rgba(0,0,0,0.15); 
        }
        .header { 
            background: linear-gradient(135deg, #e74c3c, #c0392b); 
            color: white; 
            padding: 40px; 
            text-align: center; 
        }
        .header h1 { margin: 0; font-size: 2.8rem; font-weight: 800; }
        .header p { margin: 10px 0 0; font-size: 1.4rem; }
        .content { padding: 40px; }
        .tour-img { width: 100%; height: 350px; object-fit: cover; border-radius: 15px; margin-bottom: 30px; }
        .info-row { display: flex; justify-content: space-between; margin: 15px 0; font-size: 1.1rem; }
        .info-label { font-weight: bold; color: #2c3e50; width: 200px; }
        .price-table { width: 100%; border-collapse: collapse; margin: 30px 0; font-size: 1.1rem; }
        .price-table td { padding: 12px 15px; border-bottom: 1px dashed #ddd; }
        .price-total td { background: #e74c3c; color: white; font-size: 1.6rem; font-weight: bold; padding: 20px; }
        .qr-code { text-align: center; margin: 40px 0; }
        .qr-code img { max-width: 220px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .footer { text-align: center; padding: 30px; background: #2c3e50; color: white; margin-top: 50px; border-radius: 0 0 20px 20px; }
        .badge-status { padding: 12px 40px; border-radius: 50px; font-weight: bold; font-size: 1.2rem; margin: 20px 0; display: inline-block; }
        .text-success { color: #27ae60; }
        .text-danger { color: #e74c3c; }
        @media print {
            body { background: white; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="ticket">
        <div class="header">
            <h1>TRAVELA - VÉ TOUR DU LỊCH</h1>
            <p>Mã vé: <strong><?= $booking['booking_code'] ?></strong></p>
        </div>

        <div class="content">
            <img src="<?= BASE_URL ?>uploads/<?= htmlspecialchars($booking['image'] ?? 'default.jpg') ?>" class="tour-img" alt="Tour">

            <h2 class="text-center mb-4"><?= htmlspecialchars($booking['tour_title']) ?></h2>

            <div class="info-row">
                <div class="info-label">Khởi hành:</div>
                <div><strong><?= date('d/m/Y', strtotime($booking['start_date'])) ?></strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Kết thúc:</div>
                <div><strong><?= date('d/m/Y', strtotime($booking['end_date'])) ?></strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Thời gian:</div>
                <div><strong><?= $booking['duration'] ?></strong> (<?= $booking['days'] ?> ngày <?= $booking['nights'] ?> đêm)</div>
            </div>
            <div class="info-row">
                <div class="info-label">Mã lịch:</div>
                <div><strong><?= htmlspecialchars($booking['departure_code']) ?></strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Số khách:</div>
                <div>
                    <strong>
                        <?= $booking['adults'] ?> người lớn
                        <?= $booking['children'] > 0 ? " + {$booking['children']} trẻ em" : '' ?>
                        <?= $booking['infants'] > 0 ? " + {$booking['infants']} trẻ nhỏ" : '' ?>
                    </strong>
                </div>
            </div>

            <hr class="my-5">

            <h3 class="text-primary mb-4 text-center fw-bold">CHI TIẾT THANH TOÁN</h3>
            <table class="price-table">
                <tr>
                    <td>Người lớn x <?= $booking['adults'] ?></td>
                    <td class="text-end"><?= format_price($booking['price_adult'] * $booking['adults']) ?></td>
                </tr>
                <?php if ($booking['children'] > 0): ?>
                <tr>
                    <td>Trẻ em x <?= $booking['children'] ?></td>
                    <td class="text-end"><?= format_price($booking['price_child'] * $booking['children']) ?></td>
                </tr>
                <?php endif; ?>
                <?php if ($booking['infants'] > 0): ?>
                <tr>
                    <td>Trẻ nhỏ x <?= $booking['infants'] ?></td>
                    <td class="text-end"><?= format_price($booking['price_infant'] * $booking['infants']) ?></td>
                </tr>
                <?php endif; ?>
                <?php if ($voucher): ?>
                <tr class="text-success fw-bold">
                    <td><?= $voucher ?></td>
                    <td class="text-end">- <?= format_price($booking['discount_amount']) ?></td>
                </tr>
                <?php endif; ?>
                <tr class="price-total">
                    <td><strong>TỔNG THANH TOÁN</strong></td>
                    <td class="text-end"><strong><?= format_price($booking['total_price']) ?></strong></td>
                </tr>
            </table>

            <?php if ($booking['qr_code']): ?>
            <div class="qr-code">
                <p class="fw-bold fs-4 mb-3">MÃ QR CHECK-IN / THANH TOÁN</p>
                <img src="<?= $booking['qr_code'] ?>" class="qr-code">
            </div>
            <?php endif; ?>

            <div class="text-center my-5">
                <p class="fs-5">
                    <strong>Khách hàng:</strong> <?= htmlspecialchars($booking['customer_name']) ?><br>
                    <strong>Số điện thoại:</strong> <?= $booking['customer_phone'] ?><br>
                    <strong>Email:</strong> <?= $booking['customer_email'] ?>
                </p>
                <?php if (!empty($booking['notes'])): ?>
                <div class="alert alert-info">
                    <strong>Ghi chú:</strong> <?= nl2br(htmlspecialchars($booking['notes'])) ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="footer">
            <p class="mb-1">Cảm ơn quý khách đã tin tưởng Travela!</p>
            <p>Hotline: 1900 1234 | Email: support@travela.vn</p>
        </div>
    </div>

    <!-- Nút in/tải PDF (chỉ hiện trên màn hình, không in ra) -->
    <div class="text-center my-5 no-print">
        <button onclick="window.print()" class="btn btn-success btn-lg px-5">
            <i class="bi bi-printer"></i> In / Tải vé PDF ngay
        </button>
        <a href="booking-detail.php?id=<?= $booking_id ?>" class="btn btn-outline-primary btn-lg px-5 ms-3">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>
</body>
</html>