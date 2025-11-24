<?php
define('PAGE_TITLE', 'Hồ sơ của tôi');
require_once 'include/config.php';

// === KIỂM TRA ĐĂNG NHẬP ===
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';

// === LẤY THÔNG TIN NGƯỜI DÙNG ===
$user = null;
try {
    // SỬA TÊN CỘT ĐÚNG VỚI BẢNG CỦA BẠN
    $sql = "SELECT 
                id, name, email, phone, password, role, 
                created_at, updated_at,
                address, dob, gender, avatar
            FROM users 
            WHERE id = :id 
            LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // User không tồn tại → đăng xuất
        session_destroy();
        header('Location: login.php');
        exit;
    }
} catch (Exception $e) {
    error_log("Lỗi lấy hồ sơ user $user_id: " . $e->getMessage());
    $message = '<div class="alert alert-danger">Lỗi hệ thống. Vui lòng thử lại sau.</div>';
}

// === CẬP NHẬT HỒ SƠ ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $ho_ten = trim($_POST['name'] ?? '');
    $dien_thoai = trim($_POST['phone'] ?? '');
    $dia_chi = trim($_POST['address'] ?? '');
    $ngay_sinh = $_POST['dob'] ?? '';
    $gioi_tinh = $_POST['gender'] ?? '';

    $errors = [];
    if (empty($ho_ten)) $errors[] = "Họ tên không được để trống";
    if (!empty($dien_thoai) && !preg_match('/^0[0-9]{9}$/', $dien_thoai)) {
        $errors[] = "Số điện thoại không hợp lệ";
    }

    if (empty($errors)) {
        try {
            $sql = "UPDATE users SET 
                    name = :name,
                    phone = :phone,
                    address = :address,
                    dob = :dob,
                    gender = :gender
                    WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':name' => $ho_ten,
                ':phone' => $dien_thoai ?: null,
                ':address' => $dia_chi ?: null,
                ':dob' => $ngay_sinh ?: null,
                ':gender' => $gioi_tinh ?: null,
                ':id' => $user_id
            ]);

            $_SESSION['user_name'] = $ho_ten;
            $message = '<div class="alert alert-success">Cập nhật hồ sơ thành công!</div>';
            
            // Reload user data
            $stmt = $pdo->prepare($sql = "SELECT * FROM users WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Lỗi cập nhật hồ sơ user $user_id: " . $e->getMessage());
            $message = '<div class="alert alert-danger">Cập nhật thất bại. Vui lòng thử lại.</div>';
        }
    } else {
        $message = '<div class="alert alert-danger">' . implode('<br>', $errors) . '</div>';
    }
}

require_once 'include/head.php';
require_once 'include/navbar.php';
?>

<div class="container py-5">
    <?php if (!$user): ?>
        <div class="alert alert-danger">Không tìm thấy thông tin tài khoản. Vui lòng đăng nhập lại.</div>
        <a href="login.php" class="btn btn-primary">Đăng nhập lại</a>
    <?php else: ?>
        <div class="row g-4">
            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <img src="<?= htmlspecialchars($user['avatar'] ?? 'img/default-avatar.png') ?>" 
                             alt="Avatar" class="rounded-circle mb-3" 
                             width="100" height="100" style="object-fit: cover;">
                        <h5 class="mb-1"><?= htmlspecialchars($user['name']) ?></h5>
                        <p class="text-muted small"><?= htmlspecialchars($user['email']) ?></p>
                        <div class="mt-3">
                            <a href="<?= BASE_URL ?>my-bookings.php" class="btn btn-outline-primary btn-sm w-100 mb-2">
                                <i class="fas fa-ticket-alt"></i> Đơn hàng
                            </a>
                            <a href="<?= BASE_URL ?>my-vouchers.php" class="btn btn-outline-warning btn-sm w-100">
                                <i class="fas fa-gift"></i> Voucher
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form chỉnh sửa -->
            <div class="col-lg-9">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-user-edit me-2"></i> Chỉnh sửa hồ sơ</h4>
                    </div>
                    <div class="card-body p-4">
                        <?= $message ?>

                        <form method="POST">
                            <input type="hidden" name="update_profile" value="1">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" 
                                           value="<?= htmlspecialchars($user['name']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Email</label>
                                    <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Số điện thoại</label>
                                    <input type="tel" name="phone" class="form-control" 
                                           value="<?= htmlspecialchars($user['phone'] ?? '') ?>" 
                                           placeholder="0901234567">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Ngày sinh</label>
                                    <input type="date" name="dob" class="form-control" 
                                           value="<?= $user['dob'] ?? '' ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Giới tính</label>
                                    <select name="gender" class="form-select">
                                        <option value="">-- Chọn --</option>
                                        <option value="Nam" <?= ($user['gender'] ?? '') === 'Nam' ? 'selected' : '' ?>>Nam</option>
                                        <option value="Nữ" <?= ($user['gender'] ?? '') === 'Nữ' ? 'selected' : '' ?>>Nữ</option>
                                        <option value="Khác" <?= ($user['gender'] ?? '') === 'Khác' ? 'selected' : '' ?>>Khác</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Địa chỉ</label>
                                    <textarea name="address" class="form-control" rows="3"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary px-5">
                                        <i class="fas fa-save"></i> Lưu thay đổi
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Đổi mật khẩu -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-key me-2"></i> Đổi mật khẩu</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="change-password.php">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Mật khẩu cũ</label>
                                    <input type="password" name="old_password" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Mật khẩu mới</label>
                                    <input type="password" name="new_password" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Xác nhận mật khẩu mới</label>
                                    <input type="password" name="confirm_password" class="form-control" required>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-outline-warning">
                                        <i class="fas fa-sync"></i> Đổi mật khẩu
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'include/footer.php'; ?>