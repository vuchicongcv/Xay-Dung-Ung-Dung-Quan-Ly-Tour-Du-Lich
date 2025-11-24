<?php 
require_once 'include/config.php';
require_once 'include/functions.php';

define('PAGE_TITLE', 'Travela - Tour Du Lịch Trong Nước & Nước Ngoài Giá Tốt Nhất 2025');
define('PAGE_DESCRIPTION', 'Đặt tour du lịch giá rẻ, tour HOT, khởi hành hàng ngày: Phú Quốc, Đà Lạt, Hàn Quốc, Nhật Bản, Châu Âu...');

// LẤY DỮ LIỆU
$hot_tours   = $pdo->query("SELECT * FROM tours WHERE is_hot = 1 ORDER BY created_at DESC LIMIT 8")->fetchAll();
$nuoc_ngoai  = $pdo->query("SELECT * FROM tours WHERE category = 'nuoc_ngoai' ORDER BY is_hot DESC, created_at DESC LIMIT 12")->fetchAll();
$trong_nuoc  = $pdo->query("SELECT * FROM tours WHERE category = 'trong_nuoc' ORDER BY is_hot DESC, created_at DESC LIMIT 12")->fetchAll();

include 'include/head.php'; 
include 'include/navbar.php'; 
?>

<!-- CSS CHỈ CẦN 1 DÒNG -->
<link rel="stylesheet" href="<?= BASE_URL ?>css/css.css?v=<?= time() ?>">

<main class="py-5">
    <div class="container">

        <!-- TOUR HOT -->
        <?php if ($hot_tours): ?>
        <section id="tour-hot" class="mb-6">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold text-danger">
                    <i class="fas fa-fire fa-beat text-warning"></i> TOUR HOT - KHUYẾN MÃI CỰC SỐC
                </h2>
                <p class="lead text-muted">Còn chỗ có hạn - Đặt ngay hôm nay!</p>
            </div>
            <div class="row g-4">
                <?php foreach ($hot_tours as $t): ?>
                <div class="col-lg-3 col-md-6 col-12">
                    <article class="tour-card position-relative shadow-lg rounded-3 overflow-hidden border border-danger border-3">
                        <div class="hot-ribbon"><span>HOT</span></div>
                        <a href="<?= BASE_URL ?>tour/<?= $t['slug'] ?>">
                            <img class="lazyload w-100" 
                                 data-src="<?= BASE_URL ?>uploads/<?= htmlspecialchars($t['image'] ?: 'default.jpg') ?>"
                                 src="<?= BASE_URL ?>uploads/placeholder.jpg"
                                 alt="<?= htmlspecialchars($t['title']) ?>"
                                 style="height:220px; object-fit:cover;">
                        </a>
                        <div class="p-3">
                            <h3 class="fs-6 fw-bold mb-2">
                                <a href="<?= BASE_URL ?>tour/<?= $t['slug'] ?>" class="text-dark text-decoration-none">
                                    <?= htmlspecialchars($t['title']) ?>
                                </a>
                            </h3>
                            <div class="small text-muted mb-2">
                                <i class="fas fa-clock"></i> <?= $t['duration'] ?? 'Liên hệ' ?>
                                • <i class="fas fa-hotel"></i> <?= $t['hotel'] ?? 'Tiêu chuẩn' ?>
                            </div>
                            <?php if($t['price_from'] > 0 && $t['price_from'] < $t['price']): ?>
                                <del class="text-muted small d-block"><?= format_price($t['price']) ?></del>
                                <div class="text-danger fw-bold fs-4"><?= format_price($t['price_from']) ?></div>
                                <span class="badge bg-danger">GIẢM <?= round(100 - ($t['price_from']*100/$t['price'])) ?>%</span>
                            <?php else: ?>
                                <div class="text-danger fw-bold fs-4"><?= format_price($t['price']) ?></div>
                            <?php endif; ?>
                            <?php if($t['tour_code']): ?>
                                <div class="text-center mt-2"><span class="badge bg-dark">Mã: <?= $t['tour_code'] ?></span></div>
                            <?php endif; ?>
                        </div>
                    </article>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- TOUR NƯỚC NGOÀI -->
        <section class="mb-6">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fs-3 fw-bold text-primary">
                    <i class="fas fa-globe-asia"></i> Du Lịch Nước Ngoài
                </h2>
                <a href="<?= BASE_URL ?>packages.php?cat=nuoc_ngoai" class="btn btn-outline-primary">Xem tất cả</a>
            </div>
            <?php if(empty($nuoc_ngoai)): ?>
                <p class="text-center py-5 text-muted fs-4">Chưa có tour nước ngoài</p>
            <?php else: ?>
            <div class="row g-4">
                <?php foreach($nuoc_ngoai as $t): ?>
                <div class="col-lg-3 col-md-6">
                    <article class="tour-card shadow rounded-3 <?= $t['is_hot']?'border border-danger':'' ?>">
                        <?php if($t['is_hot']): ?><div class="hot-ribbon"><span>HOT</span></div><?php endif; ?>
                        <a href="<?= BASE_URL ?>tour/<?= $t['slug'] ?>">
                            <img class="lazyload w-100" data-src="<?= BASE_URL ?>uploads/<?= $t['image']?:'default.jpg' ?>" 
                                 src="<?= BASE_URL ?>uploads/placeholder.jpg" alt="<?= $t['title'] ?>" style="height:200px; object-fit:cover;">
                        </a>
                        <div class="p-3">
                            <h3 class="fs-6 fw-bold"><a href="<?= BASE_URL ?>tour/<?= $t['slug'] ?>"><?= $t['title'] ?></a></h3>
                            <div class="small text-muted"><?= $t['duration'] ?> • <?= $t['airline']??'Bay thẳng' ?></div>
                            <div class="text-primary fw-bold fs-5 mt-2">
                                <?= $t['price_from']>0 ? format_price($t['price_from']) : format_price($t['price']) ?>
                            </div>
                        </div>
                    </article>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>

        <!-- TOUR TRONG NƯỚC -->
        <section class="mb-6">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fs-3 fw-bold text-success">
                    <i class="fas fa-mountain"></i> Du Lịch Trong Nước
                </h2>
                <a href="<?= BASE_URL ?>packages.php?cat=trong_nuoc" class="btn btn-outline-success">Xem tất cả</a>
            </div>
            <?php if(empty($trong_nuoc)): ?>
                <p class="text-center py-5 text-muted fs-4">Chưa có tour trong nước</p>
            <?php else: ?>
            <div class="row g-4">
                <?php foreach($trong_nuoc as $t): ?>
                <div class="col-lg-3 col-md-6">
                    <article class="tour-card shadow rounded-3 <?= $t['is_hot']?'border border-danger':'' ?>">
                        <?php if($t['is_hot']): ?><div class="hot-ribbon"><span>HOT</span></div><?php endif; ?>
                        <a href="<?= BASE_URL ?>tour/<?= $t['slug'] ?>">
                            <img class="lazyload w-100" data-src="<?= BASE_URL ?>uploads/<?= $t['image']?:'default.jpg' ?>" 
                                 src="<?= BASE_URL ?>uploads/placeholder.jpg" alt="<?= $t['title'] ?>" style="height:200px; object-fit:cover;">
                        </a>
                        <div class="p-3">
                            <h3 class="fs-6 fw-bold"><a href="<?= BASE_URL ?>tour/<?= $t['slug'] ?>"><?= $t['title'] ?></a></h3>
                            <div class="small text-muted"><?= $t['duration'] ?> • <?= $t['hotel']??'Tiêu chuẩn' ?></div>
                            <div class="text-success fw-bold fs-5 mt-2">
                                <?= $t['price_from']>0 ? format_price($t['price_from']) : format_price($t['price']) ?>
                            </div>
                        </div>
                    </article>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>

    </div>
</main>

<!-- LAZYLOAD + BOOTSTRAP JS -->
<script src="https://cdn.jsdelivr.net/npm/lazysizes@5.3.2/lazysizes.min.js" async=""></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- CSS RIÊNG -->
<style>
.hero-section img { height: 90vh; object-fit: cover; }
.hot-ribbon {
    position: absolute; top: 15px; left: -35px; background: #e74c3c; color: white;
    padding: 8px 45px; font-weight: bold; transform: rotate(-45deg); z-index: 10;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
}
.hot-ribbon span { animation: pulse 2s infinite; }
@keyframes pulse { 0%,100% { transform: scale(1); } 50% { transform: scale(1.2); } }
.tour-card:hover { transform: translateY(-10px); transition: all 0.3s; }
</style>

<?php include 'include/footer.php'; ?>