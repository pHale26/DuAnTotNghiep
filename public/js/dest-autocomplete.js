/**
 * Destination Autocomplete Component
 * Real-time filtering by each keystroke, accent-sensitive, case-insensitive
 */
(function () {
    'use strict';

    const API_URL = '/api/destinations/search';

    /**
     * Initialize a destination autocomplete on a given input element.
     * @param {HTMLInputElement} input
     */
    function initAutocomplete(input) {
        // Create wrapper (make input's parent relative if not already)
        const wrapper = input.closest('.autocomplete-wrapper') || input.parentElement;
        wrapper.style.position = 'relative';

        // Create dropdown
        const dropdown = document.createElement('ul');
        dropdown.className = 'dest-autocomplete-dropdown';
        dropdown.setAttribute('role', 'listbox');
        wrapper.appendChild(dropdown);

        let currentFocus = -1;
        let debounceTimer = null;
        let lastQuery = '';

        // ------- Styles (injected once) -------
        if (!document.getElementById('dest-autocomplete-style')) {
            const style = document.createElement('style');
            style.id = 'dest-autocomplete-style';
            style.textContent = `
                .dest-autocomplete-dropdown {
                    position: absolute;
                    top: calc(100% + 4px);
                    left: 0;
                    right: 0;
                    z-index: 9999;
                    background: #fff;
                    border: 1px solid #e2e8f0;
                    border-radius: 12px;
                    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
                    max-height: 280px;
                    overflow-y: auto;
                    padding: 6px 0;
                    margin: 0;
                    list-style: none;
                    display: none;
                    animation: fadeSlideDown 0.15s ease;
                }
                @keyframes fadeSlideDown {
                    from { opacity: 0; transform: translateY(-6px); }
                    to   { opacity: 1; transform: translateY(0); }
                }
                .dest-autocomplete-dropdown li {
                    padding: 10px 16px;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    font-size: 0.9rem;
                    color: #374151;
                    transition: background 0.1s;
                    border-radius: 0;
                }
                .dest-autocomplete-dropdown li:hover,
                .dest-autocomplete-dropdown li.active {
                    background: #eff6ff;
                    color: #1677ff;
                }
                .dest-autocomplete-dropdown li .dest-icon {
                    color: #1677ff;
                    font-size: 1rem;
                    flex-shrink: 0;
                }
                .dest-autocomplete-dropdown li .dest-name mark {
                    background: transparent;
                    color: #1677ff;
                    font-weight: 700;
                    padding: 0;
                }
                .dest-autocomplete-dropdown .dest-empty {
                    padding: 12px 16px;
                    color: #9ca3af;
                    font-size: 0.85rem;
                    text-align: center;
                    cursor: default;
                }
                .dest-autocomplete-dropdown .dest-empty:hover {
                    background: none;
                    color: #9ca3af;
                }
                .dest-autocomplete-dropdown::-webkit-scrollbar { width: 4px; }
                .dest-autocomplete-dropdown::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
                .dest-autocomplete-dropdown::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
            `;
            document.head.appendChild(style);
        }

        // ------- Helpers -------
        function highlightMatch(text, query) {
            if (!query) return escapeHtml(text);
            // Build regex that handles diacritics by escaping the query
            try {
                const escaped = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                const regex = new RegExp('(' + escaped + ')', 'gi');
                return escapeHtml(text).replace(regex, '<mark>$1</mark>');
            } catch (e) {
                return escapeHtml(text);
            }
        }

        function escapeHtml(str) {
            return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        function showDropdown() {
            dropdown.style.display = 'block';
        }

        function hideDropdown() {
            dropdown.style.display = 'none';
            currentFocus = -1;
        }

        function setActiveItem(index) {
            const items = dropdown.querySelectorAll('li:not(.dest-empty)');
            items.forEach((el, i) => el.classList.toggle('active', i === index));
            currentFocus = index;
        }

        function renderResults(destinations, query) {
            dropdown.innerHTML = '';
            if (destinations.length === 0) {
                dropdown.innerHTML = '<li class="dest-empty">Không tìm thấy địa điểm nào</li>';
                showDropdown();
                return;
            }
            destinations.forEach(function (dest) {
                const li = document.createElement('li');
                li.setAttribute('role', 'option');
                li.innerHTML = `
                    <span class="dest-icon"><i class="bi bi-geo-alt-fill"></i></span>
                    <span class="dest-name">${highlightMatch(dest.name, query)}</span>
                `;
                li.addEventListener('mousedown', function (e) {
                    e.preventDefault(); // prevent blur before click
                    input.value = dest.name;
                    hideDropdown();
                    // Auto submit the parent form
                    const form = input.closest('form');
                    if (form) form.submit();
                });
                dropdown.appendChild(li);
            });
            showDropdown();
        }

        // ------- Fetch -------
        function fetchDestinations(query) {
            if (query === lastQuery) return;
            lastQuery = query;

            if (query.length < 1) {
                hideDropdown();
                return;
            }

            fetch(API_URL + '?q=' + encodeURIComponent(query), {
                headers: { 'Accept': 'application/json' }
            })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    renderResults(data, query);
                })
                .catch(function () {
                    hideDropdown();
                });
        }

        // ------- Events -------
        input.setAttribute('autocomplete', 'off');
        input.removeAttribute('list'); // remove old datalist connection

        input.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            const query = input.value.trim();
            if (!query) {
                hideDropdown();
                lastQuery = '';
                return;
            }
            debounceTimer = setTimeout(function () {
                fetchDestinations(query);
            }, 200);
        });

        input.addEventListener('keydown', function (e) {
            const items = dropdown.querySelectorAll('li:not(.dest-empty)');
            if (dropdown.style.display === 'none') return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                setActiveItem(Math.min(currentFocus + 1, items.length - 1));
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                setActiveItem(Math.max(currentFocus - 1, 0));
            } else if (e.key === 'Enter' && currentFocus >= 0 && items[currentFocus]) {
                e.preventDefault();
                items[currentFocus].dispatchEvent(new Event('mousedown'));
            } else if (e.key === 'Escape') {
                hideDropdown();
            }
        });

        input.addEventListener('blur', function () {
            // Short delay to allow mousedown on item to fire first
            setTimeout(hideDropdown, 150);
        });

        input.addEventListener('focus', function () {
            if (input.value.trim().length >= 1) {
                fetchDestinations(input.value.trim());
            }
        });
    }

    // ------- Boot: initialize all destination inputs -------
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-dest-autocomplete]').forEach(initAutocomplete);
    });
})();
