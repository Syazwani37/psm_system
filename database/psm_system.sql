-- PSM System Database Schema

CREATE DATABASE IF NOT EXISTS psm_system;
USE psm_system;

-- 1. Users table
-- Stores registration data for Mothers, Professionals (Doctors/Experts), and Admins.
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('mother', 'professional', 'admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Consultations table
-- Manages session bookings and professional responses.
CREATE TABLE IF NOT EXISTS consultations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mother_id INT NOT NULL,
    professional_id INT,
    status ENUM('pending', 'accepted', 'rescheduled', 'completed', 'cancelled') DEFAULT 'pending',
    session_date DATETIME,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mother_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (professional_id) REFERENCES users(id) ON DELETE SET NULL
);

-- 3. Resources table
-- Metadata for expert articles, support resources, and plans.
CREATE TABLE IF NOT EXISTS resources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category ENUM('expert_article', 'support_resource', 'nutrition_plan', 'exercise_plan') NOT NULL,
    file_path VARCHAR(255),
    external_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. Community Posts table
-- For the support forum and communication tools.
CREATE TABLE IF NOT EXISTS community_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 5. Recovery Tracking table
-- Detailed metrics for mothers to monitor their progress.
CREATE TABLE IF NOT EXISTS recovery_tracking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mother_id INT NOT NULL,
    tracking_date DATE NOT NULL,
    weight DECIMAL(5,2),
    mood VARCHAR(50),
    baby_weight DECIMAL(5,2),
    baby_height DECIMAL(5,2),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mother_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 6. Notifications table
-- Alerts mothers/professionals about booking statuses or new resources.
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
