# 📘 CẨM NANG HƯỚNG DẪN DOCKER, NGINX, REDIS & CI/CD TỪ A - Z
> **Dành riêng cho dự án:** Xưởng Gốm Mộc Bookverse  
> **Tác giả:** Technical Lead & Software Architect  
> **Mục tiêu:** Hướng dẫn người mới bắt đầu làm quen, thực hành trên máy tính cá nhân (Local) và sẵn sàng đưa lên máy chủ thật (Production).

---

## 🧭 TỔNG QUAN HỆ THỐNG ĐÃ XÂY DỰNG

Hệ thống của bạn được cấu trúc thành **4 container độc lập** cùng làm việc qua mạng nội bộ `bookverse_net`:

| Tên Dịch Vụ | Công Nghệ | Vai Trò & Lợi Ích |
| :--- | :--- | :--- |
| **`nginx`** | Nginx Alpine | Cổng tiếp nhận lưu lượng truy cập tốc độ cao, nén Gzip, phục vụ ảnh/CSS/JS tức thì và bảo mật chặn file độc hại. |
| **`wordpress`** | PHP 8.1 FPM | Bộ xử lý logic đơn hàng, giỏ hàng WooCommerce, tích hợp sẵn tiện ích mở rộng **Redis** và xử lý ảnh WebP. |
| **`db`** | MariaDB 10.11 LTS | Hệ quản trị cơ sở dữ liệu tối ưu nhất cho WooCommerce, nhẹ, mượt và chống phân mảnh dữ liệu. |
| **`redis`** | Redis 7 Alpine | Bộ nhớ đệm tốc hành trên RAM (256MB), lưu trữ truy vấn để website phản hồi chỉ trong **< 0.1 giây**. |

---

## 🚀 PHẦN 1: CÀI ĐẶT DOCKER DESKTOP TRÊN WINDOWS (10 / 11)

Để chạy được Docker trên máy tính của bạn, bạn chỉ cần làm theo 3 bước sau:

### Bước 1.1: Tải bộ cài đặt Docker Desktop
1. Truy cập trang chủ chính thức: [https://www.docker.com/products/docker-desktop/](https://www.docker.com/products/docker-desktop/)
2. Bấm nút **"Download for Windows"**.
3. Chạy file vừa tải về (`Docker Desktop Installer.exe`).
4. Trong quá trình cài, hãy **tích chọn mục "Use WSL 2 instead of Hyper-V (recommended)"**.
5. Bấm **OK** và chờ máy tính cài đặt xong. Khi hoàn tất, máy tính có thể yêu cầu **Restart (Khởi động lại)**.

### Bước 1.2: Bật tính năng WSL 2 (nếu máy tính yêu cầu)
* Nếu khi mở Docker Desktop lên máy báo lỗi WSL 2, bạn chỉ cần mở **PowerShell (Run as Administrator)** và gõ lệnh:
  ```powershell
  wsl --update
  ```
* Sau đó mở lại **Docker Desktop**. Khi nhìn thấy biểu tượng chú cá voi màu xanh lá ở góc dưới bên trái của Docker Desktop chuyển sang trạng thái **Engine running** là thành công!

---

## 💻 PHẦN 2: THỰC HÀNH KHỞI CHẠY TRÊN MÁY TÍNH CỦA BẠN (LOCAL)

### Bước 2.1: Chuẩn bị tệp cấu hình môi trường (.env)
Trong thư mục dự án, bạn tạo một bản sao từ file `.env.example` và đặt tên là `.env`:
* Cách 1: Copy file `.env.example` rồi đổi tên thành `.env`.
* Cách 2: Mở PowerShell tại thư mục dự án và gõ lệnh:
  ```powershell
  Copy-Item .env.example .env
  ```

### Bước 2.2: Tắt USBWebServer (tránh xung đột)
* Trước khi khởi chạy Docker, bạn hãy bấm **Stop** hoặc tắt bảng điều khiển USBWebServer để giải phóng cổng mạng.

### Bước 2.3: Khởi động toàn bộ hệ thống bằng Docker
Mở PowerShell tại thư mục `Bookverse` và gõ lệnh:
```powershell
docker compose up -d --build
```
* **Giải thích lệnh:**
  * `up`: Khởi chạy các container.
  * `-d` (detached mode): Cho phép hệ thống chạy ngầm dưới nền, không chiếm màn hình dòng lệnh.
  * `--build`: Tự động biên dịch `Dockerfile` (cài PHP 8.1 kèm Redis extension).
* Lần đầu chạy sẽ mất khoảng 2 - 3 phút để tải các "thùng hàng" về máy. Khi hoàn tất, bạn sẽ thấy 4 dòng màu xanh lá:
  * `[+] Running 4/4`: `nginx`, `wordpress`, `db`, `redis` đều hiển thị **Started**.

### Bước 2.4: Nhập dữ liệu cũ từ USBWebServer vào MariaDB Docker
1. Xuất file database hiện tại từ USBWebServer ra file `backup.sql` (bạn có thể xuất từ phpMyAdmin).
2. Chạy lệnh sau để nhập dữ liệu vào database mới trong Docker:
   ```powershell
   docker exec -i bookverse_db mariadb -u bookverse_user -pBookversePass2026@ bookverse < backup.sql
   ```
3. Mở trình duyệt và truy cập: **`http://localhost:8080`**. Website Xưởng gốm sẽ hiển thị mượt mà trên nền Nginx!

---

## ⚡ PHẦN 3: KÍCH HOẠT BỘ NHỚ ĐỆM SIÊU TỐC REDIS TRÊN WORDPRESS

Sau khi website đã chạy trên Docker, hãy kích hoạt Redis để cảm nhận tốc độ siêu nhanh:

1. **Thêm cấu hình vào file `wp-config.php`:**
   Mở `wp-config.php`, tìm đến trước dòng `/* That's all, stop editing! Happy publishing. */` và thêm vào:
   ```php
   // CẤU HÌNH REDIS OBJECT CACHE
   define('WP_REDIS_HOST', 'redis');
   define('WP_REDIS_PORT', 6379);
   define('WP_REDIS_TIMEOUT', 1);
   define('WP_REDIS_READ_TIMEOUT', 1);
   define('WP_CACHE_KEY_SALT', 'bookverse_');
   ```
2. **Kích hoạt plugin Redis:**
   * Vào **Quản trị WordPress (`/wp-admin`)** -> **Gói mở rộng (Plugins)** -> **Cài mới**.
   * Tìm kiếm: **Redis Object Cache** (tác giả Till Krüss).
   * Bấm **Cài đặt** và **Kích hoạt**.
   * Vào **Cài đặt (Settings)** -> **Redis** -> Bấm nút **"Enable Object Cache"**.
   * Màn hình sẽ hiển thị chữ **Connected** màu xanh lá! Từ lúc này, mọi truy vấn sản phẩm và giỏ hàng đều được phục vụ trực tiếp từ RAM!

---

## 🐙 PHẦN 4: ĐẨY CODE LÊN GITHUB & KẾT NỐI TỰ ĐỘNG HÓA CI/CD

### Bước 4.1: Tạo kho lưu trữ trên GitHub
1. Đăng nhập vào [github.com](https://github.com).
2. Bấm vào dấu `+` ở góc trên bên phải -> Chọn **New repository**.
3. Đặt tên kho lưu trữ: `bookverse-ceramics`.
4. Chọn chế độ: **Private** (Riêng tư - để bảo mật mã nguồn).
5. Bấm **Create repository**.

### Bước 4.2: Đẩy mã nguồn từ máy lên GitHub
Tại cửa sổ dòng lệnh PowerShell trong thư mục dự án, bạn gõ 2 lệnh sau (thay `<tai-khoan>` bằng username GitHub của bạn):
```powershell
git remote add origin https://github.com/<tai-khoan>/bookverse-ceramics.git
git branch -M master
git push -u origin master
```

### Bước 4.3: Tự động hóa CI/CD hoạt động ra sao?
* Mỗi khi bạn gõ `git push`:
  1. **GitHub Actions** sẽ tự động kích hoạt luồng **`Build, Test & Deploy CI/CD Pipeline`**.
  2. Nó tự động tạo một máy ảo Linux, quét toàn bộ file `.php` trong theme xem bạn có gõ nhầm dấu chấm phẩy `;` hay lỗi cú pháp nào không.
  3. Nếu có lỗi, nó sẽ báo đỏ và gửi email cảnh báo để bạn sửa ngay trước khi người dùng phát hiện.
  4. Khi sau này bạn mua máy chủ VPS, bạn chỉ cần điền 4 thông số bí mật vào **Settings -> Secrets and variables -> Actions** trên GitHub:
     * `SERVER_HOST`: Địa chỉ IP của VPS.
     * `SERVER_USER`: Tên tài khoản (ví dụ `root` hoặc `ubuntu`).
     * `SERVER_SSH_KEY`: Khóa bảo mật SSH để đăng nhập máy chủ.
     * `DEPLOY_PATH`: Thư mục chứa code trên máy chủ (ví dụ `/var/www/bookverse`).
  5. Khi đó, mỗi lần bạn sửa code trên máy mình và đẩy lên GitHub, website trên mạng thật sẽ **tự động cập nhật 100%** mà bạn không cần phải đụng tay vào máy chủ!

---

## 🛠️ BẢNG TRA CỨU CÁC LỆNH DOCKER THƯỜNG DÙNG

| Lệnh thực thi | Ý nghĩa & Khi nào cần dùng |
| :--- | :--- |
| `docker compose up -d` | Khởi động hệ thống chạy ngầm. |
| `docker compose down` | Dừng và tắt toàn bộ các container an toàn (không mất dữ liệu). |
| `docker compose ps` | Xem danh sách và trạng thái sức khỏe của 4 container. |
| `docker compose logs -f nginx` | Xem nhật ký truy cập trực tiếp của máy chủ Nginx. |
| `docker compose logs -f wordpress` | Xem nhật ký lỗi của PHP và WordPress. |
| `docker compose restart nginx` | Khởi động lại riêng máy chủ Nginx (sau khi chỉnh sửa cấu hình). |
| `docker exec -it bookverse_php sh` | Mở cửa sổ dòng lệnh bên trong container WordPress/PHP. |

---
*Chúc bạn có những trải nghiệm thực hành DevOps và Docker thật thú vị! Nếu gặp bất kỳ thông báo nào chưa rõ trong quá trình cài đặt, bạn hãy hỏi tôi để được giải đáp ngay lập tức.*
