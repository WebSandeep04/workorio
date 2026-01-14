class NotificationManager {
    constructor() {
        this.notifications = [];
        this.unreadCount = 0;
        this.init();
    }

    init() {
        this.createNotificationUI();
        this.hideDropdowns();
        this.loadNotifications();
        this.startPolling();
    }

    hideDropdowns() {
        ['notification-dropdown', 'mobile-notification-dropdown'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.style.display = 'none';
                el.style.visibility = 'hidden';
                el.style.opacity = '0';
            }
        });
    }

    createNotificationUI() {
        this.bindBellClick('bell-btn', 'desktop');
        this.bindBellClick('mobile-bell-btn', 'mobile');
        this.bindAction('mark-all-read-btn', () => this.markAllAsRead());
        this.bindAction('clear-all-btn', () => this.clearAllNotifications());
        this.bindAction('mobile-mark-all-read-btn', () => this.markAllAsRead());
        this.bindAction('mobile-clear-all-btn', () => this.clearAllNotifications());

        document.addEventListener('click', (e) => {
            if (!e.target.closest('#notification-bell') && !e.target.closest('#mobile-notification-bell')) {
                this.closeDropdown();
            }
        });
    }

    bindBellClick(id, type) {
        const btn = document.getElementById(id);
        if (!btn) return;
        const clone = btn.cloneNode(true);
        btn.parentNode.replaceChild(clone, btn);
        clone.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.toggleDropdown(type);
        });
    }

    bindAction(id, handler) {
        const btn = document.getElementById(id);
        if (!btn) return;
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            handler();
        });
    }

    async loadNotifications() {
        try {
            const response = await fetch('/notifications', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) return;

            const data = await response.json();
            this.notifications = data.notifications || [];
            this.updateUnreadCount();
            this.renderNotifications();
        } catch (error) {
            console.error('Failed to load notifications:', error);
        }
    }

    updateUnreadCount() {
        this.unreadCount = this.notifications.filter(n => !n.read_at).length;

        this.toggleCounter('notification-count', this.unreadCount);
        this.toggleCounter('mobile-notification-count', this.unreadCount);
    }

    toggleCounter(id, count) {
        const el = document.getElementById(id);
        if (!el) return;
        if (count > 0) {
            el.textContent = count;
            el.style.display = 'flex';
        } else {
            el.style.display = 'none';
        }
    }

    renderNotifications() {
        // Render for desktop
        const listEl = document.getElementById('notification-list');
        if (listEl) {
            this.renderNotificationList(listEl);
        }
        
        // Render for mobile
        const mobileListEl = document.getElementById('mobile-notification-list');
        if (mobileListEl) {
            this.renderNotificationList(mobileListEl);
        }
    }
    
    renderNotificationList(listEl) {
        if (this.notifications.length === 0) {
            listEl.innerHTML = '<div style="padding: 20px; text-align: center; color: #666; border: 1px solid #ddd;">No notifications</div>';
            return;
        }

        const html = this.notifications.map(notification => {
            const isRead = notification.read_at ? 'read' : 'unread';
            const timeAgo = this.getTimeAgo(notification.created_at);
            const bgColor = isRead ? '#f8f9fa' : '#fff';
            
            return `
                <div class="notification-item ${isRead}" style="padding: 8px 12px; border-bottom: 1px solid #f0f0f0; background: ${bgColor}; display: flex; justify-content: space-between; align-items: start; font-size: 13px;" data-id="${notification.id}">
                    <div style="flex: 1; cursor: pointer;" class="notification-content">
                        <div style="font-weight: bold; color: #333; margin-bottom: 2px; font-size: 12px;">${notification.data.title}</div>
                        <div style="color: #666; font-size: 11px; margin-bottom: 2px;">${notification.data.message}</div>
                        <div style="font-size: 10px; color: #999;">${timeAgo}</div>
                    </div>
                    <button class="delete-notification-btn" data-id="${notification.id}" style="background: none; border: none; color: #dc3545; cursor: pointer; padding: 2px 6px; font-size: 14px; margin-left: 8px;" title="Delete">
                        ✕
                    </button>
                </div>
            `;
        }).join('');

        listEl.innerHTML = html;

        // Add click handlers for notification content
        listEl.querySelectorAll('.notification-content').forEach(item => {
            item.addEventListener('click', () => {
                const notificationId = item.parentElement.dataset.id;
                this.markAsRead(notificationId);
                this.loadNotifications(); // Refresh
            });
        });

        // Add click handlers for delete buttons
        listEl.querySelectorAll('.delete-notification-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.deleteNotification(btn.dataset.id);
            });
        });
    }

    async markAsRead(notificationId) {
        try {
            await fetch(`/notifications/${notificationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });
        } catch (error) {
            console.error('Failed to mark notification as read:', error);
        }
    }

    async deleteNotification(notificationId) {
        try {
            const response = await fetch(`/notifications/${notificationId}/delete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });
            
            if (response.ok) {
                this.loadNotifications(); // Refresh list
            }
        } catch (error) {
            console.error('Failed to delete notification:', error);
        }
    }

    async markAllAsRead() {
        try {
            const response = await fetch('/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });
            
            if (response.ok) {
                this.loadNotifications(); // Refresh list
            }
        } catch (error) {
            console.error('Failed to mark all as read:', error);
        }
    }

    async clearAllNotifications() {
        if (!confirm('Are you sure you want to clear all notifications? This cannot be undone.')) {
            return;
        }
        
        try {
            const response = await fetch('/notifications/clear-all', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });
            
            if (response.ok) {
                this.loadNotifications(); // Refresh list
            }
        } catch (error) {
            console.error('Failed to clear all notifications:', error);
        }
    }

    toggleDropdown(type = 'desktop') {
        const dropdownId = type === 'mobile' ? 'mobile-notification-dropdown' : 'notification-dropdown';
        const dropdown = document.getElementById(dropdownId);
        
        if (dropdown) {
            const isHidden = dropdown.style.display === 'none' || 
                            dropdown.style.display === '' || 
                            dropdown.style.visibility === 'hidden' ||
                            window.getComputedStyle(dropdown).display === 'none';
            
            if (isHidden) {
                // Show dropdown
                dropdown.style.display = 'block';
                dropdown.style.visibility = 'visible';
                dropdown.style.opacity = '1';
                dropdown.style.position = 'absolute';
                dropdown.style.top = '40px';
                dropdown.style.right = '0';
                dropdown.style.zIndex = '99999';
                dropdown.style.background = 'white';
                dropdown.style.border = '2px solid #007bff';
            } else {
                // Hide dropdown
                dropdown.style.display = 'none';
                dropdown.style.visibility = 'hidden';
                dropdown.style.opacity = '0';
            }
        }
    }

    closeDropdown() {
        const dropdown = document.getElementById('notification-dropdown');
        const mobileDropdown = document.getElementById('mobile-notification-dropdown');
        if (dropdown) dropdown.style.display = 'none';
        if (mobileDropdown) mobileDropdown.style.display = 'none';
    }

    startPolling() {
        // Check for new notifications every 30 seconds
        setInterval(() => {
            this.loadNotifications();
        }, 30000);
    }

    getTimeAgo(dateString) {
        const now = new Date();
        const notificationTime = new Date(dateString);
        const diffInSeconds = Math.floor((now - notificationTime) / 1000);

        if (diffInSeconds < 60) return 'Just now';
        if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
        if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
        return `${Math.floor(diffInSeconds / 86400)}d ago`;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        window.notificationManager = new NotificationManager();
    }, 300);
});
