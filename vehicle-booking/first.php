<?php
// ========================================
// FILE: api/config/database.php
// Database Configuration
// ========================================




// ========================================
// FILE: api/config/cors.php
// CORS Configuration
// ========================================



// ========================================
// FILE: api/helpers/jwt.php
// JWT Helper Functions
// ========================================



// ========================================
// FILE: api/auth/register.php
// User Registration
// ========================================



// ========================================
// FILE: api/auth/login.php
// User Login
// ========================================



// ========================================
// FILE: api/vehicles/list.php
// Get All Vehicles
// ========================================




// ========================================
// FILE: api/vehicles/details.php
// Get Vehicle Details
// ========================================




// ========================================
// FILE: api/bookings/create.php
// Create Booking
// ========================================



// ========================================
// FILE: api/bookings/user-bookings.php
// Get User's Bookings
// ========================================


// ========================================
// FILE: api/bookings/cancel.php
// Cancel Booking
// ========================================


// ========================================
// FILE: api/user/dashboard-stats.php
// Get User Dashboard Statistics
// ========================================



// ========================================
// FILE: database.sql
// Database Schema
// ========================================

/*
CREATE DATABASE vehicle_booking;
USE vehicle_booking;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Vehicles table
CREATE TABLE vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(50) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    status ENUM('Available', 'Booked', 'Unavailable') DEFAULT 'Available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bookings table
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('Active', 'Completed', 'Cancelled') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
);

-- Insert sample admin user (password: admin123)
INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@vehiclebook.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample vehicles
INSERT INTO vehicles (name, type, price, status) VALUES
('Tesla Model 3', 'Electric', 120.00, 'Available'),
('BMW X5', 'SUV', 150.00, 'Available'),
('Mercedes C-Class', 'Sedan', 130.00, 'Available'),
('Toyota Camry', 'Sedan', 80.00, 'Available'),
('Honda CR-V', 'SUV', 90.00, 'Available'),
('Audi A4', 'Sedan', 140.00, 'Available');
*/
?>