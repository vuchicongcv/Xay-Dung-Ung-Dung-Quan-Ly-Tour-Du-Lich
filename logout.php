<?php
session_start();

// Xóa toàn bộ biến session
$_SESSION = [];

// Hủy session hiện tại
session_destroy();

// Nếu dùng cookie để lưu đăng nhập thì xóa luôn
if (isset($_COOKIE['user_id'])) {
    setcookie('user_id', '', time() - 3600, '/');
}
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

// Chuyển hướng về trang đăng nhập hoặc trang chủ
header("Location: login.php");
exit();
?>
