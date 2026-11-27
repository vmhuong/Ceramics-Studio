# Ceramic Studio

Website thương mại điện tử bán đồ gốm thủ công & đặt lịch trải nghiệm Workshop làm gốm (**WordPress + WooCommerce + Docker**).

## Kiến trúc hệ thống

Hệ thống được đóng gói bằng **Docker & Docker Compose** với các dịch vụ tách biệt:

* **Nginx** (`.docker/nginx`): Reverse proxy, phục vụ static assets và chuyển tiếp request PHP qua FastCGI.
* **PHP 8.1-FPM** (`.docker/php`): Tích hợp `OPcache`, `gd`, `intl`, `pdo_mysql`, `zip`, `redis`.
* **MariaDB**: Lưu trữ dữ liệu WordPress/WooCommerce.
* **Redis**: Object Cache tăng tốc truy vấn database.
* **phpMyAdmin** *(chỉ bật ở môi trường Dev)*: Quản trị database trực quan; tự động tắt ở cấu hình Production (`docker-compose.prod.yml`).

## Tính năng nổi bật

Code tùy biến nằm tại `wp-content/themes/hello-elementor-child/`:

* **Cửa hàng gốm & Giỏ hàng nhanh**: Giao diện tối ưu responsive, shopping panels, cache-busting CSS/JS bằng `filemtime()`.
* **Đặt lịch Workshop & VietQR**: Quy trình đặt vé workshop theo ca/lịch và thanh toán chuyển khoản VietQR.
* **Tự động giải phóng chỗ (Auto-release)**: Tự động nhả slot workshop nếu đơn `pending` quá 10 phút chưa thanh toán.
* **Email giao dịch chuyên biệt (SMTP)**: Tách riêng mẫu email xác nhận chuyên nghiệp cho **Đơn hàng sản phẩm** và **Vé lịch Workshop**.
* **CI/CD với GitHub Actions**:
  * [`ci.yml`](.github/workflows/ci.yml): Tự động kiểm tra cú pháp PHP (`php -l`), script backup (`bash -n`), validate `docker-compose` (Dev & Prod) và build thử Docker images.
  * [`deploy.yml`](.github/workflows/deploy.yml): Tự động backup database (`scripts/backup-db.sh`) và triển khai lên VPS Production qua SSH khi `ENABLE_AUTO_DEPLOY=true` hoặc chạy thủ công.

## Cấu trúc thư mục chính

```text
.
├── .docker/                         # Dockerfile & cấu hình Nginx, PHP-FPM, MariaDB, Redis
├── .github/workflows/               # GitHub Actions CI & CD workflows
├── scripts/                         # Script hỗ trợ Docker & backup database (backup-db.sh)
├── wp-content/
│   ├── themes/hello-elementor-child/ # Child theme chứa toàn bộ giao diện & logic tùy biến
│   └── plugins/                     # WooCommerce, Redis Cache, FluentSMTP, ...
├── docker-compose.yml               # Cấu hình Docker môi trường phát triển (Dev)
├── docker-compose.prod.yml          # Ghi đè cấu hình an toàn cho Production
├── .env.example                     # Mẫu biến môi trường (không chứa thông tin nhạy cảm)
├── Makefile                         # Các lệnh tắt quản lý Docker
├── HUONG_DAN_DOCKER_A_Z.md          # Tài liệu vận hành Docker chi tiết
└── HUONG_DAN_DEPLOY_VPS.md          # Hướng dẫn triển khai lên VPS Production
```

## Hướng dẫn chạy nhanh (Local Development)

1. Tạo file biến môi trường từ mẫu và cập nhật mật khẩu phù hợp:
   ```bash
   cp .env.example .env
   ```
2. Khởi động toàn bộ container:
   ```bash
   docker compose up -d --build
   # Hoặc nếu đã cài make:
   make up
   ```
3. Kiểm tra trạng thái container:
   ```bash
   docker compose ps
   ```

Chi tiết về cấu hình Production, SSL/HTTPS và backup tự động, xem thêm tại [HUONG_DAN_DEPLOY_VPS.md](HUONG_DAN_DEPLOY_VPS.md).
