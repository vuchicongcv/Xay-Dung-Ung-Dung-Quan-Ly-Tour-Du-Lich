<?php
require_once 'include/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tour_id = $_POST['tour_id'] ?? 0;
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $passengers = (int)($_POST['passengers'] ?? 1);
    $note = trim($_POST['note'] ?? '');

    if ($tour_id && $full_name && $phone) {
        try {
            $stmt = $pdo->prepare("INSERT INTO bookings (tour_id, full_name, phone, email, passengers, note, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())");
            $stmt->execute([$tour_id, $full_name, $phone, $email, $passengers, $note]);

            // Gửi email thông báo (tùy chọn)
            // mail(...)

            $_SESSION['success'] = "Yêu cầu đặt tour đã được gửi! Chúng tôi sẽ liên hệ sớm.";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        } catch (Exception $e) {
            error_log($e->getMessage());
            $_SESSION['error'] = "Có lỗi xảy ra. Vui lòng thử lại.";
        }
    }
}
header("Location: index.php");