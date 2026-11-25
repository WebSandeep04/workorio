/* ==========================================================================
   Workorio — Global Nexora Searchable Dropdown Auto-Enhancer
   Automatically upgrades <select class="form-select"> across all pages
   ========================================================================== */
(function (window, document) {
    'use strict';

    const instances = new WeakMap();

    function shouldEnhance(sel) {
        if (!sel || sel.tagName !== 'SELECT') return false;
        if (sel.dataset.nxEnhanced === '1') return false;
        if (sel.dataset.nxSelect === 'false') return false;
        if (sel.multiple || (sel.size && sel.size > 1)) return false;
        if (sel.classList.contains('no-nx-select')) return false;
        if (sel.classList.contains('select2-hidden-accessible')) return false;
        if (sel.classList.contains('d-none')) return false;
        if (sel.closest('.dataTables_length')) return false;
        if (sel.closest('td')) return false;
        if (sel.closest('.nx-select')) return false;
        if (sel.closest('.nx-auto-select')) return false;
        if (!sel.classList.contains('form-select') && !sel.classList.contains('form-control')) return false;
        return true;
    }

    function escapeHtml(str) {
        return String(str == null ? '' : str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    let activeApi = null;

    function closeAll(exceptApi) {
        if (activeApi && activeApi !== exceptApi) {
            activeApi.close();
        }
    }

    function enhanceSelect(sel) {
        if (!shouldEnhance(sel)) return null;
        sel.dataset.nxEnhanced = '1';

        const wrap = document.createElement('div');
        wrap.className = 'nx-auto-select';
        if (sel.classList.contains('form-select-sm') || sel.classList.contains('form-control-sm') || sel.closest('.filterBox')) wrap.classList.add('nx-sm');
        if (sel.classList.contains('form-select-lg')) wrap.classList.add('nx-lg');

        // Insert wrapper right after the native select, then move native select inside wrapper
        sel.parentNode.insertBefore(wrap, sel);
        sel.classList.add('nx-native-hidden');
        wrap.appendChild(sel);

        const trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.className = 'nx-auto-trigger';
        trigger.setAttribute('aria-haspopup', 'listbox');
        trigger.setAttribute('aria-expanded', 'false');
        trigger.innerHTML =
            '<span class="nx-auto-val is-placeholder">Select</span>' +
            '<span class="nx-auto-icons">' +
                '<span class="nx-auto-clear" title="Clear"><i class="bi bi-x"></i></span>' +
                '<i class="bi bi-chevron-down nx-auto-chev"></i>' +
            '</span>';

        const menu = document.createElement('div');
        menu.className = 'nx-auto-menu';
        menu.setAttribute('role', 'listbox');
        menu.innerHTML =
            '<div class="nx-auto-search-wrap">' +
                '<i class="bi bi-search"></i>' +
                '<input type="text" class="nx-auto-search" placeholder="Search..." autocomplete="off">' +
            '</div>' +
            '<div class="nx-auto-list"></div>';

        wrap.appendChild(trigger);
        const portalHost = sel.closest('.modal') || document.body;
        portalHost.appendChild(menu);

        const valEl = trigger.querySelector('.nx-auto-val');
        const clearEl = trigger.querySelector('.nx-auto-clear');
        const searchInput = menu.querySelector('.nx-auto-search');
        const listEl = menu.querySelector('.nx-auto-list');

        let focusedIdx = -1;

        function getPlaceholderText() {
            const emptyOpt = Array.from(sel.options).find(function (o) { return o.value === ''; });
            return emptyOpt ? emptyOpt.textContent.trim() : 'Select';
        }

        function hasEmptyOption() {
            return Array.from(sel.options).some(function (o) { return o.value === ''; });
        }

        function syncLabel() {
            trigger.disabled = !!sel.disabled;
            wrap.classList.toggle('has-clear', hasEmptyOption() && !sel.disabled);

            const selectedOpt = sel.options[sel.selectedIndex];
            const val = sel.value;
            if (selectedOpt && val !== '') {
                valEl.textContent = selectedOpt.textContent.trim();
                valEl.classList.remove('is-placeholder');
                wrap.classList.add('has-value');
            } else {
                valEl.textContent = getPlaceholderText();
                valEl.classList.add('is-placeholder');
                wrap.classList.remove('has-value');
            }
        }

        function renderOptions(filterText) {
            const q = (filterText || '').trim().toLowerCase();
            const currentVal = sel.value;
            focusedIdx = -1;

            const opts = Array.from(sel.options).filter(function (o) {
                const txt = (o.textContent || '').trim();
                if (!txt) return false;
                if (o.value === '' && q !== '') return false;
                if (q && txt.toLowerCase().indexOf(q) === -1) return false;
                return true;
            });

            if (!opts.length) {
                listEl.innerHTML = '<div class="nx-auto-empty">No results found</div>';
                return;
            }

            let html = '';
            opts.forEach(function (o) {
                const isActive = o.value === currentVal && o.value !== '';
                const disabledAttr = o.disabled ? ' disabled' : '';
                html += '<button type="button" class="nx-auto-item' + (isActive ? ' is-active' : '') + '" data-val="' + escapeHtml(o.value) + '"' + disabledAttr + '>' +
                    '<span class="nx-auto-item-text">' + escapeHtml(o.textContent.trim()) + '</span>' +
                    '<i class="bi bi-check2 nx-auto-check"></i>' +
                '</button>';
            });
            listEl.innerHTML = html;
        }

        function positionMenu() {
            const rect = trigger.getBoundingClientRect();
            const width = Math.max(rect.width, 190);
            menu.style.width = Math.round(width) + 'px';

            const menuH = Math.min(menu.scrollHeight || 280, 290);
            const spaceBelow = window.innerHeight - rect.bottom;
            const spaceAbove = rect.top;

            let left = rect.left;
            if (left + width > window.innerWidth - 12) {
                left = Math.max(12, window.innerWidth - width - 12);
            }

            let top = rect.bottom + 6;
            if (spaceBelow < menuH + 12 && spaceAbove > spaceBelow) {
                top = Math.max(12, rect.top - menuH - 6);
            }

            menu.style.left = Math.round(left) + 'px';
            menu.style.top = Math.round(top) + 'px';
        }

        function openMenu() {
            if (sel.disabled) return;
            closeAll(api);
            renderOptions(searchInput.value);

            wrap.classList.add('open');
            menu.classList.add('show');
            trigger.setAttribute('aria-expanded', 'true');
            positionMenu();
            activeApi = api;

            setTimeout(function () {
                searchInput.focus();
            }, 20);
        }

        function closeMenu() {
            wrap.classList.remove('open');
            menu.classList.remove('show');
            trigger.setAttribute('aria-expanded', 'false');
            searchInput.value = '';
            if (activeApi === api) activeApi = null;
        }

        function selectValue(val) {
            if (sel.value !== val) {
                sel.value = val;
                sel.dispatchEvent(new Event('change', { bubbles: true }));
                if (window.jQuery) {
                    window.jQuery(sel).trigger('change');
                }
            }
            syncLabel();
            closeMenu();
        }

        trigger.addEventListener('click', function (e) {
            if (e.target.closest('.nx-auto-clear')) return;
            if (wrap.classList.contains('open')) {
                closeMenu();
            } else {
                openMenu();
            }
        });

        clearEl.addEventListener('click', function (e) {
            e.stopPropagation();
            selectValue('');
        });

        searchInput.addEventListener('input', function () {
            renderOptions(searchInput.value);
        });

        searchInput.addEventListener('keydown', function (e) {
            const items = listEl.querySelectorAll('.nx-auto-item:not(:disabled)');
            if (e.key === 'Escape') {
                closeMenu();
                trigger.focus();
                return;
            }
            if (!items.length) return;
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                focusedIdx = Math.min(focusedIdx + 1, items.length - 1);
                items.forEach(function (el) { el.classList.remove('is-focused'); });
                items[focusedIdx].classList.add('is-focused');
                items[focusedIdx].scrollIntoView({ block: 'nearest' });
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                focusedIdx = Math.max(focusedIdx - 1, 0);
                items.forEach(function (el) { el.classList.remove('is-focused'); });
                items[focusedIdx].classList.add('is-focused');
                items[focusedIdx].scrollIntoView({ block: 'nearest' });
            } else if (e.key === 'Enter') {
                e.preventDefault();
                const idx = focusedIdx >= 0 ? focusedIdx : 0;
                if (items[idx]) items[idx].click();
            }
        });

        listEl.addEventListener('click', function (e) {
            const btn = e.target.closest('.nx-auto-item');
            if (!btn || btn.disabled) return;
            selectValue(btn.getAttribute('data-val') || '');
        });

        sel.addEventListener('change', syncLabel);

        // Watch for AJAX-populated <option>s or disabled attribute changes
        const optObserver = new MutationObserver(function () {
            syncLabel();
            if (wrap.classList.contains('open')) {
                renderOptions(searchInput.value);
            }
        });
        optObserver.observe(sel, { childList: true, subtree: true, attributes: true, attributeFilter: ['disabled'] });

        syncLabel();
        const api = { sync: syncLabel, open: openMenu, close: closeMenu, position: positionMenu, wrap: wrap, menu: menu };
        instances.set(sel, api);
        return api;
    }

    function enhanceAll(root) {
        const scope = root || document;
        if (scope.querySelectorAll) {
            scope.querySelectorAll('select.form-select, select.form-control').forEach(enhanceSelect);
        }
    }

    // Hook jQuery .val() so programmatic $('#state').val(id) updates the UI immediately
    function hookJQueryVal() {
        if (!window.jQuery || !window.jQuery.valHooks) return;
        const origHook = window.jQuery.valHooks.select || {};
        const origSet = origHook.set;
        window.jQuery.valHooks.select = Object.assign({}, origHook, {
            set: function (elem, value) {
                const res = origSet ? origSet.call(this, elem, value) : undefined;
                if (origSet === undefined) {
                    elem.value = value == null ? '' : value;
                }
                const api = instances.get(elem);
                if (api) api.sync();
                return res;
            }
        });
    }

    document.addEventListener('click', function (e) {
        if (!activeApi) return;
        if (activeApi.wrap.contains(e.target) || activeApi.menu.contains(e.target)) return;
        activeApi.close();
    });

    window.addEventListener('scroll', function (e) {
        if (!activeApi) return;
        if (e.target && activeApi.menu.contains(e.target)) return;
        activeApi.position();
    }, true);

    window.addEventListener('resize', function () {
        if (activeApi) activeApi.close();
    });

    document.addEventListener('reset', function (e) {
        const form = e.target;
        if (!form || !form.querySelectorAll) return;
        setTimeout(function () {
            form.querySelectorAll('select.nx-native-hidden').forEach(function (sel) {
                const api = instances.get(sel);
                if (api) api.sync();
            });
        }, 0);
    });

    function init() {
        hookJQueryVal();
        enhanceAll(document);

        // Auto-enhance dynamically added selects (e.g. AJAX-rendered modals/forms)
        const bodyObserver = new MutationObserver(function (mutations) {
            mutations.forEach(function (m) {
                m.addedNodes.forEach(function (node) {
                    if (node.nodeType !== 1) return;
                    if (node.tagName === 'SELECT') {
                        enhanceSelect(node);
                    } else if (node.querySelectorAll) {
                        enhanceAll(node);
                    }
                });
            });
        });
        bodyObserver.observe(document.body, { childList: true, subtree: true });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    window.NexoraSelect = {
        enhance: enhanceSelect,
        enhanceAll: enhanceAll,
        sync: function (el) {
            const api = instances.get(el);
            if (api) api.sync();
        }
    };
})(window, document);
