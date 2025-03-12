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
    
    // For now, just add a placeholder notification since students aren't implemented yet
    // This can be replaced with actual fetch logic later
    
    // View all notifications functionality
    document.getElementById('view-all-notifications').addEventListener('click', function(e) {
        e.preventDefault();
        // Hide dropdown before switching tabs
        dropdown.style.display = 'none';
        
        // Redirect to the Students list tab
        const studentLink = document.querySelector('.nav-link[data-section="student-list"]');
        if (studentLink) {
            studentLink.click();
        }
    });
});