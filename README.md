student-portal/
│
├── index.php          # Homepage
├── login.php          # Login page
├── register.php       # Registration page
├── dashboard.php      # Dashboard with admission form
├── logout.php         # Logout script
├── conn.php           # Database connection
├── style.css          # Additional CSS (optional)
│
├── README.md          # Project documentation
└── database/          # SQL files
    └── schema.sql




1.	Complete Authentication System
o	Secure registration with password hashing
o	Session-based login system
o	Protected dashboard access
2.	Database Integration
o	MySQL database with two related tables
o	Prepared statements to prevent SQL injection
o	Proper data validation
3.	Responsive Design
o	Mobile-friendly interface
o	Clean, modern UI with gradients
o	Consistent styling across pages
4.	Form Validation
o	Client-side JavaScript validation
o	Server-side PHP validation
o	Real-time feedback
5.	Security Features
o	Password hashing using password_hash()
o	Session management
o	Input sanitization
o	SQL injection prevention
Setup Instructions:
1.	Database Setup:
o	Create a MySQL database named student_portal
o	Import the SQL schema provided above
o	Update database credentials in conn.php
2.	File Structure:
o	Place all PHP files in your web server's document root
o	Ensure PHP and MySQL are installed and running
3.	Configuration:
o	Update database credentials in conn.php
o	Adjust file paths if needed
o	Set proper permissions for uploads (if adding file upload feature)
4.	Access the Application:
o	Open http://localhost/student-portal/index.php in your browser
o	Register a new account or login with existing credentials

# student_portal
student_portal
