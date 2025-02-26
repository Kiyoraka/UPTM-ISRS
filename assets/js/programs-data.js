/**
 * Program data organized by academic levels
 */
const programsData = {
    "bachelors": [
        { code: "BAC", name: "Bachelor of Accountancy (Honours)" },
        { code: "BBAHRM", name: "Bachelor of Business Administration (Honours) in Human Resource Management" },
        { code: "BBA", name: "Bachelor of Business Administration (Honours)" },
        { code: "BCC", name: "Bachelor of Communication (Honours) in Corporate Communication" },
        { code: "BBAH", name: "Bachelor of Business Administration (Hybrid)" },
        { code: "BAAELS", name: "Bachelor of Arts (Honours) in Applied English Language Studies" },
        { code: "BECE", name: "Bachelor of Early Childhood Education (Honours)" },
        { code: "BEDTESL", name: "Bachelor of Education (Honours) in Teaching English as a Second Language (TESL)" },
        { code: "BCA", name: "Bachelor of Corporate Administration (Honours)" },
        { code: "BA3D", name: "Bachelor of Arts in 3D Animation and Digital Media (Honours)" },
        { code: "BITBC", name: "Bachelor of Information Technology (Honours) in Business Computing" },
        { code: "BITCAD", name: "Bachelor of Information Technology (Honours) in Computer Application Development" },
        { code: "BITCS", name: "Bachelor of Information Technology (Honours) in Cyber Security" }
    ],
    "masters": [
        { code: "MSIS", name: "Master of Science in Information Systems" },
        { code: "MBA", name: "Master of Business Administration (in collaboration with CMI)" },
        { code: "MBACAG", name: "MBA (Corporate Administration and Governance) (in collaboration with MAICSA)" },
        { code: "MAcc", name: "Master of Accountancy (in collaboration with CIMA)" }
    ],
    "phd": [
        { code: "PhDBA", name: "Doctor of Philosophy in Business Administration" },
        { code: "PhDIT", name: "Doctor of Philosophy in Information Technology" },
        { code: "PhDEd", name: "Doctor of Philosophy in Education" }
    ]
};

// Level labels for the optgroups
const programLevelLabels = {
    "bachelors": "Bachelor's Degree",
    "masters": "Master",
    "phd": "Doctor of Philosophy"
};