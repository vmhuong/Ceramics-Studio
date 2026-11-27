# 🚀 Hướng Dẫn Triển Khai BookVerse (Ceramic Studio) Lên VPS Thật

Tài liệu này giúp bạn đưa dự án từ máy cá nhân lên **VPS Ubuntu/Debian thật** một cách an toàn, giữ nguyên cấu hình dev ở máy cá nhân không bị ảnh hưởng.

---

## 📋 Checklist Trước Khi Chạy Production

- [ ] Thuê VPS (khuyên dùng tối thiểu **2 vCPU / 2GB RAM / 30GB SSD**, Ubuntu 22.04/24.04).
- [ ] Có tên miền (ví dụ: `ceramicstudio.vn`) trỏ bản ghi **A** về IP của VPS.
- [ ] Đổi toàn bộ mật khẩu mặc định trong file `.env` trên VPS (`DB_PASSWORD`, `DB_ROOT_PASSWORD`, `REDIS_PASSWORD`).
- [ ] Bật tường lửa (UFW): chỉ mở cổng `22` (SSH), `80` (HTTP), `443` (HTTPS).
- [ ] Dùng file `docker-compose.prod.yml` để **tự động đóng cổng `3307`, `6380` và tắt `phpMyAdmin` (`8081`)**.

---

## 🛠️ Các Bước Triển Khai Trên VPS

### Bước 1: Cài Docker & Firewall trên VPS
```bash
# Cài Docker
curl -fsSL https://get.docker.com | sh

# Bật tường lửa chỉ cho phép SSH, HTTP, HTTPS
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

### Bước 2: Đưa code & file `.env` lên VPS
```bash
git clone <url-repo-cua-ban> bookverse
cd bookverse
cp .env.example .env
nano .env   # Điền mật khẩu mạnh cho DB_PASSWORD, DB_ROOT_PASSWORD, REDIS_PASSWORD
```
*(Nhớ tạo `wp-config.php` với đúng thông số DB & `WP_REDIS_PASSWORD` khớp với `.env`)*.

### Bước 3: Cài chứng chỉ SSL miễn phí (Let's Encrypt) + Bật HTTPS & HTTP/2
```bash
# Cài Certbot
sudo apt update && sudo apt install -y certbot

# Lấy chứng chỉ SSL (tạm dừng nginx nếu đang chiếm port 80)
sudo certbot certonly --standalone -d your-domain.com -d www.your-domain.com

# Tạo file cấu hình Nginx SSL từ mẫu có sẵn
cp .docker/nginx/conf.d/production-ssl.conf.example .docker/nginx/conf.d/production-ssl.conf
sed -i 's/your-domain.com/ten-mien-that-cua-ban.com/g' .docker/nginx/conf.d/production-ssl.conf
```

### Bước 4: Khởi động hệ thống ở chế độ Production
```bash
docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d --build
```
Khi chạy lệnh này:
- ✅ Chỉ mở cổng `80` và `443` (HTTPS + HTTP/2).
- ✅ Cổng DB (`3307`) và Redis (`6380`) bị **đóng hoàn toàn** khỏi internet.
- ✅ `phpMyAdmin` (`8081`) bị **tắt hoàn toàn**.

### Bước 5: Nạp dữ liệu & Đổi đường dẫn sang HTTPS
```bash
# Nạp bản backup.sql lên DB trên VPS
docker exec -i bookverse_db mariadb -u bookverse_user -p<DB_PASSWORD> bookverse < backup.sql

# Đổi toàn bộ URL từ localhost:8080 sang https://ten-mien-that.com
docker exec bookverse_php wp search-replace 'http://localhost:8080' 'https://ten-mien-that.com' --all-tables --allow-root
```
Sau đó vào **SePay / Casso**, cập nhật URL Webhook thành `https://ten-mien-that.com/wp-json/sepay/v1/webhook` với API Token trong cài đặt VietQR.

### Bước 6: Cài Backup Database Tự Động Hằng Ngày (02:00 sáng)
```bash
chmod +x scripts/backup-db.sh
crontab -e
```
Thêm dòng này vào cuối file cron:
```text
0 2 * * * /root/bookverse/scripts/backup-db.sh >> /var/log/bookverse-backup.log 2>&1
```
*(Script sẽ tự nén `.sql.gz` mỗi ngày và tự xóa bản cũ quá 14 ngày).*

---

## 🔙 Cách Khôi Phục Nhanh Nếu Gặp Sự Cố
```bash
gunzip < backups/bookverse_YYYY-MM-DD_HHMM.sql.gz | docker exec -i bookverse_db mariadb -u bookverse_user -p<DB_PASSWORD> bookverse
docker restart bookverse_redis
```
