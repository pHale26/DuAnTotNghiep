<div id="ai-chatbot-widget">
    <!-- Chat Toggle Button -->
    <button id="chatbot-toggle" aria-label="Hỗ trợ AI">
        <i class="bi bi-robot"></i>
    </button>

    <!-- Chat Window -->
    <div id="chatbot-window" class="hidden">
        <div class="chatbot-header">
            <div>
                <div class="d-flex align-items-center gap-2">
                    <strong>Hỗ trợ viên AI</strong>
                    <span class="badge bg-warning text-dark" style="font-size: 9px; padding: 2px 6px; border-radius: 6px; font-weight: 600;">Beta</span>
                </div>
                <span style="display:block; font-size:11px; font-weight:normal; opacity:0.8;">Luôn sẵn sàng giải đáp 24/7</span>
            </div>
            <button id="chatbot-close" aria-label="Đóng"><i class="bi bi-x-lg"></i></button>
        </div>
        
        <!-- Thông báo tính năng đang hoàn thiện -->
        <div class="chatbot-notice">
            <i class="bi bi-exclamation-circle-fill me-1"></i>
            <span>Chức năng đang trong quá trình thử nghiệm & hoàn thiện.</span>
        </div>
        
        <div class="chatbot-body" id="chatbot-messages">
            <div class="chat-msg system-msg">
                Xin chào Quý khách! Em là trợ lý ảo của <strong>Travel Wonder</strong>. Em có thể hỗ trợ gì cho kế hoạch du lịch của Quý khách ạ?
            </div>
        </div>
        
        <div class="chatbot-footer">
            <form id="chatbot-form">
                <input type="text" id="chatbot-input" placeholder="Nhập câu hỏi của bạn..." autocomplete="off" required>
                <button type="submit" id="chatbot-send" aria-label="Gửi"><i class="bi bi-send-fill"></i></button>
            </form>
        </div>
    </div>
</div>

<style>
    /* Chatbot Styles */
    #ai-chatbot-widget {
        position: fixed;
        bottom: 100px;
        right: 20px;
        z-index: 1050;
        font-family: 'Inter', sans-serif;
    }

    #chatbot-toggle {
        width: 55px;
        height: 55px;
        border-radius: 50%;
        background: linear-gradient(135deg, #8b5cf6, #6366f1);
        color: white;
        border: none;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        transition: transform 0.3s ease;
    }

    #chatbot-toggle:hover {
        transform: scale(1.1);
    }

    #chatbot-window {
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
    }

    #chatbot-window.hidden {
        opacity: 0;
        transform: scale(0.8);
        pointer-events: none;
    }

    .chatbot-header {
        background: linear-gradient(135deg, #0ea5e9, #2563eb);
        color: white;
        padding: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 16px;
    }

    .chatbot-header button {
        background: none;
        border: none;
        color: white;
        font-size: 20px;
        cursor: pointer;
    }

    .chatbot-notice {
        background-color: #fffbeb;
        color: #92400e;
        border-bottom: 1px solid #fef3c7;
        padding: 6px 12px;
        font-size: 11.5px;
        display: flex;
        align-items: center;
        line-height: 1.3;
        font-weight: 500;
    }

    .chatbot-body {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .chat-msg {
        max-width: 85%;
        padding: 10px 14px;
        border-radius: 12px;
        font-size: 14px;
        line-height: 1.5;
        word-wrap: break-word;
    }
    
    .chat-msg p:last-child {
        margin-bottom: 0;
    }
    
    .chat-msg ul {
        padding-left: 20px;
        margin-bottom: 5px;
    }

    .chat-msg a {
        color: #2563eb;
        text-decoration: underline;
        font-weight: 600;
        word-break: break-word;
        transition: color 0.2s ease;
    }

    .chat-msg a:hover {
        color: #1d4ed8;
        text-decoration: none;
    }

    .system-msg {
        background: #e2e8f0;
        color: #1e293b;
        align-self: flex-start;
        border-bottom-left-radius: 2px;
    }

    .user-msg {
        background: #0ea5e9;
        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 2px;
    }

    .chatbot-footer {
        padding: 10px;
        background: #fff;
        border-top: 1px solid #e2e8f0;
    }

    #chatbot-form {
        display: flex;
        gap: 8px;
    }

    #chatbot-input {
        flex: 1;
        padding: 10px 15px;
        border: 1px solid #cbd5e1;
        border-radius: 20px;
        outline: none;
        font-size: 14px;
        transition: border-color 0.2s;
    }
    
    #chatbot-input:focus {
        border-color: #0ea5e9;
    }

    #chatbot-send {
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
    
    #chatbot-send:hover {
        background: #0284c7;
    }
    
    #chatbot-send:disabled {
        background: #94a3b8;
        cursor: not-allowed;
    }

    .typing-indicator {
        display: flex;
        gap: 4px;
        padding: 10px 14px;
        background: #e2e8f0;
        border-radius: 12px;
        border-bottom-left-radius: 2px;
        align-self: flex-start;
        width: fit-content;
    }

    .typing-dot {
        width: 6px;
        height: 6px;
        background: #64748b;
        border-radius: 50%;
        animation: typing 1.4s infinite ease-in-out both;
    }

    .typing-dot:nth-child(1) { animation-delay: -0.32s; }
    .typing-dot:nth-child(2) { animation-delay: -0.16s; }

    @keyframes typing {
        0%, 80%, 100% { transform: scale(0); }
        40% { transform: scale(1); }
    }
    
    /* Responsive */
    @media (max-width: 576px) {
        #chatbot-window {
            width: calc(100vw - 40px);
            right: -10px;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('chatbot-toggle');
    const closeBtn = document.getElementById('chatbot-close');
    const chatWindow = document.getElementById('chatbot-window');
    const messagesContainer = document.getElementById('chatbot-messages');
    const chatForm = document.getElementById('chatbot-form');
    const chatInput = document.getElementById('chatbot-input');
    const sendBtn = document.getElementById('chatbot-send');

    // Toggle Chat Window
    toggleBtn.addEventListener('click', () => {
        chatWindow.classList.toggle('hidden');
        if (!chatWindow.classList.contains('hidden')) {
            chatInput.focus();
            scrollToBottom();
            if (messagesContainer.children.length <= 1) {
                loadHistory();
            }
        }
    });

    closeBtn.addEventListener('click', () => {
        chatWindow.classList.add('hidden');
    });

    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function appendMessage(role, content) {
        const msgDiv = document.createElement('div');
        msgDiv.className = `chat-msg ${role === 'user' ? 'user-msg' : 'system-msg'}`;
        
        if (role === 'assistant') {
            msgDiv.innerHTML = marked.parse(content);
            msgDiv.querySelectorAll('a').forEach(link => {
                link.setAttribute('target', '_blank');
                link.setAttribute('rel', 'noopener noreferrer');
            });
        } else {
            msgDiv.textContent = content;
        }
        
        messagesContainer.appendChild(msgDiv);
        scrollToBottom();
    }

    function showTyping() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'typing-indicator';
        typingDiv.id = 'typing-indicator';
        typingDiv.innerHTML = '<div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div>';
        messagesContainer.appendChild(typingDiv);
        scrollToBottom();
    }

    function removeTyping() {
        const typingDiv = document.getElementById('typing-indicator');
        if (typingDiv) typingDiv.remove();
    }

    async function loadHistory() {
        try {
            const res = await fetch('{{ route("chatbot.history") }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await res.json();
            if (data.messages && data.messages.length > 0) {
                data.messages.forEach(msg => {
                    appendMessage(msg.role, msg.content);
                });
            }
        } catch (e) {
            console.error("Failed to load chat history", e);
        }
    }

    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const msgText = chatInput.value.trim();
        if (!msgText) return;

        // Reset input
        chatInput.value = '';
        chatInput.disabled = true;
        sendBtn.disabled = true;

        // Show user msg
        appendMessage('user', msgText);
        showTyping();

        try {
            const res = await fetch('{{ route("chatbot.send") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message: msgText })
            });

            removeTyping();
            if (res.ok) {
                const data = await res.json();
                appendMessage('assistant', data.content);
            } else {
                appendMessage('assistant', 'Xin lỗi, đã có lỗi xảy ra khi kết nối máy chủ.');
            }
        } catch (e) {
            removeTyping();
            appendMessage('assistant', 'Lỗi mạng, vui lòng kiểm tra kết nối.');
        } finally {
            chatInput.disabled = false;
            sendBtn.disabled = false;
            chatInput.focus();
        }
    });
});
</script>
