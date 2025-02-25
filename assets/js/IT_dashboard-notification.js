document.addEventListener('DOMContentLoaded', function() {
    const notificationContainer = document.querySelector('.notifications');
    const notificationIcon = document.querySelector('.notification-icon');
    
    // Create notification badge
    const badge = document.createElement('span');
    badge.className = 'notification-badge';
    badge.style.display = 'none'; // Hide initially
    notificationContainer.appendChild(badge);
    
    // Create notification dropdown
    const dropdown = document.createElement('div');
    dropdown.className = 'notification-dropdown';
    dropdown.innerHTML = `
        <div class="notification-header">
            <div class="notification-title">Notifications</div>
        </div>
        <div class="notification-list" id="notification-list">
            <div class="empty-notification">
                <i class="fas fa-bell-slash"></i>
                <p>No notifications yet</p>
            </div>
        </div>
        <div class="notification-footer">
            <a href="#" class="view-all-link" id="view-all-notifications">View All</a>
        </div>
    `;
    notificationContainer.appendChild(dropdown);
    
    // Toggle dropdown on click
    notificationContainer.addEventListener('click', function(event) {
        event.stopPropagation();
        
        // Close user dropdown if open
        const userDropdown = document.querySelector('.user-dropdown');
        if (userDropdown) {
            userDropdown.style.display = 'none';
        }
        
        // Toggle notification dropdown
        const dropdownStyle = window.getComputedStyle(dropdown);
        if (dropdownStyle.display === 'none') {
            dropdown.style.display = 'block';
        } else {
            dropdown.style.display = 'none';
        }
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!notificationContainer.contains(event.target)) {
            dropdown.style.display = 'none';
        }
    });
    
    // Fetch notifications
    fetchNotifications();
    
    // Refresh notifications every 60 seconds
    setInterval(fetchNotifications, 60000);
    
    // View all notifications functionality
    document.getElementById('view-all-notifications').addEventListener('click', function(e) {
        e.preventDefault();
        // Hide dropdown before switching tabs
        dropdown.style.display = 'none';
        
        // Redirect to the Agents tab with pending filter
        const agentLink = document.querySelector('.nav-link[data-section="agent"]');
        if (agentLink) {
            agentLink.click();
            // Set the filter to pending
            setTimeout(() => {
                const statusFilter = document.getElementById('agentStatusFilter');
                if (statusFilter) {
                    statusFilter.value = 'pending';
                    // Trigger the change event to reload the table
                    statusFilter.dispatchEvent(new Event('change'));
                }
            }, 300); // Small delay to ensure tab content has loaded
        }
    });
    
    // Function to fetch notifications
    function fetchNotifications() {
        fetch('IT-fetch-notifications.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateNotificationBadge(data.count);
                    updateNotificationList(data.agents);
                }
            })
            .catch(error => {
                console.error('Error fetching notifications:', error);
            });
    }
    
    // Update notification badge
    function updateNotificationBadge(count) {
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }
    
    // Update notification list
    function updateNotificationList(agents) {
        const notificationList = document.getElementById('notification-list');
        
        if (agents.length === 0) {
            notificationList.innerHTML = `
                <div class="empty-notification">
                    <i class="fas fa-bell-slash"></i>
                    <p>No pending agent applications</p>
                </div>
            `;
            return;
        }
        
        let html = '';
        
        agents.forEach(agent => {
            html += `
                <div class="notification-item" onclick="viewAgent(${agent.id})">
                    <div class="notification-icon-container">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="notification-content">
                        <p><strong>${escapeHtml(agent.company_name)}</strong> has applied to become an agent</p>
                        <p>Contact: ${escapeHtml(agent.contact_name)}</p>
                        <div class="notification-time">${agent.time_ago}</div>
                    </div>
                </div>
            `;
        });
        
        notificationList.innerHTML = html;
    }
    
    // Helper function to escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        return text
            .toString()
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
});