-- PSM System Database Schema

CREATE DATABASE IF NOT EXISTS psm_system;
USE psm_system;

-- 1. Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('mother', 'professional', 'admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Consultations table
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
CREATE TABLE IF NOT EXISTS community_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 5. Recovery Tracking table
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
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 7. Announcements table
CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_by INT NOT NULL,
    type ENUM('info', 'warning', 'success', 'danger') DEFAULT 'info',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- 8. Baby Growth table
CREATE TABLE IF NOT EXISTS baby_growth (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    weight_kg DECIMAL(5,2),
    height_cm DECIMAL(5,2),
    head_circ_cm DECIMAL(5,2),
    notes TEXT,
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 9. Mom's Journal table
CREATE TABLE IF NOT EXISTS moms_journal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    mood VARCHAR(50) NOT NULL,
    entry_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 10. EPDS Responses table
CREATE TABLE IF NOT EXISTS epds_responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    score INT NOT NULL,
    risk_level VARCHAR(50) NOT NULL,
    responses JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 11. Baby Milestones table
CREATE TABLE IF NOT EXISTS baby_milestones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    milestone_id VARCHAR(50) NOT NULL,
    milestone_name VARCHAR(100) NOT NULL,
    is_achieved BOOLEAN DEFAULT FALSE,
    achieved_at DATETIME NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 12. Symptom Logs table
CREATE TABLE IF NOT EXISTS symptom_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    week_postpartum INT,
    temperature DECIMAL(4,1),
    pain_level VARCHAR(50),
    wound_condition VARCHAR(50),
    bleeding_status VARCHAR(50),
    mood_status VARCHAR(50),
    result_status VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

