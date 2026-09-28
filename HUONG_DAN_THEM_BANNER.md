# 🎨 Hướng dẫn thêm Banner vào Website

## 📋 Cách 1: Qua giao diện Admin (Đơn giản nhất)

### Bước 1: Đăng nhập Admin
1. Truy cập: `http://localhost/travel/login`
2. Đăng nhập với tài khoản Admin/Staff

### Bước 2: Vào trang quản lý Banner
1. Nhấn vào menu **"Banner quảng cáo"** ở sidebar bên trái
2. Hoặc truy cập trực tiếp: `http://localhost/travel/admin/banners`

### Bước 3: Thêm Banner mới
1. Nhấn nút **"+ Thêm Banner"** ở góc trên bên phải
2. Điền thông tin:

#### A. Tiêu đề Banner *
- Nhập tên để quản lý (ví dụ: "Banner giảm giá mùa hè")
- Chỉ admin mới thấy, không hiển thị ra ngoài

#### B. Hình ảnh *
**Chọn 1 trong 2 cách:**

##### Cách A: Tải ảnh lên từ máy tính
- Chọn radio button **"Tải ảnh lên"**
- Nhấn nút "Choose File" và chọn ảnh
- Định dạng: JPG, PNG, WEBP
- Kích thước tối đa: 5MB
- Kích thước đề xuất: 1920x800 px (cho Hero) hoặc 800x400 px (cho Ads)

##### Cách B: Sử dụng URL ảnh từ internet
- Chọn radio button **"Sử dụng URL"**
- Dán link ảnh (ví dụ: https://example.com/image.jpg)
- Ưu điểm: Không tốn dung lượng server
- Nhược điểm: Phụ thuộc vào nguồn bên ngoài

#### C. Đường dẫn đích (Tùy chọn)
- Link đến khi người dùng click vào banner
- Ví dụ: `/tours`, `/destinations`, hoặc link bên ngoài

#### D. Mã giảm giá (Tùy chọn)
- Chọn mã giảm giá từ dropdown
- Mã sẽ hiển thị đẹp mắt trên banner với badge đỏ
- Chỉ hiển thị các mã còn hạn sử dụng

#### E. Vị trí hiển thị *
**Banner bìa (Hero):**
- Hiển thị ở đầu trang chủ, full width
- Kích thước lớn, nổi bật
- Dùng cho banner chính, quảng cáo lớn

**Quảng cáo ngang (Ads):**
- Hiển thị ở giữa trang, có thể lướt ngang
- Nhiều banner cùng lúc
- Dùng cho khuyến mãi, ưu đãi nhỏ

#### F. Thứ tự hiển thị
- Nhập số (ví dụ: 1, 2, 3...)
- Số nhỏ hơn sẽ hiển thị trước
- Mặc định: 0

#### G. Hiển thị ngoài trang chủ
- Bật: Banner sẽ hiển thị
- Tắt: Banner sẽ ẩn (dùng để chuẩn bị trước)

### Bước 4: Lưu Banner
- Nhấn **"Lưu Banner"**
- Kiểm tra trên trang chủ

---

## 💡 Mẹo để tạo Banner đẹp

### Kích thước ảnh đề xuất:
- **Banner Hero**: 1920x800 px
- **Banner Ads**: 800x400 px hoặc 1200x600 px
- **Tỷ lệ**: 2:1 hoặc 3:1 để tránh bị cắt xén

### Nội dung ảnh:
- Văn bản rõ ràng, dễ đọc
- Màu sắc nổi bật, thu hút
- Thông điệp ngắn gọn (5-7 từ)
- Có Call-to-action (ví dụ: "Đặt ngay", "Khám phá")

### Tối ưu hóa:
- Nén ảnh trước khi upload (dùng TinyPNG.com)
- Format WEBP cho tải nhanh hơn
- Tránh ảnh quá nặng làm chậm website

### Ví dụ banner tốt:
✅ Ảnh chất lượng cao, sắc nét
✅ Có mã giảm giá nổi bật
✅ Link đến trang tour/điểm đến phù hợp
✅ Thứ tự sắp xếp hợp lý

---

## 🔧 Cách 2: Thêm trực tiếp vào Database (Nâng cao)

### Sử dụng phpMyAdmin hoặc MySQL:

```sql
INSERT INTO banners (
    title, 
    image_url, 
    target_url, 
    coupon_id,
    position, 
    sort_order, 
    is_active,
    created_at,
    updated_at
) VALUES (
    'Banner mùa hè 2026',                    -- Tiêu đề
    'uploads/banners/summer_2026.jpg',        -- Đường dẫn ảnh
    '/tours',                                 -- Link đích
    1,                                        -- ID mã giảm giá (hoặc NULL)
    'home_ads',                               -- Vị trí (hero hoặc home_ads)
    1,                                        -- Thứ tự
    1,                                        -- Kích hoạt (1=có, 0=không)
    NOW(),                                    -- Ngày tạo
    NOW()                                     -- Ngày cập nhật
);
```

### Lưu ý:
- `image_url` có thể là đường dẫn local hoặc URL đầy đủ
- `coupon_id` phải tồn tại trong bảng `coupons` hoặc để NULL
- `position` chỉ có 2 giá trị: `hero` hoặc `home_ads`

---

## 📂 Cách 3: Upload ảnh thủ công

### Bước 1: Chuẩn bị ảnh
1. Đặt tên file dễ nhớ (ví dụ: `banner-summer-2026.jpg`)
2. Tối ưu kích thước file (< 2MB)

### Bước 2: Upload vào server
1. Mở FileZilla hoặc truy cập thư mục project
2. Copy ảnh vào: `public/uploads/banners/`
3. Đảm bảo thư mục có quyền ghi (chmod 755)

### Bước 3: Thêm banner qua Admin
1. Vào trang "Thêm Banner"
2. Chọn "Sử dụng URL"
3. Nhập: `uploads/banners/banner-summer-2026.jpg`
4. Lưu lại

---

## ❓ Câu hỏi thường gặp

### Q1: Tôi không thấy banner sau khi thêm?
**A:** Kiểm tra:
- ✅ Banner có được kích hoạt không? (is_active = 1)
- ✅ Vị trí banner đúng chưa? (home_ads để hiển thị ở giữa trang)
- ✅ Đường dẫn ảnh có đúng không?
- ✅ Clear cache: `php artisan cache:clear`

### Q2: Ảnh bị vỡ hoặc không hiển thị?
**A:** Kiểm tra:
- ✅ Đường dẫn ảnh có đúng không?
- ✅ File ảnh có tồn tại trong thư mục `public/uploads/banners/`?
- ✅ Quyền truy cập thư mục (755 cho folder, 644 cho file)
- ✅ Nếu dùng URL bên ngoài, kiểm tra link có hoạt động không

### Q3: Làm sao để thay đổi thứ tự banner?
**A:** 
- Vào trang "Danh sách Banner"
- Nhấn "Sửa" banner muốn thay đổi
- Đổi số trong "Thứ tự hiển thị"
- Số nhỏ hơn → hiển thị trước

### Q4: Banner có thể hiển thị ở nhiều trang không?
**A:**
- Hiện tại banner chỉ hiển thị ở trang chủ và trang tour
- Có thể mở rộng sang các trang khác nếu cần

### Q5: Tôi có thể thêm bao nhiêu banner?
**A:**
- Không giới hạn số lượng
- Banner sẽ tự động hỗ trợ cuộn ngang
- Khuyến nghị: 3-6 banner để tối ưu trải nghiệm

---

## 🎯 Ví dụ thực tế

### Tạo banner khuyến mãi mùa hè:
1. **Tiêu đề**: "Banner Giảm 30% Mùa Hè"
2. **Ảnh**: Upload ảnh biển xanh, nắng vàng
3. **Link đích**: `/tours?category=summer`
4. **Mã giảm giá**: Chọn "SUMMER30"
5. **Vị trí**: Quảng cáo ngang (Ads)
6. **Thứ tự**: 1
7. **Kích hoạt**: ✅ Có

### Tạo banner điểm đến mới:
1. **Tiêu đề**: "Banner Phú Quốc 2026"
2. **Ảnh**: URL từ Unsplash về Phú Quốc
3. **Link đích**: `/destinations/phu-quoc`
4. **Mã giảm giá**: Không chọn
5. **Vị trí**: Quảng cáo ngang (Ads)
6. **Thứ tự**: 2
7. **Kích hoạt**: ✅ Có

---

## 📞 Cần hỗ trợ?

Nếu gặp vấn đề, hãy kiểm tra:
1. Log lỗi: `storage/logs/laravel.log`
2. Console browser: F12 → Console
3. Quyền thư mục: `chmod -R 755 public/uploads/banners/`
4. Clear cache: `php artisan cache:clear` và `php artisan view:clear`

---

**Chúc bạn tạo banner thành công! 🎉**
