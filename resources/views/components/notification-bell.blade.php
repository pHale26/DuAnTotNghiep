<li class="nav-item dropdown" x-data="notificationComponent()" x-init="init()">
    <a class="nav-link dropdown-toggle d-flex align-items-center py-1 position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-weight: 600;">
        <i class="bi bi-bell fs-5"></i>
        <span x-show="unreadCount > 0" x-text="unreadCount" class="position-absolute badge rounded-pill bg-danger" style="font-size: 0.6rem; top: 0px; right: 2px; min-width: 16px; padding: 2px 5px; line-height: 1.2;" x-cloak></span>
    </a>
    <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="notificationDropdown" style="border-radius: 12px; margin-top: 5px; width: 350px; max-height: 400px; overflow-y: auto;">
        <li class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center bg-light" style="position: sticky; top: 0; z-index: 10;">
            <strong class="mb-0">Thông báo</strong>
            <button @click="markAllAsRead()" class="btn btn-sm btn-link text-decoration-none p-0" style="font-size: 0.8rem;" x-show="unreadCount > 0">Đánh dấu đã đọc</button>
        </li>
        <template x-if="notifications.length === 0">
            <li class="px-3 py-4 text-center text-muted">
                <i class="bi bi-bell-slash fs-4 mb-2 d-block"></i>
                Chưa có thông báo nào
            </li>
        </template>
        <template x-for="notification in notifications" :key="notification.id">
            <li>
                <a class="dropdown-item py-3 border-bottom text-wrap" :href="notification.data ? (notification.data.link || '#') : (notification.link || '#')" @click="markAsRead(notification.id)" :class="{ 'bg-light': !notification.read_at }">
                    <div class="d-flex w-100 justify-content-between align-items-start">
                        <div class="me-3">
                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" :class="getIconBgClass(notification.data ? notification.data.type : notification.type)">
                                <i :class="getIconClass(notification.data ? notification.data.type : notification.type)"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <strong class="d-block mb-1" style="font-size: 0.95rem;" x-text="notification.data ? (notification.data.title || 'Thông báo mới') : (notification.title || 'Thông báo mới')"></strong>
                            <p class="mb-1 text-secondary" style="font-size: 0.85rem;" x-text="notification.data ? notification.data.message : notification.message"></p>
                            <small class="text-muted" x-text="new Date(notification.created_at).toLocaleString('vi-VN')"></small>
                        </div>
                        <div x-show="!notification.read_at" class="ms-2 mt-1">
                            <span class="p-1 bg-primary rounded-circle d-inline-block"></span>
                        </div>
                    </div>
                </a>
            </li>
        </template>
        <li class="text-center py-2 bg-light" style="position: sticky; bottom: 0;">
            <a href="{{ url('/user/profile?tab=notifications') }}" class="text-decoration-none" style="font-size: 0.85rem;">Xem tất cả</a>
        </li>
    </ul>

    <!-- Toast Container for Notifications (Bootstrap + Alpine) -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999; margin-bottom: 70px;">
        <template x-for="toast in toasts" :key="toast.id">
            <div class="toast show shadow-lg border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true" style="border-radius: 12px; width: 340px; max-width: 90vw; display: block; overflow: hidden; background: #fff;">
                <div class="toast-header border-0 text-white d-flex align-items-center justify-content-between py-2 px-3" :class="getIconBgClass(toast.type)">
                    <div class="d-flex align-items-center gap-2">
                        <i :class="getIconClass(toast.type)"></i>
                        <strong class="me-auto" x-text="toast.title || 'Thông báo mới'"></strong>
                    </div>
                    <button type="button" @click="removeToast(toast.id)" class="btn-close btn-close-white" aria-label="Close"></button>
                </div>
                <div class="toast-body bg-white p-3 text-dark">
                    <p class="mb-0" style="font-size: 0.85rem; font-weight: 500; line-height: 1.4;" x-text="toast.message"></p>
                </div>
            </div>
        </template>
    </div>
</li>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('notificationComponent', () => ({
        notifications: [],
        unreadCount: 0,
        toasts: [],
        userId: {{ auth()->id() ?? 'null' }},

        init() {
            if (!this.userId) return;

            this.fetchNotifications();
            this.listenForNotifications();
        },

        fetchNotifications() {
            fetch('/notifications')
                .then(res => res.json())
                .then(data => {
                    if (data.notifications) {
                        this.notifications = data.notifications.data;
                        this.unreadCount = data.unread_count;
                    }
                })
                .catch(err => console.error(err));
        },

        listenForNotifications() {
            if (window.Echo) {
                window.Echo.private('App.Models.User.' + this.userId)
                    .notification((notification) => {
                        this.notifications.unshift(notification);
                        this.unreadCount++;
                        this.showToast(notification);
                    });
            }
        },

        markAsRead(id) {
            fetch(`/notifications/${id}/mark-as-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '{{ csrf_token() }}'
                }
            }).then(() => {
                let notif = this.notifications.find(n => n.id === id);
                if (notif && !notif.read_at) {
                    notif.read_at = new Date().toISOString();
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                }
            });
        },

        markAllAsRead() {
            fetch('/notifications/mark-all-as-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '{{ csrf_token() }}'
                }
            }).then(() => {
                this.notifications.forEach(n => n.read_at = n.read_at || new Date().toISOString());
                this.unreadCount = 0;
            });
        },

        showToast(notification) {
            const id = Date.now();
            const message = notification.message || (notification.data ? notification.data.message : '');
            const title = notification.title || (notification.data ? notification.data.title : 'Thông báo mới');
            const type = notification.type || (notification.data ? notification.data.type : 'default');
            
            this.toasts.push({ id, message, title, type });
            setTimeout(() => {
                this.removeToast(id);
            }, 5000);
        },

        removeToast(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        },

        getIconClass(type) {
            if (type === 'payment_success') return 'bi bi-cash-coin';
            if (type === 'booking_success') return 'bi bi-check-circle-fill';
            if (type === 'departure_reminder') return 'bi bi-alarm-fill';
            if (type === 'booking_status') return 'bi bi-arrow-repeat';
            return 'bi bi-bell-fill';
        },

        getIconBgClass(type) {
            if (type === 'payment_success') return 'bg-success';
            if (type === 'booking_success') return 'bg-success';
            if (type === 'departure_reminder') return 'bg-warning text-dark';
            if (type === 'booking_status') return 'bg-info';
            return 'bg-primary';
        }
    }));
});
</script>
