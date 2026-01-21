-- Create database
CREATE DATABASE IF NOT EXISTS student_portal;
USE student_portal;

-- Table for login/registration
CREATE TABLE IF NOT EXISTS students_login (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id VARCHAR(20) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for admission form data
CREATE TABLE IF NOT EXISTS student_admission (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id VARCHAR(20) UNIQUE NOT NULL,
    date_of_birth DATE,
    gender ENUM('Male', 'Female', 'Other'),
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    pincode VARCHAR(10),
    father_name VARCHAR(100),
    mother_name VARCHAR(100),
    previous_school VARCHAR(200),
    course_applied VARCHAR(100),
    guardian_phone VARCHAR(15),
    emergency_contact VARCHAR(15),
    blood_group VARCHAR(5),
    nationality VARCHAR(50),
    admission_date DATE,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students_login(student_id) ON DELETE CASCADE
);