# 🔒 Hệ Thống Khóa Tài Khoản

## 📋 Tổng Quan

Hệ thống khóa tài khoản đã được hoàn thiện với các tính năng:
- ✅ Khóa/Mở khóa tài khoản
- ✅ Ngăn đăng nhập khi bị khóa
- ✅ Tự động logout khi bị khóa
- ✅ Phân quyền thao tác theo role
- ✅ Bảo vệ tài khoản quan trọng

---

## 🎯 Chức Năng

### 1. **Khóa Tài Khoản**
- Admin có thể khóa bất kỳ tài khoản nào (trừ chính mình)
- Không thể khóa admin cuối cùng
- Khi khóa:
  - User không thể đăng nhập
  - Nếu đang online sẽ bị logout ngay lập tức
  - Hiển thị thông báo "Tài khoản đã bị khóa"

### 2. **Mở Khóa Tài Khoản**
- Admin có thể mở khóa tài khoản bất kỳ
- User có thể đăng nhập lại ngay sau khi mở khóa

### 3. **Phân Quyền Thao Tác**

#### **Khách Hàng (Customer):**
- ✅ **Xem:** Xem chi tiết tài khoản
- ✅ **Khóa/Mở:** Khóa hoặc mở khóa tài khoản
- ❌ **Sửa:** Không thể chỉnh sửa thông tin
- ❌ **Xóa:** Không thể xóa tài khoản

#### **Admin/Staff:**
- ✅ **Xem:** Xem chi tiết tài khoản
- ✅ **Sửa:** Chỉnh sửa thông tin
- ✅ **Khóa/Mở:** Khóa hoặc mở khóa tài khoản
- ✅ **Xóa:** Xóa tài khoản (chỉ Admin/Staff)

---

## 🔐 Bảo Mật

### 1. **Middleware CheckUserActive**
```php
// Tự động kiểm tra mỗi request
// Nếu user bị khóa -> logout ngay lập tức
```

### 2. **Login Request Validation**
```php
// Kiểm tra ngay khi đăng nhập
// Ngăn user bị khóa đăng nhập thành công
```

### 3. **Bảo Vệ Đặc Biệt**
- ❌ Không thể tự khóa chính mình
- ❌ Không thể khóa admin cuối cùng
- ❌ Khách hàng không thể bị xóa (chỉ khóa)

---

## 💻 Cách Sử Dụng

### **Từ Danh Sách Users**

1. Truy cập: `http://localhost:8000/admin/users`

2. Tìm user cần khóa/mở

3. Click nút **"Khóa"** hoặc **"Mở"**

4. Xác nhận thao tác

### **Từ Chi Tiết User**

1. Truy cập: `http://localhost:8000/admin/users/{id}`

2. Click nút **"Khóa tài khoản"** hoặc **"Mở khóa"** ở góc trên

3. Xác nhận thao tác

---

## 🎨 Giao Diện

### **Trạng Thái Badge**

**Hoạt động:**
```
🟢 Đang hoạt động (màu xanh)
```

**Bị Khóa:**
```
🔴 Bị khóa (màu đỏ)
```

### **Nút Action**

**Tài khoản hoạt động:**
```
🟡 [Khóa] - Màu vàng warning
```

**Tài khoản bị khóa:**
```
🟢 [Mở] - Màu xanh success
```

---

## 🔧 Cấu Trúc Code

### **Database**
```sql
ALTER TABLE users ADD COLUMN is_active BOOLEAN DEFAULT TRUE;
```

### **Model Methods**
```php
// User.php
$user->isActive()      // Kiểm tra active
$user->activate()      // Mở khóa
$user->deactivate()    // Khóa
```

### **Controller**
```php
// UserController.php
toggleStatus(User $user)  // Toggle active/inactive
```

### **Middleware**
```php
// CheckUserActive.php
// Kiểm tra is_active mỗi request
```

### **Routes**
```php
POST /admin/users/{user}/toggle-status
```

---

## 📊 Flow Diagram

### **Khi Khóa Tài Khoản:**

```
Admin click [Khóa]
    ↓
Confirm dialog
    ↓
POST /admin/users/{id}/toggle-status
    ↓
$user->deactivate() → is_active = false
    ↓
Nếu user đang online:
    ↓
CheckUserActive middleware
    ↓
Auto logout
    ↓
Redirect to login với message
```

### **Khi Đăng Nhập:**

```
User submit login form
    ↓
Validate credentials
    ↓
Check is_active
    ↓
Nếu is_active = false:
    ↓
Logout ngay lập tức
    ↓
Show error: "Tài khoản đã bị khóa"
```

---

## 🚫 Các Trường Hợp Bị Chặn

### 1. **Tự khóa chính mình**
```
❌ Không thể thay đổi trạng thái tài khoản của chính mình!
```

### 2. **Khóa admin cuối cùng**
```
❌ Không thể khóa quản trị viên cuối cùng!
```

### 3. **Đăng nhập khi bị khóa**
```
❌ Tài khoản của bạn đã bị khóa. 
   Vui lòng liên hệ quản trị viên.
```

### 4. **Xóa khách hàng**
```
❌ Không thể xóa tài khoản khách hàng. 
   Vui lòng sử dụng chức năng khóa tài khoản.
```

---

## 🧪 Testing Scenarios

### **Test 1: Khóa tài khoản khách hàng**
1. Login với admin
2. Vào `/admin/users`
3. Tìm customer account
4. Click "Khóa"
5. ✅ Tài khoản bị khóa
6. ✅ Badge đổi thành "Bị khóa"
7. ✅ Nút đổi thành "Mở"

### **Test 2: User bị khóa cố đăng nhập**
1. Khóa một customer account
2. Logout
3. Cố đăng nhập với account đó
4. ✅ Hiện thông báo "Tài khoản đã bị khóa"
5. ✅ Không thể đăng nhập

### **Test 3: User đang online bị khóa**
1. Customer đang đăng nhập
2. Admin khóa account đó
3. Customer refresh trang hoặc navigate
4. ✅ Tự động logout
5. ✅ Redirect về login với message

### **Test 4: Mở khóa tài khoản**
1. Login với admin
2. Tìm tài khoản bị khóa
3. Click "Mở"
4. ✅ Tài khoản được mở khóa
5. ✅ User có thể đăng nhập lại

### **Test 5: Cố tự khóa mình**
1. Login với admin
2. Vào `/admin/users`
3. Tìm chính tài khoản mình
4. ❌ Không có nút "Khóa"
5. ✅ System protected

---

## 📱 UI Examples

### **Danh Sách Users**
```
┌─────────────────────────────────────────────────────┐
│ Tài khoản         │ Vai trò │ Trạng thái │ Actions │
├─────────────────────────────────────────────────────┤
│ Nguyễn Văn A      │ Admin   │ 🟢 Hoạt động │ Xem    │
│ admin@gmail.com   │         │            │        │
├─────────────────────────────────────────────────────┤
│ Trần Thị B        │ Customer│ 🔴 Bị khóa  │ Xem Mở │
│ user@gmail.com    │         │            │        │
└─────────────────────────────────────────────────────┘
```

### **Chi Tiết User (Bị Khóa)**
```
┌────────────────────────────────────────┐
│          Chi Tiết Tài Khoản           │
│  [🟢 Mở khóa] [⬅ Quay lại]            │
├────────────────────────────────────────┤
│        👤 Trần Thị B                  │
│        user@gmail.com                 │
│                                        │
│        🔵 Khách hàng                  │
│        🔴 Bị khóa                     │
└────────────────────────────────────────┘
```

---

## ✅ Checklist

- [x] Migration add is_active column
- [x] User model methods (activate/deactivate)
- [x] CheckUserActive middleware
- [x] Update LoginRequest
- [x] Register middleware in bootstrap
- [x] Controller toggleStatus method
- [x] Update views (index, show)
- [x] Different actions for customer vs admin/staff
- [x] Self-protection (can't lock yourself)
- [x] Last admin protection
- [x] Prevent deleting customers (lock instead)
- [x] Auto logout on lock
- [x] Prevent login when locked

---

## 🔄 Workflow Summary

1. **Admin khóa user** → `is_active = false`
2. **User đang online** → Middleware detect → Auto logout
3. **User cố login** → LoginRequest check → Reject với message
4. **Admin mở khóa** → `is_active = true`
5. **User login lại** → Success ✅

---

**Hệ thống khóa tài khoản đã hoàn thiện! 🎉**
