<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Registration - UPTM ISRS</title>
    <link rel="stylesheet" href="../assets/css/agent-register.css">
    <link rel="stylesheet" href="../assets/css/agent-ProgressBarForm.css">
    <link rel="stylesheet" href="../assets/css/agent-RegisterForm1.css">
    <link rel="stylesheet" href="../assets/css/agent-RegisterForm2.css">
    <link rel="stylesheet" href="../assets/css/agent-RegisterForm3.css">
    <link rel="stylesheet" href="../assets/css/agent-RegisterForm4.css">
    <link rel="stylesheet" href="../assets/css/agent-RegisterForm5.css">
    <link rel="stylesheet" href="../assets/css/agent-RegisterForm6.css">
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
                <span class="step-text">Contact Information</span>
            </div>
            <div class="step">
                <span class="step-number">3</span>
                <span class="step-text">Experience Details</span>
            </div>
            <div class="step">
                <span class="step-number">4</span>
                <span class="step-text">Bank Account Details</span>
            </div>
            <div class="step">
                <span class="step-number">5</span>
                <span class="step-text">Agreement & Declaration</span>
            </div>
            <div class="step">
                <span class="step-number">6</span>
                <span class="step-text">Documents Upload</span>
            </div>
        </div>

        <!-- Logo -->
        <div class="logo-container">
            <a href="../index.html" class="logo-link">
                <img src="../assets/img/uptm-logo.png" alt="UPTM Logo" class="logo">
            </a>
        </div>

        <h1>INTERNATIONAL STUDENT RECRUITMENT AGENT APPLICATION FORM</h1>
        
        <form id="agentRegistrationForm" action="agent-registration.php" method="POST" enctype="multipart/form-data">
            <!-- Personal Details Section -->
            <div class="form-section active" id="section-a">
                <h2>Section A: Personal Details</h2>
                
                <div class="form-group">
                    <label>Type of Application *</label>
                    <div class="radio-group">
                        <label><input type="radio" name="application_type" value="new" required> New</label>
                        <label><input type="radio" name="application_type" value="renew"> Renew</label>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="company_name">Company Name / Individual Name *</label>
                        <input type="text" id="company_name" name="company_name" required>
                    </div>

                    <div class="form-group">
                        <label for="registration_no">Registration / NRIC / Passport No. *</label>
                        <input type="text" id="registration_no" name="registration_no" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Correspondence Address *</label>
                    <textarea id="address" name="address" rows="3" required></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="postal_code">Postal Code *</label>
                        <input type="text" id="postal_code" name="postal_code" required>
                    </div>

                    <div class="form-group">
                        <label for="country">Country *</label>
                        <select id="country" name="country" required>
                            <option value="" disabled selected>Select Country</option>
                            <option value="MY">Malaysia</option>
                            <option value="SG">Singapore</option>
                            <option value="ID">Indonesia</option>
                        </select>
                    </div>
                </div>

                <div class="form-navigation">
                    <button type="button" class="btn-next">Next</button>
                </div>
            </div>

            <!-- Contact Information Section -->
            <div class="form-section" id="section-b">
                <h2>Section B: Contact Information</h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="contact_name">Contact Person Name *</label>
                        <input type="text" id="contact_name" name="contact_name" required>
                    </div>

                    <div class="form-group">
                        <label for="contact_designation">Designation *</label>
                        <input type="text" id="contact_designation" name="contact_designation" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="contact_phone">Telephone (Office) *</label>
                        <input type="tel" id="contact_phone" name="contact_phone" required>
                    </div>

                    <div class="form-group">
                        <label for="contact_fax">Fax</label>
                        <input type="tel" id="contact_fax" name="contact_fax">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="contact_mobile">Mobile Phone *</label>
                        <input type="tel" id="contact_mobile" name="contact_mobile" required>
                    </div>

                    <div class="form-group">
                        <label for="contact_email">Email Address *</label>
                        <input type="email" id="contact_email" name="contact_email" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="website">Website Address</label>
                        <input type="url" id="website" name="website">
                    </div>
                </div>

                <div class="form-navigation">
                    <button type="button" class="btn-prev">Previous</button>
                    <button type="button" class="btn-next">Next</button>
                </div>
            </div>

            <!-- Experience Details Section -->
            <div class="form-section" id="section-c">
                <h2>Section C: Experience Details</h2>
                
                <div class="experience-section">
                    <div class="form-group countries-covered">
                        <label>The countries you cover:</label>
                        <select name="countries_covered" class="form-input" required>
                            <option value="" disabled selected>Select Country</option>
                            <option value="MY">Malaysia</option>
                            <option value="SG">Singapore</option>
                            <option value="ID">Indonesia</option>
                            <option value="TH">Thailand</option>
                            <option value="VN">Vietnam</option>
                            <option value="PH">Philippines</option>
                            <option value="CN">China</option>
                            <option value="IN">India</option>
                            <option value="JP">Japan</option>
                            <option value="KR">South Korea</option>
                            <option value="US">United States</option>
                            <option value="GB">United Kingdom</option>
                            <option value="AU">Australia</option>
                            <option value="CA">Canada</option>
                            <option value="NZ">New Zealand</option>
                        </select>
                    </div>

                    <div class="form-group recruitment-experience">
                        <label>
                            Have you / Has your company ever been appointed as a recruitment agent by other educational institutions?
                        </label>
                        <div class="radio-group">
                            <label>
                                <input type="radio" name="recruitment_experience" value="yes" required> Yes
                            </label>
                            <label>
                                <input type="radio" name="recruitment_experience" value="no"> No
                            </label>
                        </div>
                    </div>

                    <div id="experience-details" style="display: none;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="width: 50px; text-align: center; border: 1px solid #e2e8f0; padding: 10px;">No.</th>
                                    <th style="width: 25%; border: 1px solid #e2e8f0; padding: 10px;">Institution</th>
                                    <th style="width: 15%; border: 1px solid #e2e8f0; padding: 10px;">From</th>
                                    <th style="width: 15%; border: 1px solid #e2e8f0; padding: 10px;">Until</th>
                                    <th style="width: 15%; border: 1px solid #e2e8f0; padding: 10px;">No. Recruited</th>
                                    <th style="width: 100px; text-align: center; border: 1px solid #e2e8f0; padding: 10px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="experience-table-body">
                                <tr>
                                    <td style="text-align: center; border: 1px solid #e2e8f0; padding: 10px;">1</td>
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
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="form-navigation">
                    <button type="button" class="btn back-btn btn-prev">Previous</button>
                    <button type="button" class="btn login-btn btn-next">Next</button>
                </div>
            </div>

            <!-- Bank Account Details Section -->
            <div class="form-section" id="section-d">
                <h2>Section D: Bank Account Details</h2>
                <div class="bank-account-container">
                    <table class="bank-account-table">
                        <tbody>
                            <tr>
                                <td class="label-cell">
                                    <label for="account_name">Account Name:</label>
                                </td>
                                <td class="input-cell">
                                    <input type="text" id="account_name" name="account_name" class="form-input">
                                </td>
                            </tr>
                            <tr>
                                <td class="label-cell">
                                    <label for="account_no">Account No.:</label>
                                </td>
                                <td class="input-cell">
                                    <input type="text" id="account_no" name="account_no" class="form-input">
                                </td>
                            </tr>
                            <tr>
                                <td class="label-cell">
                                    <label for="bank_name">Bank Name:</label>
                                </td>
                                <td class="input-cell">
                                    <input type="text" id="bank_name" name="bank_name" class="form-input">
                                </td>
                            </tr>
                            <tr>
                                <td class="label-cell">
                                    <label for="bank_branch">Bank Branch:</label>
                                </td>
                                <td class="input-cell">
                                    <input type="text" id="bank_branch" name="bank_branch" class="form-input">
                                </td>
                            </tr>
                            <tr>
                                <td class="label-cell">
                                    <label for="swift_code">Swift Code:</label>
                                </td>
                                <td class="input-cell">
                                    <input type="text" id="swift_code" name="swift_code" class="form-input">
                                </td>
                            </tr>
                            <tr>
                                <td class="label-cell">
                                    <label for="bank_address">Bank Address:</label>
                                </td>
                                <td class="input-cell">
                                    <textarea id="bank_address" name="bank_address" class="form-input" rows="3"></textarea>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="form-navigation">
                    <button type="button" class="btn back-btn btn-prev">Previous</button>
                    <button type="button" class="btn login-btn btn-next">Next</button>
                </div>
            </div>

            <!-- Agreement & Declaration Details Section -->
            <div class="form-section" id="section-e">
                <h2>Section E: Agreement & Declaration</h2>
                <div class="agreement-container">
                    <table class="agreement-table">
                        <thead>
                            <tr>
                                <th class="section-header">SIGNEE OF AGREEMENT</th>
                                <th class="section-header">WITNESS OF AGREEMENT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <label for="signee_full_name">Full Name:</label>
                                    <input type="text" id="signee_full_name" name="signee_full_name" class="form-input">
                                </td>
                                <td>
                                    <label for="witness_full_name">Full Name:</label>
                                    <input type="text" id="witness_full_name" name="witness_full_name" class="form-input">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="signee_designation">Designation:</label>
                                    <input type="text" id="signee_designation" name="signee_designation" class="form-input">
                                </td>
                                <td>
                                    <label for="witness_designation">Designation:</label>
                                    <input type="text" id="witness_designation" name="witness_designation" class="form-input">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="signee_nric">NRIC/Passport:</label>
                                    <input type="text" id="signee_nric" name="signee_nric" class="form-input">
                                </td>
                                <td>
                                    <label for="witness_nric">NRIC/Passport:</label>
                                    <input type="text" id="witness_nric" name="witness_nric" class="form-input">
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <table class="declaration-table">
                        <thead>
                            <tr>
                                <th class="section-header">DECLARATION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="declaration-text">
                                    <p>I hereby declare that the information given in this application form is true and complete.</p>
                                    <p>I understand that any false or incomplete information submitted may result my application being rejected.</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="signature-section">
                                        <div class="signature-row">
                                            <label for="signature_checkbox">
                                                <input type="checkbox" id="signature_checkbox" name="signature_confirmation" required>
                                                I confirm the above declaration is true
                                            </label>
                                        </div>
                                        <div class="signature-row">
                                            <label for="signature_date">Date:</label>
                                            <input type="date" id="signature_date" name="signature_date" class="form-input" required>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="form-navigation">
                    <button type="button" class="btn back-btn btn-prev">Previous</button>
                    <button type="button" class="btn login-btn btn-next">Next</button>
                </div>
            </div>

            <!-- Document Upload Details Section -->
            <div class="form-section" id="section-f">
                <h2>Section F: Documents Upload</h2>
                <div class="document-upload-container">
                    <table class="document-checklist-table">
                        <thead>
                            <tr>
                                <th class="section-header">APPLICATION CHECKLIST (please submit certified true copies of the following documents)</th>
                            </tr>
                        </thead>
                    </table>

                    <div class="agent-type-selection">
                        <label>Select Agent Type:</label>
                        <div class="radio-group">
                            <label>
                                <input type="radio" name="agent_type" value="corporate" required> Corporate Agent
                            </label>
                            <label>
                                <input type="radio" name="agent_type" value="individual"> Individual Agent
                            </label>
                        </div>
                    </div>

                    <div id="corporate-documents" class="document-section" style="display: none;">
                        <h3>Corporate Agent Documents</h3>
                        <div class="form-group">
                            <label for="company_profile">1. Company Profile</label>
                            <input type="file" id="company_profile" name="company_profile" class="form-input" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        </div>
                        <div class="form-group">
                            <label for="forms_24">2. Form 24</label>
                            <input type="file" id="forms_24" name="forms_24" class="form-input" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        </div>
                        <div class="form-group">
                            <label for="forms_24">3. Form 32A</label>
                            <input type="file" id="forms_32A" name="forms_32A" class="form-input" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        </div>
                        <div class="form-group">
                            <label for="forms_24">4. Form 44</label>
                            <input type="file" id="forms_44" name="forms_44" class="form-input" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        </div>
                        <div class="form-group">
                            <label for="forms_24">5. Form 49</label>
                            <input type="file" id="forms_49" name="forms_49" class="form-input" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        </div>
                    </div>

                    <div id="individual-documents" class="document-section" style="display: none;">
                        <h3>Individual Agent Documents</h3>
                        <div class="form-group">
                            <label for="curriculum_vitae">1. Curriculum Vitae</label>
                            <input type="file" id="curriculum_vitae" name="curriculum_vitae" class="form-input" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        </div>
                        <div class="form-group">
                            <label for="passport_copy">2. Copy of Passport / My Kad</label>
                            <input type="file" id="passport_copy" name="passport_copy" class="form-input" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        </div>
                    </div>

                    <div class="upload-instructions">
                        <p><strong>Important:</strong></p>
                        <ul>
                            <li>Only PDF, DOC, DOCX, JPG, JPEG, and PNG files are allowed</li>
                            <li>Maximum file size is 5MB per document</li>
                            <li>Ensure all documents are clear and legible</li>
                        </ul>
                    </div>
                </div>

                <div class="form-navigation">
                    <button type="button" class="btn back-btn btn-prev">Previous</button>
                    <button type="submit" class="btn btn-next">Submit Application</button>
                </div>
            </div>
        </form>
    </div>

    <script src="../assets/js/agent-register.js"></script>
    <script src="../assets/js/agent-registerform3.js"></script>
    <script src="../assets/js/agent-registerform6.js"></script>
</body>
</html>
