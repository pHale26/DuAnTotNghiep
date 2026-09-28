@extends('layouts.master')

@section('title', 'Trung tâm Hỗ trợ - Travel Wonder')

@section('content')

<!-- Hero Section -->
<section class="support-hero">
    <div class="support-hero-overlay"></div>
    <div class="container position-relative">
        <div class="support-hero-content text-center text-white">
            <h1 class="display-4 fw-bold mb-3">Trung tâm Hỗ trợ</h1>
            <p class="lead mb-0">Chúng tôi luôn sẵn sàng hỗ trợ bạn 24/7</p>
        </div>
    </div>
</section>

<!-- Quick Contact Section -->
<section class="container py-4">
    <div class="row g-4">
        <div class="col-md-3 col-6">
            <a href="tel:1900-xxxx" class="quick-contact-card text-decoration-none">
                <div class="icon-wrapper bg-primary">
                    <i class="bi bi-telephone-fill text-white"></i>
                </div>
                <h6 class="mt-3 mb-1">Hotline</h6>
                <p class="text-muted small mb-0">1900-xxxx</p>
            </a>
        </div>
        <div class="col-md-3 col-6">
            <a href="mailto:support@travelwonder.vn" class="quick-contact-card text-decoration-none">
                <div class="icon-wrapper bg-success">
                    <i class="bi bi-envelope-fill text-white"></i>
                </div>
                <h6 class="mt-3 mb-1">Email</h6>
                <p class="text-muted small mb-0">support@travelwonder.vn</p>
            </a>
        </div>
        <div class="col-md-3 col-6">
            <a href="#" target="_blank" class="quick-contact-card text-decoration-none">
                <div class="icon-wrapper bg-info">
                    <i class="bi bi-facebook text-white"></i>
                </div>
                <h6 class="mt-3 mb-1">Facebook</h6>
                <p class="text-muted small mb-0">Chat trực tuyến</p>
            </a>
        </div>
        <div class="col-md-3 col-6">
            <a href="#" target="_blank" class="quick-contact-card text-decoration-none">
                <div class="icon-wrapper bg-warning">
                    <i class="bi bi-chat-dots-fill text-white"></i>
                </div>
                <h6 class="mt-3 mb-1">Zalo</h6>
                <p class="text-muted small mb-0">Hỗ trợ nhanh</p>
            </a>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold mb-2">Câu hỏi thường gặp</h2>
        <p class="text-muted">Tìm câu trả lời nhanh chóng cho các thắc mắc phổ biến</p>
    </div>

    <div class="row">
        <!-- Sidebar Categories -->
        <div class="col-lg-3 mb-4">
            <div class="faq-sidebar">
                <div class="faq-sidebar-header">
                    <i class="bi bi-list-ul me-2"></i>
                    <span>Trợ giúp</span>
                </div>
                <ul class="faq-category-list" id="categoryList">
                    @foreach($faqs as $index => $category)
                    <li class="faq-category-item {{ $index === 0 ? 'active' : '' }}" data-category="{{ $index }}">
                        <i class="bi {{ $category['icon'] }} me-2"></i>
                        <span>{{ $category['category'] }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- FAQ Content -->
        <div class="col-lg-9">
            @foreach($faqs as $index => $category)
            <div class="faq-content-section {{ $index === 0 ? 'active' : '' }}" data-category="{{ $index }}">
                <div class="faq-content-header">
                    <h4 class="mb-1">Trợ giúp: <strong>{{ $category['category'] }}</strong></h4>
                    <p class="text-muted mb-0">Giải đáp các thắc mắc của bạn về {{ strtolower($category['category']) }}</p>
                </div>
                
                <div class="accordion" id="accordion{{ $index }}">
                    @foreach($category['questions'] as $qIndex => $item)
                    <div class="accordion-item faq-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" 
                                    type="button" 
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#collapse{{ $index }}_{{ $qIndex }}">
                                {{ $item['question'] }}
                            </button>
                        </h2>
                        <div id="collapse{{ $index }}_{{ $qIndex }}" 
                             class="accordion-collapse collapse" 
                             data-bs-parent="#accordion{{ $index }}">
                            <div class="accordion-body">
                                {{ $item['answer'] }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>



<style>
/* Hero Section */
.support-hero {
    position: relative;
    height: 350px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    background: linear-gradient(135deg, #007CE8, #0056b3);
}

.support-hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.1);
    z-index: 1;
}

.support-hero-content {
    position: relative;
    z-index: 2;
}

/* Quick Contact Cards */
.quick-contact-card {
    display: block;
    text-align: center;
    padding: 2rem 1rem;
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}

.quick-contact-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 24px rgba(0, 124, 232, 0.15);
}

.quick-contact-card .icon-wrapper {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: 1.5rem;
}

.quick-contact-card h6 {
    color: #1f2937;
    font-weight: 600;
}

.quick-contact-card p {
    font-size: 0.875rem;
}

/* FAQ Sidebar */
.faq-sidebar {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    position: sticky;
    top: 100px;
}

.faq-sidebar-header {
    background: linear-gradient(135deg, #007CE8, #0056b3);
    color: white;
    padding: 1.25rem 1.5rem;
    font-weight: 600;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
}

.faq-category-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.faq-category-item {
    padding: 1rem 1.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
    display: flex;
    align-items: center;
    color: #4b5563;
    font-weight: 500;
}

.faq-category-item:hover {
    background: #f8f9fa;
    color: #007CE8;
}

.faq-category-item.active {
    background: #e8f4ff;
    color: #007CE8;
    border-left-color: #007CE8;
    font-weight: 600;
}

/* FAQ Content */
.faq-content-section {
    display: none;
}

.faq-content-section.active {
    display: block;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.faq-content-header {
    background: white;
    padding: 1.5rem;
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    margin-bottom: 1.5rem;
}

.faq-content-header h4 {
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.faq-content-header strong {
    color: #007CE8;
}

.faq-item {
    border: none;
    margin-bottom: 1rem;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    background: white;
}

.faq-item .accordion-button {
    background: white;
    color: #1f2937;
    font-weight: 500;
    padding: 1.25rem 1.5rem;
    border: none;
    box-shadow: none;
    font-size: 1rem;
}

.faq-item .accordion-button:not(.collapsed) {
    background: #f8f9fa;
    color: #007CE8;
    box-shadow: none;
}

.faq-item .accordion-button:focus {
    box-shadow: none;
    border: none;
}

.faq-item .accordion-button::after {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23007CE8'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
}

.faq-item .accordion-body {
    padding: 1.25rem 1.5rem;
    background: #f8f9fa;
    color: #4b5563;
    line-height: 1.8;
    border-top: 1px solid #e5e7eb;
}

/* Responsive */
@media (max-width: 991px) {
    .faq-sidebar {
        position: static;
        margin-bottom: 2rem;
    }
    
    .support-hero {
        height: 250px;
    }
    
    .support-hero-content h1 {
        font-size: 2rem;
    }
    
    .quick-contact-card {
        padding: 1.5rem 0.75rem;
    }
    
    .quick-contact-card .icon-wrapper {
        width: 50px;
        height: 50px;
        font-size: 1.25rem;
    }
}

@media (max-width: 768px) {
    .faq-category-item {
        padding: 0.875rem 1rem;
        font-size: 0.9rem;
    }
    
    .faq-sidebar-header {
        padding: 1rem 1.25rem;
        font-size: 1rem;
    }
    
    .faq-item .accordion-button {
        padding: 1rem;
        font-size: 0.95rem;
    }
    
    .faq-item .accordion-body {
        padding: 1rem;
        font-size: 0.9rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoryItems = document.querySelectorAll('.faq-category-item');
    const contentSections = document.querySelectorAll('.faq-content-section');
    
    categoryItems.forEach(item => {
        item.addEventListener('click', function() {
            const categoryIndex = this.getAttribute('data-category');
            
            // Update active state for sidebar items
            categoryItems.forEach(cat => cat.classList.remove('active'));
            this.classList.add('active');
            
            // Show corresponding content section
            contentSections.forEach(section => {
                section.classList.remove('active');
            });
            
            const targetSection = document.querySelector(`.faq-content-section[data-category="${categoryIndex}"]`);
            if (targetSection) {
                targetSection.classList.add('active');
                
                // Smooth scroll to top of content on mobile
                if (window.innerWidth < 992) {
                    targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    });
});
</script>

@endsection
