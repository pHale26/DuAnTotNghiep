// Ghi đè hàm addEventListener toàn cục để xử lý DOMContentLoaded cho PJAX
(function() {
    const originalAddEventListener = document.addEventListener;
    document.addEventListener = function(type, listener, options) {
        if (type === 'DOMContentLoaded' && (document.readyState === 'complete' || document.readyState === 'interactive')) {
            // Trang đã tải xong (PJAX inject HTML), thực thi script ngay lập tức
            setTimeout(listener, 1);
            return;
        }
        originalAddEventListener.call(document, type, listener, options);
    };
})();

document.addEventListener('DOMContentLoaded', () => {
    // Top progress loading bar
    const showProgressBar = () => {
        let bar = document.getElementById('pjax-loading-bar');
        if (!bar) {
            bar = document.createElement('div');
            bar.id = 'pjax-loading-bar';
            bar.style.position = 'fixed';
            bar.style.top = '0';
            bar.style.left = '0';
            bar.style.height = '3px';
            bar.style.backgroundColor = '#007CE8'; // Admin primary color
            bar.style.zIndex = '9999';
            bar.style.transition = 'width 0.4s ease, opacity 0.4s ease';
            bar.style.width = '0%';
            document.body.appendChild(bar);
        }
        bar.style.width = '10%';
        bar.style.opacity = '1';
        
        if (window.pjaxInterval) clearInterval(window.pjaxInterval);
        window.pjaxInterval = setInterval(() => {
            let width = parseFloat(bar.style.width);
            if (width < 90) {
                bar.style.width = (width + (90 - width) * 0.15) + '%';
            }
        }, 100);
    };

    const hideProgressBar = () => {
        if (window.pjaxInterval) clearInterval(window.pjaxInterval);
        let bar = document.getElementById('pjax-loading-bar');
        if (bar) {
            bar.style.width = '100%';
            setTimeout(() => {
                bar.style.opacity = '0';
                setTimeout(() => {
                    bar.style.width = '0%';
                }, 400);
            }, 200);
        }
    };

    // Execute scripts dynamically
    const executeScripts = (container) => {
        container.querySelectorAll('script').forEach(oldScript => {
            const newScript = document.createElement('script');
            Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
            newScript.appendChild(document.createTextNode(oldScript.innerHTML));
            oldScript.parentNode.replaceChild(newScript, oldScript);
        });
    };

    // Main PJAX load function
    const loadPage = (url, pushToHistory = true) => {
        showProgressBar();

        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error('Response status not OK');
                return response.text();
            })
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // 1. Update Title
                document.title = doc.title;

                // 2. Replace Sidebar (inner content to preserve element references)
                const newSidebar = doc.querySelector('.sidebar');
                const oldSidebar = document.querySelector('.sidebar');
                if (newSidebar && oldSidebar) {
                    oldSidebar.innerHTML = newSidebar.innerHTML;
                }

                // 3. Replace Main Content (inner content to preserve references)
                const newMainContent = doc.querySelector('.main-content');
                const oldMainContent = document.querySelector('.main-content');
                if (newMainContent && oldMainContent) {
                    oldMainContent.innerHTML = newMainContent.innerHTML;
                }

                // 4. Update URL history
                if (pushToHistory) {
                    window.history.pushState({ url }, doc.title, url);
                }

                // 5. Re-execute scripts to trigger chart.js or other initializers
                if (oldMainContent) {
                    executeScripts(oldMainContent);
                }

                // 6. Re-initialize Alpine.js components in new content
                if (typeof Alpine !== 'undefined' && oldMainContent) {
                    Alpine.initTree(oldMainContent);
                }

                // 7. Re-initialize Bootstrap components if needed
                if (typeof bootstrap !== 'undefined') {
                    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                    [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
                    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
                    [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
                }

                // 8. Re-bind click event listeners to new elements
                setupPjax();
                hideProgressBar();
            })
            .catch(error => {
                console.error('PJAX Navigation Failed:', error);
                hideProgressBar();
                // Fallback to normal browser redirection
                if (pushToHistory) {
                    window.location.href = url;
                }
            });
    };

    // Intercept clicks on links
    const setupPjax = () => {
        document.querySelectorAll('a').forEach(link => {
            // Check if link is eligible for PJAX
            const url = link.href;
            if (
                url &&
                url.startsWith(window.location.origin) &&
                !link.getAttribute('target') &&
                !link.href.includes('logout') &&
                !link.href.includes('#') &&
                !link.closest('form') &&
                !link.hasAttribute('onclick') &&
                !link.classList.contains('no-pjax')
            ) {
                // Remove existing event listener if any to avoid duplication
                link.removeEventListener('click', linkClickEventHandler);
                link.addEventListener('click', linkClickEventHandler);
            }
        });
    };

    const linkClickEventHandler = (e) => {
        // Only trigger on left clicks without modifier keys
        if (e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
        
        e.preventDefault();
        const url = e.currentTarget.href;
        loadPage(url);
    };

    // Watch for back/forward navigation
    window.addEventListener('popstate', (e) => {
        if (e.state && e.state.url) {
            loadPage(e.state.url, false);
        } else {
            // Default reload if no state exists
            window.location.reload();
        }
    });

    // Initialize on first page load
    // Store current state in history so popstate knows where to go back
    window.history.replaceState({ url: window.location.href }, document.title, window.location.href);
    setupPjax();
});
