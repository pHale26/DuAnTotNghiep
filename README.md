# Hướng dẫn chạy dự án Travel Wonder

Dự án **Travel Wonder** được xây dựng trên nền tảng Laravel 12, sử dụng Vite để biên dịch tài nguyên frontend (CSS/JS) và Laravel Reverb làm WebSocket server để xử lý các tính năng thời gian thực (Real-time Live Chat).

Dưới đây là các bước chi tiết để thiết lập và chạy dự án trên môi trường Local của bạn.

---

## Yêu cầu hệ thống
- PHP >= 8.2
- Composer
- Node.js & npm (phiên bản mới nhất)
- XAMPP/MySQL Server

## Cài đặt ban đầu

**1. Clone dự án và cài đặt thư viện PHP**
Mở terminal tại thư mục gốc của dự án và chạy:
```bash
composer install
```

**2. Cài đặt các thư viện Frontend**
```bash
npm install
```

**3. Cấu hình biến môi trường (.env)**
Copy file `.env.example` thành `.env` (nếu chưa có):
```bash
copy .env.example .env
```
Kiểm tra và cấu hình các thông số kết nối Database trong `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=travel  # Đổi tên database phù hợp
DB_USERNAME=root
DB_PASSWORD=
```
Đảm bảo đã cấu hình `BROADCAST_CONNECTION=reverb` trong file `.env`.

**4. Khởi tạo Application Key**
```bash
php artisan key:generate
```

**5. Chạy Migration và Seeder**
Để tạo cấu trúc bảng trong CSDL và thêm dữ liệu mẫu (các Role, Users, v.v.):
```bash
php artisan migrate:fresh --seed
```

---

## Các lệnh cần thiết để chạy dự án (Mở nhiều Terminal)

Để ứng dụng hoạt động đầy đủ tính năng (đặc biệt là tính năng Live Chat và giao diện), bạn cần mở song song **3 cửa sổ Terminal** và chạy các lệnh sau:

### Terminal 1: Chạy Web Server (Laravel)
```bash
php artisan serve
```
*Truy cập trang web tại: `http://127.0.0.1:8000`*

### Terminal 2: Chạy Frontend Vite (Biên dịch CSS/JS)
Vì dự án dùng Vite, bạn cần chạy Vite Server trong quá trình phát triển để code JS/CSS được tự động cập nhật:
```bash
npm run dev
```
*(Nếu muốn build code để chạy Productive / Không cần dev server thì dùng lệnh `npm run build` thay thế).*

### Terminal 3: Chạy WebSocket Server (Laravel Reverb)
Tính năng Live Chat thời gian thực (Chatbox User và Chat Admin) yêu cầu Laravel Reverb phải luôn hoạt động.
```bash
php artisan reverb:start
```
*Reverb sẽ chạy ở cổng 8080 (hoặc cấu hình trong .env) để phát tín hiệu realtime giữa server và client.*

---

## Tài khoản quản trị mẫu

Sau khi chạy lệnh `php artisan migrate --seed`, hệ thống có các tài khoản mẫu sau:

- **Super Admin**: Sử dụng tài khoản có sẵn trong database hoặc seeder của bạn.
- **Nhân viên CSKH**: `cskh@travel.com` / `password`

Đường dẫn trang quản trị: `http://127.0.0.1:8000/admin/dashboard`
     
## Cấu trúc tính năng Live Chat

- **Người dùng (Guest/User)**: Hiển thị Chatbox thu gọn ở góc dưới bên phải màn hình ngoài trang chủ. Cho phép gửi tin nhắn, đính kèm file (ví dụ: file mẫu danh sách hành khách `.csv`).
- **CSKH/Admin**: Truy cập vào menu `Live Chat` ở bảng điều khiển Admin để phản hồi tin nhắn khách hàng. Các tin nhắn sẽ được đồng bộ ngay lập tức (không cần tải lại trang) nhờ cơ chế `ShouldBroadcastNow` và Laravel Reverb.
