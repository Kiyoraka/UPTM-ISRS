/**
 * Country and nationality data organized by regions
 */
const countriesData = {
    "middleEast": [
        { code: "SA", country: "Saudi Arabia", nationality: "Saudi Arabian" },
        { code: "YE", country: "Yemen", nationality: "Yemeni" },
        { code: "OM", country: "Oman", nationality: "Omani" },
        { code: "AE", country: "United Arab Emirates", nationality: "Emirati" },
        { code: "IR", country: "Iran", nationality: "Iranian" },
        { code: "IQ", country: "Iraq", nationality: "Iraqi" },
        { code: "JO", country: "Jordan", nationality: "Jordanian" }
    ],
    "southAsia": [
        { code: "BD", country: "Bangladesh", nationality: "Bangladeshi" },
        { code: "PK", country: "Pakistan", nationality: "Pakistani" },
        { code: "NP", country: "Nepal", nationality: "Nepalese" },
        { code: "LK", country: "Sri Lanka", nationality: "Sri Lankan" },
        { code: "MV", country: "Maldives", nationality: "Maldivian" },
        { code: "BT", country: "Bhutan", nationality: "Bhutanese" },
        { code: "IN", country: "India", nationality: "Indian" }
    ],
    "southeastAsia": [
        { code: "ID", country: "Indonesia", nationality: "Indonesian" },
        { code: "TH", country: "Thailand", nationality: "Thai" },
        { code: "VN", country: "Vietnam", nationality: "Vietnamese" },
        { code: "MM", country: "Myanmar", nationality: "Myanmar" },
        { code: "KH", country: "Cambodia", nationality: "Cambodian" },
        { code: "LA", country: "Laos", nationality: "Laotian" },
        { code: "PH", country: "Philippines", nationality: "Filipino" },
        { code: "BN", country: "Brunei", nationality: "Bruneian" },
        { code: "TL", country: "Timor-Leste", nationality: "East Timorese" }
    ],
    "eastAsia": [
        { code: "CN", country: "China", nationality: "Chinese" },
        { code: "KR", country: "South Korea", nationality: "South Korean" },
        { code: "JP", country: "Japan", nationality: "Japanese" },
        { code: "TW", country: "Taiwan", nationality: "Taiwanese" },
        { code: "HK", country: "Hong Kong", nationality: "Hong Konger" }
    ],
    "centralAsia": [
        { code: "KZ", country: "Kazakhstan", nationality: "Kazakh" },
        { code: "UZ", country: "Uzbekistan", nationality: "Uzbek" },
        { code: "TM", country: "Turkmenistan", nationality: "Turkmen" },
        { code: "KG", country: "Kyrgyzstan", nationality: "Kyrgyz" },
        { code: "TJ", country: "Tajikistan", nationality: "Tajik" }
    ],
    "africa": [
        { code: "NG", country: "Nigeria", nationality: "Nigerian" },
        { code: "SO", country: "Somalia", nationality: "Somali" },
        { code: "SD", country: "Sudan", nationality: "Sudanese" },
        { code: "LY", country: "Libya", nationality: "Libyan" },
        { code: "EG", country: "Egypt", nationality: "Egyptian" },
        { code: "DZ", country: "Algeria", nationality: "Algerian" },
        { code: "MA", country: "Morocco", nationality: "Moroccan" },
        { code: "TN", country: "Tunisia", nationality: "Tunisian" },
        { code: "GH", country: "Ghana", nationality: "Ghanaian" },
        { code: "CM", country: "Cameroon", nationality: "Cameroonian" },
        { code: "CI", country: "Côte d'Ivoire", nationality: "Ivorian" },
        { code: "TZ", country: "Tanzania", nationality: "Tanzanian" },
        { code: "KE", country: "Kenya", nationality: "Kenyan" },
        { code: "UG", country: "Uganda", nationality: "Ugandan" }
    ],
    "southAmerica": [
        { code: "BR", country: "Brazil", nationality: "Brazilian" },
        { code: "CO", country: "Colombia", nationality: "Colombian" },
        { code: "AR", country: "Argentina", nationality: "Argentinian" },
        { code: "PE", country: "Peru", nationality: "Peruvian" },
        { code: "VE", country: "Venezuela", nationality: "Venezuelan" },
        { code: "CL", country: "Chile", nationality: "Chilean" }
    ]
};

// Region labels for the optgroups
const regionLabels = {
    "middleEast": "Middle East",
    "southAsia": "South Asia",
    "southeastAsia": "Southeast Asia",
    "eastAsia": "East Asia",
    "centralAsia": "Central Asia",
    "africa": "Africa",
    "southAmerica": "South America"
};