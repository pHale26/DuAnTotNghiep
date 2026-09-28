# BÁO CÁO CHUYÊN SÂU: MÔ HÌNH LIVE CHAT & AI CHATBOT CSKH - TRAVEL WONDER

> **Tài liệu phục vụ thuyết trình chuyên sâu về Kiến trúc, Nghiệp vụ, Kỹ thuật, Danh mục Hàm/File phụ trách và Định hướng phát triển Hệ thống Chăm sóc Khách hàng Trực tuyến.**

---

## 📋 MỤC LỤC
1. [Tổng quan Mô hình Live Chat CSKH Kép (Hybrid CSKH Model)](#1-tổng-quan-mô-hình-live-chat-cskh-kép-hybrid-cskh-model)
2. [Bản đồ File & Hàm Phụ trách Chi tiết (File & Method Directory)](#2-bản-đồ-file--hàm-phụ-trách-chi-tiết-file--method-directory)
3. [Sơ đồ Kiến trúc & Công nghệ Sử dụng (Tech Stack)](#3-sơ-đồ-kiến-trúc--công-nghệ-sử-dụng-tech-stack)
4. [Cách thức Hoạt động & Thuật toán Thực thi (How It Works & Implementation Detail)](#4-cách-thức-hoạt-động--thuật-toán-thực-thi-how-it-works--implementation-detail)
   - [A. Thuật toán phân phối tải Least Connections](#a-thuật-toán-phân-phối-tải-least-connections)
   - [B. Cơ chế theo dõi trạng thái Online (Middleware)](#b-cơ-chế-theo-dõi-trạng-thái-online-middleware)
   - [C. Tự động điều phối lại khi CSKH offline (Artisan Command)](#c-tự-động-điều-phối-lại-khi-cskh-offline-artisan-command)
   - [D. Luồng xử lý tin nhắn Real-time & Broadcasting (WebSockets)](#d-luồng-xử-lý-tin-nhắn-real-time--broadcasting-websockets)
   - [E. Kiểm duyệt từ ngữ thô tục (Profanity Filter)](#e-kiểm-duyệt-từ-ngữ-thô-tục-profanity-filter)
   - [F. Luồng xử lý AI Chatbot & Function Calling (Gemini API)](#f-luồng-xử-lý-ai-chatbot--function-calling-gemini-api)
   - [G. Chuẩn hóa & Khôi phục Link thật cho AI Chatbot](#g-chuẩn-hóa--khôi-phục-link-thật-cho-ai-chatbot)
5. [Nghiệp vụ Chăm sóc Khách hàng & Phân quyền](#5-nghiệp-vụ-chăm-sóc-khách-hàng--phân-quyền)
6. [Các Tính năng Đặc biệt & Điểm Nổi Bật Dùng Để Thuyết Trình](#6-các-tính-năng-đặc-biệt--điểm-nổi-bật-dùng-để-thuyết-trình)
7. [Các Chức năng Cần Bổ Sung & Hoàn Thiện Trong Tương Lai (Future Roadmap)](#7-các-chức-năng-cần-bổ-sung--hoàn-thiện-trong-tương-lai-future-roadmap)

---

## 1. TỔNG QUAN MÔ HÌNH LIVE CHAT CSKH KÉP (HYBRID CSKH MODEL)

Hệ thống Chăm sóc Khách hàng (CSKH) trên nền tảng **Travel Wonder** được thiết kế theo mô hình **Hybrid CSKH Model (Kết hợp Kép giữa Trợ lý Ảo AI & Nhân viên CSKH Trực tuyến)**.

```
                         ┌─────────────────────────────────────────┐
                         │              KHÁCH HÀNG                 │
                         └──────────────────┬──────────────────────┘
                                            │
                    ┌───────────────────────┴───────────────────────┐
                    ▼                                               ▼
     ┌─────────────────────────────┐                 ┌─────────────────────────────┐
     │   TRỢ LÝ ẢO AI (CHATBOT)    │                 │   LIVE CHAT NGƯỜI THẬT     │
     │  - Gemini 3.1 Flash Lite    │                 │  - Real-time WebSockets     │
     │  - Tư vấn 24/7 tức thì      │                 │  - Phân phối Least Conns    │
     │  - Function Calling tra DB  │                 │  - Đính kèm file / Mẫu CSV │
     └─────────────────────────────┘                 └─────────────────────────────┘
```

---

## 2. BẢN ĐỒ FILE & HÀM PHỤ TRÁCH CHI TIẾT (FILE & METHOD DIRECTORY)

Dưới đây là danh mục toàn bộ các File, Lớp (Class), và Hàm (Method) phụ trách từng tính năng trong hệ thống:

| STT | Tính năng / Nghiệp vụ | Vị trí File | Lớp / Hàm phụ trách | Vai trò & Cách thực hiện |
| :---: | :--- | :--- | :--- | :--- |
| **1** | Phân phối tải chat thông minh | [`app/Services/ChatDistributionService.php`](file:///c:/xampp/htdocs/travel/travel/app/Services/ChatDistributionService.php) | `ChatDistributionService::getAvailableAgent()` & `assign()` | Thực thi thuật toán Least Connections: Lấy CSKH online trong 5 phút qua, đếm số ca chat `open`, gán cho CSKH có ít ca nhất. |
| **2** | Theo dõi trạng thái Online CSKH | [`app/Http/Middleware/UpdateLastSeenAt.php`](file:///c:/xampp/htdocs/travel/travel/app/Http/Middleware/UpdateLastSeenAt.php) | `UpdateLastSeenAt::handle()` | Tự động cập nhật `$user->last_seen_at = now()` mỗi khi người dùng thao tác hệ thống (sau mỗi 60 giây). |
| **3** | Xóa mốc Online khi Logout | [`app/Http/Controllers/Auth/AuthenticatedSessionController.php`](file:///c:/xampp/htdocs/travel/travel/app/Http/Controllers/Auth/AuthenticatedSessionController.php) | `AuthenticatedSessionController::destroy()` | Đặt `$user->last_seen_at = null` khi đăng xuất để dừng nhận ca chat mới ngay lập tức. |
| **4** | Quét & Điều phối lại ca chat | [`app/Console/Commands/ReRouteOfflineChats.php`](file:///c:/xampp/htdocs/travel/travel/app/Console/Commands/ReRouteOfflineChats.php) | `ReRouteOfflineChats::handle()` | Artisan command (`chat:re-route-offline`) tự động thu hồi ca chat của CSKH offline (> 5 phút) để gán cho CSKH khác đang online. |
| **5** | Kiểm duyệt từ ngữ thô tục | [`app/Services/ProfanityFilter.php`](file:///c:/xampp/htdocs/travel/travel/app/Services/ProfanityFilter.php) | `ProfanityFilter::filter()` | Duyệt danh sách từ cấm tiếng Việt, dùng Regex thay thế các từ vi phạm thành chuỗi dấu `***`. |
| **6** | Xử lý API Live Chat | [`app/Http/Controllers/ChatController.php`](file:///c:/xampp/htdocs/travel/travel/app/Http/Controllers/ChatController.php) | `startConversation()`, `sendMessage()`, `getConversations()`, `getMessages()`, `markImportant()`, `markAsRead()`, `getUnreadCount()` | Quản lý vòng đời ca chat, nhận file đính kèm (up to 10MB), kích hoạt phân phối ca, lưu tin nhắn, đảo cờ quan trọng và đếm tin chưa đọc. |
| **7** | Real-time Broadcasting | [`app/Events/MessageSent.php`](file:///c:/xampp/htdocs/travel/travel/app/Events/MessageSent.php) & [`routes/channels.php`](file:///c:/xampp/htdocs/travel/travel/routes/channels.php) | `MessageSent::broadcastOn()` & `Broadcast::channel()` | Đưa tin nhắn lên WebSockets Laravel Reverb qua kênh riêng tư `conversation.{id}` và `admin.chat`. |
| **8** | Xử lý AI Chatbot & Function Calling | [`app/Services/ChatbotService.php`](file:///c:/xampp/htdocs/travel/travel/app/Services/ChatbotService.php) | `chat()`, `getSystemPrompt()`, `getToolsDeclaration()`, `handleLocalFunctionCall()`, `searchTours()`, `getTourItinerary()`, `searchAccommodations()` | Tích hợp Gemini 3.1 Flash Lite API, khai báo 3 tools JSON Schema, tự động truy vấn DB khi AI yêu cầu và phản hồi kết quả cho AI. |
| **9** | Quản lý Session & Khôi phục Link AI | [`app/Http/Controllers/Frontend/ChatbotController.php`](file:///c:/xampp/htdocs/travel/travel/app/Http/Controllers/Frontend/ChatbotController.php) | `sendMessage()`, `getHistory()`, `sanitizeContentLinks()` | Lưu session token qua Cookie 30 ngày, chạy vòng lặp xử lý function call (`maxIter = 5`), thay thế link AI bịa bằng link slug tour thật. |
| **10** | UI Widget Live Chat Khách hàng | [`resources/views/components/chatbox.blade.php`](file:///c:/xampp/htdocs/travel/travel/resources/views/components/chatbox.blade.php) | JS: `checkUnreadOnLoad()`, `sendChatMessage()`, `listenToChat()` | Khung chat người thật giao diện khách hàng. Lắng nghe WebSockets Echo, đính kèm file/CSV template, đánh dấu sao tin nhắn. |
| **11** | Dashboard Live Chat Admin/CSKH | [`resources/views/admin/chat/index.blade.php`](file:///c:/xampp/htdocs/travel/travel/resources/views/admin/chat/index.blade.php) | Alpine.js component: `chatAdmin()` | Màn hình quản lý ca chat của nhân viên CSKH. Hiển thị danh sách hội thoại, chat thời gian thực, xem file/ảnh đính kèm. |
| **12** | UI Widget AI Chatbot | [`resources/views/frontend/components/chatbot.blade.php`](file:///c:/xampp/htdocs/travel/travel/resources/views/frontend/components/chatbot.blade.php) | JS: `loadHistory()`, `appendMessage()`, `showTyping()` | Khung chat Trợ lý ảo AI. Tự động hiển thị hiệu ứng gõ tin nhắn (`typing-indicator`), render Markdown văn bản & link tour. |

---

## 3. SƠ ĐỒ KIẾN TRÚC & CÔNG NGHỆ SỬ DỤNG (TECH STACK)

### 🛠️ Công nghệ Backend:
- **Framework:** Laravel 12 (PHP 8.2).
- **Real-time Engine:** Laravel Reverb v1 (WebSockets server chạy ẩn, hiệu năng cực cao).
- **Phân quyền (RBAC):** `spatie/laravel-permission` (phân chia vai trò `customer`, `cskh`, `Staff`, `Admin`).
- **AI Engine:** Google Gemini API (`gemini-3.1-flash-lite` via Laravel HTTP Client).
- **Database:** MySQL (`conversations`, `messages`, `chat_sessions`, `chat_messages`, `users`).

### 💻 Công nghệ Frontend:
- **UI Render:** Laravel Blade Templates + Bootstrap 5 + CSS Glassmorphism.
- **Reactivity:** Alpine.js (Khung chat Admin) & Vanilla JavaScript (Khung chat User).
- **WebSockets Client:** Laravel Echo v2 + Pusher-JS client protocol.
- **Markdown Parsing:** `marked.js` hiển thị văn bản định dạng & link từ AI.

---

## 4. CÁCH THỨC HOẠT ĐỘNG & THUẬT TOÁN THỰC THI (HOW IT WORKS & IMPLEMENTATION DETAIL)

### A. Thuật toán phân phối tải Least Connections
- **Vị trí File:** [`app/Services/ChatDistributionService.php`](file:///c:/xampp/htdocs/travel/travel/app/Services/ChatDistributionService.php)
- **Hàm chính:** `getAvailableAgent()` và `assign(Conversation $conversation)`
- **Cách làm (How it works):**
  1. Quét danh sách nhân viên có vai trò `cskh` (nếu không có ai online thì fallback tìm `Staff` / `Admin`).
  2. Điều kiện nhân viên được coi là sẵn sàng: `is_active == true` và `last_seen_at >= Carbon::now()->subMinutes(5)`.
  3. Đếm số lượng cuộc trò chuyện đang mở (`status = 'open'`) trong bảng `conversations` nhóm theo `cskh_id`.
  4. Lựa chọn nhân viên có số lượng hội thoại đang xử lý ít nhất (`$minCount`).
  5. Cập nhật `cskh_id`, `routing_status = 'assigned'`, và `assigned_at = now()`.

```php
// Trích đoạn mã trong ChatDistributionService.php
$agents = User::role('cskh')
    ->where('is_active', true)
    ->where('last_seen_at', '>=', Carbon::now()->subMinutes(5))
    ->get();

$conversationCounts = Conversation::whereIn('cskh_id', $agentIds)
    ->where('status', 'open')
    ->select('cskh_id', DB::raw('count(*) as aggregate'))
    ->groupBy('cskh_id')
    ->pluck('aggregate', 'cskh_id')
    ->toArray();
```

---

### B. Cơ chế theo dõi trạng thái Online (Middleware)
- **Vị trí File:** [`app/Http/Middleware/UpdateLastSeenAt.php`](file:///c:/xampp/htdocs/travel/travel/app/Http/Middleware/UpdateLastSeenAt.php)
- **Hàm chính:** `handle()`
- **Cách làm:** Khi bất kỳ người dùng đã đăng nhập (bao gồm CSKH) thực hiện một HTTP Request, Middleware kiểm tra nếu `last_seen_at` là `null` hoặc đã quá 60 giây so với hiện tại thì cập nhật `$user->last_seen_at = now()`. Sử dụng `saveQuietly()` để không kích hoạt model events.
- **Khi Logout:** Tại [`AuthenticatedSessionController::destroy()`](file:///c:/xampp/htdocs/travel/travel/app/Http/Controllers/Auth/AuthenticatedSessionController.php), hệ thống đặt `$user->last_seen_at = null` để CSKH dừng nhận ca chat ngay khi đăng xuất.

---

### C. Tự động điều phối lại khi CSKH offline (Artisan Command)
- **Vị trí File:** [`app/Console/Commands/ReRouteOfflineChats.php`](file:///c:/xampp/htdocs/travel/travel/app/Console/Commands/ReRouteOfflineChats.php)
- **Hàm chính:** `handle(ChatDistributionService $distributionService)`
- **Lệnh thực thi:** `php artisan chat:re-route-offline`
- **Cách làm:**
  1. Truy vấn các `Conversation` có `status = 'open'` và `cskh_id IS NOT NULL`.
  2. Kiểm tra tài khoản CSKH đó: nếu bị khóa (`is_active = false`), bị xóa, hoặc không tương tác > 5 phút (`last_seen_at < subMinutes(5)`).
  3. Gọi `$distributionService->assign($conversation)` để thu hồi ca chat và phân phối cho nhân viên CSKH khác đang online.

---

### D. Luồng xử lý tin nhắn Real-time & Broadcasting (WebSockets)
- **Vị trí Controller:** [`app/Http/Controllers/ChatController.php`](file:///c:/xampp/htdocs/travel/travel/app/Http/Controllers/ChatController.php) -> `sendMessage()`
- **Vị trí Event:** [`app/Events/MessageSent.php`](file:///c:/xampp/htdocs/travel/travel/app/Events/MessageSent.php) -> `broadcastOn()`
- **Vị trí Channel Auth:** [`routes/channels.php`](file:///c:/xampp/htdocs/travel/travel/routes/channels.php)
- **Cách làm:**
  1. Khi tin nhắn được gửi, nếu là khách gửi và CSKH chưa có/offline, tự động gọi `ChatDistributionService::assign()`.
  2. Nếu có tệp đính kèm, lưu file vào `public/chat_attachments`.
  3. Lọc nội dung tin nhắn qua `ProfanityFilter::filter()`.
  4. Lưu tin nhắn vào bảng `messages` và gọi `broadcast(new MessageSent($message))->toOthers()`.
  5. Event `MessageSent` triển khai `ShouldBroadcastNow` đẩy dữ liệu qua Laravel Reverb đến channel `conversation.{id}` và `admin.chat`.
  6. Ở Frontend, Laravel Echo (`window.Echo.private('conversation.' + id).listen('MessageSent', ...)` lắng nghe và tự động chèn tin nhắn mới vào DOM mà không cần load lại trang.

---

### E. Kiểm duyệt từ ngữ thô tục (Profanity Filter)
- **Vị trí File:** [`app/Services/ProfanityFilter.php`](file:///c:/xampp/htdocs/travel/travel/app/Services/ProfanityFilter.php)
- **Hàm chính:** `ProfanityFilter::filter(?string $text)`
- **Cách làm:** Định nghĩa danh sách từ vi phạm tiếng Việt. Sắp xếp mảng từ cấm theo độ dài giảm dần (để thay thế cụm từ dài trước, tránh ghi đè từ ngắn). Dùng Regex unicode `/\b' . preg_quote($word) . '\b/iu` thay thế bằng chuỗi ký tự `*` tương ứng với số lượng ký tự của từ.

---

### F. Luồng xử lý AI Chatbot & Function Calling (Gemini API)
- **Vị trí Service:** [`app/Services/ChatbotService.php`](file:///c:/xampp/htdocs/travel/travel/app/Services/ChatbotService.php)
- **Vị trí Controller:** [`app/Http/Controllers/Frontend/ChatbotController.php`](file:///c:/xampp/htdocs/travel/travel/app/Http/Controllers/Frontend/ChatbotController.php)
- **Hàm chính:** `getToolsDeclaration()`, `chat()`, `handleLocalFunctionCall()`, `searchTours()`, `getTourItinerary()`, `searchAccommodations()`
- **Cách làm:**
  1. `getSystemPrompt()` định nghĩa quy tắc xưng hô (*xưng Em/Travel Wonder, gọi Quý khách*), bảng giá mẫu và chính sách hoàn tiền.
  2. `getToolsDeclaration()` khai báo cấu trúc JSON Schema của 3 công cụ:
     - `search_tours`: Tìm kiếm tour theo keyword, vùng miền, ngân sách tối đa (`max_price`), sắp xếp.
     - `get_tour_itinerary`: Lấy lịch trình từng ngày của Tour theo `tour_id`.
     - `search_accommodations`: Tìm nơi lưu trú theo điểm đến & số sao.
  3. Khi khách gửi câu hỏi, Controller gửi lịch sử hội thoại lên Gemini API via `chat()`.
  4. Nếu Gemini phát hiện cần thông tin thực tế, nó trả về `type === 'function_call'` kèm tham số.
  5. `ChatbotController` chạy vòng lặp (`maxIter = 5`), gọi `handleLocalFunctionCall()` để truy vấn trực tiếp DB MySQL của website, lấy kết quả thật và gửi ngược lại cho Gemini để Gemini tổng hợp câu trả lời cho khách.

---

### G. Chuẩn hóa & Khôi phục Link thật cho AI Chatbot
- **Vị trí File:** [`app/Http/Controllers/Frontend/ChatbotController.php`](file:///c:/xampp/htdocs/travel/travel/app/Http/Controllers/Frontend/ChatbotController.php)
- **Hàm phụ trách:** `sanitizeContentLinks(string $content)`
- **Cách làm:** Tránh trường hợp AI tự sáng tạo ra link giả (ví dụ: `https://travelwonder.vn/tour-abc`). Hàm này sử dụng Regex bắt toàn bộ cú pháp Markdown link `[tên tour](url)`. Nếu URL chưa chuẩn, hệ thống tự động bóc tách tiêu đề tour, tìm kiếm trong bảng `tours` bằng Eloquent và thay thế bằng URL route chính xác của hệ thống: `route('frontend.tours.show', $tour->slug)`.

---

## 5. NGHIỆP VỤ CHĂM SÓC KHÁCH HÀNG & PHÂN QUYỀN

### Phân quyền người dùng (RBAC - Spatie Permission):
- **Khách hàng (`customer`):** Chỉ xem và gửi tin nhắn trong ca chat của chính mình.
- **Nhân viên CSKH (`cskh`):** Xem các ca chat được phân công cho mình + các ca chat chưa có người tiếp nhận (`cskh_id IS NULL`).
- **Quản trị viên & Nhân viên chung (`Admin`, `Staff`):** Có toàn quyền xem tất cả cuộc trò chuyện trong hệ thống để can thiệp hỗ trợ khi cần thiết.

---

## 6. CÁC TÍNH NĂNG ĐẶC BIỆT & ĐIỂM NỔI BẬT DÙNG ĐỂ THUYẾT TRÌNH

1. **⚡ Real-time Zero Latency với Laravel Reverb:**
   WebSockets chạy native trên Laravel 12, tốc độ phản hồi tức thì, không gây ngẽn tài nguyên do polling.
2. **🤖 AI Chatbot thông minh với Gemini Function Calling:**
   AI thực sự kết nối với Cơ sở dữ liệu website chứ không chỉ trả lời theo dữ liệu đóng đóng băng.
3. **⚖️ Phân phối tải thông minh (Least Connections) & Auto Re-routing:**
   Đảm bảo công bằng công việc giữa các CSKH và không bao giờ để khách hàng đợi vô thời hạn khi CSKH rớt mạng.
4. **🛡️ Bảo mật & Văn minh giao tiếp:**
   Lọc từ thô tục tự động, kiểm soát quyền truy cập channel riêng tư và giới hạn file upload 10MB.

---

## 7. CÁC CHỨC NĂNG CẦN BỔ SUNG & HOÀN THIỆN TRONG TƯƠNG LAI (FUTURE ROADMAP)

1. **🔄 Hợp nhất Widget Chatbot AI & Live Chat CSKH (Bot-to-Human Handoff):**
   Gộp 2 nút chat thành 1. Khi AI không trả lời được hoặc khách bấm "Gặp CSKH", tự động chuyển toàn bộ hội thoại AI sang cho CSKH tiếp quản.
2. **⭐ Đánh giá Chất lượng CSKH (CSAT Rating):**
   Hiển thị khung chấm 1-5 Sao sau khi đóng ca chat.
3. **📝 Câu trả lời mẫu nhanh (Canned Responses / Quick Replies):**
   Hỗ trợ gõ phím tắt `/xinchao`, `/huytour` trong khung chat Admin.
4. **📊 Thống kê KPI CSKH (Analytics Dashboard):**
   Đo lường thời gian phản hồi trung bình (ART) và tổng số ca xử lý của từng nhân viên.
5. **🌍 AI CSKH Đa ngôn ngữ (Multilingual Assistant):**
   Nhận diện và trả lời bằng Tiếng Anh / Tiếng Trung / Tiếng Hàn cho khách quốc tế.
