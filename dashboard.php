<?php
session_start();
require_once 'conn.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit();
}

$student_id = $_SESSION['student_id'];
$message = '';

// Handle admission form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_admission'])) {
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $state = trim($_POST['state']);
    $pincode = trim($_POST['pincode']);
    $father_name = trim($_POST['father_name']);
    $mother_name = trim($_POST['mother_name']);
    $previous_school = trim($_POST['previous_school']);
    $course_applied = trim($_POST['course_applied']);
    $guardian_phone = trim($_POST['guardian_phone']);
    $emergency_contact = trim($_POST['emergency_contact']);
    $blood_group = trim($_POST['blood_group']);
    $nationality = trim($_POST['nationality']);
    $admission_date = $_POST['admission_date'];
    
    // Check if admission data already exists
    $check_stmt = $conn->prepare("SELECT id FROM student_admission WHERE student_id = ?");
    $check_stmt->bind_param("s", $student_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        // Update existing record
        $stmt = $conn->prepare("UPDATE student_admission SET 
            date_of_birth = ?, gender = ?, address = ?, city = ?, state = ?, pincode = ?,
            father_name = ?, mother_name = ?, previous_school = ?, course_applied = ?,
            guardian_phone = ?, emergency_contact = ?, blood_group = ?, nationality = ?,
            admission_date = ?
            WHERE student_id = ?");
        
        $stmt->bind_param("ssssssssssssssss", 
            $date_of_birth, $gender, $address, $city, $state, $pincode,
            $father_name, $mother_name, $previous_school, $course_applied,
            $guardian_phone, $emergency_contact, $blood_group, $nationality,
            $admission_date, $student_id
        );
    } else {
        // Insert new record
        $stmt = $conn->prepare("INSERT INTO student_admission 
            (student_id, date_of_birth, gender, address, city, state, pincode,
            father_name, mother_name, previous_school, course_applied,
            guardian_phone, emergency_contact, blood_group, nationality, admission_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param("ssssssssssssssss", 
            $student_id, $date_of_birth, $gender, $address, $city, $state, $pincode,
            $father_name, $mother_name, $previous_school, $course_applied,
            $guardian_phone, $emergency_contact, $blood_group, $nationality,
            $admission_date
        );
    }
    
    if ($stmt->execute()) {
        $message = '<div class="alert success">Admission form submitted successfully!</div>';
    } else {
        $message = '<div class="alert error">Error submitting form. Please try again.</div>';
    }
    $stmt->close();
    $check_stmt->close();
}

// Fetch student login details
$stmt = $conn->prepare("SELECT * FROM students_login WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

// Fetch admission data if exists
$stmt = $conn->prepare("SELECT * FROM student_admission WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$admission_result = $stmt->get_result();
$admission = $admission_result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #f5f7fa;
        }

        .header {
            background: linear-gradient(to right, #4b6cb7, #182848);
            color: white;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 1.8rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logout-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }

        .logout-btn:hover {
            background: #c82333;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        .profile-card, .admission-form {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            color: #182848;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #4b6cb7;
        }

        .profile-info {
            display: grid;
            gap: 15px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .info-label {
            font-weight: 600;
            color: #555;
        }

        .info-value {
            color: #333;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #555;
        }

        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        .btn {
            background: #4b6cb7;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background: #3a5ca9;
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .home-link {
            display: inline-block;
            margin-top: 20px;
            color: #4b6cb7;
            text-decoration: none;
            font-weight: 600;
        }

        .home-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>Student Dashboard</h1>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($student['full_name']); ?></span>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </div>

    <div class="container">
        <?php echo $message; ?>
        
        <div class="dashboard-grid">
            <div class="profile-card">
                <h2 class="section-title">Profile Information</h2>
                <div class="profile-info">
                    <div class="info-item">
                        <span class="info-label">Student ID:</span>
                        <span class="info-value"><?php echo htmlspecialchars($student['student_id']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Full Name:</span>
                        <span class="info-value"><?php echo htmlspecialchars($student['full_name']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email:</span>
                        <span class="info-value"><?php echo htmlspecialchars($student['email']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Phone:</span>
                        <span class="info-value"><?php echo htmlspecialchars($student['phone'] ?? 'Not provided'); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Registered On:</span>
                        <span class="info-value"><?php echo date('F j, Y', strtotime($student['created_at'])); ?></span>
                    </div>
                </div>
                
                <a href="index.php" class="home-link">← Back to Home</a>
            </div>

            <div class="admission-form">
                <h2 class="section-title">Admission Form</h2>
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="date_of_birth">Date of Birth</label>
                            <input type="date" id="date_of_birth" name="date_of_birth" 
                                   value="<?php echo isset($admission['date_of_birth']) ? htmlspecialchars($admission['date_of_birth']) : ''; ?>"
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male" <?php echo (isset($admission['gender']) && $admission['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo (isset($admission['gender']) && $admission['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php echo (isset($admission['gender']) && $admission['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="address">Address</label>
                            <textarea id="address" name="address" required><?php echo isset($admission['address']) ? htmlspecialchars($admission['address']) : ''; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" id="city" name="city" 
                                   value="<?php echo isset($admission['city']) ? htmlspecialchars($admission['city']) : ''; ?>"
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="state">State</label>
                            <input type="text" id="state" name="state" 
                                   value="<?php echo isset($admission['state']) ? htmlspecialchars($admission['state']) : ''; ?>"
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="pincode">Pincode</label>
                            <input type="text" id="pincode" name="pincode" 
                                   value="<?php echo isset($admission['pincode']) ? htmlspecialchars($admission['pincode']) : ''; ?>"
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="father_name">Father's Name</label>
                            <input type="text" id="father_name" name="father_name" 
                                   value="<?php echo isset($admission['father_name']) ? htmlspecialchars($admission['father_name']) : ''; ?>"
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="mother_name">Mother's Name</label>
                            <input type="text" id="mother_name" name="mother_name" 
                                   value="<?php echo isset($admission['mother_name']) ? htmlspecialchars($admission['mother_name']) : ''; ?>"
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="previous_school">Previous School</label>
                            <input type="text" id="previous_school" name="previous_school" 
                                   value="<?php echo isset($admission['previous_school']) ? htmlspecialchars($admission['previous_school']) : ''; ?>"
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="course_applied">Course Applied</label>
                            <input type="text" id="course_applied" name="course_applied" 
                                   value="<?php echo isset($admission['course_applied']) ? htmlspecialchars($admission['course_applied']) : ''; ?>"
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="guardian_phone">Guardian Phone</label>
                            <input type="tel" id="guardian_phone" name="guardian_phone" 
                                   value="<?php echo isset($admission['guardian_phone']) ? htmlspecialchars($admission['guardian_phone']) : ''; ?>"
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="emergency_contact">Emergency Contact</label>
                            <input type="tel" id="emergency_contact" name="emergency_contact" 
                                   value="<?php echo isset($admission['emergency_contact']) ? htmlspecialchars($admission['emergency_contact']) : ''; ?>"
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="blood_group">Blood Group</label>
                            <input type="text" id="blood_group" name="blood_group" 
                                   value="<?php echo isset($admission['blood_group']) ? htmlspecialchars($admission['blood_group']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="nationality">Nationality</label>
                            <input type="text" id="nationality" name="nationality" 
                                   value="<?php echo isset($admission['nationality']) ? htmlspecialchars($admission['nationality']) : ''; ?>"
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="admission_date">Admission Date</label>
                            <input type="date" id="admission_date" name="admission_date" 
                                   value="<?php echo isset($admission['admission_date']) ? htmlspecialchars($admission['admission_date']) : ''; ?>"
                                   required>
                        </div>
                    </div>
                    
                    <button type="submit" name="submit_admission" class="btn">Submit Admission Form</button>
                </form>
            </div>
        </div>
    </div>

    <script>
    // Set max date for date inputs to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('date_of_birth').max = today;
    document.getElementById('admission_date').max = today;
    
    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const guardianPhone = document.getElementById('guardian_phone').value.trim();
        const emergencyContact = document.getElementById('emergency_contact').value.trim();
        const pincode = document.getElementById('pincode').value.trim();
        
        // Clear any existing error highlights
        document.querySelectorAll('.error-border').forEach(el => {
            el.classList.remove('error-border');
        });
        
        let isValid = true;
        let errorMessage = '';
        
        // Guardian Phone validation
        if (guardianPhone === '') {
            isValid = false;
            errorMessage = 'Guardian phone number is required';
            document.getElementById('guardian_phone').classList.add('error-border');
        } else {
            // Remove any spaces, dashes, parentheses
            const cleanedPhone = guardianPhone.replace(/[\s\-\(\)]/g, '');
            // Check if it's a valid Indian phone number (10 digits, optionally with +91)
            const phoneRegex = /^(\+91)?[6-9]\d{9}$/;
            
            if (!phoneRegex.test(cleanedPhone)) {
                isValid = false;
                errorMessage = 'Please enter a valid Indian phone number for guardian (10 digits, starting with 6-9)';
                document.getElementById('guardian_phone').classList.add('error-border');
            }
        }
        
        // Emergency Contact validation
        if (emergencyContact === '') {
            isValid = false;
            if (errorMessage) errorMessage += '\n';
            errorMessage += 'Emergency contact number is required';
            document.getElementById('emergency_contact').classList.add('error-border');
        } else {
            // Remove any spaces, dashes, parentheses
            const cleanedEmergency = emergencyContact.replace(/[\s\-\(\)]/g, '');
            // Check if it's a valid Indian phone number
            const phoneRegex = /^(\+91)?[6-9]\d{9}$/;
            
            if (!phoneRegex.test(cleanedEmergency)) {
                isValid = false;
                if (errorMessage) errorMessage += '\n';
                errorMessage += 'Please enter a valid Indian phone number for emergency contact (10 digits, starting with 6-9)';
                document.getElementById('emergency_contact').classList.add('error-border');
            }
        }
        
        // Pincode validation
        if (pincode === '') {
            isValid = false;
            if (errorMessage) errorMessage += '\n';
            errorMessage += 'Pincode is required';
            document.getElementById('pincode').classList.add('error-border');
        } else {
            const pincodeRegex = /^[1-9][0-9]{5}$/;
            if (!pincodeRegex.test(pincode)) {
                isValid = false;
                if (errorMessage) errorMessage += '\n';
                errorMessage += 'Please enter a valid 6-digit pincode';
                document.getElementById('pincode').classList.add('error-border');
            }
        }
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fix the following errors:\n\n' + errorMessage);
            return false;
        }
        
        return true;
    });
    
    // Add CSS for error border
    const style = document.createElement('style');
    style.textContent = `
        .error-border {
            border: 2px solid #dc3545 !important;
            background-color: #fff5f5;
        }
        .error-border:focus {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
    `;
    document.head.appendChild(style);
</script>
</body>
</html>