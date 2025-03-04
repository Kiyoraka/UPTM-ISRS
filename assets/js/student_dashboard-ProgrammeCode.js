// Function to find the program name from code
function getProgramName(code) {
    console.log('Searching for program code:', code); // Add console log
    
    // Search through all program levels
    for (const level in programsData) {
        const programs = programsData[level];
        for (const program of programs) {
            console.log('Checking:', program.code, program.name); // Add console log
            if (program.code === code) {
                return program.name;
            }
        }
    }
    return code; // Return code itself if not found
}

// Update program displays with full names
document.addEventListener('DOMContentLoaded', function() {
    console.log('Programme Code script running'); // Debugging log
    console.log('Programs data:', programsData); // Log the entire programs data
    
    // Find all program spans
    const programElements = [
        document.getElementById('program1'),
        document.getElementById('program2'),
        document.getElementById('program3'),
        document.getElementById('program4'),
        document.getElementById('program5')
    ];

    // Update each program element with full program name
    programElements.forEach(element => {
        if (element) {
            const code = element.textContent.trim();
            console.log('Processing code:', code); // Add console log
            const name = getProgramName(code);
            console.log('Found name:', name); // Add console log
            element.textContent = `${code} - ${name}`;
        }
    });
});