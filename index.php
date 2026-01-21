<?php
session_start();
require_once 'conn.php';

// Check if user is logged in
$logged_in = false;
$user_name = '';

if (isset($_SESSION['student_id'])) {
    $logged_in = true;
    $student_id = $_SESSION['student_id'];
    
    // Fetch user details
    $stmt = $conn->prepare("SELECT full_name FROM students_login WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $user_name = $user['full_name'];
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - Home</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            width: 100%;
            max-width: 1200px;
            min-height: 600px;
        }

        .header {
            background: linear-gradient(to right, #4b6cb7, #182848);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .content {
            display: flex;
            min-height: 500px;
        }

        .welcome-section {
            flex: 1;
            padding: 40px;
            background: #f8f9fa;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .welcome-section h2 {
            color: #182848;
            margin-bottom: 20px;
            font-size: 2rem;
        }

        .welcome-section p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
            font-size: 1.1rem;
        }

        .auth-section {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .btn {
            display: inline-block;
            padding: 15px 30px;
            margin: 10px 0;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
        }

        .btn-login {
            background: linear-gradient(to right, #4b6cb7, #182848);
            color: white;
            width: 100%;
        }

        .btn-register {
            background: transparent;
            color: #4b6cb7;
            border: 2px solid #4b6cb7;
            width: 100%;
        }

        .btn-logout {
            background: #dc3545;
            color: white;
            width: 200px;
            margin-top: 20px;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .btn-login:hover {
            background: linear-gradient(to right, #3a5ca9, #0d1f3d);
        }

        .btn-register:hover {
            background: #4b6cb7;
            color: white;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 30px;
        }

        .feature {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .feature h3 {
            color: #182848;
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .content {
                flex-direction: column;
            }
            
            .features {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎓 Student Portal</h1>
            <p>Welcome to Your Academic Management System</p>
        </div>
        
        <div class="content">
            <?php if ($logged_in): ?>
                <div class="welcome-section" style="flex: 2;">
                    <h2>Welcome back, <?php echo htmlspecialchars($user_name); ?>! 👋</h2>
                    <p>You are now logged into the Student Portal. Access your dashboard to view your profile, update information, and manage your academic records.</p>
                    
                    <div class="features">
                        <div class="feature">
                            <h3>📋 View Profile</h3>
                            <p>Check your personal and academic details</p>
                        </div>
                        <div class="feature">
                            <h3>✏️ Update Information</h3>
                            <p>Edit your admission form and contact details</p>
                        </div>
                        <div class="feature">
                            <h3>📊 Academic Records</h3>
                            <p>Access your grades and course information</p>
                        </div>
                        <div class="feature">
                            <h3>🔔 Notifications</h3>
                            <p>Stay updated with important announcements</p>
                        </div>
                    </div>
                    
                    <div style="margin-top: 30px;">
                        <a href="dashboard.php" class="btn btn-login">Go to Dashboard</a>
                        <a href="logout.php" class="btn btn-logout">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="welcome-section">
                    <h2>Welcome to Student Portal! 🎓</h2>
                    <p>Manage your academic journey with our comprehensive student portal. Access your profile, update information, and stay connected with your academic progress.</p>
                    
                    <div class="features">
                        <div class="feature">
                            <h3>Easy Registration</h3>
                            <p>Quick and simple sign-up process</p>
                        </div>
                        <div class="feature">
                            <h3>Secure Login</h3>
                            <p>Protected access to your personal data</p>
                        </div>
                        <div class="feature">
                            <h3>Profile Management</h3>
                            <p>Update your information anytime</p>
                        </div>
                        <div class="feature">
                            <h3>24/7 Access</h3>
                            <p>Available whenever you need it</p>
                        </div>
                    </div>
                </div>
                
                <div class="auth-section">
                    <h2 style="color: #182848; margin-bottom: 30px; text-align: center;">Get Started</h2>
                    <a href="login.php" class="btn btn-login">Login</a>
                    <a href="register.php" class="btn btn-register">Register Now</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>