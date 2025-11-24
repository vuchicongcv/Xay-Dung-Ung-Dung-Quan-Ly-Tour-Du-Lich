<?php
ob_start();
require_once __DIR__ . '/include/config.php';
require_once __DIR__ . '/include/functions.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) redirect('index.php');

$errors = [];
$success_msg = '';
$old_data = $_SESSION['old_booking'] ?? [];
unset($_SESSION['old_booking'], $_SESSION['booking_errors']);

try {
    $stmt = $pdo->prepare("SELECT * FROM tours WHERE slug = ? LIMIT 1");
    $stmt->execute([$slug]);
    $tour = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$tour) die(show_alert('danger', 'Tour không tồn tại!'));

    $image = file_exists(__DIR__ . '/uploads/' . $tour['image']) ? $tour['image'] : 'default.jpg';

    // Decode các trường JSON
    $tour['destinations'] = json_decode($tour['destinations'] ?? '[]', true);
    $tour['vehicles'] = json_decode($tour['vehicles'] ?? '[]', true);
    $tour['highlights'] = json_decode($tour['highlights'] ?? '[]', true);

    $stmt = $pdo->prepare("SELECT * FROM tour_departures WHERE tour_id = ? AND status = 'active' ORDER BY start_date");
    $stmt->execute([$tour['id']]);
    $departures = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM tour_itineraries WHERE tour_id = ? ORDER BY day_number");
    $stmt->execute([$tour['id']]);
    $itineraries = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    die(show_alert('danger', 'Lỗi hệ thống khi tải tour.'));
}

// ==============================
// AJAX VOUCHER
// ==============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_voucher'])) {
    header('Content-Type: application/json');
    $code = trim($_POST['voucher_code'] ?? '');
    $temp_total = floatval($_POST['temp_total'] ?? 0);

    if (empty($code)) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng nhập mã voucher']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM vouchers WHERE ma_voucher = ? AND trang_thai = 'active' LIMIT 1");
    $stmt->execute([$code]);
    $v = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$v) {
        echo json_encode(['success' => false, 'message' => 'Mã voucher không tồn tại']);
        exit;
    }

    $today = date('Y-m-d');
    if ($today < $v['ngay_bat_dau'] || $today > $v['ngay_ket_thuc']) {
        echo json_encode(['success' => false, 'message' => 'Mã voucher đã hết hạn']);
        exit;
    }
    if ($v['da_dung'] >= $v['so_luong']) {
        echo json_encode(['success' => false, 'message' => 'Mã voucher đã hết lượt']);
        exit;
    }
    if ($temp_total < $v['gia_tri_toi_thieu']) {
        echo json_encode(['success' => false, 'message' => 'Đơn chưa đủ điều kiện áp dụng mã']);
        exit;
    }

    echo json_encode([
        'success' => true,
        'discount_percent' => $v['giam_gia'],
        'message' => "Áp dụng thành công! Giảm {$v['giam_gia']}%"
    ]);
    exit;
}

// ==============================
// XỬ LÝ ĐẶT TOUR
// ==============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_tour'])) {
    $departure_id = (int)($_POST['departure_id'] ?? 0);
    $adults = (int)($_POST['adults'] ?? 1);
    $children = (int)($_POST['children'] ?? 0);
    $infants = (int)($_POST['infants'] ?? 0);
    $customer_name = trim($_POST['customer_name'] ?? '');
    $customer_email = trim($_POST['customer_email'] ?? '');
    $customer_phone = trim($_POST['customer_phone'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $voucher_code = trim($_POST['voucher_code_hidden'] ?? '');

    if ($adults < 1) $errors[] = "Cần ít nhất 1 người lớn.";
    if (empty($customer_name)) $errors[] = "Vui lòng nhập họ tên.";
    if (!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email không hợp lệ.";
    if (!preg_match('/^0[35789][0-9]{8}$/', $customer_phone)) $errors[] = "Số điện thoại không đúng định dạng.";

    $stmt = $pdo->prepare("SELECT * FROM tour_departures WHERE id = ? AND tour_id = ? AND status = 'active'");
    $stmt->execute([$departure_id, $tour['id']]);
    $dep = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$dep) $errors[] = "Lịch khởi hành không hợp lệ.";
    elseif ($dep['available_seats'] < ($adults + $children + $infants)) $errors[] = "Không đủ chỗ! Chỉ còn {$dep['available_seats']} chỗ.";

    $sub_total = $dep['price_adult'] * $adults + $dep['price_child'] * $children + $dep['price_infant'] * $infants;
    $final_total = $sub_total;
    $discount_amount = 0;
    $voucher_id = null;

    if (!empty($voucher_code)) {
        $stmt = $pdo->prepare("SELECT * FROM vouchers WHERE ma_voucher = ? AND trang_thai = 'active' AND so_luong > da_dung AND ? BETWEEN ngay_bat_dau AND ngay_ket_thuc AND ? >= gia_tri_toi_thieu");
        $stmt->execute([$voucher_code, date('Y-m-d'), $sub_total]);
        $v = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($v) {
            $discount_amount = round(($v['giam_gia'] / 100) * $sub_total);
            $final_total = $sub_total - $discount_amount;
            $voucher_id = $v['id'];
        } else {
            $errors[] = "Mã voucher không hợp lệ hoặc không áp dụng được.";
        }
    }

    if (empty($errors)) {
        $booking_code = 'BK' . date('Ymd') . str_pad($pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn() + 1, 4, '0', STR_PAD_LEFT);

        $bank_info = "Ngân hàng: Vietcombank\nChủ TK: CÔNG TY DU LỊCH TRAVELA\nSố TK: 0011001933888\nNội dung: $booking_code";
        $qr_code = generateVietQR('VCB', '0011001933888', $final_total, $booking_code) ?: '';

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                INSERT INTO bookings 
                (booking_code, tour_id, departure_id, user_id, customer_name, customer_phone, customer_email,
                 adults, children, infants, total_price, voucher_id, discount_amount, notes,
                 payment_method, payment_status, bank_info, qr_code, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'bank_transfer', 'pending', ?, ?, 'pending')
            ");
            $stmt->execute([
                $booking_code, $tour['id'], $departure_id, $_SESSION['user_id'] ?? null,
                $customer_name, $customer_phone, $customer_email,
                $adults, $children, $infants, $final_total, $voucher_id, $discount_amount, $notes,
                $bank_info, $qr_code
            ]);

            $pdo->prepare("UPDATE tour_departures SET available_seats = available_seats - ? WHERE id = ?")
               ->execute([$adults + $children + $infants, $departure_id]);

            if ($voucher_id) {
                $pdo->prepare("UPDATE vouchers SET da_dung = da_dung + 1 WHERE id = ?")->execute([$voucher_id]);
            }

            $pdo->commit();

            $booking_data = [
                'booking_code' => $booking_code,
                'tour_title' => $tour['title'],
                'start_date' => $dep['start_date'],
                'total_price' => $final_total,
                'discount' => $discount_amount,
                'voucher_code' => $voucher_code,
                'bank_info' => $bank_info,
                'qr_code' => $qr_code
            ];
            sendBookingEmail($customer_email, $booking_data);

            $success_msg = "Đặt tour thành công!" . ($voucher_code ? " Đã áp dụng mã <strong>$voucher_code</strong>." : "");
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "Lỗi hệ thống khi đặt tour.";
        }
    }

    if (!empty($errors)) {
        $_SESSION['booking_errors'] = $errors;
        $_SESSION['old_booking'] = $_POST;
    }
}

ob_clean();
require_once 'include/head.php';
require_once 'include/navbar.php';
?>

<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
.loading-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.8); display: none; justify-content: center; align-items: center; z-index: 9999; }
.spinner-border { width: 3rem; height: 3rem; }
.form-control:focus, .form-select:focus { border-color: #dc3545; box-shadow: 0 0 0 0.2rem rgba(220,53,69,0.25); }
.btn-danger { background: linear-gradient(90deg, #dc3545, #c82333); }
.btn-danger:hover { background: linear-gradient(90deg, #c82333, #dc3545); }
.card { border-radius: 10px; overflow: hidden; }
.hot-badge { position: absolute; top: 20px; right: 20px; background: #e74c3c; color: white; padding: 10px 20px; font-size: 1.2rem; border-radius: 50px; font-weight: bold; z-index: 10; }
.info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; }
.info-item { display: flex; align-items: center; }
.info-item i { width: 35px; color: #dc3545; font-size: 1.2rem; }
</style>

<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner-border text-danger" role="status"></div>
</div>

<div class="container py-5">
    <?php if ($success_msg || !empty($errors)): ?>
        <script>
            Swal.fire({
                icon: '<?= $success_msg ? "success" : "error" ?>',
                title: '<?= $success_msg ? "Đặt tour thành công!" : "Lỗi!" ?>',
                html: '<?= $success_msg ? htmlspecialchars($success_msg) : implode("<br>", array_map("htmlspecialchars", $errors)) ?>',
                confirmButtonColor: '#dc3545'
            }).then(() => {
                <?php if ($success_msg): ?>
                    window.location.href = '<?= BASE_URL ?>tour-detail.php?slug=<?= urlencode($slug) ?>';
                <?php endif; ?>
            });
        </script>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4 position-relative">
                <?php if ($tour['is_hot']): ?>
                <div class="hot-badge">HOT TOUR</div>
                <?php endif; ?>

                <img src="<?= BASE_URL ?>uploads/<?= htmlspecialchars($image) ?>" class="card-img-top" style="height:450px; object-fit:cover;" alt="<?= htmlspecialchars($tour['title']) ?>">

                <div class="card-body p-5">
                    <h1 class="display-5 fw-bold mb-4 text-danger"><?= htmlspecialchars($tour['title']) ?></h1>

                    <!-- THÔNG TIN CHI TIẾT MỚI -->
                    <div class="info-grid mb-5">
                        <?php if (!empty($tour['destinations'])): ?>
                        <div class="info-item"><i class="fas fa-map-marker-alt"></i> <strong>Điểm đến:</strong> <?= implode(', ', $tour['destinations']) ?></div>
                        <?php endif; ?>
                        <?php if (!empty($tour['vehicles'])): ?>
                        <div class="info-item"><i class="fas fa-plane"></i> <strong>Phương tiện:</strong> <?= implode(', ', $tour['vehicles']) ?></div>
                        <?php endif; ?>
                        <?php if (!empty($tour['highlights'])): ?>
                        <div class="info-item"><i class="fas fa-star"></i> <strong>Hoạt động:</strong> <?= implode(', ', $tour['highlights']) ?></div>
                        <?php endif; ?>
                        <?php if ($tour['hotel']): ?>
                        <div class="info-item"><i class="fas fa-hotel"></i> <strong>Khách sạn:</strong> <?= htmlspecialchars($tour['hotel']) ?></div>
                        <?php endif; ?>
                        <?php if ($tour['duration']): ?>
                        <div class="info-item"><i class="fas fa-calendar-alt"></i> <strong>Thời gian:</strong> <?= htmlspecialchars($tour['duration']) ?></div>
                        <?php endif; ?>
                        <?php if ($tour['days'] && $tour['nights']): ?>
                        <div class="info-item"><i class="fas fa-sun"></i> <strong>Số ngày/đêm:</strong> <?= $tour['days'] ?> ngày <?= $tour['nights'] ?> đêm</div>
                        <?php endif; ?>
                        <?php if ($tour['min_pax'] && $tour['max_pax']): ?>
                        <div class="info-item"><i class="fas fa-users"></i> <strong>Số khách:</strong> <?= $tour['min_pax'] ?> - <?= $tour['max_pax'] ?> người</div>
                        <?php endif; ?>
                        <?php if ($tour['tour_code']): ?>
                        <div class="info-item"><i class="fas fa-tag"></i> <strong>Mã tour:</strong> <span class="text-primary fw-bold"><?= htmlspecialchars($tour['tour_code']) ?></span></div>
                        <?php endif; ?>
                        <?php if ($tour['airline']): ?>
                        <div class="info-item"><i class="fas fa-plane-departure"></i> <strong>Hãng bay:</strong> <?= htmlspecialchars($tour['airline']) ?></div>
                        <?php endif; ?>
                        <?php if ($tour['category']): ?>
                        <div class="info-item"><i class="fas fa-tags"></i> <strong>Chuyên mục:</strong> <?= strtoupper($tour['category']) ?></div>
                        <?php endif; ?>
                    </div>

                  
                </div>
            </div>

            <!-- LỊCH KHỞI HÀNH -->
            <?php if ($departures): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-calendar-alt me-2"></i>Lịch khởi hành (<?= count($departures) ?> lịch)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã lịch</th>
                                    <th>Ngày đi</th>
                                    <th>Ngày về</th>
                                    <th>Chỗ còn</th>
                                    <th>Giá người lớn</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($departures as $d): ?>
                                <tr>
                                    <td><strong class="text-primary"><?= htmlspecialchars($d['departure_code']) ?></strong></td>
                                    <td class="text-success fw-bold"><?= date('d/m/Y', strtotime($d['start_date'])) ?></td>
                                    <td><?= date('d/m/Y', strtotime($d['end_date'])) ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-<?= $d['available_seats'] > 10 ? 'success' : ($d['available_seats'] > 0 ? 'warning' : 'danger') ?> px-3 py-1">
                                            <?= $d['available_seats'] ?>
                                        </span>
                                    </td>
                                    <td><strong class="text-danger"><?= format_price($d['price_adult']) ?></strong></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- LỊCH TRÌNH -->
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-route me-2"></i>Lịch trình chi tiết</h5>
                </div>
                <div class="card-body p-4">
                    <?php foreach ($itineraries as $i): ?>
                    <div class="border-start border-primary border-4 ps-4 mb-4">
                        <h6 class="fw-bold text-primary mb-2"><?= htmlspecialchars($i['title']) ?></h6>
                        <p class="small text-success mb-2 fw-medium">
                            Bữa ăn:
                            <?php
                            $meals = explode(',', $i['meals'] ?? '');
                            $meal_str = '';
                            foreach (['Sáng', 'Trưa', 'Tối'] as $m) {
                                $meal_str .= in_array($m, $meals) ? "<i class='fas fa-check-circle text-success me-1'></i>$m: có, " : "<i class='fas fa-times-circle text-muted me-1'></i>$m: không, ";
                            }
                            echo rtrim($meal_str, ', ');
                            ?>
                        </p>
                        <div class="text-muted lh-lg">
                            <?= nl2br(htmlspecialchars($i['description'])) ?>
                        </div>
                    </div>
                    <hr class="my-4">
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- FORM ĐẶT TOUR + VOUCHER TỰ ĐỘNG -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 100px;">
                <div class="card-header bg-danger text-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-shopping-cart me-2"></i>Đặt tour ngay</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" id="bookingForm" novalidate>
                        <input type="hidden" name="book_tour" value="1">
                        <input type="hidden" name="voucher_code_hidden" id="voucher_code_hidden" value="<?= htmlspecialchars($old_data['voucher_code_hidden'] ?? '') ?>">

                        <!-- Chọn ngày khởi hành -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Chọn ngày khởi hành <span class="text-danger">*</span></label>
                            <select name="departure_id" class="form-select shadow-sm" required onchange="updatePrices()">
                                <option value="">-- Chọn ngày --</option>
                                <?php foreach ($departures as $d): ?>
                                <option value="<?= $d['id'] ?>"
                                    data-adult="<?= $d['price_adult'] ?>"
                                    data-child="<?= $d['price_child'] ?>"
                                    data-infant="<?= $d['price_infant'] ?>"
                                    <?= ($old_data['departure_id'] ?? '') == $d['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($d['departure_code']) ?> | <?= date('d/m', strtotime($d['start_date'])) ?> - <?= date('d/m/Y', strtotime($d['end_date'])) ?> (Còn <?= $d['available_seats'] ?> chỗ)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Người lớn / Trẻ em / Trẻ nhỏ -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Người lớn <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <button type="button" class="btn btn-outline-secondary" onclick="changeQty('adults', -1)">-</button>
                                <input type="number" name="adults" id="adults" class="form-control text-center fw-bold" value="<?= $old_data['adults'] ?? 1 ?>" min="1" readonly required>
                                <button type="button" class="btn btn-outline-secondary" onclick="changeQty('adults', 1)">+</button>
                            </div>
                            <small class="text-muted d-block mt-1">Giá: <span id="price-adult" class="fw-bold text-danger">0₫</span></small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Trẻ em (2-10 tuổi)</label>
                            <div class="input-group">
                                <button type="button" class="btn btn-outline-secondary" onclick="changeQty('children', -1)">-</button>
                                <input type="number" name="children" id="children" class="form-control text-center fw-bold" value="<?= $old_data['children'] ?? 0 ?>" min="0" readonly>
                                <button type="button" class="btn btn-outline-secondary" onclick="changeQty('children', 1)">+</button>
                            </div>
                            <small class="text-muted d-block mt-1">Giá: <span id="price-child" class="fw-bold text-danger">0₫</span></small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Trẻ nhỏ (&lt; 2 tuổi)</label>
                            <div class="input-group">
                                <button type="button" class="btn btn-outline-secondary" onclick="changeQty('infants', -1)">-</button>
                                <input type="number" name="infants" id="infants" class="form-control text-center fw-bold" value="<?= $old_data['infants'] ?? 0 ?>" min="0" readonly>
                                <button type="button" class="btn btn-outline-secondary" onclick="changeQty('infants', 1)">+</button>
                            </div>
                            <small class="text-muted d-block mt-1">Giá: <span id="price-infant" class="fw-bold text-danger">0₫</span></small>
                        </div>

                        <!-- VOUCHER -->
                        <div class="mb-4">
                            <label class="form-label fw-bold text-primary">Mã giảm giá (nếu có)</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="voucher_code" placeholder="Nhập mã voucher" value="<?= htmlspecialchars($old_data['voucher_code_hidden'] ?? '') ?>">
                                <button type="button" class="btn btn-danger" id="applyVoucherBtn">Áp dụng</button>
                            </div>
                            <div id="voucherMsg" class="mt-2 fw-bold"></div>
                        </div>

                        <hr class="my-4">
                        <div class="d-flex justify-content-between fw-bold mb-2">
                            <span>Tạm tính:</span>
                            <span id="sub-total">0₫</span>
                        </div>
                        <div class="d-flex justify-content-between text-success fw-bold mb-2" id="discount-row" style="display:none;">
                            <span>Giảm giá voucher:</span>
                            <span id="discount-amount">-0₫</span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold fs-4 bg-light p-3 rounded">
                            <span>Tổng cộng:</span>
                            <span id="total-price" class="text-danger">0₫</span>
                        </div>

                        <div class="mb-4 mt-4">
                            <label class="form-label fw-bold">Phương thức thanh toán</label>
                            <div class="border rounded p-3 bg-light">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" value="bank_transfer" checked disabled>
                                    <label class="form-check-label fw-medium">
                                        <i class="fas fa-university text-info me-2"></i>Chuyển khoản ngân hàng (VietQR)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <input type="text" name="customer_name" class="form-control shadow-sm" placeholder="Họ tên *" value="<?= htmlspecialchars($old_data['customer_name'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" name="customer_email" class="form-control shadow-sm" placeholder="Email *" value="<?= htmlspecialchars($old_data['customer_email'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <input type="tel" name="customer_phone" class="form-control shadow-sm" placeholder="SĐT *" value="<?= htmlspecialchars($old_data['customer_phone'] ?? '') ?>" pattern="^0[35789][0-9]{8}$" required>
                        </div>
                        <div class="mb-4">
                            <textarea name="notes" class="form-control shadow-sm" rows="2" placeholder="Ghi chú (tùy chọn)"><?= htmlspecialchars($old_data['notes'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-danger w-100 fw-bold py-3 shadow-sm" id="submitBtn">
                            <i class="fas fa-paper-plane me-2"></i>Gửi yêu cầu đặt tour
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let currentDiscountPercent = 0;
let subTotal = 0;

function changeQty(id, delta) {
    const input = document.getElementById(id);
    let val = parseInt(input.value) + delta;
    if (val < 0) val = 0;
    if (id === 'adults' && val < 1) val = 1;
    input.value = val;
    updatePrices();
}

function updatePrices() {
    const select = document.querySelector('[name="departure_id"]');
    if (!select || !select.value) {
        document.getElementById('sub-total').textContent = '0₫';
        document.getElementById('total-price').textContent = '0₫';
        return;
    }

    const option = select.options[select.selectedIndex];
    const prices = {
        adult: parseFloat(option.dataset.adult) || 0,
        child: parseFloat(option.dataset.child) || 0,
        infant: parseFloat(option.dataset.infant) || 0
    };

    const qty = {
        adults: parseInt(document.getElementById('adults').value) || 0,
        children: parseInt(document.getElementById('children').value) || 0,
        infants: parseInt(document.getElementById('infants').value) || 0
    };

    document.getElementById('price-adult').textContent = prices.adult.toLocaleString('vi-VN') + '₫';
    document.getElementById('price-child').textContent = prices.child.toLocaleString('vi-VN') + '₫';
    document.getElementById('price-infant').textContent = prices.infant.toLocaleString('vi-VN') + '₫';

    subTotal = prices.adult * qty.adults + prices.child * qty.children + prices.infant * qty.infants;
    document.getElementById('sub-total').textContent = subTotal.toLocaleString('vi-VN') + '₫';

    if (currentDiscountPercent > 0) {
        const discount = Math.round((currentDiscountPercent / 100) * subTotal);
        document.getElementById('discount-row').style.display = 'block';
        document.getElementById('discount-amount').textContent = '-' + discount.toLocaleString('vi-VN') + '₫';
        document.getElementById('total-price').textContent = (subTotal - discount).toLocaleString('vi-VN') + '₫';
    } else {
        document.getElementById('discount-row').style.display = 'none';
        document.getElementById('total-price').textContent = subTotal.toLocaleString('vi-VN') + '₫';
    }
}

document.getElementById('applyVoucherBtn').addEventListener('click', function() {
    const code = document.getElementById('voucher_code').value.trim();
    if (!code) {
        Swal.fire('Thông báo', 'Vui lòng nhập mã voucher', 'warning');
        return;
    }

    const fd = new FormData();
    fd.append('apply_voucher', '1');
    fd.append('voucher_code', code);
    fd.append('temp_total', subTotal);

    fetch('', {method: 'POST', body: fd})
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                currentDiscountPercent = data.discount_percent;
                document.getElementById('voucher_code_hidden').value = code;
                document.getElementById('voucherMsg').innerHTML = `<span class="text-success"><strong>✓ ${data.message}</strong></span>`;
                updatePrices();
                Swal.fire('Thành công', data.message, 'success');
            } else {
                currentDiscountPercent = 0;
                document.getElementById('voucher_code_hidden').value = '';
                document.getElementById('voucherMsg').innerHTML = `<span class="text-danger"><strong>✗ ${data.message}</strong></span>`;
                updatePrices();
                Swal.fire('Lỗi', data.message, 'error');
            }
        })
        .catch(() => Swal.fire('Lỗi', 'Không thể kết nối', 'error'));
});

function handleSubmit(event) {
    event.preventDefault();
    const form = document.getElementById('bookingForm');
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        Swal.fire('Lỗi', 'Vui lòng điền đầy đủ thông tin', 'warning');
        return;
    }
    document.getElementById('loadingOverlay').style.display = 'flex';
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
    form.submit();
}

document.addEventListener('DOMContentLoaded', () => {
    updatePrices();
    document.getElementById('bookingForm').addEventListener('submit', handleSubmit);

    if (document.getElementById('voucher_code_hidden').value) {
        document.getElementById('voucher_code').value = document.getElementById('voucher_code_hidden').value;
        document.getElementById('applyVoucherBtn').click();
    }
});
</script>

<?php
require_once 'include/footer.php';
ob_end_flush();
?>