<?php
ob_start();
require_once 'include/config.php';
require_once 'include/functions.php';

// Bắt buộc đăng nhập
if (!isset($_SESSION['user_id'])) {
    redirect('login.php?return=' . urlencode($_SERVER['REQUEST_URI']));
}

$user_id = $_SESSION['user_id'];

// Lấy tất cả booking của user
$stmt = $pdo->prepare("
    SELECT b.*, t.title AS tour_title, t.image, td.start_date, td.end_date, td.departure_code,
           v.ma_voucher
    FROM bookings b
    LEFT JOIN tours t ON b.tour_id = t.id
    LEFT JOIN tour_departures td ON b.departure_id = td.id
    LEFT JOIN vouchers v ON b.voucher_id = v.id
    WHERE b.user_id = ?
    ORDER BY b.created_at DESC
");
$stmt->execute([$user_id]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vé Của Tôi - Travela</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; }
        .page-header { background: linear-gradient(rgba(231,76,60,0.9), rgba(192,57,43,0.9)), url('assets/img/hero-bg.jpg'); background-size: cover; color: white; padding: 100px 0 60px; margin-bottom: -50px; border-radius: 0 0 50px 50px; }
        .page-title { font-size: 3rem; font-weight: 800; text-shadow: 0 4px 10px rgba(0,0,0,0.3); }
        .booking-card { 
            transition: all 0.4s ease; 
            border: none; 
            border-radius: 25px; 
            overflow: hidden; 
            box-shadow: 0 15px 40px rgba(0,0,0,0.12);
            background: white;
            margin-bottom: 30px;
        }
        .booking-card:hover { 
            transform: translateY(-15px); 
            box-shadow: 0 30px 60px rgba(231,76,60,0.25); 
        }
        .tour-img { height: 300px; object-fit: cover; }
        .status-badge {
            font-size: 1rem; 
            padding: 12px 30px; 
            border-radius: 50px; 
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .price-big { font-size: 2.2rem; font-weight: 800; color: #e74c3c; }
        .btn-action {
            border-radius: 50px; 
            padding: 14px 32px; 
            font-weight: 600; 
            min-width: 180px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            transition: all 0.3s;
        }
        .btn-action:hover { transform: translateY(-4px); box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
        .empty-state { text-align: center; padding: 80px 20px; }
        .empty-state i { font-size: 5rem; color: #ddd; margin-bottom: 20px; }
        @media (max-width: 768px) {
            .page-title { font-size: 2.2rem; }
            .tour-img { height: 220px; }
            .price-big { font-size: 1.8rem; }
        }
    </style>
</head>
<body>
    <?php include 'include/navbar.php'; ?>

    <!-- Header -->
    <div class="page-header text-center">
        <div class="container">
            <h1 class="page-title">
                <i class="bi bi-ticket-detailed me-3"></i>VÉ CỦA TÔI
            </h1>
            <p class="lead mb-0 opacity-90">Theo dõi và quản lý tất cả các chuyến đi của bạn</p>
        </div>
    </div>

    <div class="container py-5 mt-5">
        <?php if (empty($bookings)): ?>
            <div class="empty-state bg-white rounded-4 shadow-lg p-5">
                <i class="bi bi-inbox"></i>
                <h3 class="mt-4 text-muted">Bạn chưa đặt tour nào</h3>
                <p class="text-muted mb-4 fs-5">Hãy khám phá và đặt ngay hành trình mơ ước của bạn!</p>
                <a href="index.php" class="btn btn-danger btn-lg px-5 rounded-pill shadow-lg">
                    <i class="bi bi-compass me-2"></i> Khám Phá Tour Ngay
                </a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($bookings as $b): ?>
                <div class="col-12">
                    <div class="booking-card">
                        <div class="row g-0">
                            <!-- Ảnh tour -->
                            <div class="col-lg-4">
                                <img src="<?= BASE_URL ?>uploads/<?= htmlspecialchars($b['image'] ?? 'default.jpg') ?>" 
                                     class="img-fluid tour-img w-100" alt="<?= htmlspecialchars($b['tour_title']) ?>">
                                <?php if ($b['status'] == 'cancelled'): ?>
                                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-50"></div>
                                    <div class="position-absolute top-50 start-50 translate-middle text-white text-center">
                                        <i class="bi bi-slash-circle fs-1"></i>
                                        <p class="fs-4 fw-bold mb-0">ĐÃ HỦY</p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Thông tin booking -->
                            <div class="col-lg-8">
                                <div class="p-4 p-lg-5">
                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4">
                                        <div>
                                            <span class="text-primary fw-bold fs-5">#<?= $b['booking_code'] ?></span>
                                            <h3 class="fw-bold mt-2 mb-1"><?= htmlspecialchars($b['tour_title']) ?></h3>
                                            <p class="text-muted mb-0">
                                                <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                                Mã lịch: <strong><?= htmlspecialchars($b['departure_code']) ?></strong>
                                            </p>
                                        </div>

                                        <!-- Trạng thái -->
                                        <?php
                                        if ($b['status'] == 'cancelled') {
                                            $status_text = 'ĐÃ HỦY';
                                            $badge = 'bg-dark';
                                        } elseif ($b['payment_status'] == 'paid' && $b['status'] == 'confirmed') {
                                            $status_text = 'HOÀN TẤT';
                                            $badge = 'bg-success';
                                        } elseif ($b['payment_status'] == 'paid') {
                                            $status_text = 'ĐÃ THANH TOÁN';
                                            $badge = 'bg-info';
                                        } elseif ($b['status'] == 'confirmed') {
                                            $status_text = 'ĐÃ DUYỆT';
                                            $badge = 'bg-primary';
                                        } else {
                                            $status_text = 'CHỜ XỬ LÝ';
                                            $badge = 'bg-warning text-dark';
                                        }
                                        ?>
                                        <span class="status-badge <?= $badge ?>"><?= $status_text ?></span>
                                    </div>

                                    <!-- Thông tin chi tiết -->
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center text-muted">
                                                <i class="bi bi-calendar3 text-primary me-3 fs-5"></i>
                                                <div>
                                                    <small>Khởi hành</small><br>
                                                    <strong class="text-dark"><?= date('d/m/Y', strtotime($b['start_date'])) ?></strong>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center text-muted">
                                                <i class="bi bi-calendar-check text-success me-3 fs-5"></i>
                                                <div>
                                                    <small>Kết thúc</small><br>
                                                    <strong class="text-dark"><?= date('d/m/Y', strtotime($b['end_date'])) ?></strong>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center text-muted">
                                                <i class="bi bi-people-fill text-info me-3 fs-5"></i>
                                                <div>
                                                    <small>Số khách</small><br>
                                                    <strong class="text-dark">
                                                        <?= $b['adults'] ?> NL
                                                        <?= $b['children'] > 0 ? " + {$b['children']} TE" : '' ?>
                                                        <?= $b['infants'] > 0 ? " + {$b['infants']} EB" : '' ?>
                                                    </strong>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center text-muted">
                                                <i class="bi bi-clock-history text-secondary me-3 fs-5"></i>
                                                <div>
                                                    <small>Ngày đặt</small><br>
                                                    <strong class="text-dark"><?= date('d/m/Y H:i', strtotime($b['created_at'])) ?></strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Giá & Voucher -->
                                    <div class="d-flex align-items-end justify-content-between flex-wrap gap-3 mb-4">
                                        <div>
                                            <?php if ($b['discount_amount'] > 0): ?>
                                                <del class="text-muted fs-5"><?= format_price($b['total_price'] + $b['discount_amount']) ?></del>
                                                <span class="price-big d-block"><?= format_price($b['total_price']) ?></span>
                                                <span class="badge bg-success fs-6 px-3 py-2">
                                                    <i class="bi bi-tag-fill me-1"></i>
                                                    Tiết kiệm <?= format_price($b['discount_amount']) ?> với mã <?= $b['ma_voucher'] ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="price-big"><?= format_price($b['total_price']) ?></span>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Các nút hành động -->
                                        <div class="d-flex flex-wrap gap-3">
                                            <a href="booking-detail.php?id=<?= $b['id'] ?>" class="btn btn-outline-primary btn-action">
                                                <i class="bi bi-eye me-2"></i> Chi tiết
                                            </a>

                                            <?php if ($b['payment_status'] == 'pending' && $b['status'] != 'cancelled'): ?>
                                                <a href="payment.php?id=<?= $b['id'] ?>" class="btn btn-success btn-action">
                                                    <i class="bi bi-credit-card-2-back me-2"></i> Thanh toán
                                                </a>
                                            <?php endif; ?>

                                            <?php if ($b['payment_status'] == 'paid' || $b['status'] == 'confirmed'): ?>
                                                <a href="booking-pdf.php?id=<?= $b['id'] ?>" target="_blank" class="btn btn-info btn-action text-white">
                                                    <i class="bi bi-file-earmark-pdf me-2"></i> Tải vé PDF
                                                </a>
                                            <?php endif; ?>

                                            <?php if ($b['status'] == 'pending' && $b['payment_status'] == 'pending'): ?>
                                                <button class="btn btn-outline-danger btn-action" onclick="cancelBooking(<?= $b['id'] ?>)">
                                                    <i class="bi bi-x-circle me-2"></i> Hủy vé
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- QR Code nhỏ nếu đã thanh toán -->
                                    <?php if ($b['qr_code'] && ($b['payment_status'] == 'paid' || $b['status'] == 'confirmed')): ?>
                                    <div class="text-center mt-3">
                                        <img src="<?= $b['qr_code'] ?>" alt="QR Code" style="max-height: 120px; border-radius: 12px;">
                                        <p class="text-muted small mt-2 mb-0">Mã QR thanh toán / Check-in</p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
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
            cancelButtonText: 'Không, giữ lại'
        }).then((result) => {
            if (result.isConfirmed) {
                const reason = prompt('Vui lòng nhập lý do hủy vé (bắt buộc):');
                if (reason && reason.trim() !== '') {
                    window.location.href = 'cancel-booking.php?id=' + id + '&reason=' + encodeURIComponent(reason.trim());
                } else if (reason !== null) {
                    Swal.fire('Thiếu lý do', 'Bạn phải nhập lý do hủy vé!', 'error');
                }
            }
        });
    }
    </script>
</body>
</html>