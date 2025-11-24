<?php
require_once 'include/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['book_tour'])) {
    header('Location: index.php');
    exit;
}

$departure_id = (int)$_POST['departure_id'];
$adults = (int)$_POST['adults'];
$children = (int)$_POST['children'];
$infants = (int)$_POST['infants'];
$customer_name = trim($_POST['customer_name']);
$customer_email = trim($_POST['customer_email']);
$customer_phone = trim($_POST['customer_phone']);
$notes = trim($_POST['notes']);
$payment_method = $_POST['payment_method'] ?? 'cash';

// === VALIDATE ===
$errors = [];
if (!$customer_name) $errors[] = "Vui lòng nhập họ tên";
if (!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email không hợp lệ";
if (!preg_match('/^0[3|5|7|8|9]\d{8}$/', $customer_phone)) $errors[] = "Số điện thoại không hợp lệ";
if ($adults < 1) $errors[] = "Cần ít nhất 1 người lớn";

// Lấy thông tin tour + lịch khởi hành
$stmt = $pdo->prepare("SELECT t.*, d.* FROM tours t 
                       JOIN tour_departures d ON t.id = d.tour_id 
                       WHERE d.id = ? AND d.status = 'active'");
$stmt->execute([$departure_id]);
$dep = $stmt->fetch();

if (!$dep || $dep['available_seats'] < ($adults + $children + $infants)) {
    $errors[] = "Không đủ chỗ trống!";
}

if (!empty($errors)) {
    $_SESSION['booking_errors'] = $errors;
    $_SESSION['old_booking'] = $_POST;
    header("Location: tour-detail.php?slug=" . urlencode($dep['slug'] ?? ''));
    exit;
}

// === TÍNH TIỀN ===
$total = $dep['price_adult'] * $adults + $dep['price_child'] * $children + $dep['price_infant'] * $infants;

// === TẠO MÃ BOOKING ===
$booking_code = 'BK' . date('Ymd') . str_pad($pdo->lastInsertId() + 1, 4, '0', STR_PAD_LEFT);

// === THÔNG TIN NGÂN HÀNG (TÙY CHỈNH) ===
$bank_info = "Ngân hàng: Vietcombank\nChủ tài khoản: CÔNG TY DU LỊCH TRAVELA\nSố tài khoản: 0011001933888\nNội dung: $booking_code";

// === TẠO QR CODE (nếu chuyển khoản) ===
$qr_code = null;
if ($payment_method === 'bank_transfer') {
    require_once 'lib/phpqrcode/qrlib.php';
    $qr_dir = 'img/qr/';
    if (!is_dir($qr_dir)) mkdir($qr_dir, 0755, true);
    $qr_file = $qr_dir . $booking_code . '.png';
    $qr_content = "Bank: Vietcombank\nSTK: 0011001933888\nND: $booking_code\nSo tien: " . number_format($total) . " VND";
    QRcode::png($qr_content, $qr_file, QR_ECLEVEL_L, 6);
    $qr_code = BASE_URL . $qr_file;
}

// === LƯU BOOKING ===
try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO bookings 
        (booking_code, tour_id, departure_id, customer_name, customer_email, customer_phone, 
         adults, children, infants, total_price, notes, payment_method, payment_status, bank_info, qr_code, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?, NOW())
    ");
    $stmt->execute([
        $booking_code, $dep['tour_id'], $departure_id, $customer_name, $customer_email, $customer_phone,
        $adults, $children, $infants, $total, $notes, $payment_method, $bank_info, $qr_code
    ]);

    $booking_id = $pdo->lastInsertId();

    // Cập nhật chỗ trống
    $pdo->prepare("UPDATE tour_departures SET available_seats = available_seats - ? WHERE id = ?")
        ->execute([$adults + $children + $infants, $departure_id]);

    $pdo->commit();

    // Chuyển hướng đến trang xác nhận
    header("Location: booking-success.php?id=$booking_id");
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Lỗi đặt tour: " . $e->getMessage());
    die("Lỗi hệ thống. Vui lòng thử lại sau.");
}
?>