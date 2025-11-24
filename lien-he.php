<?php 
require_once 'include/config.php';
define('PAGE_TITLE', 'Liên Hệ Travela - Hotline 1900 1234');
define('PAGE_DESCRIPTION', 'Liên hệ ngay Travela để được tư vấn tour du lịch trong nước & quốc tế miễn phí. Hotline 1900 1234 - Phản hồi trong 5 phút!');

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu + chống XSS
    $type       = trim($_POST['type'] ?? 'Du lịch');
    $fullname   = trim($_POST['fullname'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $company    = trim($_POST['company'] ?? '');
    $passengers = (int)($_POST['passengers'] ?? 1);
    $address    = trim($_POST['address'] ?? '');
    $subject    = trim($_POST['subject'] ?? '');
    $message    = trim($_POST['message'] ?? '');
    $ip         = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

    // Validate cơ bản
    if (!$fullname || !$email || !$phone || !$subject || !$message) {
        $error = "Vui lòng điền đầy đủ các trường có dấu (*)";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email không hợp lệ!";
    } elseif (strlen($phone) < 9) {
        $error = "Số điện thoại không hợp lệ!";
    } else {
        // Lưu vào CSDL
        try {
            $sql = "INSERT INTO contacts 
                    (type, fullname, email, phone, company, passengers, address, subject, message, ip_address) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$type, $fullname, $email, $phone, $company, $passengers, $address, $subject, $message, $ip]);

            // Gửi email thông báo cho admin
            $to = "support@travela.vn";
            $mail_subject = "KHÁCH MỚI LIÊN HỆ: $subject";
            $mail_body = "
                <h2>Khách hàng mới liên hệ từ website</h2>
                <p><strong>Họ tên:</strong> $fullname</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Điện thoại:</strong> $phone</p>
                <p><strong>Số khách:</strong> $passengers người</p>
                <p><strong>Tiêu đề:</strong> $subject</p>
                <p><strong>Nội dung:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>
                <hr>
                <small>IP: $ip | Thời gian: " . date('d/m/Y H:i') . "</small>
            ";
            $headers  = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8\r\n";
            $headers .= "From: no-reply@travela.vn\r\n";

            mail($to, $mail_subject, $mail_body, $headers);

            $success = "Cảm ơn quý khách $fullname! Chúng tôi đã nhận thông tin và sẽ liên hệ ngay trong vòng 5-30 phút tới!";
            
            // Reset form
            $_POST = [];
        } catch (Exception $e) {
            $error = "Có lỗi xảy ra, vui lòng thử lại sau ít phút!";
        }
    }
}
?>

<?php include 'include/head.php'; ?>
<?php include 'include/navbar.php'; ?>

<!-- Hero -->
<div class="page-header text-center text-white" style="background: linear-gradient(rgba(0,0,0,0.75),rgba(0,0,0,0.75)), url('<?=BASE_URL?>assets/img/contact-hero.jpg') center/cover no-repeat; padding: 180px 0;">
    <div class="container">
        <h1 class="display-2 fw-bold">LIÊN HỆ TRAVELA</h1>
        <p class="lead fs-2">Hotline 1900 1234 - Tư vấn miễn phí 24/7</p>
    </div>
</div>

<div class="container py-5">
    <?php if ($success): ?>
        <div class="alert alert-success text-center fs-4 p-4 rounded-4 shadow">
            <i class="bi bi-check-circle-fill me-3"></i><?= $success ?>
        </div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger text-center p-4 rounded-4 shadow"><?= $error ?></div>
    <?php endif; ?>

    <div class="row g-5">
        <!-- Form liên hệ -->
        <div class="col-lg-8">
            <div class="bg-white p-5 rounded-4 shadow">
                <h3 class="fw-bold mb-4 text-primary">Gửi yêu cầu tư vấn ngay</h3>
                <form method="POST" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Loại thông tin <span class="text-danger">*</span></label>
                        <select name="type" class="form-select form-select-lg" required>
                            <option value="Du lịch">Du lịch cá nhân / gia đình</option>
                            <option value="Doanh nghiệp">Du lịch doanh nghiệp / MICE</option>
                            <option value="Hợp tác">Hợp tác kinh doanh</option>
                            <option value="Khiếu nại">Khiếu nại / Góp ý</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Họ tên <span class="text-danger">*</span></label>
                        <input type="text" name="fullname" class="form-control form-control-lg" required value="<?= htmlspecialchars($_POST['fullname'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control form-control-lg" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Điện thoại <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control form-control-lg" required value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tên công ty (nếu có)</label>
                        <input type="text" name="company" class="form-control form-control-lg" value="<?= htmlspecialchars($_POST['company'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Số khách</label>
                        <input type="number" name="passengers" min="1" class="form-control form-control-lg" value="<?= $_POST['passengers'] ?? '1' ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Địa chỉ</label>
                        <input type="text" name="address" class="form-control form-control-lg" value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Tiêu đề <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control form-control-lg" required value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Nội dung yêu cầu <span class="text-danger">*</span></label>
                        <textarea name="message" rows="6" class="form-control form-control-lg" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-danger btn-lg px-5 rounded-pill w-100 fw-bold">
                            <i class="bi bi-send-fill me-2"></i> GỬI YÊU CẦU NGAY
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Thông tin liên hệ nhanh -->
        <div class="col-lg-4">
            <div class="bg-primary text-white p-5 rounded-4 shadow h-100">
                <h3 class="fw-bold mb-4">THÔNG TIN LIÊN HỆ</h3>
                <div class="mb-4">
                    <p><i class="bi bi-telephone-fill fs-3 me-3"></i><strong>1900 1234</strong><br><small class="opacity-75">Tư vấn 24/7</small></p>
                    <p><i class="bi bi-phone-fill fs-3 me-3"></i>0909 123 456 (Zalo)</p>
                    <p><i class="bi bi-envelope-fill fs-3 me-3"></i>support@travela.vn</p>
                    <p><i class="bi bi-geo-alt-fill fs-3 me-3"></i>123 Đường Lữ Hành, Q.1, TP.HCM</p>
                </div>
                <hr class="border-light">
                <div class="text-center">
                    <a href="tel:19001234" class="btn btn-light btn-lg rounded-pill px-5 mb-3">Gọi ngay</a>
                    <a href="https://zalo.me/0909123456" target="_blank" class="btn btn-warning btn-lg rounded-pill px-5">Chat Zalo</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'include/footer.php'; ?>
</body>
</html>