document.addEventListener('DOMContentLoaded', function() {
    // Notification toggle functionality
    const notificationIcon = document.querySelector('.notifications');
    const notificationDropdown = document.querySelector('.notification-dropdown');

    if (notificationIcon && notificationDropdown) {
        // Toggle notification dropdown
        notificationIcon.addEventListener('click', function(e) {
            e.stopPropagation();
            
            // Close user dropdown if open
            const userDropdown = document.querySelector('.user-dropdown');
            if (userDropdown) {
                userDropdown.style.display = 'none';
            }
            
            // Toggle notification dropdown
            if (notificationDropdown.style.display === 'block') {
                notificationDropdown.style.display = 'none';
            } else {
                notificationDropdown.style.display = 'block';
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!notificationIcon.contains(e.target)) {
                notificationDropdown.style.display = 'none';
            }
        });
    }

    // User dropdown toggle functionality
    const userProfile = document.querySelector('.user-profile');
    const userDropdown = document.querySelector('.user-dropdown');

    if (userProfile && userDropdown) {
        // Toggle user dropdown
        userProfile.addEventListener('click', function(e) {
            e.stopPropagation();
            
            // Close notification dropdown if open
            if (notificationDropdown) {
                notificationDropdown.style.display = 'none';
            }
            
            // Toggle user dropdown
            if (userDropdown.style.display === 'block') {
                userDropdown.style.display = 'none';
            } else {
                userDropdown.style.display = 'block';
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userProfile.contains(e.target)) {
                userDropdown.style.display = 'none';
            }
        });
    }
});