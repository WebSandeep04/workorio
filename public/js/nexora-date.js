/* ==========================================================================
   Workorio — Global Nexora Rounded Date Picker Auto-Enhancer
   Automatically upgrades <input type="date"> across all pages
   ========================================================================== */
(function (window, document) {
    'use strict';

    const MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    const SHORT_MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const instances = new WeakMap();

    let popover = null;
    let activeInput = null;
    let activeWrap = null;
    let viewYear = new Date().getFullYear();
    let viewMonth = new Date().getMonth();

    function pad2(n) {
        return String(n).padStart(2, '0');
    }

    function toIso(y, m, d) {
        return y + '-' + pad2(m + 1) + '-' + pad2(d);
    }

    function parseIso(str) {
        if (!str || !/^\d{4}-\d{2}-\d{2}$/.test(str)) return null;
        const parts = str.split('-').map(Number);
        const dt = new Date(parts[0], parts[1] - 1, parts[2]);
        if (dt.getFullYear() !== parts[0] || dt.getMonth() !== parts[1] - 1 || dt.getDate() !== parts[2]) return null;
        return dt;
    }

    function formatDisplay(iso) {
        const dt = parseIso(iso);
        if (!dt) return '';
        return pad2(dt.getDate()) + ' ' + SHORT_MONTHS[dt.getMonth()] + ' ' + dt.getFullYear();
    }

    function shouldEnhance(inp) {
        if (!inp || inp.tagName !== 'INPUT' || inp.type !== 'date') return false;
        if (inp.dataset.nxDateEnhanced === '1') return false;
        if (inp.dataset.nxDate === 'false') return false;
        if (inp.classList.contains('no-nx-date')) return false;
        if (inp.classList.contains('d-none')) return false;
        if (inp.closest('td')) return false;
        if (inp.closest('.nx-date-wrap')) return false;
        return true;
    }

    function ensurePopover() {
        if (popover) return popover;
        popover = document.createElement('div');
        popover.className = 'nx-date-popover';
        popover.setAttribute('role', 'dialog');
        popover.innerHTML =
            '<div class="nx-dp-head">' +
                '<button type="button" class="nx-dp-nav" data-dir="-1" aria-label="Previous month"><i class="bi bi-chevron-left"></i></button>' +
                '<div class="nx-dp-title">' +
                    '<select class="nx-dp-select" data-nx-select="false" id="nxDpMonth"></select>' +
                    '<select class="nx-dp-select" data-nx-select="false" id="nxDpYear"></select>' +
                '</div>' +
                '<button type="button" class="nx-dp-nav" data-dir="1" aria-label="Next month"><i class="bi bi-chevron-right"></i></button>' +
            '</div>' +
            '<div class="nx-dp-weekdays"><span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span></div>' +
            '<div class="nx-dp-days" id="nxDpDays"></div>' +
            '<div class="nx-dp-foot">' +
                '<button type="button" class="nx-dp-foot-btn is-clear" id="nxDpClear">Clear</button>' +
                '<button type="button" class="nx-dp-foot-btn is-today-btn" id="nxDpToday">Today</button>' +
            '</div>';

        document.body.appendChild(popover);

        const monthSel = popover.querySelector('#nxDpMonth');
        MONTHS.forEach(function (mName, idx) {
            const opt = document.createElement('option');
            opt.value = idx;
            opt.textContent = mName;
            monthSel.appendChild(opt);
        });

        const yearSel = popover.querySelector('#nxDpYear');
        const curY = new Date().getFullYear();
        for (let y = curY - 70; y <= curY + 15; y++) {
            const opt = document.createElement('option');
            opt.value = y;
            opt.textContent = y;
            yearSel.appendChild(opt);
        }

        popover.querySelectorAll('.nx-dp-nav').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const dir = Number(btn.getAttribute('data-dir'));
                viewMonth += dir;
                if (viewMonth < 0) { viewMonth = 11; viewYear--; }
                if (viewMonth > 11) { viewMonth = 0; viewYear++; }
                renderCalendar();
            });
        });

        monthSel.addEventListener('change', function () {
            viewMonth = Number(monthSel.value);
            renderCalendar();
        });

        yearSel.addEventListener('change', function () {
            viewYear = Number(yearSel.value);
            renderCalendar();
        });

        popover.querySelector('#nxDpClear').addEventListener('click', function () {
            if (activeInput) applyValue(activeInput, '');
            closePopover();
        });

        popover.querySelector('#nxDpToday').addEventListener('click', function () {
            const now = new Date();
            const iso = toIso(now.getFullYear(), now.getMonth(), now.getDate());
            if (activeInput) applyValue(activeInput, iso);
            closePopover();
        });

        popover.querySelector('#nxDpDays').addEventListener('click', function (e) {
            const btn = e.target.closest('.nx-dp-day');
            if (!btn || btn.disabled) return;
            const iso = btn.getAttribute('data-iso');
            if (activeInput && iso) applyValue(activeInput, iso);
            closePopover();
        });

        return popover;
    }

    function applyValue(inp, iso) {
        if (inp.value !== iso) {
            inp.value = iso;
            inp.dispatchEvent(new Event('input', { bubbles: true }));
            inp.dispatchEvent(new Event('change', { bubbles: true }));
            if (window.jQuery) {
                window.jQuery(inp).trigger('change');
            }
        }
        const api = instances.get(inp);
        if (api) api.sync();
    }

    function renderCalendar() {
        const pop = ensurePopover();
        const monthSel = pop.querySelector('#nxDpMonth');
        const yearSel = pop.querySelector('#nxDpYear');
        const daysWrap = pop.querySelector('#nxDpDays');

        monthSel.value = viewMonth;
        yearSel.value = viewYear;

        const selectedIso = activeInput ? activeInput.value : '';
        const minIso = activeInput ? (activeInput.min || '') : '';
        const maxIso = activeInput ? (activeInput.max || '') : '';
        const now = new Date();
        const todayIso = toIso(now.getFullYear(), now.getMonth(), now.getDate());

        const firstDay = new Date(viewYear, viewMonth, 1).getDay();
        const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();
        const prevMonthDays = new Date(viewYear, viewMonth, 0).getDate();

        let html = '';

        // Leading days from previous month
        for (let i = firstDay - 1; i >= 0; i--) {
            const d = prevMonthDays - i;
            let py = viewYear, pm = viewMonth - 1;
            if (pm < 0) { pm = 11; py--; }
            const iso = toIso(py, pm, d);
            const dis = (minIso && iso < minIso) || (maxIso && iso > maxIso);
            html += '<button type="button" class="nx-dp-day is-other' + (iso === selectedIso ? ' is-selected' : '') + '" data-iso="' + iso + '"' + (dis ? ' disabled' : '') + '>' + d + '</button>';
        }

        // Current month days
        for (let d = 1; d <= daysInMonth; d++) {
            const iso = toIso(viewYear, viewMonth, d);
            const dis = (minIso && iso < minIso) || (maxIso && iso > maxIso);
            let cls = 'nx-dp-day';
            if (iso === todayIso) cls += ' is-today';
            if (iso === selectedIso) cls += ' is-selected';
            html += '<button type="button" class="' + cls + '" data-iso="' + iso + '"' + (dis ? ' disabled' : '') + '>' + d + '</button>';
        }

        // Trailing days to complete grid
        const totalCells = firstDay + daysInMonth;
        const remainder = totalCells % 7 === 0 ? 0 : 7 - (totalCells % 7);
        for (let d = 1; d <= remainder; d++) {
            let ny = viewYear, nm = viewMonth + 1;
            if (nm > 11) { nm = 0; ny++; }
            const iso = toIso(ny, nm, d);
            const dis = (minIso && iso < minIso) || (maxIso && iso > maxIso);
            html += '<button type="button" class="nx-dp-day is-other' + (iso === selectedIso ? ' is-selected' : '') + '" data-iso="' + iso + '"' + (dis ? ' disabled' : '') + '>' + d + '</button>';
        }

        daysWrap.innerHTML = html;
    }

    function positionPopover(triggerEl) {
        const pop = ensurePopover();
        const rect = triggerEl.getBoundingClientRect();
        const popW = pop.offsetWidth || 276;
        const popH = pop.offsetHeight || 310;

        let left = rect.left;
        if (left + popW > window.innerWidth - 12) {
            left = Math.max(12, window.innerWidth - popW - 12);
        }

        let top = rect.bottom + 6;
        if (top + popH > window.innerHeight - 12 && rect.top - popH - 6 >= 12) {
            top = rect.top - popH - 6;
        }

        pop.style.left = Math.round(left) + 'px';
        pop.style.top = Math.round(top) + 'px';
    }

    function openPopover(inp, wrap, triggerEl) {
        if (inp.disabled || inp.readOnly) return;
        if (activeWrap && activeWrap !== wrap) {
            activeWrap.classList.remove('open');
        }
        activeInput = inp;
        activeWrap = wrap;

        const dt = parseIso(inp.value) || new Date();
        viewYear = dt.getFullYear();
        viewMonth = dt.getMonth();

        const pop = ensurePopover();
        const portalHost = wrap.closest('.modal') || document.body;
        if (pop.parentNode !== portalHost) {
            portalHost.appendChild(pop);
        }
        renderCalendar();
        wrap.classList.add('open');
        pop.classList.add('show');
        positionPopover(triggerEl);
    }

    function closePopover() {
        if (popover) popover.classList.remove('show');
        if (activeWrap) activeWrap.classList.remove('open');
        activeInput = null;
        activeWrap = null;
    }

    function enhanceDateInput(inp) {
        if (!shouldEnhance(inp)) return null;
        inp.dataset.nxDateEnhanced = '1';

        const wrap = document.createElement('div');
        wrap.className = 'nx-date-wrap';
        if (inp.classList.contains('form-control-sm') || inp.closest('.filterBox')) {
            wrap.classList.add('nx-sm');
        }

        inp.parentNode.insertBefore(wrap, inp);
        inp.classList.add('nx-date-native-hidden');
        wrap.appendChild(inp);

        const trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.className = 'nx-date-trigger';
        trigger.innerHTML =
            '<span class="nx-date-val is-placeholder">Select date</span>' +
            '<span class="nx-date-icons">' +
                '<span class="nx-date-clear" title="Clear date"><i class="bi bi-x"></i></span>' +
                '<i class="bi bi-calendar3 nx-date-cal-ic"></i>' +
            '</span>';
        wrap.appendChild(trigger);

        const valEl = trigger.querySelector('.nx-date-val');
        const clearEl = trigger.querySelector('.nx-date-clear');

        function sync() {
            trigger.disabled = !!inp.disabled;
            const formatted = formatDisplay(inp.value);
            if (formatted) {
                valEl.textContent = formatted;
                valEl.classList.remove('is-placeholder');
                wrap.classList.add('has-value');
            } else {
                valEl.textContent = inp.getAttribute('placeholder') || 'Select date';
                valEl.classList.add('is-placeholder');
                wrap.classList.remove('has-value');
            }
        }

        trigger.addEventListener('click', function (e) {
            if (e.target.closest('.nx-date-clear')) return;
            if (activeInput === inp && popover && popover.classList.contains('show')) {
                closePopover();
            } else {
                openPopover(inp, wrap, trigger);
            }
        });

        clearEl.addEventListener('click', function (e) {
            e.stopPropagation();
            applyValue(inp, '');
            if (activeInput === inp) closePopover();
        });

        inp.addEventListener('change', sync);
        sync();

        const api = { sync: sync };
        instances.set(inp, api);
        return api;
    }

    function enhanceAll(root) {
        const scope = root || document;
        if (scope.querySelectorAll) {
            scope.querySelectorAll('input[type="date"]').forEach(enhanceDateInput);
        }
    }

    function hookJQueryVal() {
        if (!window.jQuery || !window.jQuery.valHooks) return;
        const origHook = window.jQuery.valHooks.date || {};
        const origSet = origHook.set;
        window.jQuery.valHooks.date = Object.assign({}, origHook, {
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
        if (!popover || !popover.classList.contains('show')) return;
        if (e.target.closest('.nx-date-popover') || e.target.closest('.nx-date-wrap')) return;
        closePopover();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && popover && popover.classList.contains('show')) {
            closePopover();
        }
    });

    window.addEventListener('scroll', function () {
        if (popover && popover.classList.contains('show') && activeWrap) {
            const trig = activeWrap.querySelector('.nx-date-trigger');
            if (trig) positionPopover(trig);
        }
    }, true);

    window.addEventListener('resize', closePopover);

    document.addEventListener('reset', function (e) {
        const form = e.target;
        if (!form || !form.querySelectorAll) return;
        setTimeout(function () {
            form.querySelectorAll('input.nx-date-native-hidden').forEach(function (inp) {
                const api = instances.get(inp);
                if (api) api.sync();
            });
        }, 0);
    });

    function init() {
        hookJQueryVal();
        enhanceAll(document);

        const bodyObserver = new MutationObserver(function (mutations) {
            mutations.forEach(function (m) {
                m.addedNodes.forEach(function (node) {
                    if (node.nodeType !== 1) return;
                    if (node.tagName === 'INPUT' && node.type === 'date') {
                        enhanceDateInput(node);
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

    window.NexoraDate = {
        enhance: enhanceDateInput,
        enhanceAll: enhanceAll,
        sync: function (el) {
            const api = instances.get(el);
            if (api) api.sync();
        }
    };
})(window, document);
