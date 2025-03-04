document.addEventListener('DOMContentLoaded', function() {
    // Optional: Add a note about qualification changes
    const qualificationsSection = document.getElementById('section-qualifications');
    
    // Create a note explaining how to update qualifications
    const noteElement = document.createElement('div');
    noteElement.className = 'qualification-update-note';
    noteElement.innerHTML = `
        <p class="text-muted">
            <strong>Note:</strong> To update your qualifications, please contact the International Office.
            Qualification updates require official documentation and administrative review.
        </p>
    `;
    
    // Append the note after the qualifications table
    qualificationsTable.parentNode.insertBefore(noteElement, qualificationsTable.nextSibling);
});