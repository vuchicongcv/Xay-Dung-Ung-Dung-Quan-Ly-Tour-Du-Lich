<?php
require_once 'include/config.php';
$errors = [];
$success = false;

if ($_POST['register'] ?? false) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if (!$name) $errors[] = "Vui lòng nhập họ tên";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email không hợp lệ";
    if (!preg_match('/^0[3|5|7|8|9]\d{8}$/', $phone)) $errors[] = "Số điện thoại không hợp lệ";
    if (strlen($password) < 6) $errors[] = "Mật khẩu ít nhất 6 ký tự";
    if ($password !== $confirm) $errors[] = "Mật khẩu không khớp";

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) $errors[] = "Email đã được đăng ký";

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $hash]);
        $success = true;
    }
}
?>

<?php include 'include/head.php'; ?>
<?php include 'include/navbar.php'; ?>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
/* Body và Background */
body {
    margin:0;
    font-family:'Inter',sans-serif;
    background: linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%);
    min-height:100vh;
    display:flex;
    flex-direction:column;
}

/* Navbar fix */
.navbar {
    position: relative;
    z-index: 10;
    background: #ffffffcc;
    backdrop-filter: blur(10px);
}

/* Container Form */
.register-container {
    background:#ffffffdd;
    backdrop-filter: blur(10px);
    border-radius:20px;
    padding:40px 50px;
    width:100%;
    max-width:450px;
    margin:80px auto;
    box-shadow:0 15px 35px rgba(0,0,0,0.2);
    transition: transform .3s ease, box-shadow .3s ease;
}
.register-container:hover { transform: translateY(-5px); box-shadow:0 25px 50px rgba(0,0,0,0.25); }

/* Heading */
h1 {
    text-align:center;
    font-weight:700;
    color:#1e3a8a;
    margin-bottom:10px;
}
p.subtitle {
    text-align:center;
    color:#4b5563;
    margin-bottom:25px;
    font-weight:500;
}

/* Form Input */
.auth-form label {
    display:block;
    margin-top:15px;
    font-weight:500;
    color:#374151;
    font-size:14px;
}

.auth-form input {
    width:100%;
    padding:14px 15px;
    margin-top:6px;
    border-radius:12px;
    border:1px solid #d1d5db;
    font-size:15px;
    transition: all .3s;
}
.auth-form input:focus {
    border:2px solid #3b82f6;
    box-shadow:0 0 8px rgba(59,130,246,0.3);
    outline:none;
}

/* Button */
.btn-submit {
    background: linear-gradient(90deg,#3b82f6,#06b6d4);
    color:#fff;
    border:none;
    width:100%;
    padding:15px;
    margin-top:20px;
    border-radius:12px;
    font-weight:600;
    font-size:16px;
    cursor:pointer;
    transition: all .3s;
    position:relative;
    overflow:hidden;
}
.btn-submit:hover { background: linear-gradient(90deg,#2563eb,#0891b2); }
.btn-submit:active { transform: scale(0.98); }

/* Alerts */
.alert {
    padding:14px 16px;
    border-radius:10px;
    margin-bottom:18px;
    font-size:15px;
    text-align:center;
}
.alert.error { background:#fee2e2; color:#b91c1c; }
.alert.success { background:#dcfce7; color:#166534; }

.text-center { text-align:center; }
.text-center a { color:#2563eb; text-decoration:none; font-weight:600; }

/* Footer */
.footer {
    position: relative;
    z-index: 5;
    background: #ffffffcc;
    backdrop-filter: blur(10px);
    padding: 20px 0;
    text-align: center;
    color: #374151;
    margin-top:auto;
}

/* Responsive */
@media(max-width:500px){
    .register-container { padding:30px 20px; margin:50px 10px; }
}
</style>

<div class="register-container">
    <h1>Đăng ký tài khoản</h1>
    <p class="subtitle">Tạo tài khoản để đặt vé ngay hôm nay</p>

    <?php if($success): ?>
        <div class="alert success">🎉 Đăng ký thành công!<br>
            <a href="login.php" class="btn-submit" style="margin-top:10px;">Đăng nhập ngay</a>
        </div>
    <?php elseif($errors): ?>
        <div class="alert error">
            <ul>
                <?php foreach($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" class="auth-form">
        <input type="hidden" name="register" value="1">
        <label>Họ tên</label>
        <input type="text" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
        <label>Email</label>
        <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        <label>Số điện thoại</label>
        <input type="tel" name="phone" required value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
        <label>Mật khẩu</label>
        <input type="password" name="password" required>
        <label>Xác nhận mật khẩu</label>
        <input type="password" name="confirm" required>
        <button type="submit" class="btn-submit">Đăng ký</button>
    </form>

    <p class="text-center" style="margin-top:15px;">Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
</div>

<?php include 'include/footer.php'; ?>
