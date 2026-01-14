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
});






