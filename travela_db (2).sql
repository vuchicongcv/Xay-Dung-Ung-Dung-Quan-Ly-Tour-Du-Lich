-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 21, 2025 at 09:32 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `travela_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `booking_code` varchar(20) NOT NULL,
  `tour_id` int(11) NOT NULL,
  `departure_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `customer_email` varchar(100) DEFAULT NULL,
  `adults` int(11) DEFAULT NULL,
  `children` int(11) DEFAULT NULL,
  `infants` int(11) DEFAULT NULL,
  `total_price` decimal(15,2) DEFAULT NULL,
  `voucher_id` int(11) DEFAULT NULL,
  `discount_amount` decimal(12,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT 'pending',
  `bank_info` text DEFAULT NULL,
  `qr_code` text DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `booking_code`, `tour_id`, `departure_id`, `user_id`, `customer_name`, `customer_phone`, `customer_email`, `adults`, `children`, `infants`, `total_price`, `voucher_id`, `discount_amount`, `notes`, `payment_method`, `payment_status`, `bank_info`, `qr_code`, `status`, `created_at`, `updated_at`) VALUES
(1, 'BK202511050001', 1, 1, 1, 'công vũ', '0393814097', 'congdeptrai20055@gmail.com', 1, 0, 0, 22390000.00, NULL, 0.00, '', 'bank_transfer', 'paid', 'Ngân hàng: Vietcombank\nChủ TK: CÔNG TY DU LỊCH TRAVELA\nSố TK: 0011001933888\nNội dung: BK202511050001', 'https://img.vietqr.io/image/VCB-0011001933888-compact2.png?amount=22390000&addInfo=BK202511050001&accountName=C%C3%94NG+TY+DU+L%E1%BB%8ACH+TRAVELA', 'confirmed', '2025-11-05 01:19:17', '2025-11-21 00:48:33'),
(2, 'BK202511050002', 1, 1, NULL, 'công vũ', '0393814097', 'congvudemo@gmail.com', 1, 0, 0, 22390000.00, NULL, 0.00, '', 'bank_transfer', 'pending', 'Ngân hàng: Vietcombank\nChủ TK: CÔNG TY DU LỊCH TRAVELA\nSố TK: 0011001933888\nNội dung: BK202511050002', 'https://img.vietqr.io/image/VCB-0011001933888-compact2.png?amount=22390000&addInfo=BK202511050002&accountName=C%C3%94NG+TY+DU+L%E1%BB%8ACH+TRAVELA', 'cancelled', '2025-11-05 01:22:05', '2025-11-18 02:17:47'),
(3, 'BK202511170003', 1, 1, NULL, 'công vũ', '0393814097', 'congdeptrai20055@gmail.com', 1, 1, 1, 52616000.00, NULL, 0.00, '', 'bank_transfer', 'paid', 'Ngân hàng: Vietcombank\nChủ TK: CÔNG TY DU LỊCH TRAVELA\nSố TK: 0011001933888\nNội dung: BK202511170003', 'https://img.vietqr.io/image/VCB-0011001933888-compact2.png?amount=52616000&addInfo=BK202511170003&accountName=C%C3%94NG+TY+DU+L%E1%BB%8ACH+TRAVELA', 'confirmed', '2025-11-17 23:44:33', '2025-11-18 02:15:47'),
(4, 'BK202511200004', 1, 1, NULL, 'công vũ', '0393814097', 'vuchicongprovip@gmail.com', 1, 1, 0, 41421000.00, NULL, 0.00, '1', 'bank_transfer', 'pending', 'Ngân hàng: Vietcombank\nChủ TK: CÔNG TY DU LỊCH TRAVELA\nSố TK: 0011001933888\nNội dung: BK202511200004', 'https://img.vietqr.io/image/VCB-0011001933888-compact2.png?amount=41421000&addInfo=BK202511200004&accountName=C%C3%94NG+TY+DU+L%E1%BB%8ACH+TRAVELA', 'pending', '2025-11-20 23:32:57', NULL),
(5, 'BK202511210005', 1, 1, 1, 'công vũ', '0393814097', 'vuchicongprovip@gmail.com', 1, 1, 0, 37278900.00, 1, 4142100.00, '', 'bank_transfer', 'paid', 'Ngân hàng: Vietcombank\nChủ TK: CÔNG TY DU LỊCH TRAVELA\nSố TK: 0011001933888\nNội dung: BK202511210005', 'https://img.vietqr.io/image/VCB-0011001933888-compact2.png?amount=37278900&addInfo=BK202511210005&accountName=C%C3%94NG+TY+DU+L%E1%BB%8ACH+TRAVELA', 'pending', '2025-11-21 00:04:04', '2025-11-21 02:21:41'),
(6, 'BK202511210006', 1, 3, 1, 'công vũ', '0393814097', 'vuchicongprovip@gmail.com', 2, 1, 0, 42040350.00, 1, 4671150.00, 'ok', 'bank_transfer', 'paid', 'Ngân hàng: Vietcombank\nChủ TK: CÔNG TY DU LỊCH TRAVELA\nSố TK: 0011001933888\nNội dung: BK202511210006', 'https://img.vietqr.io/image/VCB-0011001933888-compact2.png?amount=42040350&addInfo=BK202511210006&accountName=C%C3%94NG+TY+DU+L%E1%BB%8ACH+TRAVELA', 'confirmed', '2025-11-21 00:41:07', '2025-11-21 00:45:38'),
(7, 'BK202511210007', 1, 1, 1, 'công vũ', '0393814097', 'vuchicongprovip@gmail.com', 1, 1, 1, 47354400.00, 1, 5261600.00, '', 'bank_transfer', 'paid', 'Ngân hàng: Vietcombank\nChủ TK: CÔNG TY DU LỊCH TRAVELA\nSố TK: 0011001933888\nNội dung: BK202511210007', 'https://img.vietqr.io/image/VCB-0011001933888-compact2.png?amount=47354400&addInfo=BK202511210007&accountName=C%C3%94NG+TY+DU+L%E1%BB%8ACH+TRAVELA', 'pending', '2025-11-21 13:22:36', '2025-11-21 15:31:00'),
(8, 'BK202511210008', 1, 1, 1, 'công vũ', '0393814097', 'vuchicongprovip@gmail.com', 1, 1, 1, 47354400.00, 1, 5261600.00, '', 'bank_transfer', 'paid', 'Ngân hàng: Vietcombank\nChủ TK: CÔNG TY DU LỊCH TRAVELA\nSố TK: 0011001933888\nNội dung: BK202511210008', 'https://img.vietqr.io/image/VCB-0011001933888-compact2.png?amount=47354400&addInfo=BK202511210008&accountName=C%C3%94NG+TY+DU+L%E1%BB%8ACH+TRAVELA', 'pending', '2025-11-21 13:23:50', '2025-11-21 15:30:38');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'Du lịch',
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `company` varchar(150) DEFAULT NULL,
  `passengers` int(11) DEFAULT 1,
  `address` text DEFAULT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('new','processed','replied') DEFAULT 'new',
  `phan_hoi` text DEFAULT NULL,
  `replied_at` datetime DEFAULT NULL,
  `trang_thai` enum('new','processed','replied') DEFAULT 'new'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `type`, `fullname`, `email`, `phone`, `company`, `passengers`, `address`, `subject`, `message`, `ip_address`, `created_at`, `status`, `phan_hoi`, `replied_at`, `trang_thai`) VALUES
(1, 'Du lịch', 'công vũ', 'vuchicongprovip@gmail.com', '0393814097', 'cong', 1, 'hà nội', 'tại sao vps của tôi 40gb mà cài lại còn 11gb vậy', 'tại sao vps của tôi 40gb mà cài lại còn 11gb vậy', '::1', '2025-11-20 20:15:01', 'new', 'ok', '2025-11-21 03:17:12', 'replied');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `excerpt` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `author_id` int(11) DEFAULT 1,
  `status` enum('draft','published') DEFAULT 'draft',
  `views` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `title`, `slug`, `content`, `excerpt`, `image`, `author_id`, `status`, `views`, `created_at`, `updated_at`) VALUES
(1, 'Khám phá Đà Lạt 2025: Thành phố sương mù với nhiều điểm check-in mới', 'kham-pha-da-lat-2025-diem-check-in-moi', 'Đà Lạt luôn là một trong những điểm đến được yêu thích nhất Việt Nam. Năm 2025, thành phố tiếp tục ra mắt nhiều địa điểm tham quan hoàn toàn mới như Khu Rừng Đom Đóm, nơi du khách có thể trải nghiệm ánh sáng lung linh về đêm. Ngoài ra, Cầu Gỗ Panorama với view núi cực rộng cũng trở thành điểm sống ảo được giới trẻ lựa chọn nhiều nhất. Du khách đến Đà Lạt còn có thể ghé Vùng Trời Hồng, một không gian cà phê kết hợp nghệ thuật đầy ấn tượng.', 'Đà Lạt bước sang năm 2025 với hàng loạt điểm check-in mới thu hút giới trẻ như Khu Rừng Đom Đóm, Cầu Gỗ Panorama và Vùng Trời Hồng.', 'uploads/posts/dalat-2025.jpg', 1, 'published', 0, '2025-11-20 20:23:14', '2025-11-20 20:23:14'),
(2, 'Phú Quốc khai trương tuyến cáp treo mới dài 4km nối thị trấn Dương Đông', 'phu-quoc-khai-truong-cap-treo-moi-2025', 'Phú Quốc vừa chính thức đưa vào hoạt động tuyến cáp treo dài hơn 4km nối liền thị trấn Dương Đông với khu nghỉ dưỡng phía nam đảo. Hệ thống cáp treo có sức chứa lớn, phục vụ tối đa 3.000 lượt khách mỗi giờ. Du khách sẽ được ngắm nhìn toàn cảnh biển ngọc bích và thiên nhiên Phú Quốc từ độ cao hơn 120m.', 'Tuyến cáp treo mới mở tại Phú Quốc giúp rút ngắn thời gian di chuyển và mang lại trải nghiệm ngắm biển tuyệt đẹp từ trên cao.', 'uploads/posts/phuquoc-cap-treo.jpg', 1, 'published', 0, '2025-11-20 20:23:14', '2025-11-20 20:23:14'),
(3, 'Review Marina Resort Vũng Tàu 2025: Sang trọng, dịch vụ cực tốt', 'review-marina-resort-vung-tau-2025', 'Marina Resort vừa mở cửa đầu 2025 và nhanh chóng trở thành điểm dừng chân yêu thích của du khách. Khu resort được thiết kế theo phong cách châu Âu hiện đại, phòng ốc rộng rãi và trang bị đầy đủ tiện nghi. Hồ bơi vô cực hướng biển là điểm thu hút nhất, cùng dịch vụ spa – buffet cực kỳ chất lượng.', 'Marina Resort là khu nghỉ dưỡng mới nổi tại Vũng Tàu với thiết kế sang trọng, hồ bơi vô cực và view biển tuyệt đẹp.', 'uploads/posts/marina-resort-vt.jpg', 1, 'published', 0, '2025-11-20 20:23:14', '2025-11-20 20:23:14'),
(4, 'AI du lịch bùng nổ 2025: Tự động gợi ý lịch trình và đặt vé', 'ai-du-lich-2025-goi-y-lich-trinh', 'Năm 2025 đánh dấu sự phát triển mạnh của AI trong lĩnh vực du lịch. Các ứng dụng mới cho phép phân tích sở thích của người dùng để đưa ra gợi ý điểm đến, khách sạn, thời gian hợp lý. Một số hệ thống còn hỗ trợ đặt vé máy bay, đặt phòng và tính chi phí tối ưu hóa chỉ với một cú chạm.', 'Công nghệ AI đã được tích hợp vào nhiều nền tảng du lịch giúp người dùng lên lịch trình tự động và đặt vé chỉ trong vài giây.', 'uploads/posts/ai-travel-2025.jpg', 1, 'published', 0, '2025-11-20 20:23:14', '2025-11-20 20:23:14'),
(5, '5 mẹo du lịch tiết kiệm dành cho người đi tour lần đầu', 'meo-du-lich-tiet-kiem-cho-nguoi-moi', 'Để có chuyến đi tiết kiệm nhưng vẫn đầy đủ trải nghiệm, bạn có thể áp dụng các mẹo sau: 1. Đặt vé sớm từ 2–4 tuần. 2. Ưu tiên chọn khách sạn gần trung tâm để tiết kiệm chi phí di chuyển. 3. Canh giờ giảm giá trên các ứng dụng du lịch. 4. Mang theo đồ ăn nhẹ để tránh mua giá cao ở sân bay. 5. Đi theo nhóm để chia sẻ chi phí tốt hơn.', 'Lần đầu đi du lịch? Hãy ghi nhớ 5 mẹo sau để vừa tiết kiệm chi phí vừa có chuyến đi trọn vẹn.', 'uploads/posts/meo-du-lich-1.jpg', 1, 'published', 0, '2025-11-20 20:23:14', '2025-11-20 20:23:14');

-- --------------------------------------------------------

--
-- Table structure for table `tours`
--

CREATE TABLE `tours` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `banner` varchar(255) DEFAULT NULL,
  `gallery` longtext DEFAULT NULL,
  `description` text DEFAULT NULL,
  `itinerary` longtext DEFAULT NULL,
  `itinerary_days` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`itinerary_days`)),
  `policy` text DEFAULT NULL,
  `is_hot` tinyint(1) DEFAULT 0,
  `hotel` varchar(50) DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `days` int(11) DEFAULT 0,
  `nights` int(11) DEFAULT 0,
  `min_pax` int(11) DEFAULT 10,
  `max_pax` int(11) DEFAULT 40,
  `price_from` decimal(12,2) DEFAULT 0.00,
  `airline` varchar(100) DEFAULT NULL,
  `tour_code` varchar(50) DEFAULT NULL,
  `price` decimal(15,0) NOT NULL,
  `category` enum('nuoc_ngoai','trong_nuoc') NOT NULL,
  `destinations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`destinations`)),
  `vehicles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`vehicles`)),
  `highlights` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`highlights`)),
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tours`
--

INSERT INTO `tours` (`id`, `title`, `slug`, `image`, `banner`, `gallery`, `description`, `itinerary`, `itinerary_days`, `policy`, `is_hot`, `hotel`, `duration`, `days`, `nights`, `min_pax`, `max_pax`, `price_from`, `airline`, `tour_code`, `price`, `category`, `destinations`, `vehicles`, `highlights`, `created_at`) VALUES
(1, 'Hong Kong', 'hong-kong', 'du-lich-hong-kong-kham-pha-xu-cang-thom-vn-2026-thumb-1763397432.webp', 'du-lich-hong-kong-kham-pha-xu-cang-thom-vn-2026-banner-1763397432.webp', '[]', '', '', '[]', '', 1, '', '', 3, 1, 10, 40, 0.00, '', '', 500000000, 'nuoc_ngoai', '[\"Hong Kong\"]', '[\"Máy bay\"]', '[]', '2025-11-02 23:54:56'),
(2, 'DU LỊCH CHÂU ÂU: ĐỨC – THỤY SĨ – PHÁP TẾT 2026 (VN)', 'du-lich-chau-au-duc-thuy-si-phap-tet-2026-vn', 'switzerland-cover_1.jpg_small.webp', NULL, NULL, NULL, NULL, NULL, NULL, 0, '4 sao', '11 Ngày 10 Đêm', 0, 0, 10, 40, 0.00, 'Vietnam Airlines', '', 77900000, 'nuoc_ngoai', NULL, NULL, NULL, '2025-11-02 23:54:56'),
(3, 'DU LỊCH HÀNH HƯƠNG: ẤN ĐỘ - NEPAL TẾT 2026 (INDIGO)', 'du-lich-hanh-huong-an-do-nepal-tet-2026-indigo', 'Den_Dai_Niet_Ban.jpg_small.webp', NULL, NULL, NULL, NULL, NULL, NULL, 0, '3 sao', '7 Ngày 7 Đêm', 0, 0, 10, 40, 0.00, 'Indigo', '', 27990000, 'nuoc_ngoai', NULL, NULL, NULL, '2025-11-02 23:54:56'),
(4, 'DU LỊCH TRUNG QUỐC: THÀNH ĐÔ – CỬU TRẠI CÂU – HUYỆN MẬU – TRƯỢT TUYẾT GIA CÔ SƠN (2025)', 'du-lich-trung-quoc-thanh-do-cuu-trai-cau-huyen-mau-truot-tuyet-gia-co-son-2025', 'dia-diem-du-lich-Thanh-Do--11-_1.png_small.webp', NULL, NULL, NULL, NULL, NULL, NULL, 0, '4 sao', '5 ngày 4 đêm', 0, 0, 10, 40, 0.00, 'Sichuan Airlines', 'TQ260825CTC', 17490000, 'nuoc_ngoai', NULL, NULL, NULL, '2025-11-02 23:54:56'),
(5, 'DU LỊCH TRUNG QUỐC: TRÙNG KHÁNH - THÀNH ĐÔ - CỬU TRẠI CÂU 2025 (CA)', 'du-lich-trung-quoc-trung-khanh-thanh-do-cuu-trai-cau-2025-ca', 'unnamed (39)_5.png_small.webp', NULL, NULL, NULL, NULL, NULL, NULL, 0, '4 sao', '6 ngày 5 đêm', 0, 0, 10, 40, 0.00, 'Air China', 'TQ180825TKCTC', 19490000, 'nuoc_ngoai', NULL, NULL, NULL, '2025-11-02 23:54:56'),
(6, 'DU LỊCH ĐÀI LOAN: ĐÀI BẮC – ĐÀI TRUNG – CAO HÙNG TẾT 2026 (EVA)', 'du-lich-dai-loan-dai-bac-dai-trung-cao-hung-tet-2026-eva', 'van-vo-mieu-3-1698983858_3.jpg_small.webp', NULL, NULL, NULL, NULL, NULL, NULL, 0, '4 sao', '5 Ngày 4 Đêm', 0, 0, 10, 40, 0.00, 'Eva Air', '', 20990000, 'nuoc_ngoai', NULL, NULL, NULL, '2025-11-02 23:54:56'),
(7, 'DU LỊCH TRUNG QUỐC: ÂN THI – PHƯỢNG HOÀNG CỔ TRẤN – TRƯƠNG GIA GIỚI 2025 (VJ)', 'du-lich-trung-quoc-an-thi-phuong-hoang-co-tran-truong-gia-gioi-2025-vj', 'image(2321)_1.png_small.webp', NULL, NULL, NULL, NULL, NULL, NULL, 0, '4 sao', '6 Ngày 5 Đêm', 0, 0, 10, 40, 0.00, 'Vietjet Air', '', 13990000, 'nuoc_ngoai', NULL, NULL, NULL, '2025-11-02 23:54:56'),
(8, 'DU LỊCH HÀN QUỐC: SEOUL - NAMI - TRƯỢT TUYẾT MÙA ĐÔNG TẾT 2026 (VJ)', 'du-lich-han-quoc-seoul-nami-truot-tuyet-mua-dong-tet-2026-vj', '1_2.jpg_small.webp', NULL, NULL, NULL, NULL, NULL, NULL, 0, '4 sao', '5 Ngày 4 Đêm', 0, 0, 10, 40, 0.00, 'Vietjet Air', '', 21490000, 'nuoc_ngoai', NULL, NULL, NULL, '2025-11-02 23:54:56'),
(9, 'Du Lịch Miền Bắc: Hà Giang - Cao Bằng Lễ 2/9 (2025)', 'du-lich-mien-bac-ha-giang-cao-bang-le-29-2025', 'tour_thac_ban_gioc_2_ngay_1.jpg_small.webp', NULL, NULL, NULL, NULL, NULL, NULL, 0, '4 sao', '5 Ngày 4 Đêm', 0, 0, 10, 40, 0.00, 'Vietjet Air', 'TQ300925LG', 9990000, 'trong_nuoc', NULL, NULL, NULL, '2025-11-02 23:54:56'),
(10, 'Du Lịch Miền Bắc: Hà Nội – Hà Giang – Sapa – Fansipan (2025)', 'Du-Lich-Mien-Bac-Ha-Noi-Ha-Giang-Sapa-Fansipan-2025', 'LUNGCU.png_small.webp', NULL, NULL, NULL, NULL, NULL, NULL, 0, '3 sao', '5 Ngày 4 Đêm', 0, 0, 10, 40, 0.00, 'Vietjet Air', 'MB291025SP', 8290000, 'trong_nuoc', NULL, NULL, NULL, '2025-11-02 23:54:56'),
(11, 'Du Lịch Miền Bắc: Hà Nội – Hạ Long – Chùa Bái Đính – Kdl Tràng An (2025)', 'Du-Lich-Mien-Bac-Ha-Noi-Ha-Long-Chua-Bai-Dinh-Kdl-Trang-An-2025', 'banner_MB.jpg_small.webp', NULL, NULL, NULL, NULL, NULL, NULL, 0, '3 sao', '5 Ngày 4 Đêm', 0, 0, 10, 40, 0.00, 'Bamboo Airways', 'MB210825TABĐ-HN', 8990000, 'trong_nuoc', NULL, NULL, NULL, '2025-11-02 23:54:56'),
(12, 'Du Lịch Đảo Ngọc Phú Quốc (2025)', 'Du-Lich-Dao-Ngoc-Phu-Quoc-2025', 'du_lich_phu_quoc_thang_10_.jpg_small.webp', NULL, NULL, NULL, NULL, NULL, NULL, 0, '3 sao', '3 Ngày 2 Đêm', 0, 0, 10, 40, 0.00, 'Vietjet Air', 'PQ101025', 5290000, 'trong_nuoc', NULL, NULL, NULL, '2025-11-02 23:54:56'),
(13, 'Du Lịch Miền Bắc: Hà Giang - Cao Bằng (2025)', 'Du-Lich-Mien-Bac-Ha-Giang-Cao-Bang-2025', 'banner tẾt-Recovered_1.jpg_small.webp', NULL, NULL, NULL, NULL, NULL, NULL, 0, '3 sao', '5 Ngày 4 Đêm', 0, 0, 10, 40, 0.00, 'Vietjet Air', 'MB081025CB', 8290000, 'trong_nuoc', NULL, NULL, NULL, '2025-11-02 23:54:56'),
(14, 'Du Lịch Nha Trang – Biển Gọi (tết 2025)', 'Du-Lich-Nha-Trang-Bien-Goi-tet-2025', 'thap_ba_Ponagar_1.jpg_small.webp', NULL, NULL, NULL, NULL, NULL, NULL, 0, '3 sao', '3 Ngày 3 Đêm', 0, 0, 10, 40, 0.00, 'XE 45 CHỔ', '', 3690000, 'trong_nuoc', NULL, NULL, NULL, '2025-11-02 23:54:56'),
(15, 'Du Lịch Tết Đà Nẵng – Đà Nẵng – Bà Nà Hill – Huế (2025)', 'Du-Lich-Tet-Da-Nang-Da-Nang-Ba-Na-Hill-Hue-2025', 'Lang_Khai_Dinh.jpg_small.webp', NULL, NULL, NULL, NULL, NULL, NULL, 0, '3 sao', '4 Ngày 3 Đêm', 0, 0, 10, 40, 0.00, 'Vietjet Air', '', 6590000, 'trong_nuoc', NULL, NULL, NULL, '2025-11-02 23:54:56'),
(16, 'Du Lịch Miền Trung: Đà Nẵng – Bà Nà – Huế – Quảng Bình (2025)', 'Du-Lich-Mien-Trung-Da-Nang-Ba-Na-Hue-Quang-Binh-2025', 'Ba_Na_Hill.jpg_small.webp', NULL, NULL, NULL, NULL, NULL, NULL, 0, '3 sao', '4 Ngày 3 Đêm', 0, 0, 10, 40, 0.00, 'Vietjet Air', '', 9990000, 'trong_nuoc', NULL, NULL, NULL, '2025-11-02 23:54:56'),
(17, 'Du Lịch Miền Tây: Cần Thơ – Chợ Nổi Cái Răng – Mỹ Tho (2025)', 'du-lich-mien-tay-can-tho-cho-noi-cai-rang-my-tho-2025', 'cho-noi-cai-rang.jpg_small.webp', NULL, NULL, NULL, NULL, NULL, NULL, 0, '3 sao', '3 Ngày 2 Đêm', 0, 0, 10, 40, 0.00, 'XE 45 CHỔ', 'MT150925CT', 3990000, 'trong_nuoc', NULL, NULL, NULL, '2025-11-02 23:54:56'),
(18, 'Du Lịch Sapa – Cát Cát – Hàm Rồng (2025)', 'du-lich-sapa-cat-cat-ham-rong-2025', 'sapa.jpg_small.webp', NULL, NULL, NULL, NULL, NULL, NULL, 0, '3 sao', '3 Ngày 2 Đêm', 0, 0, 10, 40, 0.00, 'XE 45 CHỔ', 'SP200925', 4590000, 'trong_nuoc', NULL, NULL, NULL, '2025-11-02 23:54:56'),
(19, 'Du Lịch Huế – Lăng Khải Định – Đại Nội (2025)', 'du-lich-hue-lang-khai-dinh-dai-noi-2025', 'dai-noi-hue.jpg_small.webp', NULL, NULL, NULL, NULL, NULL, NULL, 0, '3 sao', '3 Ngày 2 Đêm', 0, 0, 10, 40, 0.00, 'Vietjet Air', 'HUE180925', 5990000, 'trong_nuoc', NULL, NULL, NULL, '2025-11-02 23:54:56'),
(20, 'Du Lịch Phú Quốc – Vinpearl Safari (2025)', 'du-lich-phu-quoc-vinpearl-safari-2025', 'vinpearl-safari.jpg_small.webp', NULL, NULL, NULL, NULL, NULL, NULL, 0, '4 sao', '4 Ngày 3 Đêm', 0, 0, 10, 40, 0.00, 'Vietjet Air', 'PQ250925VP', 7990000, 'trong_nuoc', NULL, NULL, NULL, '2025-11-02 23:54:56'),
(21, 'Ngày 3', 'ngay-3', '11-thumb-1763396946.jpg', '11-banner-1763396946.png', '[\"11-gal-1763396946-0.png\"]', '', '', '[{\"title\":\"Ngày 1\",\"time\":\"\",\"content\":\"\"},{\"title\":\"Ngày 2\",\"time\":\"\",\"content\":\"\"},{\"title\":\"Ngày 3\",\"time\":\"\",\"content\":\"\"}]', '', 0, '', '', 0, 0, 10, 40, 0.00, '', '1', 1, 'nuoc_ngoai', '[\"Nha Trang\"]', '[]', '[]', '2025-11-03 00:43:34');

-- --------------------------------------------------------

--
-- Table structure for table `tour_departures`
--

CREATE TABLE `tour_departures` (
  `id` int(11) NOT NULL,
  `tour_id` int(11) NOT NULL,
  `departure_code` varchar(20) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `available_seats` int(11) DEFAULT 30,
  `price_adult` decimal(12,2) NOT NULL,
  `price_child` decimal(12,2) NOT NULL,
  `price_infant` decimal(12,2) NOT NULL,
  `status` enum('active','full','cancelled') DEFAULT 'active',
  `departure_date` date NOT NULL,
  `seats_available` int(11) DEFAULT 30
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tour_departures`
--

INSERT INTO `tour_departures` (`id`, `tour_id`, `departure_code`, `start_date`, `end_date`, `available_seats`, `price_adult`, `price_child`, `price_infant`, `status`, `departure_date`, `seats_available`) VALUES
(1, 1, 'HK190226VN', '2026-02-19', '2026-02-22', 11, 22390000.00, 19031000.00, 11195000.00, 'active', '0000-00-00', 30),
(2, 1, 'HK270326VN', '2026-03-27', '2026-03-30', 24, 16390000.00, 13931500.00, 8195000.00, 'active', '0000-00-00', 30),
(3, 1, 'HK110426VN', '2026-04-11', '2026-04-14', 21, 16390000.00, 13931500.00, 8195000.00, 'active', '0000-00-00', 30),
(4, 1, 'HK250426VN', '2026-04-25', '2026-04-28', 24, 17390000.00, 14781500.00, 8695000.00, 'active', '0000-00-00', 30),
(5, 1, 'HK230526VN', '2026-05-23', '2026-05-26', 24, 16390000.00, 13931500.00, 8195000.00, 'active', '0000-00-00', 30),
(6, 1, 'HK300526VN', '2026-05-30', '2026-06-02', 24, 16390000.00, 13931500.00, 8195000.00, 'active', '0000-00-00', 30),
(7, 1, 'HK130626VN', '2026-06-13', '2026-06-16', 24, 16390000.00, 13931500.00, 8195000.00, 'active', '0000-00-00', 30),
(8, 1, 'HK200626VN', '2026-06-20', '2026-06-23', 24, 16390000.00, 13931500.00, 8195000.00, 'active', '0000-00-00', 30),
(9, 1, 'HK270626VN', '2026-06-27', '2026-06-30', 24, 16390000.00, 13931500.00, 8195000.00, 'active', '0000-00-00', 30);

-- --------------------------------------------------------

--
-- Table structure for table `tour_gallery`
--

CREATE TABLE `tour_gallery` (
  `id` int(11) NOT NULL,
  `tour_id` int(11) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tour_gallery`
--

INSERT INTO `tour_gallery` (`id`, `tour_id`, `image_url`) VALUES
(1, 1, 'http://localhost/trieuhaotravel/uploads/files/Cau_Thanh_Ma_hongkong.jpg_medium.webp'),
(2, 1, 'http://localhost/trieuhaotravel/uploads/files/Chua_Wong_Tai_Sin.jpg_medium.webp'),
(3, 1, 'http://localhost/trieuhaotravel/uploads/files/tthoinghihongkong.jpg_medium.webp'),
(4, 1, 'http://localhost/trieuhaotravel/uploads/files/disney_hk_hinh.jpg_medium.webp');

-- --------------------------------------------------------

--
-- Table structure for table `tour_itineraries`
--

CREATE TABLE `tour_itineraries` (
  `id` int(11) NOT NULL,
  `tour_id` int(11) NOT NULL,
  `day_number` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `meals` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tour_itineraries`
--

INSERT INTO `tour_itineraries` (`id`, `tour_id`, `day_number`, `title`, `description`, `meals`) VALUES
(9, 1, 1, 'Ngày 1', 'ăn', ''),
(10, 1, 2, 'Ngày 2', 'uống', ''),
(11, 1, 3, 'Ngày 3', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `tour_schedules`
--

CREATE TABLE `tour_schedules` (
  `id` int(11) NOT NULL,
  `tour_id` int(11) DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `departure_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `stock` int(11) DEFAULT 24,
  `price` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tour_schedules`
--

INSERT INTO `tour_schedules` (`id`, `tour_id`, `code`, `departure_date`, `return_date`, `stock`, `price`) VALUES
(1, 1, 'HK190226VN', '2026-02-19', '2026-02-22', 24, 22390000.00),
(2, 1, 'HK270326VN', '2026-03-27', '2026-03-30', 24, 16390000.00),
(3, 1, 'HK110426VN', '2026-04-11', '2026-04-14', 24, 16390000.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `address` text DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` enum('Nam','Nữ','Khác') DEFAULT NULL,
  `avatar` varchar(255) DEFAULT 'img/default-avatar.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `role`, `created_at`, `updated_at`, `address`, `dob`, `gender`, `avatar`) VALUES
(1, 'công vũ', 'vuchicongprovip@gmail.com', '0393814097', '$2y$10$IkKafteIQKjp4mLn5DiJYOQLKMG46FPIGaoaomDiLWNwj4Wnd/gGa', 'admin', '2025-11-04 00:56:40', '2025-11-18 10:57:44', '1', '0000-00-00', 'Nam', 'uploads/avatars/avatar_1763438264_7524.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `vouchers`
--

CREATE TABLE `vouchers` (
  `id` int(11) NOT NULL,
  `ma_voucher` varchar(20) NOT NULL,
  `ten_voucher` varchar(100) NOT NULL,
  `giam_gia` decimal(5,2) NOT NULL,
  `gia_tri_toi_thieu` decimal(12,2) DEFAULT 0.00,
  `ngay_bat_dau` date NOT NULL,
  `ngay_ket_thuc` date NOT NULL,
  `so_luong` int(11) DEFAULT 1,
  `da_dung` int(11) DEFAULT 0,
  `user_id` int(11) DEFAULT NULL,
  `trang_thai` enum('active','used','expired') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vouchers`
--

INSERT INTO `vouchers` (`id`, `ma_voucher`, `ten_voucher`, `giam_gia`, `gia_tri_toi_thieu`, `ngay_bat_dau`, `ngay_ket_thuc`, `so_luong`, `da_dung`, `user_id`, `trang_thai`, `created_at`) VALUES
(1, 'SALE', '20', 10.00, 0.00, '2025-11-18', '2025-12-18', 100, 4, NULL, 'active', '2025-11-18 10:33:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_code` (`booking_code`),
  ADD KEY `tour_id` (`tour_id`),
  ADD KEY `departure_id` (`departure_id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `tours`
--
ALTER TABLE `tours`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_hot` (`is_hot`);

--
-- Indexes for table `tour_departures`
--
ALTER TABLE `tour_departures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tour_id` (`tour_id`);

--
-- Indexes for table `tour_gallery`
--
ALTER TABLE `tour_gallery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tour_id` (`tour_id`);

--
-- Indexes for table `tour_itineraries`
--
ALTER TABLE `tour_itineraries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tour_id` (`tour_id`);

--
-- Indexes for table `tour_schedules`
--
ALTER TABLE `tour_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tour_id` (`tour_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `email` (`email`) USING BTREE,
  ADD KEY `phone` (`phone`) USING BTREE;

--
-- Indexes for table `vouchers`
--
ALTER TABLE `vouchers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ma_voucher` (`ma_voucher`),
  ADD KEY `idx_ma` (`ma_voucher`),
  ADD KEY `idx_user` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tours`
--
ALTER TABLE `tours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `tour_departures`
--
ALTER TABLE `tour_departures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tour_gallery`
--
ALTER TABLE `tour_gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tour_itineraries`
--
ALTER TABLE `tour_itineraries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tour_schedules`
--
ALTER TABLE `tour_schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`departure_id`) REFERENCES `tour_departures` (`id`);

--
-- Constraints for table `tour_departures`
--
ALTER TABLE `tour_departures`
  ADD CONSTRAINT `tour_departures_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tour_gallery`
--
ALTER TABLE `tour_gallery`
  ADD CONSTRAINT `tour_gallery_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tour_itineraries`
--
ALTER TABLE `tour_itineraries`
  ADD CONSTRAINT `tour_itineraries_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tour_schedules`
--
ALTER TABLE `tour_schedules`
  ADD CONSTRAINT `tour_schedules_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vouchers`
--
ALTER TABLE `vouchers`
  ADD CONSTRAINT `vouchers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
