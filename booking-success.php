<?php
session_start();
require_once __DIR__ . '/include/config.php';
require_once __DIR__ . '/include/functions.php';

$booking_id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
$stmt->execute([$booking_id]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    die(show_alert('danger', 'Không tìm thấy đặt tour!'));
}

$stmt = $pdo->prepare("SELECT * FROM tours WHERE id = ?");
$stmt->execute([$booking['tour_id']]);
$tour = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM tour_departures WHERE id = ?");
$stmt->execute([$booking['departure_id']]);
$dep = $stmt->fetch(PDO::FETCH_ASSOC);

require_once 'include/head.php';
require_once 'include/navbar.php';
?>

<div class="container py-5">
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white py-3">
            <h5 class="mb-0 fw-bold"><i class="fas fa-check-circle me-2"></i>Đặt tour thành công!</h5>
        </div>
        <div class="card-body p-4">
            <h2 class="mb-4">Mã đặt tour: <?= htmlspecialchars($booking['booking_code']) ?></h2>
            <p>Cảm ơn bạn đã đặt tour <strong><?= htmlspecialchars($tour['title']) ?></strong>!</p>
            <?php if ($booking['payment_method'] === 'bank_transfer'): ?>
                <h3>Hướng dẫn thanh toán</h3>
                <p>Vui lòng chuyển khoản theo thông tin sau:</p>
                <pre class="bg-light p-3 rounded"><?= htmlspecialchars($booking['bank_info']) ?></pre>
                <?php if ($booking['qr_code']): ?>
                    <p>Quét mã QR để thanh toán:</p>
                    <img src="<?= htmlspecialchars($booking['qr_code']) ?>" alt="QR Code" class="img-fluid" style="max-width: 200px;">
                <?php else: ?>
                    <p class="text-danger">Không thể hiển thị mã QR. Vui lòng chuyển khoản theo thông tin trên.</p>
                <?php endif; ?>
            <?php endif; ?>
            <a href="index.php" class="btn btn-primary mt-3"><i class="fas fa-home me-2"></i>Quay về trang chủ</a>
        </div>
    </div>
</div>

<?php require_once 'include/footer.php'; ?>