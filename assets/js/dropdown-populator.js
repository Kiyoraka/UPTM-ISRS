/**
 * Functions to populate dropdowns with country, nationality, and program data
 */

// Populate a country dropdown
function populateCountryDropdown(selectId) {
    const selectElement = document.getElementById(selectId);
    if (!selectElement) return;
    
    // Clear existing options
    selectElement.innerHTML = '<option value="">Select Country</option>';
    
    // Add options from data, organized by region
    Object.keys(regionLabels).forEach(regionKey => {
        const optgroup = document.createElement('optgroup');
        optgroup.label = regionLabels[regionKey];
        
        countriesData[regionKey].forEach(country => {
            const option = document.createElement('option');
            option.value = country.code;
            option.textContent = country.country;
            optgroup.appendChild(option);
        });
        
        selectElement.appendChild(optgroup);
    });
}

// Populate a nationality dropdown
function populateNationalityDropdown(selectId) {
    const selectElement = document.getElementById(selectId);
    if (!selectElement) return;
    
    // Clear existing options
    selectElement.innerHTML = '<option value="">Select Nationality</option>';
    
    // Add options from data, organized by region
    Object.keys(regionLabels).forEach(regionKey => {
        const optgroup = document.createElement('optgroup');
        optgroup.label = regionLabels[regionKey];
        
        countriesData[regionKey].forEach(country => {
            const option = document.createElement('option');
            option.value = country.code;
            option.textContent = country.nationality;
            optgroup.appendChild(option);
        });
        
        selectElement.appendChild(optgroup);
    });
}

// Populate a program dropdown
function populateProgramDropdown(selectId) {
    const selectElement = document.getElementById(selectId);
    if (!selectElement) return;
    
    // Clear existing options
    selectElement.innerHTML = '<option value="">Select Programme</option>';
    
    // Add options from data, organized by academic level
    Object.keys(programLevelLabels).forEach(levelKey => {
        const optgroup = document.createElement('optgroup');
        optgroup.label = programLevelLabels[levelKey];
        
        programsData[levelKey].forEach(program => {
            const option = document.createElement('option');
            option.value = program.code;
            option.textContent = `${program.code} - ${program.name}`;
            optgroup.appendChild(option);
        });
        
        selectElement.appendChild(optgroup);
    });
}

// Auto-calculate age from date of birth
function setupAgeCalculation() {
    const dobInput = document.getElementById('date_of_birth');
    const ageInput = document.getElementById('age');
    
    if (dobInput && ageInput) {
        dobInput.addEventListener('change', function() {
            if (this.value) {
                const dob = new Date(this.value);
                const today = new Date();
                let age = today.getFullYear() - dob.getFullYear();
                
                // Adjust age if birthday hasn't occurred yet this year
                const monthDiff = today.getMonth() - dob.getMonth();
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                    age--;
                }
                
                ageInput.value = age;
            }
        });
    }
}

// Prevent duplicate program selection
function setupProgramValidation() {
    const programSelects = [
        document.getElementById('programme_code_1'),
        document.getElementById('programme_code_2'),
        document.getElementById('programme_code_3'),
        document.getElementById('programme_code_4'),
        document.getElementById('programme_code_5')
    ].filter(Boolean); // Remove any null elements
    
    if (programSelects.length > 0) {
        programSelects.forEach((select, index) => {
            select.addEventListener('change', function() {
                validateProgramSelections(programSelects, index);
            });
        });
    }
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

// Initialize all dropdowns and functionality when document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Populate country dropdowns
    populateCountryDropdown('country');
    populateCountryDropdown('guardian_country');
    
    // Populate nationality dropdowns
    populateNationalityDropdown('nationality');
    populateNationalityDropdown('guardian_nationality');
    
    // Populate program dropdowns
    populateProgramDropdown('programme_code_1');
    populateProgramDropdown('programme_code_2');
    populateProgramDropdown('programme_code_3');
    populateProgramDropdown('programme_code_4');
    populateProgramDropdown('programme_code_5');
    
    // Setup additional functionality
    setupAgeCalculation();
    setupProgramValidation();
});