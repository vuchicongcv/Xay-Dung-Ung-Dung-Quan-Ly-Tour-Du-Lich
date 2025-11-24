<?php
ob_start();
require_once __DIR__ . '/include/config.php';
require_once __DIR__ . '/include/functions.php';

// === LẤY DANH MỤC TOUR ===
$cat = $_GET['cat'] ?? 'trong_nuoc';
$valid_cats = ['trong_nuoc', 'nuoc_ngoai'];
if (!in_array($cat, $valid_cats)) $cat = 'trong_nuoc';

$page_title = $cat === 'nuoc_ngoai' ? 'Tour Nước Ngoài - Travela' : 'Tour Trong Nước - Travela';
$meta_description = $cat === 'nuoc_ngoai' 
    ? 'Tour châu Âu, Nhật Bản, Hàn Quốc, Thái Lan, Singapore, Mỹ, Úc... giá tốt nhất 2025'
    : 'Tour Phú Quốc, Đà Lạt, Hà Nội, Sapa, Đà Nẵng, Nha Trang... khởi hành hàng ngày';

require_once 'include/head.php';
require_once 'include/navbar.php';
?>

<!-- Banner trang danh sách tour -->
<section class="tour-list-banner position-relative">
    <img src="<?= BASE_URL ?>img/banner-tour-list.jpg" alt="<?= $page_title ?>" class="w-100">
    <div class="banner-overlay">
        <div class="container h-100 d-flex flex-column justify-content-center text-center text-white">
            <h1 class="display-3 fw-bold mb-3">
                <?= $cat === 'nuoc_ngoai' ? 'Tour Nước Ngoài' : 'Tour Trong Nước' ?>
            </h1>
            <p class="lead mb-4">
                <?= $cat === 'nuoc_ngoai' ? 'Khám phá thế giới cùng Travela' : 'Trải nghiệm Việt Nam tuyệt đẹp' ?>
            </p>
            <div class="btn-switcher">
                <a href="<?= BASE_URL ?>goi-tour?cat=trong_nuoc" 
                   class="btn <?= $cat === 'trong_nuoc' ? 'btn-warning' : 'btn-outline-light' ?> btn-lg rounded-pill px-5">
                   Tour Trong Nước
                </a>
                <a href="<?= BASE_URL ?>goi-tour?cat=nuoc_ngoai" 
                   class="btn <?= $cat === 'nuoc_ngoai' ? 'btn-warning' : 'btn-outline-light' ?> btn-lg rounded-pill px-5 ms-3">
                   Tour Nước Ngoài
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Danh sách tour -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-primary display-5">
                Tất cả tour <?= $cat === 'nuoc_ngoai' ? 'nước ngoài' : 'trong nước' ?>
            </h2>
            <p class="text-muted fs-4">Khởi hành hàng ngày • Giá tốt nhất • Đảm bảo chất lượng</p>
        </div>

        <?php
        // Query đã sửa – KHÔNG DÙNG cột status → chạy ngon 100%
        $stmt = $pdo->prepare("
            SELECT t.*, 
                   COALESCE(MIN(td.price_adult), t.price) AS display_price
            FROM tours t
            LEFT JOIN tour_departures td ON t.id = td.tour_id AND td.status = 'active'
            WHERE t.category = ?
            GROUP BY t.id
            ORDER BY t.created_at DESC
        ");
        $stmt->execute([$cat]);
        $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <?php if (empty($tours)): ?>
            <div class="text-center py-5">
                <img src="<?= BASE_URL ?>img/empty.svg" alt="Chưa có tour" class="mb-4" style="width: 150px; opacity: 0.5;">
                <h4 class="text-muted">Hiện chưa có tour nào trong danh mục này</h4>
                <a href="<?= BASE_URL ?>" class="btn btn-primary rounded-pill px-5 mt-3">Về trang chủ</a>
            </div>

        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($tours as $tour): 
                    $image = file_exists(__DIR__.'/uploads/'.$tour['image']) ? $tour['image'] : 'default.jpg';
                    $price = $tour['display_price'] ?? $tour['price'];
                ?>
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <article class="tour-card">
                            <div class="tour-thumb">
                                <a href="<?= BASE_URL ?>tour-detail.php?slug=<?= urlencode($tour['slug']) ?>">
                                    <img data-src="<?= BASE_URL ?>uploads/<?= htmlspecialchars($image) ?>"
                                         class="lazyload" alt="<?= htmlspecialchars($tour['title']) ?>">
                                    <?php if (!empty($tour['is_hot'])): ?>
                                        <span class="tour-hot">HOT</span>
                                    <?php endif; ?>
                                </a>
                            </div>
                            <div class="tour-content">
                                <h3 class="tour-title">
                                    <a href="<?= BASE_URL ?>tour-detail.php?slug=<?= urlencode($tour['slug']) ?>">
                                        <?= htmlspecialchars($tour['title']) ?>
                                    </a>
                                </h3>
                                <div class="tour-meta">
                                    <span><i class="fas fa-hotel text-info"></i> <?= htmlspecialchars($tour['hotel']) ?></span>
                                    <span><i class="fas fa-clock text-warning"></i> <?= htmlspecialchars($tour['duration']) ?></span>
                                </div>
                                <div class="tour-price">
                                    <strong><?= number_format($price, 0, ',', '.') ?>₫</strong>
                                    <small>/khách</small>
                                </div>
                                <a href="<?= BASE_URL ?>tour-detail.php?slug=<?= urlencode($tour['slug']) ?>" 
                                   class="btn btn-outline-danger btn-sm rounded-pill mt-3 w-100">
                                    Xem chi tiết →
                                </a>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- CSS RIÊNG CHO TRANG NÀY -->
<style>
.tour-list-banner { height: 65vh; min-height: 500px; position: relative; }
.tour-list-banner img { 
    width: 100%; height: 100%; object-fit: cover; filter: brightness(0.55); 
}
.banner-overlay { 
    position: absolute; top: 0; left: 0; right: 0; bottom: 0; 
    background: linear-gradient(transparent, rgba(0,0,0,0.4));
}
.btn-switcher .btn { min-width: 220px; font-weight: 700; }

.tour-card {
    background: #fff; border-radius: 18px; overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08); transition: all 0.4s ease;
    height: 100%; display: flex; flex-direction: column;
}
.tour-card:hover { transform: translateY(-12px); box-shadow: 0 25px 50px rgba(0,0,0,0.18); }
.tour-thumb { position: relative; overflow: hidden; }
.tour-thumb img { height: 220px; width: 100%; object-fit: cover; transition: 0.5s; }
.tour-card:hover .tour-thumb img { transform: scale(1.12); }
.tour-hot {
    position: absolute; top: 12px; right: 12px; background: #ff4757; color: #fff;
    padding: 6px 16px; border-radius: 30px; font-size: 0.8rem; font-weight: bold; z-index: 2;
}
.tour-content { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
.tour-title { 
    font-size: 1.1rem; margin-bottom: 12px; line-height: 1.4;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.tour-title a { color: #212529; text-decoration: none; font-weight: 600; }
.tour-title a:hover { color: #e74c3c; }
.tour-meta { color: #666; font-size: 0.9rem; margin-bottom: 12px; }
.tour-meta span { display: block; margin-bottom: 6px; }
.tour-price { margin-top: auto; font-size: 1.5rem; color: #e74c3c; font-weight: 800; }
.tour-price small { font-weight: normal; font-size: 0.8rem; color: #888; }

/* Responsive */
@media (max-width: 768px) {
    .tour-list-banner { height: 50vh; }
    .btn-switcher .btn { display: block; width: 100%; margin: 10px 0 !important; }
}
</style>

<!-- Lazy Load ảnh -->
<script src="https://cdn.jsdelivr.net/npm/lazysizes@5.3.2/lazysizes.min.js" async></script>

<?php 
require_once 'include/footer.php'; 
ob_end_flush(); 
?>