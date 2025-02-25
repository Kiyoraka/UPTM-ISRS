document.addEventListener('DOMContentLoaded', function() {
    // Get the stats elements
    const totalStudentsElement = document.querySelector('.stat-number.registered');
    const totalStaffElement = document.querySelector('.stat-number.approved');
    const totalAgentsElement = document.querySelector('.stat-number.rejected');
    
    // Initial load of stats
    loadDashboardStats();
    
    // Refresh stats every 5 minutes
    setInterval(loadDashboardStats, 300000);
    
    // Function to load dashboard statistics
    function loadDashboardStats() {
        fetch('IT_dashboard-Stats.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the stats on the dashboard
                    totalStudentsElement.textContent = data.stats.total_students;
                    totalStaffElement.textContent = data.stats.total_staff;
                    totalAgentsElement.textContent = data.stats.total_agents;
                } else {
                    console.error('Failed to load dashboard stats:', data.message);
                }
            })
            .catch(error => {
                console.error('Error fetching dashboard stats:', error);
            });
    }
});