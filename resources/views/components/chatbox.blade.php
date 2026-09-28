@auth
<div id="live-chatbox" class="live-chatbox">
    <!-- Bubble Button -->
    <div id="chatbox-bubble" class="chatbox-bubble position-relative" onclick="toggleChatbox()">
        <i class="bi bi-headset"></i>
        <span id="chat-badge" class="chatbox-badge" style="display: none;"></span>
    </div>
    
    <!-- Chat Panel -->
    <div id="chatbox-panel" class="chatbox-panel" style="display: none;">
        <div class="chatbox-header">
            <div>
                <strong>Hỗ trợ trực tuyến</strong>
                <span style="display:block; font-size:11px; font-weight:normal; opacity:0.8;">Nhân viên CSKH</span>
            </div>
            <button onclick="toggleChatbox()" aria-label="Đóng"><i class="bi bi-x-lg"></i></button>
        </div>
        
        <div class="chatbox-messages" id="chat-messages">
            <div class="text-center text-muted small my-2">Bắt đầu cuộc trò chuyện với CSKH</div>
        </div>
    
        <div class="chatbox-footer">
            <div class="d-flex align-items-center mb-2">
                <a href="{{ asset('downloads/danh_sach_hanh_khach_template.csv') }}" class="btn btn-sm btn-outline-success w-100" download>
                    <i class="bi bi-file-earmark-excel"></i> Tải template hành khách
                </a>
            </div>
            <form id="chat-form" onsubmit="sendChatMessage(event)" class="d-flex align-items-center gap-2">
                <input type="file" id="chat-attachment" class="d-none" accept=".xlsx,.xls,.csv" onchange="updateFileName()">
                <button type="button" class="btn btn-light border" onclick="document.getElementById('chat-attachment').click()">
                    <i class="bi bi-paperclip text-secondary"></i>
                </button>
                <input type="text" id="chat-message-input" class="chatbox-input" placeholder="Nhập tin nhắn..." autocomplete="off">
                <button type="submit" class="chatbox-send-btn" id="btn-send-msg">
                    <i class="bi bi-send-fill"></i>
                </button>
            </form>
            <div id="file-name-display" class="small text-muted mt-1" style="display: none;"></div>
        </div>
    </div>
</div>

<style>
.live-chatbox {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1050;
    font-family: 'Inter', sans-serif;
}
.chatbox-bubble {
    width: 55px;
    height: 55px;
    background: linear-gradient(135deg, #0ea5e9, #2563eb);
    color: white;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 26px;
    cursor: pointer;
    transition: transform 0.3s ease;
    box-shadow: 0 4px 15px rgba(37, 99, 235, 0.4);
    border: none;
}
.chatbox-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background-color: #ff3b30;
    color: white;
    font-size: 11px;
    font-weight: 700;
    border-radius: 10px;
    min-width: 20px;
    height: 20px;
    padding: 0 6px;
    display: flex;
    justify-content: center;
    align-items: center;
    border: 2px solid white;
    z-index: 10;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}
.chatbox-bubble:hover {
    transform: scale(1.1);
}
.chatbox-panel {
    position: absolute;
    bottom: 70px;
    right: 0;
    width: 350px;
    height: 500px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.2);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transition: opacity 0.3s ease, transform 0.3s ease;
    transform-origin: bottom right;
    border: none;
}
.chatbox-header {
    background: linear-gradient(135deg, #0ea5e9, #2563eb);
    color: white;
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 16px;
}
.chatbox-header button {
    background: none;
    border: none;
    color: white;
    font-size: 20px;
    cursor: pointer;
}
.chatbox-messages {
    flex: 1;
    padding: 15px;
    overflow-y: auto;
    background: #f8fafc;
}
.chatbox-footer {
    padding: 10px;
    background: #fff;
    border-top: 1px solid #e2e8f0;
}
.chatbox-input {
    flex: 1;
    padding: 10px 15px;
    border: 1px solid #cbd5e1;
    border-radius: 20px;
    outline: none;
    font-size: 14px;
    transition: border-color 0.2s;
}
.chatbox-input:focus {
    border-color: #0ea5e9;
}
.chatbox-send-btn {
    background: #0ea5e9;
    color: white;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.2s;
}
.chatbox-send-btn:hover {
    background: #0284c7;
}
.chatbox-send-btn:disabled {
    background: #94a3b8;
    cursor: not-allowed;
}
/* Message bubbles */
.chat-msg {
    margin-bottom: 10px;
    max-width: 85%;
    clear: both;
}
.chat-msg.sent {
    float: right;
}
.chat-msg.received {
    float: left;
}
.msg-bubble {
    padding: 10px 14px;
    border-radius: 12px;
    font-size: 14px;
    line-height: 1.5;
    word-wrap: break-word;
    position: relative;
}
.chat-msg.sent .msg-bubble {
    background: #0ea5e9;
    color: white;
    border-bottom-right-radius: 2px;
}
.chat-msg.received .msg-bubble {
    background: #e2e8f0;
    color: #1e293b;
    border-bottom-left-radius: 2px;
}
.msg-star-btn {
    position: absolute;
    top: -8px;
    cursor: pointer;
    background: white;
    border-radius: 50%;
    padding: 1px 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    font-size: 0.8rem;
}
.chat-msg.sent .msg-star-btn {
    left: -10px;
}
.chat-msg.received .msg-star-btn {
    right: -10px;
}
.msg-time {
    font-size: 0.7rem;
    color: #6c757d;
    margin-top: 2px;
}
.chat-msg.sent .msg-time {
    text-align: right;
}
@media (max-width: 576px) {
    .chatbox-panel {
        width: calc(100vw - 40px);
        right: -10px;
    }
}
</style>

<script>
    let chatConversationId = null;
    let allMessages = [];
    let unreadCount = 0;
    const storageKey = 'chat_last_read_time_user_{{ auth()->id() }}';

    function getStorageLastReadTime() {
        const val = localStorage.getItem(storageKey);
        return val ? new Date(val) : new Date(0);
    }

    function updateBadgeUI() {
        const badge = document.getElementById('chat-badge');
        if (!badge) return;

        const panel = document.getElementById('chatbox-panel');
        if (panel && panel.style.display !== 'none') {
            badge.style.display = 'none';
            return;
        }

        if (unreadCount > 0) {
            badge.textContent = unreadCount > 5 ? '5+' : unreadCount;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }

    function calculateUnread(messages, lastReadTime) {
        let count = 0;
        messages.forEach(msg => {
            if (msg.sender_id != {{ auth()->id() ?? 'null' }} && new Date(msg.created_at) > lastReadTime) {
                count++;
            }
        });
        return count;
    }

    function checkUnreadOnLoad() {
        fetch('/chat/conversations')
            .then(res => res.json())
            .then(conversations => {
                const openConv = conversations.find(c => c.status === 'open');
                if (openConv) {
                    chatConversationId = openConv.id;
                    listenToChat();
                    
                    fetch('/chat/' + chatConversationId + '/messages')
                        .then(res => res.json())
                        .then(messages => {
                            allMessages = messages;
                            unreadCount = calculateUnread(allMessages, getStorageLastReadTime());
                            updateBadgeUI();
                        });
                }
            });
    }

    function toggleChatbox() {
        const panel = document.getElementById('chatbox-panel');
        if (panel.style.display === 'none') {
            panel.style.display = 'flex';
            
            localStorage.setItem(storageKey, new Date().toISOString());
            unreadCount = 0;
            updateBadgeUI();
            
            if(!chatConversationId) {
                initConversation();
            } else {
                loadMessages();
            }
            
            // Mark as read after a short delay to ensure messages are loaded
            setTimeout(() => {
                markMessagesAsRead();
            }, 500);
        } else {
            panel.style.display = 'none';
        }
    }

    function markMessagesAsRead() {
        if (chatConversationId) {
            fetch('/chat/' + chatConversationId + '/mark-as-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(() => {
                updateUnreadCount();
            })
            .catch(err => console.error('Error marking messages as read:', err));
        }
    }

    function updateFileName() {
        const fileInput = document.getElementById('chat-attachment');
        const display = document.getElementById('file-name-display');
        if(fileInput.files.length > 0) {
            display.textContent = 'Đính kèm: ' + fileInput.files[0].name;
            display.style.display = 'block';
        } else {
            display.style.display = 'none';
        }
    }

    function scrollToBottom() {
        const box = document.getElementById('chat-messages');
        box.scrollTop = box.scrollHeight;
    }

    function initConversation() {
        fetch('/chat/start', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            chatConversationId = data.id;
            loadMessages();
            listenToChat();
        });
    }

    function loadMessages() {
        if (!chatConversationId) return;
        
        fetch('/chat/'+chatConversationId+'/messages')
        .then(res => res.json())
        .then(messages => {
            allMessages = messages;
            const container = document.getElementById('chat-messages');
            container.innerHTML = '';
            messages.forEach(msg => appendMessage(msg));
            scrollToBottom();
            
            localStorage.setItem(storageKey, new Date().toISOString());
            unreadCount = 0;
            updateBadgeUI();
        });
    }

    function appendMessage(msg) {
        const container = document.getElementById('chat-messages');
        const isSent = msg.sender_id == {{ auth()->id() ?? 'null' }};
        
        const div = document.createElement('div');
        div.className = "chat-msg " + (isSent ? "sent" : "received");
        
        let contentHtml = msg.message ? "<div>"+msg.message+"</div>" : '';
        if (msg.attachment_path) {
            let isImage = msg.attachment_path.match(/\.(jpeg|jpg|gif|png)$/i) != null;
            if(isImage) {
                contentHtml += '<div class="mt-1"><a href="'+msg.attachment_path+'" target="_blank"><img src="'+msg.attachment_path+'" class="img-fluid rounded" style="max-height:150px;"></a></div>';
            } else {
                contentHtml += '<div class="mt-1"><a href="'+msg.attachment_path+'" target="_blank" class="'+(isSent ? 'text-white text-decoration-underline' : 'text-primary')+'"><i class="bi bi-file-earmark-arrow-down"></i> '+msg.attachment_name+'</a></div>';
            }
        }

        const date = new Date(msg.created_at);
        const timeStr = date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        
        let starClass = msg.is_important ? 'bi-star-fill text-warning' : 'bi-star text-muted';
        let starHtml = '<div class="msg-star-btn" onclick="toggleImportantUser('+msg.id+', this)"><i class="bi '+starClass+'"></i></div>';

        let senderLabel = '';
        if (isSent) {
            senderLabel = 'Tôi';
        } else {
            if (msg.sender) {
                senderLabel = msg.sender.name + ' (NV Hỗ trợ)';
            } else if (msg.is_admin_sender) {
                senderLabel = 'NV Hỗ trợ';
            } else {
                senderLabel = 'Khách hàng';
            }
        }
        let labelHtml = '<div class="small fw-bold mb-1 opacity-75 ' + (isSent ? 'text-white-50 text-end' : 'text-primary') + '" style="font-size: 0.7rem;">' + senderLabel + '</div>';

        div.innerHTML = '<div class="msg-bubble shadow-sm">' + starHtml + labelHtml + contentHtml + '</div><div class="msg-time">' + timeStr + '</div>';
        container.appendChild(div);
        
        // Clear float
        const clear = document.createElement('div');
        clear.style.clear = 'both';
        container.appendChild(clear);
    }

    function toggleImportantUser(msgId, btnElement) {
        let icon = btnElement.querySelector('i');
        let isCurrentlyImportant = icon.classList.contains('bi-star-fill');
        
        // Optimistic UI update
        if(isCurrentlyImportant) {
            icon.className = 'bi bi-star text-muted';
        } else {
            icon.className = 'bi bi-star-fill text-warning';
        }
        
        fetch('/chat/' + chatConversationId + '/messages/' + msgId + '/mark-important', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(r => r.json()).then(data => {
            if(data.is_important) {
                icon.className = 'bi bi-star-fill text-warning';
            } else {
                icon.className = 'bi bi-star text-muted';
            }
        }).catch(e => {
            // Revert on error
            if(isCurrentlyImportant) {
                icon.className = 'bi bi-star-fill text-warning';
            } else {
                icon.className = 'bi bi-star text-muted';
            }
        });
    }

    function sendChatMessage(e) {
        e.preventDefault();
        const input = document.getElementById('chat-message-input');
        const fileInput = document.getElementById('chat-attachment');
        
        const text = input.value.trim();
        const file = fileInput.files.length > 0 ? fileInput.files[0] : null;

        if (!text && !file) return;

        const formData = new FormData();
        if(text) formData.append('message', text);
        if(file) formData.append('attachment', file);

        document.getElementById('btn-send-msg').disabled = true;

        fetch('/chat/'+chatConversationId+'/send', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(res => res.json())
        .then(msg => {
            input.value = '';
            fileInput.value = '';
            updateFileName();
            appendMessage(msg);
            scrollToBottom();
            allMessages.push(msg);
            localStorage.setItem(storageKey, new Date().toISOString());
            unreadCount = 0;
            updateBadgeUI();
        })
        .finally(() => {
            document.getElementById('btn-send-msg').disabled = false;
        });
    }

    let isListening = false;
    function listenToChat() {
        if(window.Echo && chatConversationId && !isListening) {
            window.Echo.private('conversation.'+chatConversationId)
                .listen('MessageSent', (e) => {
                    if(e.message.sender_id != {{ auth()->id() ?? 'null' }}) {
                        allMessages.push(e.message);
                        const panel = document.getElementById('chatbox-panel');
                        if (panel.style.display === 'none') {
                            unreadCount = calculateUnread(allMessages, getStorageLastReadTime());
                            updateBadgeUI();
                        } else {
                            appendMessage(e.message);
                            scrollToBottom();
                            localStorage.setItem(storageKey, new Date().toISOString());
                            unreadCount = 0;
                            updateBadgeUI();
                        }
                    }
                });
            isListening = true;
        }
    }

    window.addEventListener('storage', (e) => {
        if (e.key === storageKey) {
            unreadCount = calculateUnread(allMessages, getStorageLastReadTime());
            updateBadgeUI();
        }
    });

    checkUnreadOnLoad();
</script>
@endauth
