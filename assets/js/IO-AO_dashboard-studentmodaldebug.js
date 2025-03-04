document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM fully loaded");
    
    // Check if the modal elements exist
    console.log("Modal element:", document.getElementById('studentDetailsModal'));
    console.log("Modal content element:", document.getElementById('student-details-content'));
    
    // Check for any CSS issues
    const allModalElements = document.querySelectorAll('#studentDetailsModal, .modal, .modal-content');
    allModalElements.forEach(el => {
      console.log(`Element ${el.id || el.className}: visibility=${window.getComputedStyle(el).visibility}, display=${window.getComputedStyle(el).display}, z-index=${window.getComputedStyle(el).zIndex}`);
    });
    
    // Get the view button and add a direct click event
    const viewButtons = document.querySelectorAll('.btn-view');
    
    // Add console logging to the buttons
    viewButtons.forEach(button => {
      button.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log("View button clicked");
        
        // Get student ID
        const studentId = this.getAttribute('onclick').replace('viewStudent(', '').replace(')', '');
        console.log("Student ID:", studentId);
        
        // Show modal directly
        const modal = document.getElementById('studentDetailsModal');
        if (modal) {
          console.log("Modal found, displaying");
          modal.style.display = 'block';
        } else {
          console.log("Modal not found");
        }
      });
    });
  });