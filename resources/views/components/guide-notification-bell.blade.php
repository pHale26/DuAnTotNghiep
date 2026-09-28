<div class="dropdown" x-data="guideNotificationComponent()" x-init="init()" @click.outside="closeDropdown()">
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
            <strong class="mb-0">Thông báo Hướng dẫn viên</strong>
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

    <!-- Toast Container for Notifications -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999; margin-bottom: 70px;">
        <template x-for="toast in toasts" :key="toast.id">
            <div class="toast show shadow-lg border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true" style="border-radius: 12px; min-width: 320px; display: block; overflow: hidden;">
                <div class="toast-header border-0 text-white d-flex align-items-center justify-content-between py-2 px-3" :class="getIconBgClass(toast.type)">
                    <div class="d-flex align-items-center gap-2">
                        <i :class="getIconClass(toast.type)"></i>
                        <strong class="me-auto" x-text="toast.title || 'Thông báo mới'"></strong>
                    </div>
                    <button type="button" @click="removeToast(toast.id)" class="btn-close btn-close-white" aria-label="Close"></button>
                </div>
                <div class="toast-body bg-white p-3 text-dark">
                    <p class="mb-2" style="font-size: 0.85rem; font-weight: 500; line-height: 1.4;" x-text="toast.message"></p>
                    <div class="text-end" x-show="toast.link">
                        <a :href="toast.link" class="btn btn-sm btn-primary py-1 px-3 fw-bold" style="font-size: 0.75rem; border-radius: 6px;">Xem chi tiết</a>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('guideNotificationComponent', () => ({
        notifications: [],
        unreadCount: 0,
        toasts: [],
        userId: {{ auth()->id() ?? 'null' }},
        isOpen: false,
        readTimeout: null,

        init() {
            if (!this.userId) return;

            this.fetchNotifications();
            this.listenForNotifications();

            // Auto-check for status changes and update notifications list every 15 seconds
            setInterval(() => {
                this.fetchNotifications();
            }, 15000);
        },

        toggleDropdown() {
            this.isOpen = !this.isOpen;
            if (this.isOpen && this.unreadCount > 0) {
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
                        const newNotifications = data.notifications.data;
                        
                        // Compare and show Toast for any new unread notification
                        newNotifications.forEach(notif => {
                            if (!this.notifications.some(n => n.id === notif.id)) {
                                if (!notif.read_at) {
                                    this.showToast(notif);
                                }
                            }
                        });
                        
                        this.notifications = newNotifications;
                        this.unreadCount = data.unread_count;
                    }
                })
                .catch(err => console.error(err));
        },

        listenForNotifications() {
            if (window.Echo) {
                window.Echo.private('App.Models.User.' + this.userId)
                    .notification((notification) => {
                        if (!this.notifications.some(n => n.id === notification.id)) {
                            this.notifications.unshift(notification);
                            this.unreadCount++;
                            this.showToast(notification);
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

        showToast(notification) {
            const id = Date.now();
            const message = notification.message || (notification.data ? notification.data.message : '');
            const title = notification.title || (notification.data ? notification.data.title : 'Thông báo mới');
            const type = notification.type || (notification.data ? notification.data.type : 'default');
            const link = notification.link || (notification.data ? notification.data.link : '');
            
            this.toasts.push({ id, message, title, type, link });
            setTimeout(() => {
                this.removeToast(id);
            }, 8000);
        },

        removeToast(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
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
            if (type === 'group_split_overdue') return 'bi bi-clock-history';
            if (type === 'group_split_unreachable') return 'bi bi-exclamation-triangle-fill';
            if (type === 'tour_report_submitted') return 'bi bi-file-earmark-arrow-up-fill';
            if (type === 'tour_report_approved') return 'bi bi-check-circle-fill';
            if (type === 'tour_report_rejected') return 'bi bi-x-circle-fill';
            if (type === 'absence_approved') return 'bi bi-calendar-check-fill';
            if (type === 'absence_rejected') return 'bi bi-calendar-x-fill';
            return 'bi bi-bell-fill';
        },

        getIconBgClass(type) {
            if (type === 'group_split_overdue') return 'bg-warning text-dark';
            if (type === 'group_split_unreachable') return 'bg-danger';
            if (type === 'tour_report_submitted') return 'bg-info';
            if (type === 'tour_report_approved') return 'bg-success';
            if (type === 'tour_report_rejected') return 'bg-danger';
            if (type === 'absence_approved') return 'bg-success';
            if (type === 'absence_rejected') return 'bg-danger';
            return 'bg-secondary';
        }
    }));
});
</script>
