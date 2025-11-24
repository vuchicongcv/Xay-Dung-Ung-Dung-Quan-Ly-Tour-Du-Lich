<?php
require_once 'include/config.php';
define('PAGE_TITLE', 'Về Chúng Tôi');
define('PAGE_DESCRIPTION', 'Travela - Hơn 10 năm kinh nghiệm tổ chức tour du lịch trong nước & quốc tế. Giá tốt nhất • Dịch vụ 5 sao • Hàng ngàn khách hàng hài lòng!');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= PAGE_TITLE ?></title>
<meta name="description" content="<?= PAGE_DESCRIPTION ?>">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css?v=<?= time() ?>">
<style>
:root {
    --primary: #e74c3c;
    --success: #27ae60;
    --dark: #2c3e50;
    --light: #f8f9fa;
    --shadow: 0 10px 30px rgba(0,0,0,0.1);
    --shadow-hover: 0 25px 50px rgba(231,76,60,0.2);
}
body { font-family: 'Poppins', sans-serif; background: var(--light); }

/* Hero Section */
.page-header {
    background: linear-gradient(rgba(231,76,60,0.9), rgba(192,57,43,0.9)), url('<?= BASE_URL ?>assets/img/about-hero.jpg') center/cover no-repeat;
    color: #fff; padding: 180px 0;
    text-align: center;
}
.page-header h1 {
    font-size: 3.5rem; font-weight: 800; color: var(--primary);
}
.page-header p { font-size: 1.5rem; }

/* About Section */
.about-img { width: 100%; border-radius: 20px; box-shadow: var(--shadow); transition: all .4s; }
.about-img:hover { transform: scale(1.05); box-shadow: var(--shadow-hover); }

.about-content h2 { color: var(--primary); font-weight: 800; margin-bottom: 1rem; }
.about-content p { font-size: 1.1rem; line-height: 1.7; }
.about-content ul { list-style: none; padding: 0; }
.about-content li { margin-bottom: 1rem; font-size: 1.1rem; }
.about-content li i { color: var(--success); margin-right: 0.5rem; }

/* Counter */
.counter-box { text-align: center; background: var(--primary); color: #fff; padding: 50px 20px; border-radius: 20px; margin-top: 50px; }
.counter-box h2 { font-size: 2.5rem; font-weight: 800; }
.counter-box p { font-size: 1.2rem; margin-top: 0.5rem; }

/* Responsive */
@media (max-width: 992px) { .page-header h1 { font-size: 2.8rem; } }
@media (max-width: 768px) { 
    .page-header { padding: 120px 0; } 
    .page-header h1 { font-size: 2.2rem; }
    .about-content p, .about-content li { font-size: 1rem; }
}
</style>
</head>
<body>
<?php include 'include/navbar.php'; ?>

<!-- Hero -->
<div class="page-header">
    <div class="container">
        <h2>VỀ CHÚNG TÔI</h2>
        <p>Travela - Đồng hành cùng giấc mơ khám phá thế giới của bạn!</p>
    </div>
</div>

<!-- About Section -->
<div class="container py-5">
    <div class="row align-items-center g-5 mb-5">
        <div class="col-lg-6">
            <img src="<?=BASE_URL?>assets/img/about-1.jpg" class="about-img" alt="Đội ngũ Travela">
        </div>
        <div class="col-lg-6 about-content">
            <h2>Hành Trình 10 Năm Kiến Tạo Niềm Tin</h2>
            <p>Thành lập năm 2015, Travela đã đưa hơn <strong>150.000 khách hàng</strong> khám phá 63 tỉnh thành Việt Nam và hơn <strong>50 quốc gia</strong> trên thế giới.</p>
            <p>Chúng tôi tự hào là công ty du lịch được tin yêu nhất nhờ dịch vụ chuyên nghiệp, giá cả minh bạch và cam kết mang lại trải nghiệm tuyệt vời nhất.</p>
            <ul>
                <li><i class="bi bi-check-circle-fill"></i> Giấy phép lữ hành quốc tế</li>
                <li><i class="bi bi-check-circle-fill"></i> Đối tác Vietnam Airlines, Vietjet, Bamboo</li>
                <li><i class="bi bi-check-circle-fill"></i> Bảo hiểm du lịch lên đến 5 tỷ/người</li>
            </ul>
        </div>
    </div>

    <!-- Counter -->
    <div class="row g-4 text-center">
        <div class="col-6 col-lg-3">
            <div class="counter-box">
                <h2 class="counter" data-count="150000">0</h2>
                <p>Khách hàng</p>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="counter-box">
                <h2 class="counter" data-count="1200">0</h2>
                <p>Gói tour</p>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="counter-box">
                <h2 class="counter" data-count="50">0</h2>
                <p>Quốc gia</p>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="counter-box">
                <h2 class="counter" data-count="98">0</h2>
                <p>% Hài lòng</p>
            </div>
        </div>
    </div>
</div>

<?php include 'include/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Counter animation
document.querySelectorAll('.counter').forEach(counter => {
    const update = () => {
        const target = +counter.getAttribute('data-count');
        const count = +counter.innerText.replace(/,/g, '');
        const inc = target / 100;
        if (count < target) {
            counter.innerText = Math.ceil(count + inc).toLocaleString('vi-VN');
            setTimeout(update, 20);
        } else {
            counter.innerText = target.toLocaleString('vi-VN');
        }
    };
    update();
});
</script>
</body>
</html>
