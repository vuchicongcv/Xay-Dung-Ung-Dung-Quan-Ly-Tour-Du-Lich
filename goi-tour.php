<?php
require_once 'include/config.php';
require_once 'include/functions.php';

$title = 'Gói Tour Du Lịch Trong Nước & Quốc Tế 2025 - Travela';
$description = 'Khuyến mãi tour du lịch giá rẻ 2025: Phú Quốc, Đà Lạt, Hàn Quốc, Nhật Bản, Châu Âu... Đặt tour dễ dàng, khởi hành hàng ngày!';

// LẤY TOUR HOT
$hot_tours = $pdo->query("SELECT id, title, slug, image, price, price_from, duration, hotel, airline, is_hot, tour_code, category FROM tours WHERE is_hot = 1 ORDER BY created_at DESC LIMIT 8")->fetchAll(PDO::FETCH_ASSOC);

// TOUR NƯỚC NGOÀI
$tour_nuoc_ngoai = $pdo->query("SELECT id, title, slug, image, price, price_from, duration, hotel, airline, is_hot, tour_code, category FROM tours WHERE category = 'nuoc_ngoai' ORDER BY is_hot DESC, created_at DESC LIMIT 12")->fetchAll(PDO::FETCH_ASSOC);

// TOUR TRONG NƯỚC
$tour_trong_nuoc = $pdo->query("SELECT id, title, slug, image, price, price_from, duration, hotel, airline, is_hot, tour_code, category FROM tours WHERE category = 'trong_nuoc' ORDER BY is_hot DESC, created_at DESC LIMIT 12")->fetchAll(PDO::FETCH_ASSOC);

// LỌC TÌM KIẾM
$where = [];
$params = [];

if (!empty($_GET['keyword'])) {
    $where[] = "(title LIKE ? OR destinations LIKE ?)";
    $like = '%' . $_GET['keyword'] . '%';
    $params[] = $like; $params[] = $like;
}
if (!empty($_GET['destination'])) {
    $where[] = "category = ?";
    $params[] = $_GET['destination'];
}
if (!empty($_GET['duration'])) {
    if ($_GET['duration'] == '3-5') $where[] = "days BETWEEN 3 AND 5";
    elseif ($_GET['duration'] == '6-8') $where[] = "days BETWEEN 6 AND 8";
    elseif ($_GET['duration'] == '9-12') $where[] = "days BETWEEN 9 AND 12";
    elseif ($_GET['duration'] == '12+') $where[] = "days > 12";
}
if (!empty($_GET['price'])) {
    if ($_GET['price'] == '0-5') $where[] = "price_from < 5000000";
    elseif ($_GET['price'] == '5-10') $where[] = "price_from BETWEEN 5000000 AND 10000000";
    elseif ($_GET['price'] == '10-20') $where[] = "price_from BETWEEN 10000000 AND 20000000";
    elseif ($_GET['price'] == '20-50') $where[] = "price_from BETWEEN 20000000 AND 50000000";
    elseif ($_GET['price'] == '50+') $where[] = "price_from > 50000000";
}

$order = "is_hot DESC, created_at DESC";
if (!empty($_GET['sort'])) {
    if ($_GET['sort'] == 'price_asc') $order = "price_from ASC";
    if ($_GET['sort'] == 'price_desc') $order = "price_from DESC";
    if ($_GET['sort'] == 'newest') $order = "created_at DESC";
}

$sql = "SELECT id, title, slug, image, price, price_from, duration, hotel, airline, is_hot, tour_code, category, destinations FROM tours";
if (!empty($where)) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY $order LIMIT 24";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$filtered_tours = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?></title>
    <meta name="description" content="<?= $description ?>">
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
            --card-h: 540px;
        }
        body { font-family: 'Poppins', sans-serif; background: var(--light); }

        .page-header {
            background: linear-gradient(rgba(231,76,60,0.9), rgba(192,57,43,0.9)), url('<?= BASE_URL ?>assets/img/hero-tours.jpg') center/cover no-repeat;
            color: #fff; padding: 120px 0 80px; text-align: center;
        }
        .page-header h1 { font-size: 3.5rem; font-weight: 800; }
        .page-header .lead { font-size: 1.5rem; }

        /* FIX LỆCH CARD - CHUẨN ĐẸP */
        .tour-card {
            height: var(--card-h);
            display: flex;
            flex-direction: column;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: all .4s;
        }
        .tour-card:hover {
            transform: translateY(-15px);
            box-shadow: var(--shadow-hover);
        }
        .tour-img { width: 100%; height: 250px; object-fit: cover; }
        .card-body { flex: 1; display: flex; flex-direction: column; padding: 1.5rem; background: #fff; }
        .tour-info { flex: 1; }
        .bottom-section { margin-top: auto; padding-top: 1rem; }

        .badge-hot, .badge-sale {
            position: absolute; top: 15px; padding: 8px 20px;
            border-radius: 50px; font-weight: bold; color: #fff; z-index: 10;
        }
        .badge-hot { left: 15px; background: var(--primary); }
        .badge-sale { right: 15px; background: var(--success); }

        .price-from { font-size: 1.8rem; font-weight: 800; color: var(--primary); }

        .tour-title {
            font-size: 1.3rem; font-weight: 700; color: var(--dark);
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
            overflow: hidden; height: 3.2em; line-height: 1.6em;
        }

        .filter-box {
            background: #fff; border-radius: 20px; box-shadow: var(--shadow);
            padding: 30px; position: sticky; top: 100px;
        }

        .no-tour { text-align: center; padding: 100px 20px; color: #888; }

        .btn-danger { background: var(--primary); border: none; border-radius: 50px; }
        .btn-danger:hover { background: #c0392b; }

        @media (max-width: 992px) { :root { --card-h: 520px; } }
        @media (max-width: 768px) {
            :root { --card-h: 500px; }
            .page-header h1 { font-size: 2.8rem; }
            .tour-img { height: 200px; }
            .price-from { font-size: 1.5rem; }
            .badge-hot, .badge-sale { padding: 6px 14px; font-size: .9rem; }
        }
    </style>
</head>
<body>
    <?php include 'include/navbar.php'; ?>

    <!-- Header -->
    <div class="page-header">
        <div class="container">
            <h1 class="display-3 fw-bold">GÓI TOUR DU LỊCH 2025</h1>
            <p class="lead fs-3">Hơn 1.000 tour trong nước & quốc tế - Giá tốt nhất - Khởi hành hàng ngày</p>
            <a href="#tour-list" class="btn btn-light btn-lg px-5 rounded-pill shadow mt-3">
                <i class="bi bi-search me-2"></i> Tìm tour ngay
            </a>
        </div>
    </div>

    <div class="container py-5" id="tour-list">
        <div class="row">
            <!-- Bộ lọc -->
            <div class="col-lg-3 mb-5">
                <div class="filter-box">
                    <h4 class="fw-bold mb-4 text-danger"><i class="bi bi-funnel me-2"></i> Bộ lọc tìm kiếm</h4>
                    <form method="GET">
                        <div class="mb-3">
                            <input type="text" name="keyword" class="form-control" placeholder="Tên tour, điểm đến..." value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <select name="destination" class="form-select">
                                <option value="">Tất cả điểm đến</option>
                                <option value="trong_nuoc" <?= (($_GET['destination'] ?? '') == 'trong_nuoc') ? 'selected' : '' ?>>Trong nước</option>
                                <option value="nuoc_ngoai" <?= (($_GET['destination'] ?? '') == 'nuoc_ngoai') ? 'selected' : '' ?>>Nước ngoài</option>
                                <!-- Thêm các option khác nếu cần -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <select name="duration" class="form-select">
                                <option value="">Thời gian</option>
                                <option value="3-5" <?= ($_GET['duration'] ?? '') == '3-5' ? 'selected' : '' ?>>3 - 5 ngày</option>
                                <option value="6-8" <?= ($_GET['duration'] ?? '') == '6-8' ? 'selected' : '' ?>>6 - 8 ngày</optionẻ>
                                <option value="9-12" <?= ($_GET['duration'] ?? '') == '9-12' ? 'selected' : '' ?>>9 - 12 ngày</option>
                                <option value="12+" <?= ($_GET['duration'] ?? '') == '12+' ? 'selected' : '' ?>>Trên 12 ngày</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <select name="price" class="form-select">
                                <option value="">Khoảng giá</option>
                                <option value="0-5" <?= ($_GET['price'] ?? '') == '0-5' ? 'selected' : '' ?>>Dưới 5 triệu</option>
                                <option value="5-10" <?= ($_GET['price'] ?? '') == '5-10' ? 'selected' : '' ?>>5 - 10 triệu</option>
                                <option value="10-20" <?= ($_GET['price'] ?? '') == '10-20' ? 'selected' : '' ?>>10 - 20 triệu</option>
                                <option value="20-50" <?= ($_GET['price'] ?? '') == '20-50' ? 'selected' : '' ?>>20 - 50 triệu</option>
                                <option value="50+" <?= ($_GET['price'] ?? '') == '50+' ? 'selected' : '' ?>>Trên 50 triệu</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <select name="sort" class="form-select">
                                <option value="">Sắp xếp: Nổi bật</option>
                                <option value="price_asc" <?= ($_GET['sort'] ?? '') == 'price_asc' ? 'selected' : '' ?>>Giá tăng dần</option>
                                <option value="price_desc" <?= ($_GET['sort'] ?? '') == 'price_desc' ? 'selected' : '' ?>>Giá giảm dần</option>
                                <option value="newest" <?= ($_GET['sort'] ?? '') == 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-danger w-100 py-3 rounded-pill fw-bold">
                            <i class="bi bi-search me-2"></i> Tìm tour
                        </button>
                    </form>
                </div>
            </div>

            <!-- Danh sách tour -->
            <div class="col-lg-9">
                <h3 class="fw-bold mb-4">Có <?= count($filtered_tours) ?> tour phù hợp</h3>

                <?php if (empty($filtered_tours)): ?>
                    <div class="no-tour">
                        <i class="bi bi-search fs-1 text-muted mb-4"></i>
                        <h3 class="text-muted">Không tìm thấy tour nào</h3>
                        <a href="goi-tour.php" class="btn btn-danger px-5 py-3 rounded-pill">Xem tất cả tour</a>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($filtered_tours as $t): ?>
                            <div class="col-lg-4 col-md-6">
                                <div class="tour-card position-relative bg-white">
                                    <?php if ($t['is_hot']): ?>
                                        <div class="badge-hot">HOT</div>
                                    <?php endif; ?>
                                    <?php if ($t['price_from'] > 0 && $t['price_from'] < $t['price']): ?>
                                        <div class="badge-sale">-<?= round(100 - ($t['price_from'] * 100 / $t['price'])) ?>%</div>
                                    <?php endif; ?>

                                    <a href="<?= BASE_URL ?>tour/<?= $t['slug'] ?>">
                                        <img src="<?= BASE_URL ?>uploads/<?= htmlspecialchars($t['image'] ?? 'default.jpg') ?>" class="tour-img" alt="<?= htmlspecialchars($t['title']) ?>">
                                    </a>

                                    <div class="card-body">
                                        <div class="tour-info">
                                            <h5 class="tour-title mb-3">
                                                <a href="<?= BASE_URL ?>tour/<?= $t['slug'] ?>" class="text-dark text-decoration-none">
                                                    <?= htmlspecialchars($t['title']) ?>
                                                </a>
                                            </h5>
                                            <div class="small text-muted mb-3">
                                                <i class="bi bi-clock me-2"></i> <?= $t['duration'] ?>
                                                <span class="mx-3">|</span>
                                                <i class="bi bi-calendar-event me-2"></i> Khởi hành hàng ngày
                                            </div>
                                        </div>

                                        <div class="bottom-section d-flex justify-content-between align-items-end">
                                            <div>
                                                <?php if ($t['price_from'] > 0 && $t['price_from'] < $t['price']): ?>
                                                    <del class="text-muted small"><?= format_price($t['price']) ?></del>
                                                    <p class="price-from mb-0"><?= format_price($t['price_from']) ?></p>
                                                <?php else: ?>
                                                    <p class="price-from mb-0"><?= format_price($t['price'] ?: $t['price_from']) ?></p>
                                                <?php endif; ?>
                                            </div>
                                            <a href="<?= BASE_URL ?>tour/<?= $t['slug'] ?>" class="btn btn-danger px-4 py-2 rounded-pill">
                                                Xem chi tiết
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'include/footer.php'; ?>
</body>
</html>