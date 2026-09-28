@extends('layouts.admin')
@section('title', 'Quản lý Live Chat')

@section('content')
<div class="container-fluid py-4" x-data="chatAdmin()">
    <h3 class="mb-4">Hỗ trợ khách hàng trực tuyến</h3>
    
    <div class="row">
        <!-- Danh sách hội thoại -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Danh sách Chat</h5>
                </div>
                <div class="card-body p-0" style="height: 600px; overflow-y: auto;">
                    <div class="list-group list-group-flush">
                        <template x-for="conv in conversations" :key="conv.id">
                            <button class="list-group-item list-group-item-action p-3" 
                                    :class="{'active': currentConv && currentConv.id == conv.id}"
                                    @click="selectConversation(conv)">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong x-text="conv.user ? conv.user.name : 'Khách'"></strong>
                                    <small x-text="formatTime(conv.updated_at)"></small>
                                </div>
                                <div class="small text-muted text-truncate" x-text="conv.messages.length > 0 ? conv.messages[0].message : 'Chưa có tin nhắn'"></div>
                            </button>
                        </template>
                        <div x-show="conversations.length === 0" class="p-4 text-center text-muted">
                            Không có cuộc trò chuyện nào.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Khung chat chi tiết -->
        <div class="col-md-8">
            <div class="card shadow-sm h-100" x-show="currentConv">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        Chat với <span x-text="currentConv ? currentConv.user.name : ''" class="text-primary"></span>
                        <template x-if="currentConv && currentConv.booking_id">
                            <span class="badge bg-info ms-2">Booking #<span x-text="currentConv.booking_id"></span></span>
                        </template>
                    </h5>
                </div>
                <div class="card-body p-4 d-flex flex-column" style="height: 600px; background-color: #f8f9fa;">
                    <!-- Messages Container -->
                    <div class="chat-messages flex-grow-1" id="admin-chat-messages" style="overflow-y: auto; margin-bottom: 20px;">
                        <template x-for="msg in messages" :key="msg.id">
                            <div class="mb-3 d-flex" :class="msg.sender_id == adminId ? 'justify-content-end' : ''">
                                <div style="max-width: 75%; position: relative;">
                                    <div class="p-3 rounded-3 shadow-sm" 
                                         :class="msg.sender_id == adminId ? 'bg-primary text-white' : 'bg-white border'">
                                        <!-- Nút đánh dấu quan trọng -->
                                        <div class="position-absolute" style="top: -10px; right: -10px; cursor: pointer; background: white; border-radius: 50%; padding: 2px 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" @click="toggleImportant(msg)">
                                            <i class="bi" :class="msg.is_important ? 'bi-star-fill text-warning' : 'bi-star text-muted'"></i>
                                        </div>
                                        
                                        <!-- Nhãn nguồn gốc tin nhắn -->
                                        <div class="small fw-bold mb-1" 
                                             style="font-size: 0.75rem;"
                                             :class="msg.sender_id == adminId ? 'text-white-50 text-end' : (msg.sender && msg.sender.role !== 'customer' ? 'text-info' : 'text-muted')"
                                             x-text="getSenderLabel(msg)">
                                        </div>
                                        
                                        <div x-text="msg.message" x-show="msg.message"></div>
                                        <template x-if="msg.attachment_path">
                                            <div class="mt-2">
                                                <!-- Nếu là ảnh thì hiển thị ảnh, nếu không thì hiển thị nút download -->
                                                <template x-if="msg.attachment_path && msg.attachment_path.match(/\.(jpeg|jpg|gif|png)$/i) != null">
                                                    <a :href="msg.attachment_path" target="_blank">
                                                        <img :src="msg.attachment_path" class="img-fluid rounded" style="max-height: 200px;">
                                                    </a>
                                                </template>
                                                <template x-if="msg.attachment_path && msg.attachment_path.match(/\.(jpeg|jpg|gif|png)$/i) == null">
                                                    <a :href="msg.attachment_path" target="_blank" 
                                                       class="btn btn-sm"
                                                       :class="msg.sender_id == adminId ? 'btn-light text-primary' : 'btn-outline-primary'">
                                                        <i class="bi bi-file-earmark-arrow-down"></i> <span x-text="msg.attachment_name"></span>
                                                    </a>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="small mt-1 text-muted" :class="msg.sender_id == adminId ? 'text-end' : ''" x-text="formatTime(msg.created_at)"></div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Input Form -->
                    <form @submit.prevent="sendMessage" class="mt-auto bg-white p-3 border rounded">
                        <div class="d-flex align-items-center gap-2">
                            <input type="file" x-ref="attachment" class="d-none" accept=".xlsx,.xls,.csv,.doc,.docx,.png,.jpg,.jpeg" @change="updateFileName">
                            <button type="button" class="btn btn-light border" @click="$refs.attachment.click()" title="Đính kèm file">
                                <i class="bi bi-paperclip text-secondary"></i>
                            </button>
                            <input type="text" x-model="newMessage" class="form-control" placeholder="Nhập câu trả lời..." autocomplete="off">
                            <button type="submit" class="btn btn-primary px-4" :disabled="sending">
                                <i class="bi bi-send"></i> Gửi
                            </button>
                        </div>
                        <div x-show="fileName" class="small text-muted mt-2">
                            <i class="bi bi-file-earmark"></i> Đang chọn: <span x-text="fileName"></span>
                        </div>
                    </form>
                </div>
            </div>
            
            <div x-show="!currentConv" class="h-100">
                <div class="card shadow-sm h-100 d-flex justify-content-center align-items-center bg-light">
                    <div class="text-center text-muted">
                        <i class="bi bi-chat-dots" style="font-size: 3rem;"></i>
                        <p class="mt-3">Chọn một đoạn chat để bắt đầu</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function initChatAdmin() {
        if (typeof Alpine !== 'undefined' && !Alpine.data('chatAdmin')) {
            Alpine.data('chatAdmin', () => ({
                conversations: [],
                messages: [],
                currentConv: null,
                newMessage: '',
                fileName: '',
                sending: false,
                adminId: {{ auth()->id() }},
                
                init() {
                    this.loadConversations();
                    setInterval(() => {
                        if(!this.currentConv) {
                            this.loadConversations(); // Polling fallback for list
                        }
                    }, 10000);
                },

                loadConversations() {
                    fetch('/chat/conversations')
                        .then(r => r.json())
                        .then(data => {
                            this.conversations = data;
                        });
                },

                selectConversation(conv) {
                    this.currentConv = conv;
                    this.loadMessages(conv.id);
                    this.listenToConversation(conv.id);
                    this.markAsRead(conv.id);
                },

                loadMessages(id) {
                    fetch('/chat/' + id + '/messages')
                        .then(r => r.json())
                        .then(data => {
                            this.messages = data;
                            this.scrollToBottom();
                        });
                },

                updateFileName() {
                    const file = this.$refs.attachment.files[0];
                    this.fileName = file ? file.name : '';
                },

                toggleImportant(msg) {
                    const oldState = msg.is_important;
                    msg.is_important = !msg.is_important;
                    fetch('/chat/' + this.currentConv.id + '/messages/' + msg.id + '/mark-important', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    }).then(r => r.json()).then(data => {
                        msg.is_important = data.is_important;
                    }).catch(e => {
                        msg.is_important = oldState; // revert on error
                    });
                },

                sendMessage() {
                    const file = this.$refs.attachment ? this.$refs.attachment.files[0] : null;
                    if (!this.newMessage.trim() && !file) return;
                    
                    this.sending = true;
                    const formData = new FormData();
                    if(this.newMessage.trim()) formData.append('message', this.newMessage.trim());
                    if(file) formData.append('attachment', file);
                    
                    fetch('/chat/' + this.currentConv.id + '/send', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: formData
                    })
                    .then(r => r.json())
                    .then(msg => {
                        this.messages.push(msg);
                        this.newMessage = '';
                        this.fileName = '';
                        if(this.$refs.attachment) this.$refs.attachment.value = '';
                        this.scrollToBottom();
                        this.loadConversations(); // refresh list to update latest msg preview
                    })
                    .finally(() => {
                        this.sending = false;
                    });
                },

                listenToConversation(id) {
                    if(window.Echo) {
                        // Leave old channels
                        window.Echo.leaveAllChannels();
                        
                        window.Echo.private('conversation.' + id)
                            .listen('MessageSent', (e) => {
                                if(e.message.sender_id != this.adminId) {
                                    this.messages.push(e.message);
                                    this.scrollToBottom();
                                    this.loadConversations();
                                    this.markAsRead(id);
                                }
                            });
                    }
                },

                markAsRead(id) {
                    fetch('/chat/' + id + '/mark-as-read', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    })
                    .then(r => r.json())
                    .then(data => {
                        this.updateSidebarBadge();
                    });
                },

                updateSidebarBadge() {
                    fetch('/chat/unread-count')
                    .then(r => r.json())
                    .then(data => {
                        const badge = document.getElementById('admin-chat-badge');
                        if (badge) {
                            if (data.count > 0) {
                                badge.textContent = data.count;
                                badge.style.display = 'inline-block';
                            } else {
                                badge.textContent = '0';
                                badge.style.display = 'none';
                            }
                        }
                    });
                },

                scrollToBottom() {
                    setTimeout(() => {
                        const box = document.getElementById('admin-chat-messages');
                        if(box) box.scrollTop = box.scrollHeight;
                    }, 100);
                },

                formatTime(dateString) {
                    if(!dateString) return '';
                    const date = new Date(dateString);
                    return date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) + ' ' + date.toLocaleDateString();
                },

                getSenderLabel(msg) {
                    if (msg.sender_id == this.adminId) {
                        return 'Tôi';
                    }
                    if (msg.sender) {
                        if (msg.sender.role === 'customer') {
                            return msg.sender.name + ' (Khách hàng)';
                        }
                        if (msg.sender.role === 'cskh' || msg.sender.role === 'admin' || msg.sender.role === 'staff') {
                            return msg.sender.name + ' (NV CSKH khác)';
                        }
                        return msg.sender.name;
                    }
                    // Fallback using is_admin_sender from broadcast
                    if (msg.is_admin_sender) {
                        return 'NV CSKH khác';
                    }
                    return 'Khách hàng';
                }
            }));
        }
    }

    if (typeof Alpine !== 'undefined') {
        initChatAdmin();
    } else {
        document.addEventListener('alpine:init', initChatAdmin);
    }
</script>
@endpush
@endsection
