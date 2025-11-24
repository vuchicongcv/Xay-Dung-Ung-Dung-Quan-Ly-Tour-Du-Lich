# BTL_Quan_ly_doan_vien

![Banner or Logo](https://github.com/tyanzuq2811/BTL_Quan_ly_doan_vien/blob/main/docs/logo/dnu_logo.png?raw=true)  
*(Hệ thống quản lý Đoàn viên - Bài tập lớn)*

Hệ thống quản lý Đoàn viên trong trường Đại học, thay thế việc quản lý thủ công bằng giấy tờ hoặc Excel, cung cấp giải pháp tập trung, hiện đại và dễ sử dụng.

[![License](https://img.shields.io/github/license/tyanzuq2811/BTL_Quan_ly_doan_vien)](LICENSE)  
[![Issues](https://img.shields.io/github/issues/tyanzuq2811/BTL_Quan_ly_doan_vien)](https://github.com/tyanzuq2811/BTL_Quan_ly_doan_vien/issues)  
[![Stars](https://img.shields.io/github/stars/tyanzuq2811/BTL_Quan_ly_doan_vien?style=social)](https://github.com/tyanzuq2811/BTL_Quan_ly_doan_vien/stargazers)  
[![Forks](https://img.shields.io/github/forks/tyanzuq2811/BTL_Quan_ly_doan_vien?style=social)](https://github.com/tyanzuq2811/BTL_Quan_ly_doan_vien/network/members)

## 📋 Mục lục
- [Giới thiệu](#giới-thiệu)
- [Công nghệ sử dụng](#công-nghệ-sử-dụng)
- [Tính năng](#tính-năng)
- [Demo / Hình ảnh](#demo--hình-ảnh)
- [Cài đặt](#cài-đặt)
- [Cách sử dụng](#cách-sử-dụng)
- [Đóng góp](#đóng-góp)
- [Tác giả](#tác-giả)
- [Giấy phép](#giấy-phép)

## Giới thiệu
Hệ thống hỗ trợ quản lý, theo dõi và đánh giá hoạt động Đoàn Thanh niên trong môi trường giáo dục đại học. Dự án được phát triển như một bài tập lớn (BTL), sử dụng công nghệ web hiện đại để tối ưu hóa quy trình quản lý đoàn viên.

![Logos](https://github.com/tyanzuq2811/BTL_Quan_ly_doan_vien/blob/main/docs/logo/fitdnu_logo.png?raw=true)  
![AIoT Lab](https://github.com/tyanzuq2811/BTL_Quan_ly_doan_vien/blob/main/docs/logo/aiotlab_logo.png?raw=true)  

- **Liên kết liên quan:**  
  [Facebook AIoT Lab](https://www.facebook.com/DNUAIoTLab)  
  [Khoa CNTT ĐH Đại Nam](https://dainam.edu.vn/vi/khoa-cong-nghe-thong-tin)  
  [Trang chủ ĐH Đại Nam](https://dainam.edu.vn)

## 🛠️ Công nghệ sử dụng
- **Hệ điều hành:** macOS, Windows, Ubuntu  
- **Công nghệ chính:** PHP, Bootstrap  
- **Web Server & Database:** Apache, MySQL, XAMPP  
- **Database Management Tools:** MySQL Workbench  

## ✨ Tính năng
- Quản lý liên chi đoàn, chi đoàn và đoàn viên  
- Theo dõi lịch sử tham gia hoạt động  
- Quản lý đoàn phí và điểm rèn luyện  
- Tổ chức sự kiện và khen thưởng  
- Gửi thông báo và quản lý tài khoản với phân quyền (Admin, Cán bộ, Đoàn viên)  

## 🎥 Demo / Hình ảnh
Dưới đây là các screenshot minh họa giao diện chính (từ repo gốc). Bạn có thể thêm GIF demo nếu có video.

- ![Trang đăng nhập](docs/screenshots/login.png) *(Thay bằng đường dẫn ảnh thực tế nếu có trong repo)*  
- ![Dashboard Admin](docs/screenshots/admin_dashboard.png)  
- ![Dashboard Cán bộ](docs/screenshots/can_bo_dashboard.png)  
- ![Dashboard Đoàn viên](docs/screenshots/doan_vien_dashboard.png)  
- ![Quản lý Liên chi đoàn](docs/screenshots/quan_ly_lien_chi.png)  
- ![Quản lý Chi đoàn](docs/screenshots/quan_ly_chi_doan.png)  
- ![Quản lý Đoàn viên](docs/screenshots/quan_ly_doan_vien.png)  
- ![Lịch sử tham gia](docs/screenshots/lich_su_tham_gia.png)  
- ![Quản lý Đoàn phí](docs/screenshots/doan_phi.png)  
- ![Điểm rèn luyện](docs/screenshots/diem_ren_luyen.png)  
- ![Quản lý Sự kiện](docs/screenshots/su_kien.png)  
- ![Khen thưởng](docs/screenshots/khen_thuong.png)  
- ![Thông báo](docs/screenshots/thong_bao.png)  
- ![Quản lý Tài khoản](docs/screenshots/tai_khoan.png)  

*(Lưu ý: Các ảnh trên dựa trên mô tả repo gốc. Nếu repo có thư mục `docs/screenshots/`, hãy upload ảnh thực tế và cập nhật link tương tự như logo ở trên.)*

## 🚀 Cài đặt

### Yêu cầu
- XAMPP (PHP 8.x trở lên)  
- Visual Studio Code với extensions: PHP Intelephense, MySQL, Prettier  
- MySQL Workbench (tùy chọn)  

### Cách cài
1. **Tải project:**  
   ```bash
   cd C:\xampp\htdocs  # Hoặc thư mục tương ứng trên Linux/macOS
   git clone https://github.com/tyanzuq2811/BTL_Quan_ly_doan_vien.git
