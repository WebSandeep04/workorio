document.addEventListener('DOMContentLoaded', function () {
  const toggleBtn = document.getElementById('toggleSidebar');
  const sidebar = document.querySelector('.sidebar');
  const content = document.querySelector('.content');

  const isDesktop = () => window.matchMedia('(min-width: 768px)').matches;

  // Default: sidebar stays OPEN on load. Only toggle via button.

  const syncToggleState = () => {
    if (!toggleBtn || !sidebar) return;
    toggleBtn.classList.toggle('collapsed', sidebar.classList.contains('collapsed'));
  };

  const toggleSidebar = () => {
    if (!sidebar || !content) return;
    if (!isDesktop()) return; // ignore toggle on mobile where sidebar is hidden
    sidebar.classList.toggle('collapsed');
    content.classList.toggle('expanded');
    syncToggleState();
  };

  if (toggleBtn && sidebar && content) {
    toggleBtn.addEventListener('click', toggleSidebar);
  }

  // Professional collapsed-rail UX:
  // clicking any hub / section icon while the sidebar is collapsed
  // smoothly expands the sidebar first, then opens the clicked group.
  if (sidebar && content) {
    sidebar.addEventListener('click', function (e) {
      if (!sidebar.classList.contains('collapsed') || !isDesktop()) return;

      const trigger = e.target.closest('[data-bs-toggle="collapse"]');
      if (!trigger || !sidebar.contains(trigger)) return;

      // Stop Bootstrap from toggling while the rail is collapsed
      e.preventDefault();
      e.stopPropagation();

      sidebar.classList.remove('collapsed');
      content.classList.remove('expanded');
      syncToggleState();

      const targetSel = trigger.getAttribute('data-bs-target') || trigger.getAttribute('href');
      const target = targetSel ? document.querySelector(targetSel) : null;

      // Wait for the width transition, then open the group
      setTimeout(function () {
        if (target && window.bootstrap && window.bootstrap.Collapse) {
          window.bootstrap.Collapse.getOrCreateInstance(target, { toggle: false }).show();
          trigger.classList.remove('collapsed');
          trigger.setAttribute('aria-expanded', 'true');
        }
      }, 220);
    }, true); // capture phase so it runs before Bootstrap's handler
  }


  // Adjust classes on resize to keep mobile view clean
  window.addEventListener('resize', function () {
    if (!sidebar || !content) return;
    if (!isDesktop()) {
      // On mobile, always show full-width content and non-collapsed sidebar state
      sidebar.classList.remove('collapsed');
      content.classList.remove('expanded');
      syncToggleState();
    }
    // On desktop, keep current state as-is (no auto-collapse/expand)
    checkScrollable(); // Recheck scrollable state on resize
  });

  // Function to check if sidebar content is scrollable
  function checkScrollable() {
    const sideItems = document.querySelector('.side-items');
    if (sideItems && sidebar) {
      if (sideItems.scrollHeight > sideItems.clientHeight) {
        sidebar.classList.add('has-scroll');
      } else {
        sidebar.classList.remove('has-scroll');
      }
    }
  }

  // Check scrollable state on load and when content changes
  checkScrollable();
  syncToggleState();
  
  // Optional: Recheck when menu items are dynamically added/removed
  const observer = new MutationObserver(checkScrollable);
  const sideItems = document.querySelector('.side-items');
  if (sideItems) {
    observer.observe(sideItems, { childList: true, subtree: true });
  }

  const dropdownLinkMap = {};
  document.querySelectorAll('.sidebar-link.sidebar-dropdown').forEach(link => {
    const key = link.dataset.sectionKey;
    if (key) {
      dropdownLinkMap[key] = link;
    }
  });

  document.querySelectorAll('[data-open-section]').forEach(trigger => {
    trigger.addEventListener('click', () => {
      const sectionKey = trigger.dataset.openSection;
      if (!sectionKey || !dropdownLinkMap[sectionKey]) return;

      const link = dropdownLinkMap[sectionKey];
      const targetSelector = link.getAttribute('href');
      if (!targetSelector) return;

      const targetEl = document.querySelector(targetSelector);
      if (!targetEl) return;

      if (window.bootstrap && window.bootstrap.Collapse) {
        const collapseInstance = window.bootstrap.Collapse.getOrCreateInstance(targetEl, { toggle: false });
        collapseInstance.show();
      }

      link.classList.remove('collapsed');
      link.setAttribute('aria-expanded', 'true');
      targetEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });
  });

  // Auto-scroll sidebar so that the currently active/selected tab is always visible on page reload
  setTimeout(() => {
    const activeItem = document.querySelector('.sidebar .sidebar-nav-item.active, .sidebar .sub-nav-item.active');
    if (activeItem) {
      activeItem.scrollIntoView({ behavior: 'auto', block: 'center' });
    }
  }, 100);

  // Global Synced Top Horizontal Scrollbar for wide .table-responsive tables
  function initTopTableScroll(el) {
    if (!el || el.dataset.nxTopScroll === '1') return;
    if (el.closest('.modal')) return;
    el.dataset.nxTopScroll = '1';

    const topBar = document.createElement('div');
    topBar.className = 'nx-top-scroll';
    topBar.style.display = 'none';
    const spacer = document.createElement('div');
    spacer.className = 'nx-top-scroll-inner';
    topBar.appendChild(spacer);

    el.parentNode.classList.add('nx-table-scroll-host');
    el.parentNode.insertBefore(topBar, el);

    let syncingTop = false;
    let syncingBottom = false;

    function refreshWidth() {
      const sw = el.scrollWidth;
      const cw = el.clientWidth;
      const thead = el.querySelector('thead');
      if (thead && thead.offsetHeight > 0) {
        topBar.style.top = (el.offsetTop + thead.offsetHeight) + 'px';
      } else {
        topBar.style.top = el.offsetTop + 'px';
      }
      if (sw > cw + 2) {
        spacer.style.width = sw + 'px';
        topBar.style.display = 'block';
        topBar.scrollLeft = el.scrollLeft;
      } else {
        topBar.style.display = 'none';
      }
    }

    topBar.addEventListener('scroll', () => {
      if (syncingBottom) { syncingBottom = false; return; }
      syncingTop = true;
      el.scrollLeft = topBar.scrollLeft;
    });

    el.addEventListener('scroll', () => {
      if (syncingTop) { syncingTop = false; return; }
      syncingBottom = true;
      topBar.scrollLeft = el.scrollLeft;
    });

    if (typeof ResizeObserver !== 'undefined') {
      const ro = new ResizeObserver(refreshWidth);
      ro.observe(el);
      const tbl = el.querySelector('table');
      if (tbl) ro.observe(tbl);
    }

    const mo = new MutationObserver(() => requestAnimationFrame(refreshWidth));
    mo.observe(el, { childList: true, subtree: true });
    window.addEventListener('resize', refreshWidth);

    refreshWidth();
    setTimeout(refreshWidth, 250);
  }

  document.querySelectorAll('.table-responsive').forEach(initTopTableScroll);

  // Global Hover Tooltip for Truncated Table Cells
  let cellTooltip = null;
  let cellTipTimer = null;

  function ensureCellTooltip() {
    if (cellTooltip) return cellTooltip;
    cellTooltip = document.createElement('div');
    cellTooltip.className = 'nx-cell-tooltip';
    cellTooltip.setAttribute('role', 'tooltip');
    document.body.appendChild(cellTooltip);
    return cellTooltip;
  }

  function hideCellTooltip() {
    clearTimeout(cellTipTimer);
    if (cellTooltip) cellTooltip.classList.remove('show');
  }

  document.addEventListener('mouseover', (e) => {
    const td = e.target.closest('.data-table-card tbody td, .custom-table tbody td');
    if (!td) return;
    if (td.querySelector('select, input, button')) return;

    const link = td.querySelector('a');
    const targetEl = link || td;
    const nativeTitle = targetEl.getAttribute('title') || td.getAttribute('title');
    if (nativeTitle) {
      targetEl.dataset.nxFullText = nativeTitle;
      targetEl.removeAttribute('title');
      td.removeAttribute('title');
    }

    const fullText = (targetEl.dataset.nxFullText || targetEl.textContent || '').trim();
    const isTruncated = (targetEl.scrollWidth > targetEl.clientWidth + 1) || (td.scrollWidth > td.clientWidth + 1) || !!targetEl.dataset.nxFullText;
    if (!fullText || !isTruncated) return;

    clearTimeout(cellTipTimer);
    cellTipTimer = setTimeout(() => {
      const tip = ensureCellTooltip();
      tip.textContent = fullText;
      tip.classList.add('show');

      const rect = td.getBoundingClientRect();
      const tipW = tip.offsetWidth || 220;
      const tipH = tip.offsetHeight || 36;
      const left = Math.max(10, Math.min(rect.left + (rect.width / 2) - (tipW / 2), window.innerWidth - tipW - 10));
      const top = (rect.top - tipH - 8 >= 10) ? (rect.top - tipH - 8) : (rect.bottom + 8);
      tip.style.left = Math.round(left) + 'px';
      tip.style.top = Math.round(top) + 'px';
    }, 120);
  });

  document.addEventListener('mouseout', (e) => {
    const td = e.target.closest('.data-table-card tbody td, .custom-table tbody td');
    if (!td) return;
    if (e.relatedTarget && td.contains(e.relatedTarget)) return;
    hideCellTooltip();
  });

  window.addEventListener('scroll', hideCellTooltip, true);
});






