<div class="dropdown" x-data="adminNotificationComponent()" x-init="init()" @click.outside="closeDropdown()">
    <button @click="toggleDropdown()" class="btn btn-white rounded-circle shadow-sm position-relative" style="width:40px;height:40px; border: 1px solid #e2e8f0; background: #fff;">
        <i class="bi bi-bell text-dark"></i>
        <span x-show="unreadCount > 0" x-text="unreadCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;" x-cloak></span>
    </button>
    
    <div x-show="isOpen" 
         x-transition.opacity
         class="dropdown-menu dropdown-menu-end shadow-lg border-0 show" 
         style="position: absolute; right: 0; top: 100%; min-width: 350px; border-radius: 12px; margin-top: 10px; z-index: 1050; padding: 0;"
         x-cloak>
        
        <div class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center bg-light rounded-top" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
            <strong class="mb-0">Thông báo quản trị</strong>
            <button @click="markAllAsRead()" class="btn btn-sm btn-link text-decoration-none p-0" style="font-size: 0.8rem;" x-show="unreadCount > 0">Đánh dấu đã đọc</button>
        </div>
        
        <ul class="list-unstyled mb-0" style="max-height: 350px; overflow-y: auto;">
            <template x-if="notifications.length === 0">
                <li class="px-3 py-4 text-center text-muted">
                    <i class="bi bi-bell-slash fs-4 mb-2 d-block"></i>
                    Không có thông báo nào
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
                                <strong class="d-block mb-1" style="font-size: 0.9rem;" x-text="notification.data ? (notification.data.title || 'Thông báo mới') : (notification.title || 'Thông báo mới')"></strong>
                                <p class="mb-1 text-secondary" style="font-size: 0.8rem; line-height: 1.3;" x-text="notification.data ? notification.data.message : notification.message"></p>
                                <small class="text-muted" style="font-size: 0.75rem;" x-text="new Date(notification.created_at).toLocaleString('vi-VN')"></small>
                            </div>
                            <div x-show="!notification.read_at" class="ms-2 mt-1">
                                <span class="p-1 bg-primary rounded-circle d-inline-block"></span>
                            </div>
                        </div>
                    </a>
                </li>
            </template>
        </ul>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('adminNotificationComponent', () => ({
        notifications: [],
        unreadCount: 0,
        userId: {{ auth()->id() ?? 'null' }},
        isOpen: false,
        readTimeout: null,

        init() {
            if (!this.userId) return;

            this.fetchNotifications();
            this.listenForNotifications();
        },

        toggleDropdown() {
            this.isOpen = !this.isOpen;
            if (this.isOpen && this.unreadCount > 0) {
                // Nếu mở cửa sổ và có thông báo chưa đọc, set timeout 2s để tự động đánh dấu đã đọc
                this.readTimeout = setTimeout(() => {
                    this.markAllAsRead();
                }, 2000);
            } else {
                this.clearReadTimeout();
            }
        },

        closeDropdown() {
            this.isOpen = false;
            this.clearReadTimeout();
        },

        clearReadTimeout() {
            if (this.readTimeout) {
                clearTimeout(this.readTimeout);
                this.readTimeout = null;
            }
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
                        // Tránh duplicate do pusher
                        if (!this.notifications.some(n => n.id === notification.id)) {
                            this.notifications.unshift(notification);
                            this.unreadCount++;
                            // Cập nhật lại timeout nếu cửa sổ đang mở
                            if (this.isOpen) {
                                this.clearReadTimeout();
                                this.readTimeout = setTimeout(() => {
                                    this.markAllAsRead();
                                }, 2000);
                            }
                        }
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
            if (this.unreadCount === 0) return;
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

        getIconClass(type) {
            if (type === 'payment_success') return 'bi bi-cash-coin';
            if (type === 'booking_created') return 'bi bi-calendar-plus';
            if (type === 'booking_cancelled') return 'bi bi-x-circle';
            if (type === 'invoice_requested') return 'bi bi-receipt';
            return 'bi bi-bell-fill';
        },

        getIconBgClass(type) {
            if (type === 'payment_success') return 'bg-success';
            if (type === 'booking_created') return 'bg-primary';
            if (type === 'booking_cancelled') return 'bg-danger';
            if (type === 'invoice_requested') return 'bg-info';
            return 'bg-secondary';
        }
    }));
});
</script>
