// agent-registerform3.js
document.addEventListener('DOMContentLoaded', function() {
    const recruitmentRadios = document.querySelectorAll('input[name="recruitment_experience"]');
    const experienceDetails = document.getElementById('experience-details');
    const tableBody = document.getElementById('experience-table-body');

    // Toggle experience details section based on radio selection
    recruitmentRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            experienceDetails.style.display = this.value === 'yes' ? 'block' : 'none';
        });
    });

    // Function to add a new row
    function addNewRow() {
        const rowCount = tableBody.rows.length;
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td style="text-align: center; border: 1px solid #e2e8f0; padding: 10px;">${rowCount + 1}</td>
            <td style="border: 1px solid #e2e8f0; padding: 10px;">
                <input type="text" name="institution[]" style="width: 100%; padding: 8px; border: 1px solid #e2e8f0; border-radius: 4px; box-sizing: border-box; height: 38px;">
            </td>
            <td style="border: 1px solid #e2e8f0; padding: 10px;">
                <input type="date" name="from[]" style="width: 100%; padding: 8px; border: 1px solid #e2e8f0; border-radius: 4px; box-sizing: border-box; height: 38px; text-align: center;">
            </td>
            <td style="border: 1px solid #e2e8f0; padding: 10px;">
                <input type="date" name="until[]" style="width: 100%; padding: 8px; border: 1px solid #e2e8f0; border-radius: 4px; box-sizing: border-box; height: 38px; text-align: center;">
            </td>
            <td style="border: 1px solid #e2e8f0; padding: 10px;">
                <input type="text" name="recruited[]" style="width: 100%; padding: 8px; border: 1px solid #e2e8f0; border-radius: 4px; box-sizing: border-box; height: 38px;">
            </td>
            <td style="text-align: center; border: 1px solid #e2e8f0; padding: 10px;">
                            <button type="button" style="background-color: #1a73e8; color: white; border: none; padding: 8px 12px; border-radius: 4px; margin-right: 5px; margin-bottom: 5px;">Add Row</button>
                            <button type="button" style="background-color:rgb(232, 26, 26); color: white; border: none; padding: 8px 12px; border-radius: 4px; margin-right: 5px; margin-bottom: 2px;">Remove</button>
            </td>
        `;

        // Add event listeners to new row buttons
        const addBtn = newRow.querySelector('button:first-child');
        const removeBtn = newRow.querySelector('button:last-child');

        removeBtn.addEventListener('click', function() {
            tableBody.removeChild(newRow);
            updateRowNumbers();
        });

        // Add row functionality for newly added row's add button
        addBtn.addEventListener('click', addNewRow);

        // Append the new row to the table body
        tableBody.appendChild(newRow);
    }

    // Function to update row numbers after removal
    function updateRowNumbers() {
        Array.from(tableBody.rows).forEach((row, index) => {
            row.cells[0].textContent = index + 1;
        });
    }

    // Initial setup for existing buttons
    const initialAddBtn = tableBody.querySelector('button:first-child');
    const initialRemoveBtn = tableBody.querySelector('button:last-child');

    initialAddBtn.addEventListener('click', addNewRow);
    initialRemoveBtn.addEventListener('click', function() {
        const row = this.closest('tr');
        tableBody.removeChild(row);
        updateRowNumbers();
    });
});