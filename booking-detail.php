<?php
ob_start();
require_once 'include/config.php';
require_once 'include/functions.php';

// Bắt buộc đăng nhập
if (!isset($_SESSION['user_id'])) {
    redirect('login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die(show_alert('danger', 'Vé không tồn tại!'));
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
    die(show_alert('danger', 'Vé không tồn tại hoặc bạn không có quyền xem!'));
}

// Lấy voucher nếu có
$voucher = null;
if ($booking['voucher_id']) {
    $stmt = $pdo->prepare("SELECT ma_voucher, giam_gia FROM vouchers WHERE id = ?");
    $stmt->execute([$booking['voucher_id']]);
    $voucher = $stmt->fetch(PDO::FETCH_ASSOC);
}

// XỬ LÝ THANH TOÁN – CHỈ CẬP NHẬT payment_status = 'paid'
if (isset($_POST['pay_now'])) {
    if ($booking['payment_status'] === 'paid') {
        $error_msg = "Vé này đã được thanh toán rồi!";
    } elseif ($booking['status'] === 'cancelled') {
        $error_msg = "Không thể thanh toán vé đã hủy!";
    } else {
        try {
            $pdo->beginTransaction();

            $pdo->prepare("UPDATE bookings SET payment_status = 'paid', updated_at = NOW() WHERE id = ? AND user_id = ?")
                ->execute([$booking_id, $user_id]);

            $pdo->commit();

            $success_msg = "Thanh toán thành công! Vé đang <strong>CHỜ ADMIN DUYỆT</strong> để xác nhận cuối cùng. Cảm ơn quý khách!";

            header("Location: booking-detail.php?id=$booking_id");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_msg = "Lỗi hệ thống khi thanh toán. Vui lòng thử lại!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chi tiết vé #<?= $booking['booking_code'] ?> - Travela</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); }
        .header-bg { background: linear-gradient(rgba(231,76,60,0.95), rgba(192,57,43,0.95)); color: white; padding: 100px 0 60px; border-radius: 0 0 40px 40px; }
        .page-title { font-size: 3.5rem; font-weight: 800; text-shadow: 0 5px 15px rgba(0,0,0,0.4); }
        .detail-card { border-radius: 25px; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.15); background: white; }
        .tour-img { height: 100%; object-fit: cover; }
        .status-badge { font-size: 1.1rem; padding: 14px 35px; border-radius: 50px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        .price-line { font-size: 1.1rem; padding: 12px 0; border-bottom: 1px dashed #eee; }
        .price-big { font-size: 3.5rem; font-weight: 800; color: #e74c3c; }
        .btn-action { border-radius: 50px; padding: 16px 40px; font-weight: 700; font-size: 1.1rem; min-width: 220px; box-shadow: 0 8px 25px rgba(0,0,0,0.2); transition: all 0.3s; }
        .btn-action:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.3); }
        .qr-code { max-width: 220px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
    </style>
</head>
<body>
    <?php include 'include/navbar.php'; ?>

    <div class="header-bg text-center">
        <div class="container">
            <h1 class="page-title">
                <i class="bi bi-ticket-perforated me-3"></i>CHI TIẾT VÉ TOUR
            </h1>
            <p class="lead opacity-90">Mã vé: <strong class="text-warning"><?= $booking['booking_code'] ?></strong></p>
        </div>
    </div>

    <div class="container py-5 mt-5">
        <?php if (isset($success_msg)): ?>
            <div class="alert alert-success text-center fs-4 rounded-4 shadow">
                <i class="bi bi-check2-circle fs-1 mb-3"></i><br>
                <?= $success_msg ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_msg)): ?>
            <div class="alert alert-danger text-center fs-4 rounded-4 shadow">
                <i class="bi bi-exclamation-triangle fs-1 mb-3"></i><br>
                <?= $error_msg ?>
            </div>
        <?php endif; ?>

        <div class="detail-card">
            <div class="row g-0">
                <div class="col-lg-5">
                    <img src="<?= BASE_URL ?>uploads/<?= htmlspecialchars($booking['image'] ?? 'default.jpg') ?>" class="tour-img w-100" alt="<?= htmlspecialchars($booking['tour_title']) ?>">
                    <?php if ($booking['status'] == 'cancelled'): ?>
                        <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-75 d-flex align-items-center justify-content-center">
                            <h2 class="text-white fw-bold">ĐÃ HỦY</h2>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-7">
                    <div class="p-4 p-lg-5">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h2 class="fw-bold mb-2"><?= htmlspecialchars($booking['tour_title']) ?></h2>
                                <p class="text-muted mb-0">
                                    <i class="bi bi-tag-fill text-primary me-2"></i>Mã lịch: <strong><?= htmlspecialchars($booking['departure_code']) ?></strong>
                                </p>
                            </div>

                            <?php
                            if ($booking['status'] == 'cancelled') {
                                $status = 'ĐÃ HỦY'; $badge = 'bg-dark';
                            } elseif ($booking['payment_status'] == 'paid' && $booking['status'] == 'confirmed') {
                                $status = 'HOÀN TẤT'; $badge = 'bg-success';
                            } elseif ($booking['payment_status'] == 'paid') {
                                $status = 'CHỜ DUYỆT'; $badge = 'bg-warning text-dark';
                            } elseif ($booking['status'] == 'confirmed') {
                                $status = 'ĐÃ DUYỆT'; $badge = 'bg-primary';
                            } else {
                                $status = 'CHỜ THANH TOÁN'; $badge = 'bg-secondary';
                            }
                            ?>
                            <span class="status-badge <?= $badge ?>"><?= $status ?></span>
                        </div>

                        <!-- Thông tin nhanh -->
                        <div class="row g-4 mb-5">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-calendar-event text-primary fs-4 me-3"></i>
                                    <div>
                                        <small class="text-muted">Khởi hành</small>
                                        <h5 class="fw-bold mb-0"><?= date('d/m/Y', strtotime($booking['start_date'])) ?></h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-calendar-check text-success fs-4 me-3"></i>
                                    <div>
                                        <small class="text-muted">Kết thúc</small>
                                        <h5 class="fw-bold mb-0"><?= date('d/m/Y', strtotime($booking['end_date'])) ?></h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-clock-history text-info fs-4 me-3"></i>
                                    <div>
                                        <small class="text-muted">Thời gian</small>
                                        <h5 class="fw-bold mb-0"><?= $booking['days'] ?> ngày <?= $booking['nights'] ?> đêm</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-people-fill text-warning fs-4 me-3"></i>
                                    <div>
                                        <small class="text-muted">Số khách</small>
                                        <h5 class="fw-bold mb-0">
                                            <?= $booking['adults'] ?> NL
                                            <?= $booking['children'] > 0 ? " + {$booking['children']} TE" : '' ?>
                                            <?= $booking['infants'] > 0 ? " + {$booking['infants']} EB" : '' ?>
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- BẢNG GIÁ CHI TIẾT -->
                        <div class="bg-light rounded-4 p-4 mb-5">
                            <h4 class="fw-bold mb-4 text-primary">Chi tiết giá</h4>
                            <div class="price-line d-flex justify-content-between">
                                <span>Người lớn x <?= $booking['adults'] ?></span>
                                <span><?= format_price($booking['price_adult'] * $booking['adults']) ?></span>
                            </div>
                            <?php if ($booking['children'] > 0): ?>
                            <div class="price-line d-flex justify-content-between">
                                <span>Trẻ em x <?= $booking['children'] ?></span>
                                <span><?= format_price($booking['price_child'] * $booking['children']) ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($booking['infants'] > 0): ?>
                            <div class="price-line d-flex justify-content-between">
                                <span>Trẻ nhỏ x <?= $booking['infants'] ?></span>
                                <span><?= format_price($booking['price_infant'] * $booking['infants']) ?></span>
                            </div>
                            <?php endif; ?>

                            <?php if ($voucher): ?>
                            <div class="price-line d-flex justify-content-between text-success fw-bold">
                                <span>Giảm giá voucher (<?= $voucher['giam_gia'] ?>%)</span>
                                <span>- <?= format_price($booking['discount_amount']) ?></span>
                            </div>
                            <?php endif; ?>

                            <hr class="my-4 border-3">
                            <div class="d-flex justify-content-between align-items-end">
                                <h3 class="fw-bold">TỔNG THANH TOÁN</h3>
                                <div class="price-big"><?= format_price($booking['total_price']) ?></div>
                            </div>
                        </div>

                        <!-- QR CODE -->
                        <?php if ($booking['qr_code']): ?>
                        <div class="text-center mb-5">
                            <h5 class="fw-bold text-primary mb-3">Mã QR Thanh Toán / Check-in</h5>
                            <img src="<?= $booking['qr_code'] ?>" class="qr-code" alt="QR Code">
                        </div>
                        <?php endif; ?>

                        <!-- NÚT HÀNH ĐỘNG -->
                        <div class="text-center mt-5">
                            <div class="d-flex flex-wrap justify-content-center gap-3">

                                <a href="my-bookings.php" class="btn btn-outline-secondary btn-action">
                                    <i class="bi bi-arrow-left me-2"></i> Quay lại
                                </a>

                                <?php if ($booking['payment_status'] != 'paid' && $booking['status'] != 'cancelled'): ?>
                                    <form method="POST" class="d-inline">
                                        <button type="submit" name="pay_now" class="btn btn-success btn-action">
                                            <i class="bi bi-credit-card-2-back me-2"></i> THANH TOÁN NGAY
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <a href="booking-pdf.php?id=<?= $booking['id'] ?>" target="_blank" class="btn btn-info btn-action text-white">
                                    <i class="bi bi-file-earmark-pdf me-2"></i> TẢI VÉ PDF
                                </a>

                                <?php if ($booking['status'] == 'pending' && $booking['payment_status'] != 'paid'): ?>
                                    <button onclick="cancelBooking(<?= $booking['id'] ?>)" class="btn btn-outline-danger btn-action">
                                        <i class="bi bi-x-circle me-2"></i> HỦY VÉ
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'include/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    function cancelBooking(id) {
        Swal.fire({
            title: 'Xác nhận hủy vé?',
            text: "Hành động này không thể hoàn tác!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Có, hủy vé!',
            cancelButtonText: 'Không'
        }).then((result) => {
            if (result.isConfirmed) {
                const reason = prompt('Vui lòng nhập lý do hủy vé:');
                if (reason && reason.trim() !== '') {
                    window.location.href = 'cancel-booking.php?id=' + id + '&reason=' + encodeURIComponent(reason.trim());
                } else if (reason !== null) {
                    Swal.fire('Lỗi', 'Bạn phải nhập lý do hủy!', 'error');
                }
            }
        });
    }
    </script>
</body>
</html>