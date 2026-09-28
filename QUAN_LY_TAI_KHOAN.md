# 👥 Hệ Thống Quản Lý Tài Khoản & Phân Quyền

## 📋 Tổng Quan

Hệ thống quản lý tài khoản đã được hoàn thiện với các tính năng:
- ✅ Quản lý users (CRUD đầy đủ)
- ✅ Hệ thống phân quyền 3 cấp
- ✅ Bảo mật và validation
- ✅ Thống kê và báo cáo
- ✅ Tìm kiếm và lọc

---

## 🎭 Hệ Thống Phân Quyền

### 1. **Quản Trị Viên (Admin)**
- 🔴 **Quyền hạn:** Toàn quyền quản lý hệ thống
- **Có thể:**
  - Quản lý tất cả tài khoản (tạo, sửa, xóa)
  - Phân quyền cho các tài khoản khác
  - Truy cập mọi chức năng admin
  - Xem báo cáo và thống kê đầy đủ

### 2. **Nhân Viên (Staff)**
- 🟢 **Quyền hạn:** Quản lý nghiệp vụ kinh doanh
- **Có thể:**
  - Quản lý tours và đơn hàng
  - Xem thông tin khách hàng
  - Điều hành tours đang diễn ra
  - Quản lý hướng dẫn viên
- **Không thể:**
  - Quản lý tài khoản người dùng
  - Thay đổi cấu hình hệ thống
  - Phân quyền

### 3. **Khách Hàng (Customer)**
- 🔵 **Quyền hạn:** Sử dụng dịch vụ
- **Có thể:**
  - Đặt tour và mua vé
  - Xem lịch sử đơn hàng
  - Đánh giá và review
  - Quản lý thông tin cá nhân

---

## 🔐 Tài Khoản Mẫu

### Admin
```
Email: admin@gmail.com
Password: 12345678
```

### Nhân viên
```
Email: staff@gmail.com
Password: 12345678
```

### Khách hàng
```
Email: user@gmail.com
Password: 12345678
```

---

## 🛠️ Chức Năng Quản Lý Users

### 1. **Danh Sách Users** (`/admin/users`)
- Hiển thị tất cả tài khoản
- Thống kê theo vai trò
- Tìm kiếm theo tên, email, SĐT
- Lọc theo vai trò
- Pagination

### 2. **Tạo Tài Khoản Mới** (`/admin/users/create`)
**Form fields:**
- Họ tên (required)
- Email (required, unique)
- Số điện thoại (optional)
- Vai trò (required)
- Mật khẩu (required, min 8 ký tự)
- Xác nhận mật khẩu

### 3. **Xem Chi Tiết** (`/admin/users/{id}`)
Hiển thị:
- Thông tin cá nhân
- Thống kê đơn hàng
- Lịch sử booking
- Đánh giá

### 4. **Chỉnh Sửa** (`/admin/users/{id}/edit`)
- Cập nhật thông tin
- Thay đổi vai trò
- Đổi mật khẩu (optional)

### 5. **Xóa Tài Khoản** (`/admin/users/{id}`)
**Bảo vệ:**
- ❌ Không thể xóa chính mình
- ❌ Không thể xóa admin cuối cùng

---

## 🔒 Bảo Mật

### 1. **Middleware Protection**
```php
// IsAdmin middleware
Route::middleware(['auth', 'admin'])->group(function () {
    // Admin routes
});
```

### 2. **Validation Rules**
- Email phải unique
- Password tối thiểu 8 ký tự
- Role phải là một trong: admin, staff, customer

### 3. **Self-Protection**
- Không thể tự xóa tài khoản mình
- Không thể tự chỉnh sửa quyền hạn
- Không thể xóa admin cuối cùng

---

## 📊 API Endpoints

### Resource Routes
```
GET    /admin/users              # Danh sách
GET    /admin/users/create       # Form tạo mới
POST   /admin/users              # Lưu tài khoản mới
GET    /admin/users/{id}         # Chi tiết
GET    /admin/users/{id}/edit    # Form chỉnh sửa
PUT    /admin/users/{id}         # Cập nhật
DELETE /admin/users/{id}         # Xóa
```

---

## 🎨 UI/UX Features

### Dashboard Statistics
- 📊 Tổng tài khoản
- 🔴 Số admin
- 🟢 Số nhân viên
- 🔵 Số khách hàng

### Search & Filter
- Tìm kiếm realtime
- Lọc theo vai trò
- Reset filter

### User Card
- Avatar với initials
- Role badge với màu sắc
- Thông tin liên hệ
- Action buttons

---

## 💻 Code Structure

### Models
```
app/Models/User.php
app/Enums/UserRole.php
```

### Controllers
```
app/Http/Controllers/Admin/UserController.php
```

### Middleware
```
app/Http/Middleware/IsAdmin.php
```

### Views
```
resources/views/admin/users/
├── index.blade.php    # Danh sách
├── create.blade.php   # Tạo mới
├── edit.blade.php     # Chỉnh sửa
└── show.blade.php     # Chi tiết
```

---

## 🚀 Sử Dụng

### 1. Truy cập trang quản lý
```
http://localhost:8000/admin/users
```

### 2. Đăng nhập với tài khoản admin
```
admin@gmail.com / 12345678
```

### 3. Quản lý users
- Xem danh sách
- Tạo tài khoản mới
- Phân quyền
- Cập nhật thông tin

---

## 🔧 Customize

### Thêm role mới
**File:** `app/Enums/UserRole.php`
```php
case NEW_ROLE = 'new_role';
```

### Thêm quyền hạn
**File:** `app/Models/User.php`
```php
public function canDoSomething(): bool
{
    return $this->hasRole(UserRole::ADMIN);
}
```

### Thêm validation
**File:** `app/Http/Controllers/Admin/UserController.php`
```php
$validated = $request->validate([
    // Add more rules
]);
```

---

## ✅ Checklist Hoàn Thiện

- [x] UserRole Enum
- [x] User model với role methods
- [x] UserController với CRUD
- [x] IsAdmin middleware cập nhật
- [x] Views đầy đủ (index, create, edit, show)
- [x] Routes configuration
- [x] Sidebar menu link
- [x] Seeder với 3 roles
- [x] Validation & Security
- [x] Search & Filter
- [x] Statistics dashboard
- [x] Documentation

---

## 🐛 Troubleshooting

### Lỗi 403 Forbidden
➡️ Kiểm tra role của user trong database

### Không thể xóa user
➡️ Kiểm tra xem có phải tự xóa mình không

### Layout không hiển thị
➡️ Chạy `npm run build` để build assets

---

## 📝 Notes

1. Mật khẩu mặc định cho tất cả user mẫu: `12345678`
2. Nên thay đổi mật khẩu sau lần đăng nhập đầu tiên
3. Role có thể mở rộng thêm nếu cần
4. Có thể thêm permission system chi tiết hơn

---

**Hệ thống đã sẵn sàng sử dụng! 🎉**
