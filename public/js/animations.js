document.addEventListener('DOMContentLoaded', () => {
    // 1. Scroll Reveal Animation using IntersectionObserver
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.15
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
                observer.unobserve(entry.target); // Stop observing once revealed
            }
        });
    }, observerOptions);

    // Select all elements with reveal-up class
    const revealElements = document.querySelectorAll('.reveal-up');
    revealElements.forEach((el, index) => {
        // Optional: add slight staggered delay based on DOM order
        el.style.transitionDelay = `${(index % 4) * 0.1}s`;
        observer.observe(el);
    });

    // 2. Navbar Scroll Effect
    const navbar = document.querySelector('.navbar-premium');
    if (navbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }
});
