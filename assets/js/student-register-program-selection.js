document.addEventListener('DOMContentLoaded', function() {
    // Set up program selection validation
    setupProgramSelectionValidation();
    
    function setupProgramSelectionValidation() {
        const programSelects = [
            document.getElementById('programme_code_1'),
            document.getElementById('programme_code_2'),
            document.getElementById('programme_code_3'),
            document.getElementById('programme_code_4'),
            document.getElementById('programme_code_5')
        ];
        
        // Only proceed if we're on a page with program selects
        if (!programSelects[0]) return;
        
        // Add change event listeners to each select
        programSelects.forEach((select, index) => {
            select.addEventListener('change', function() {
                validateProgramSelections(programSelects, index);
            });
        });
    }
    
    function validateProgramSelections(selects, changedIndex) {
        const selectedValues = selects.map(select => select.value);
        
        // Check for duplicates
        const changedValue = selects[changedIndex].value;
        if (!changedValue) return; // Skip if empty selection
        
        let duplicateFound = false;
        
        selectedValues.forEach((value, index) => {
            if (index !== changedIndex && value === changedValue) {
                duplicateFound = true;
                // Highlight the duplicate
                selects[index].classList.add('duplicate-selection');
                setTimeout(() => {
                    selects[index].classList.remove('duplicate-selection');
                }, 2000);
            }
        });
        
        if (duplicateFound) {
            alert('You have already selected this program. Please choose different programs for each choice.');
        }
    }
});