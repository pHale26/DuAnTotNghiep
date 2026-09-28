/**
 * Favorite Handler - Xử lý lưu/bỏ tour yêu thích không reload trang
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeFavoriteHandlers();
});

function initializeFavoriteHandlers() {
    // Xử lý tất cả các form có class 'favorite-form' (toggle)
    document.querySelectorAll('.favorite-form').forEach(form => {
        form.addEventListener('submit', handleFavoriteSubmit);
    });

    // Xử lý tất cả các form có class 'favorite-form-delete' (xóa trong trang favorites)
    document.querySelectorAll('.favorite-form-delete').forEach(form => {
        form.addEventListener('submit', handleFavoriteSubmit);
    });
}

async function handleFavoriteSubmit(e) {
    e.preventDefault();
    e.stopPropagation();

    const form = e.currentTarget;
    const actionUrl = form.getAttribute('action');
    const sameTourForms = document.querySelectorAll(`form[action="${actionUrl}"]`);
    const sameTourButtons = [];
    
    sameTourForms.forEach(tForm => {
        const btn = tForm.querySelector('button[type="submit"]');
        if (btn) {
            sameTourButtons.push(btn);
        }
    });

    // Lưu trạng thái ban đầu
    const button = form.querySelector('button[type="submit"]');
    const wasActive = button ? button.classList.contains('active') : false;
    
    // Disable tất cả các nút cùng tour để tránh double click
    sameTourButtons.forEach(btn => btn.disabled = true);

    try {
        const response = await fetch(actionUrl, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: new FormData(form)
        });

        const data = await response.json();

        if (data.success) {
            // Toggle favorite state
            if (typeof data.is_favorite !== 'undefined') {
                // Đây là toggle action - cập nhật tất cả các nút của cùng tour
                sameTourButtons.forEach(btn => {
                    const icon = btn.querySelector('i');
                    if (data.is_favorite) {
                        btn.classList.add('active');
                        if (icon) {
                            icon.classList.remove('bi-heart');
                            icon.classList.add('bi-heart-fill');
                        }
                    } else {
                        btn.classList.remove('active');
                        if (icon) {
                            icon.classList.remove('bi-heart-fill');
                            icon.classList.add('bi-heart');
                        }
                    }
                });
            } else {
                // Đây là destroy action (từ trang favorites)
                // Ẩn card tour với animation
                const card = form.closest('.col-md-4, .col-lg-3, .col-12');
                if (card) {
                    card.style.transition = 'opacity 0.3s, transform 0.3s';
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.9)';
                    
                    setTimeout(() => {
                        card.remove();
                        
                        // Kiểm tra nếu không còn tour nào, hiển thị thông báo
                        const container = document.querySelector('.row');
                        if (container && container.children.length === 0) {
                            container.innerHTML = `
                                <div class="col-12 text-center py-5">
                                    <h4>Bạn chưa lưu tour nào</h4>
                                    <p class="text-muted">Hãy bấm vào trái tim ở card tour để lưu tour yêu thích.</p>
                                    <a href="/tours" class="btn btn-primary">Xem tour</a>
                                </div>
                            `;
                        }
                    }, 300);
                }
            }

            // Hiển thị toast notification
            showToast(data.message, 'success');
        } else {
            // Khôi phục trạng thái nếu có lỗi
            if (wasActive) {
                sameTourButtons.forEach(btn => btn.classList.add('active'));
            }
            showToast('Có lỗi xảy ra. Vui lòng thử lại!', 'error');
        }
    } catch (error) {
        console.error('Favorite error:', error);
        // Khôi phục trạng thái
        if (wasActive) {
            sameTourButtons.forEach(btn => btn.classList.add('active'));
        }
        showToast('Có lỗi xảy ra. Vui lòng thử lại!', 'error');
    } finally {
        // Enable lại tất cả các nút
        sameTourButtons.forEach(btn => btn.disabled = false);
    }
}

// Toast notification helper
function showToast(message, type = 'success') {
    // Kiểm tra nếu đã có toast container, nếu không thì tạo mới
    let toastContainer = document.getElementById('favorite-toast-container');
    
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'favorite-toast-container';
        toastContainer.className = 'position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }

    // Tạo toast element
    const toastId = 'toast-' + Date.now();
    const bgClass = type === 'success' ? 'bg-success' : 'bg-danger';
    const iconClass = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';

    const toastHTML = `
        <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi ${iconClass} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;

    toastContainer.insertAdjacentHTML('beforeend', toastHTML);

    // Khởi tạo và hiển thị toast
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, {
        autohide: true,
        delay: 3000
    });
    
    toast.show();

    // Xóa toast element sau khi ẩn
    toastElement.addEventListener('hidden.bs.toast', function() {
        toastElement.remove();
    });
}

// Export function để có thể gọi từ nơi khác nếu cần
window.initializeFavoriteHandlers = initializeFavoriteHandlers;
