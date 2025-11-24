<?php 
require_once 'include/config.php';

// Thiết lập SEO
define('PAGE_TITLE', 'Tin Tức Du Lịch - Travela');
define('PAGE_DESCRIPTION', 'Cập nhật tin tức du lịch mới nhất 2025, kinh nghiệm du lịch bụi, review địa điểm hot, mẹo săn tour giá rẻ từ Travela');

// Phân trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 9; // số bài/trang
$offset = ($page - 1) * $limit;

// Đếm tổng bài viết
$total_stmt = $pdo->query("SELECT COUNT(*) FROM posts WHERE status = 'published'");
$total_posts = $total_stmt->fetchColumn();
$total_pages = ceil($total_posts / $limit);

// Lấy bài viết theo trang
$stmt = $pdo->prepare("SELECT * FROM posts WHERE status = 'published' ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include 'include/head.php'; ?>
    
    <!-- CSS tùy chỉnh cho trang tin tức -->
    <style>
        :root {
            --primary: #dc3545;
            --dark: #212529;
        }
        .hero-blog {
            height: 90vh;
            min-height: 520px;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
        }
        .hero-blog::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.4) 100%);
            z-index: 1;
        }
        .hero-img {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            object-fit: cover;
            filter: brightness(0.7);
        }
        .card-blog {
            transition: all 0.4s ease;
            border: none;
            border-radius: 12px;
            overflow: hidden;
        }
        .card-blog:hover {
            transform: translateY(-12px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
        }
        .card-blog img {
            height: 240px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .card-blog:hover img {
            transform: scale(1.1);
        }
        .badge-new {
            background: var(--primary);
            color: white;
            padding: 0.4em 0.8em;
            border-radius: 50px;
            font-size: 0.8rem;
        }
        .text-danger-hover:hover {
            color: var(--primary) !important;
        }
        .pagination .page-link {
            border: none;
            color: #6c757d;
            padding: 0.6rem 1rem;
            border-radius: 8px !important;
            margin: 0 4px;
        }
        .pagination .page-item.active .page-link {
            background: var(--primary);
            border-color: var(--primary);
        }
        @media (max-width: 768px) {
            .hero-blog { height: 70vh; }
            .display-2 { font-size: 2.8rem; }
        }
    </style>
</head>
<body>

<?php include 'include/navbar.php'; ?>

<!-- Hero Section -->
<section class="hero-blog position-relative text-white text-center">
    <img src="<?=BASE_URL?>assets/img/blog-hero.jpg" class="hero-img" alt="Tin tức du lịch Travela">
    <div class="container position-relative" style="z-index: 2;">
        <h1 class="display-2 fw-bold mb-3">TIN TỨC & KINH NGHIỆM DU LỊCH</h1>
        <p class="lead fs-3 opacity-90">Khám phá thế giới cùng Travela – Cập nhật nóng hổi mỗi ngày</p>
    </div>
</section>

<!-- Danh sách bài viết -->
<section class="py-5 bg-light">
    <div class="container">
        <?php if (empty($posts)): ?>
            <div class="text-center py-5">
                <h3 class="text-muted">Hiện chưa có bài viết nào.</h3>
                <p>Hãy quay lại sau nhé!</p>
            </div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 g-lg-5">
                <?php foreach ($posts as $p): 
                    // Tạo URL thân thiện: ưu tiên slug, nếu không có thì dùng id
                    $slug = !empty($p['slug']) ? $p['slug'] : $p['id'];
                    $post_url = BASE_URL . 'tin-tuc/' . $slug;
                ?>
                    <div class="col">
                        <article class="card card-blog h-100 shadow-sm bg-white">
                            <div class="position-relative">
                                <img src="<?= BASE_URL ?>uploads/posts/<?= htmlspecialchars($p['image'] ?? 'default.jpg') ?>" 
                                     class="card-img-top" 
                                     alt="<?= htmlspecialchars($p['title']) ?>">
                                <?php if (strtotime($p['created_at']) > strtotime('-7 days')): ?>
                                    <div class="position-absolute top-0 end-0 p-3">
                                        <span class="badge-new">Mới</span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="card-body d-flex flex-column p-4">
                                <h3 class="card-title fs-5 fw-bold mb-3">
                                    <a href="<?= $post_url ?>" class="text-dark text-decoration-none text-danger-hover stretched-link">
                                        <?= htmlspecialchars($p['title']) ?>
                                    </a>
                                </h3>

                                <div class="text-muted small mb-3 d-flex align-items-center gap-3">
                                    <span><i class="bi bi-calendar3"></i> <?= date('d/m/Y', strtotime($p['created_at'])) ?></span>
                                    <span><i class="bi bi-eye"></i> <?= number_format($p['views'] ?? 0) ?> lượt xem</span>
                                </div>

                                <p class="card-text text-secondary flex-grow-1">
                                    <?= htmlspecialchars(mb_substr(strip_tags($p['excerpt'] ?: $p['content']), 0, 130, 'UTF-8')) ?>...
                                </p>

                                <div class="mt-4">
                                    <a href="<?= $post_url ?>" class="btn btn-outline-danger rounded-pill px-4 py-2">
                                        Đọc thêm <i class="bi bi-arrow-right ms-2"></i>
                                    </a>
                                </div>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Phân trang -->
            <?php if ($total_pages > 1): ?>
                <nav aria-label="Phân trang tin tức" class="mt-5">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page - 1 ?>">« Trước</a>
                            </li>
                        <?php endif; ?>

                        <?php 
                        $start = max(1, $page - 2);
                        $end = min($total_pages, $page + 2);
                        for ($i = $start; $i <= $end; $i++): 
                        ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page + 1 ?>">Sau »</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</section>

<?php include 'include/footer.php'; ?>

<!-- Bootstrap Icons (nếu chưa có trong head.php) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

</body>
</html>