document.addEventListener('DOMContentLoaded', function() {
    // Add Qualification Button Functionality
    const addQualificationBtn = document.getElementById('add-qualification-btn');
    const qualificationsContainer = document.getElementById('qualifications-container');
    
    if (addQualificationBtn && qualificationsContainer) {
        addQualificationBtn.addEventListener('click', function() {
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>
                    <input type="text" name="qualification[]">
                </td>
                <td>
                    <input type="text" name="institution[]">
                </td>
                <td>
                    <input type="text" name="grade[]">
                </td>
                <td>
                    <input type="text" name="duration[]">
                </td>
                <td>
                    <input type="text" name="year_completed[]">
                </td>
                <td>
                    <button type="button" class="remove-qualification-btn">×</button>
                </td>
            `;
            
            qualificationsContainer.appendChild(newRow);
            
            // Add remove functionality to the new row
            const removeBtn = newRow.querySelector('.remove-qualification-btn');
            removeBtn.addEventListener('click', function() {
                qualificationsContainer.removeChild(newRow);
            });
        });
    }
});