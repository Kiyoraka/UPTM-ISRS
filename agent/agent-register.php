<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Registration - UPTM ISRS</title>
    <link rel="stylesheet" href="../assets/css/agent-register.css">
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
                <span class="step-number">5</span>
                <span class="step-text">Bank Account Details</span>
            </div>
            <div class="step">
                <span class="step-number">6</span>
                <span class="step-text">Agreement & Declaration</span>
            </div>
            <div class="step">
                <span class="step-number">7</span>
                <span class="step-text">Documents Upload</span>
            </div>
        </div>

        <!-- Logo -->
        <div class="logo-container">
            <img src="../assets/img/uptm-logo.png" alt="UPTM Logo" class="logo">
        </div>

        <h1>INTERNATIONAL STUDENT RECRUITMENT AGENT APPLICATION FORM</h1>
        
        <form id="agentRegistrationForm">
            <div class="form-section active" id="section-a">
                <h2>Section A: Personal Details</h2>
                
                <div class="form-group">
                    <label>Type of Application *</label>
                    <div class="radio-group">
                        <label><input type="radio" name="application_type" value="new" required> New *</label>
                        <label><input type="radio" name="application_type" value="renew"> Renew *</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="company_name">Company Name / Individual Name *</label>
                    <input type="text" id="company_name" name="company_name" required>
                </div>

                <div class="form-group">
                    <label for="registration_no">Company Registration No. / NRIC / Passport No * *</label>
                    <input type="text" id="registration_no" name="registration_no" required>
                </div>

                <div class="form-group">
                    <label for="address">Correspondence Address *</label>
                    <textarea id="address" name="address" rows="3" required></textarea>
                </div>

                <div class="form-group">
                    <label for="postal_code">Postal Code *</label>
                    <input type="text" id="postal_code" name="postal_code" required>
                </div>

                <div class="form-group">
                    <label for="country">Country *</label>
                    <select id="country" name="country" required>
                        <option value="">Select Country</option>
                        <option value="MY">Malaysia</option>
                        <option value="SG">Singapore</option>
                        <option value="ID">Indonesia</option>
                    </select>
                </div>

                <div class="form-navigation">
                    <button type="button" class="btn-next">Next</button>
                </div>
            </div>

            <div class="form-section" id="section-b">
                <h2>Section B: Contact Information</h2>
                
                <div class="form-group">
                    <label for="contact_name">Contact Person Name *</label>
                    <input type="text" id="contact_name" name="contact_name" required>
                </div>

                <div class="form-group">
                    <label for="contact_designation">Designation *</label>
                    <input type="text" id="contact_designation" name="contact_designation" required>
                </div>

                <div class="form-group">
                    <label for="contact_phone">Telephone (Office) *</label>
                    <input type="tel" id="contact_phone" name="contact_phone" required>
                </div>

                <div class="form-group">
                    <label for="contact_fax">Fax</label>
                    <input type="tel" id="contact_fax" name="contact_fax">
                </div>

                <div class="form-group">
                    <label for="contact_mobile">Mobile Phone *</label>
                    <input type="tel" id="contact_mobile" name="contact_mobile" required>
                </div>

                <div class="form-group">
                    <label for="contact_email">Email Address *</label>
                    <input type="email" id="contact_email" name="contact_email" required>
                </div>

                <div class="form-group">
                    <label for="website">Website Address</label>
                    <input type="url" id="website" name="website">
                </div>

                <div class="form-group">
                    <label for="countries_covered">Countries Covered *</label>
                    <input type="text" id="countries_covered" name="countries_covered" required>
                </div>

                <div class="form-navigation">
                    <button type="button" class="btn-prev">Previous</button>
                    <button type="button" class="btn-next">Next</button>
                </div>
            </div>
        </form>
    </div>

    <script src="../assets/js/agent-register.js"></script>
</body>
</html>