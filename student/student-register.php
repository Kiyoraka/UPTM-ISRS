<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>International Student Registration - UPTM ISRS</title>
    <link rel="stylesheet" href="../assets/css/student-register-main.css">
    <link rel="stylesheet" href="../assets/css/student-register-progress-bar.css">
    <link rel="stylesheet" href="../assets/css/student-register-form-elements.css">
    <link rel="stylesheet" href="../assets/css/student-register-personal-details.css">
    <link rel="stylesheet" href="../assets/css/student-register-qualifications.css">
    <link rel="stylesheet" href="../assets/css/student-register-documents.css">
</head>
<body>
    <div class="registration-container">
        <!-- Progress Bar -->
        <div class="progress-bar">
            <div class="step active">
                <span class="step-number">1</span>
                <span class="step-text">Personal Details</span>
            </div>
            <div class="step">
                <span class="step-number">2</span>
                <span class="step-text">Guardian Information</span>
            </div>
            <div class="step">
                <span class="step-number">3</span>
                <span class="step-text">Qualifications</span>
            </div>
            <div class="step">
                <span class="step-number">4</span>
                <span class="step-text">Program Selection</span>
            </div>
            <div class="step">
                <span class="step-number">5</span>
                <span class="step-text">Financial Support</span>
            </div>
            <div class="step">
                <span class="step-number">6</span>
                <span class="step-text">Documents & Declaration</span>
            </div>
        </div>

        <!-- Logo -->
        <div class="logo-container">
            <a href="../index.html" class="logo-link">
                <img src="../assets/img/uptm-logo.png" alt="UPTM Logo" class="logo">
            </a>
        </div>

        <h1>INTERNATIONAL STUDENT APPLICATION FORM</h1>
        
        <form id="studentRegistrationForm" action="student-registration.php" method="POST" enctype="multipart/form-data">
            <!-- Section A: Personal Details -->
            <div class="form-section active" id="section-a">
                <h2>SECTION A: PERSONAL DETAILS</h2>
                
                <div class="photo-upload-container">
                    <label>Passport Size Photo</label>
                    <div class="photo-upload-box">
                        <img id="photo-preview" src="#" alt="Photo preview">
                        <div id="upload-placeholder">
                            <span>Click to upload photo</span>
                            <small>PNG, JPEG (Max 2MB)</small>
                        </div>
                        <input type="file" id="passport_photo" name="passport_photo" accept=".png,.jpg,.jpeg" required onchange="previewPhoto(this);">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name *</label>
                        <input type="text" id="first_name" name="first_name" required>
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name *</label>
                        <input type="text" id="last_name" name="last_name" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="passport_no">Passport No. *</label>
                    <input type="text" id="passport_no" name="passport_no" required>
                </div>

                <div class="form-group">
                    <label for="nationality">Nationality *</label>
                    <input type="text" id="nationality" name="nationality" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="date_of_birth">Date of Birth (DD/MM/YY) *</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" required>
                    </div>

                    <div class="form-group">
                        <label for="age">Age *</label>
                        <input type="number" id="age" name="age" min="16" max="100" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="place_of_birth">Place of Birth *</label>
                    <input type="text" id="place_of_birth" name="place_of_birth" required>
                </div>

                <div class="form-group">
                    <label for="home_address">Home Address *</label>
                    <textarea id="home_address" name="home_address" rows="3" required></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="city">City *</label>
                        <input type="text" id="city" name="city" required>
                    </div>

                    <div class="form-group">
                        <label for="postcode">Postcode *</label>
                        <input type="text" id="postcode" name="postcode" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="state">State *</label>
                        <input type="text" id="state" name="state" required>
                    </div>

                    <div class="form-group">
                        <label for="country">Country *</label>
                        <input type="text" id="country" name="country" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="contact_no">Contact No. (International code) *</label>
                    <input type="tel" id="contact_no" name="contact_no" placeholder="e.g. +60392819700" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label>Gender *</label>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="gender" value="male" required> Male
                        </label>
                        <label>
                            <input type="radio" name="gender" value="female"> Female
                        </label>
                    </div>
                </div>

                <div class="form-navigation">
                    <button type="button" class="btn-prev" style="visibility: hidden;">Previous</button>
                    <button type="button" class="btn-next">Next</button>
                </div>
            </div>

            <!-- Section B: Parents/Guardian Information -->
            <div class="form-section" id="section-b">
                <h2>SECTION B: PARENTS/GUARDIAN INFORMATION</h2>
                
                <div class="form-group">
                    <label for="guardian_name">Parents / Guardian Name *</label>
                    <input type="text" id="guardian_name" name="guardian_name" required>
                </div>

                <div class="form-group">
                    <label for="guardian_passport">Passport No.</label>
                    <input type="text" id="guardian_passport" name="guardian_passport">
                </div>

                <div class="form-group">
                    <label for="guardian_address">Address *</label>
                    <textarea id="guardian_address" name="guardian_address" rows="3" required></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="guardian_nationality">Nationality *</label>
                        <input type="text" id="guardian_nationality" name="guardian_nationality" required>
                    </div>

                    <div class="form-group">
                        <label for="guardian_postcode">Postcode *</label>
                        <input type="text" id="guardian_postcode" name="guardian_postcode" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="guardian_state">State *</label>
                        <input type="text" id="guardian_state" name="guardian_state" required>
                    </div>

                    <div class="form-group">
                        <label for="guardian_city">City *</label>
                        <input type="text" id="guardian_city" name="guardian_city" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="guardian_country">Country *</label>
                    <input type="text" id="guardian_country" name="guardian_country" required>
                </div>

                <div class="form-navigation">
                    <button type="button" class="btn-prev">Previous</button>
                    <button type="button" class="btn-next">Next</button>
                </div>
            </div>

            <!-- Section C: Qualification -->
            <div class="form-section" id="section-c">
                <h2>SECTION C: QUALIFICATION</h2>
                
                <h3>1. ACADEMIC QUALIFICATIONS (HIGHEST LEVEL)</h3>
                <table class="qualification-table">
                    <thead>
                        <tr>
                            <th>Qualification / Award</th>
                            <th>School / Institution Name</th>
                            <th>Grade / CGPA</th>
                            <th>Duration</th>
                            <th>Year Completed</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <input type="text" name="qualification[]" required>
                            </td>
                            <td>
                                <input type="text" name="institution[]" required>
                            </td>
                            <td>
                                <input type="text" name="grade[]" required>
                            </td>
                            <td>
                                <input type="text" name="duration[]" required>
                            </td>
                            <td>
                                <input type="text" name="year_completed[]" required>
                            </td>
                        </tr>
                        <tr>
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
                        </tr>
                    </tbody>
                </table>

                <h3>2. OTHER QUALIFICATION - ENGLISH PROFICIENCY</h3>
                <table class="proficiency-table">
                    <tr>
                        <td>Year:</td>
                        <td><input type="text" name="muet_year" placeholder="MUET"></td>
                        <td>Year:</td>
                        <td><input type="text" name="ielts_year" placeholder="IELTS"></td>
                        <td>Year:</td>
                        <td><input type="text" name="toefl_year" placeholder="TOEFL"></td>
                        <td>Year:</td>
                        <td><input type="text" name="toiec_year" placeholder="TOIEC"></td>
                    </tr>
                    <tr>
                        <td>SCORE:</td>
                        <td><input type="text" name="muet_score"></td>
                        <td>SCORE:</td>
                        <td><input type="text" name="ielts_score"></td>
                        <td>SCORE:</td>
                        <td><input type="text" name="toefl_score"></td>
                        <td>SCORE:</td>
                        <td><input type="text" name="toiec_score"></td>
                    </tr>
                </table>

                <div class="form-navigation">
                    <button type="button" class="btn-prev">Previous</button>
                    <button type="button" class="btn-next">Next</button>
                </div>
            </div>

            <!-- Section D: Programme Applied For -->
            <div class="form-section" id="section-d">
                <h2>SECTION D: PROGRAMME APPLIED FOR (HIGHLY PREFERRED TO LEAST PREFERRED)</h2>
                
                <table class="qualification-table">
                    <thead>
                        <tr>
                            <th>Choice</th>
                            <th>Programme Code</th>
                            <th>Programme Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>
                                <input type="text" name="programme_code_1">
                            </td>
                            <td>
                                <input type="text" name="programme_name_1" required>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>
                                <input type="text" name="programme_code_2">
                            </td>
                            <td>
                                <input type="text" name="programme_name_2">
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>
                                <input type="text" name="programme_code_3">
                            </td>
                            <td>
                                <input type="text" name="programme_name_3">
                            </td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>
                                <input type="text" name="programme_code_4">
                            </td>
                            <td>
                                <input type="text" name="programme_name_4">
                            </td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>
                                <input type="text" name="programme_code_5">
                            </td>
                            <td>
                                <input type="text" name="programme_name_5">
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="form-navigation">
                    <button type="button" class="btn-prev">Previous</button>
                    <button type="button" class="btn-next">Next</button>
                </div>
            </div>

            <!-- Section E: Financial Support -->
            <div class="form-section" id="section-e">
                <h2>SECTION E: FINANCIAL SUPPORT</h2>
                
                <div class="form-group">
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="financial_support" value="self" required> Self-Finance
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="account_no">Account No.</label>
                    <input type="text" id="account_no" name="account_no">
                </div>

                <div class="form-group">
                    <label for="bank_name">Bank Name</label>
                    <input type="text" id="bank_name" name="bank_name">
                </div>

                <div class="form-group">
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="financial_support" value="government"> Government Sponsored
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="financial_support" value="others"> Others
                        </label>
                    </div>
                    <input type="text" id="financial_support_others" name="financial_support_others" placeholder="Please specify">
                </div>

                <div class="form-navigation">
                    <button type="button" class="btn-prev">Previous</button>
                    <button type="button" class="btn-next">Next</button>
                </div>
            </div>

            <!-- Section F: Documents & Declaration -->
            <div class="form-section" id="section-f">
                <h2>SECTION F: DOCUMENTS & DECLARATION</h2>
                
                <div class="document-upload-container">
                    <h3>Required Documents</h3>
                    
                    <div class="form-group">
                        <label for="academic_certificates">Academic Certificates (O'Level, A'Level, or Related Certificates) *</label>
                        <input type="file" id="academic_certificates" name="academic_certificates" accept=".pdf,.jpg,.jpeg,.png" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="passport_copy">Passport Copy (All Pages) *</label>
                        <input type="file" id="passport_copy" name="passport_copy" accept=".pdf,.jpg,.jpeg,.png" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="health_declaration">Health Declaration Form *</label>
                        <input type="file" id="health_declaration" name="health_declaration" accept=".pdf,.jpg,.jpeg,.png" required>
                    </div>
                    
                    <div class="upload-instructions">
                        <p><strong>Important Notes:</strong></p>
                        <ul>
                            <li>Only PDF, JPG, JPEG, and PNG files are allowed</li>
                            <li>Maximum file size is 5MB per document</li>
                            <li>Ensure all documents are clear and legible</li>
                            <li>THREE (3) certified true copies of certificates must be provided during submission</li>
                            <li>EIGHT (8) passport size color photographs (3.5 cm X 5.0 cm) with white background and name written on reverse must be submitted</li>
                            <li>Please submit your application TWO (2) MONTHS before the beginning of a semester</li>
                        </ul>
                    </div>
                </div>
                
                <div class="declaration-text">
                    <p><strong>DECLARATION:</strong></p>
                    <p>I declare that information I provided to be true and correct and that any false information provided or lack of disclosure may lead to my application being rejected or termination of my enrolment.</p>
                </div>
                
                <div class="form-group">
                    <div class="checkbox-group">
                        <label>
                            <input type="checkbox" name="declaration_agree" required>
                            I agree with the declaration above
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="signature_date">Date:</label>
                    <input type="date" id="signature_date" name="signature_date" required>
                </div>

                <div class="form-navigation">
                    <button type="button" class="btn-prev">Previous</button>
                    <button type="submit" class="btn-submit">Submit Application</button>
                </div>
            </div>
        </form>
    </div>

    <script src="../assets/js/student-registration.js"></script>
</body>
</html>