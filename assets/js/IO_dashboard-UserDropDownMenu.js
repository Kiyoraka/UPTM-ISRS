// User dropdown toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const userProfile = document.querySelector('.user-profile');
    const userDropdown = document.querySelector('.user-dropdown');

    if (userProfile && userDropdown) {
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!userProfile.contains(event.target)) {
                userDropdown.style.display = 'none';
            }
        });

        // Toggle dropdown on user icon click
        userProfile.addEventListener('click', function(event) {
            event.stopPropagation();
            
            // Close notification dropdown if open
            const notificationDropdown = document.querySelector('.notification-dropdown');
            if (notificationDropdown) {
                notificationDropdown.style.display = 'none';
            }
            
            // Toggle user dropdown
            const dropdownStyle = window.getComputedStyle(userDropdown);
            if (dropdownStyle.display === 'none') {
                userDropdown.style.display = 'block';
            } else {
                userDropdown.style.display = 'none';
            }
        });
    }
});