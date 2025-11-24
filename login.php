<?php
require_once 'include/config.php';
$errors = [];

if ($_POST['login'] ?? false) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        header("Location: index.php");
        exit;
    } else {
        $errors[] = "Email hoặc mật khẩu không đúng!";
    }
}
?>

<?php include 'include/head.php'; ?>
<?php include 'include/navbar.php'; ?>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
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
.login-container {
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
.login-container:hover { transform: translateY(-5px); box-shadow:0 25px 50px rgba(0,0,0,0.25); }

/* Heading */
h2.auth-title {
    text-align:center;
    font-weight:700;
    color:#1e3a8a;
    margin-bottom:10px;
}
p.auth-subtitle {
    text-align:center;
    color:#4b5563;
    margin-bottom:25px;
    font-weight:500;
}

/* Form Input */
.auth-group label {
    display:block;
    margin-top:15px;
    font-weight:500;
    color:#374151;
    font-size:14px;
}

.auth-group input {
    width:100%;
    padding:14px 15px;
    margin-top:6px;
    border-radius:12px;
    border:1px solid #d1d5db;
    font-size:15px;
    transition: all .3s;
}
.auth-group input:focus {
    border:2px solid #3b82f6;
    box-shadow:0 0 8px rgba(59,130,246,0.3);
    outline:none;
}

/* Button */
.auth-btn {
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
}
.auth-btn:hover { background: linear-gradient(90deg,#2563eb,#0891b2); }
.auth-btn:active { transform: scale(0.98); }

/* Alerts */
.auth-alert {
    background:#fee2e2;
    color:#b91c1c;
    padding:14px 16px;
    border-radius:10px;
    margin-bottom:18px;
    font-size:15px;
    text-align:center;
}

/* Footer */
.auth-footer {
    text-align:center;
    margin-top:20px;
    font-size:14px;
    color:#374151;
}
.auth-footer a { color:#2563eb; font-weight:600; text-decoration:none; }

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
    .login-container { padding:30px 20px; margin:50px 10px; }
}
</style>

<div class="login-container">
    <h2 class="auth-title">Đăng Nhập Travela</h2>
    <p class="auth-subtitle">Khám phá thế giới dễ dàng hơn</p>

    <?php if($errors): ?>
        <div class="auth-alert"><?= htmlspecialchars($errors[0]) ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="login" value="1">

        <div class="auth-group">
            <label><i class="fas fa-envelope"></i> Email</label>
            <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>

        <div class="auth-group">
            <label><i class="fas fa-lock"></i> Mật khẩu</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit" class="auth-btn">Đăng Nhập</button>

        <div class="auth-footer">
            Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a>
        </div>
    </form>
</div>

<?php include 'include/footer.php'; ?>
