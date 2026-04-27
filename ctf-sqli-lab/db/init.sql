-- ============================================
-- CTF SQL Injection Lab - Database Setup
-- Basic Penetration Testing Course
-- ============================================

CREATE DATABASE IF NOT EXISTS ctf_company;
USE ctf_company;

-- ============================================
-- USERS TABLE (Login bypass challenge)
-- ============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(150),
    role ENUM('user', 'editor', 'admin') DEFAULT 'user',
    full_name VARCHAR(200),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1
);

INSERT INTO users (username, password, email, role, full_name) VALUES
('admin', MD5('Sup3rS3cur3P@ss!'), 'admin@ctfcompany.local', 'admin', 'System Administrator'),
('john.doe', MD5('john2024'), 'john@ctfcompany.local', 'editor', 'John Doe'),
('jane.smith', MD5('janepass'), 'jane@ctfcompany.local', 'user', 'Jane Smith'),
('bob.wilson', MD5('bobwilson99'), 'bob@ctfcompany.local', 'user', 'Bob Wilson'),
('alice.tech', MD5('alice!tech'), 'alice@ctfcompany.local', 'editor', 'Alice Technology'),
('FLAG_USER', MD5('FLAG{sql1_l0g1n_byp4ss_succ3ss}'), 'flag@ctfcompany.local', 'admin', 'FLAG{sql1_l0g1n_byp4ss_succ3ss}');

-- ============================================
-- PRODUCTS TABLE (UNION-based injection)
-- ============================================
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2),
    category VARCHAR(100),
    stock INT DEFAULT 0,
    image_url VARCHAR(300),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO products (name, description, price, category, stock) VALUES
('Laptop ProMax 15', 'High-performance laptop with 16GB RAM, 512GB SSD, Intel i7 12th Gen', 15999000, 'Electronics', 25),
('Wireless Mouse X200', 'Ergonomic wireless mouse with 2.4GHz connectivity', 299000, 'Accessories', 150),
('Mechanical Keyboard RGB', 'Cherry MX Blue switches with RGB backlighting', 1299000, 'Accessories', 75),
('Monitor UltraWide 34"', '34-inch ultrawide curved monitor, 3440x1440, 144Hz', 8999000, 'Electronics', 15),
('USB-C Hub 7-in-1', 'Multiport adapter with HDMI, USB 3.0, SD card reader', 599000, 'Accessories', 200),
('SSD NVMe 1TB', 'High-speed NVMe SSD with 3500MB/s read speed', 1599000, 'Storage', 100),
('Webcam HD 1080p', 'Full HD webcam with built-in microphone and auto-focus', 799000, 'Electronics', 50),
('Network Cable Cat6 10m', 'Premium Cat6 ethernet cable, gold-plated connectors', 89000, 'Networking', 500),
('Router WiFi 6', 'Dual-band WiFi 6 router with mesh capability', 2499000, 'Networking', 30),
('Power Bank 20000mAh', 'Fast charging power bank with PD and QC support', 449000, 'Accessories', 120);

-- ============================================
-- SECRET FLAGS TABLE (Hidden - discoverable via UNION)
-- ============================================
CREATE TABLE secret_flags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    flag_name VARCHAR(100),
    flag_value VARCHAR(200),
    difficulty VARCHAR(50),
    hint TEXT
);

INSERT INTO secret_flags (flag_name, flag_value, difficulty, hint) VALUES
('Database Discovery', 'FLAG{un10n_b4s3d_d4t4b4s3_3xpl0r3r}', 'Easy', 'Use UNION to explore database schema'),
('Table Explorer', 'FLAG{t4bl3_enum3r4t10n_m4st3r}', 'Medium', 'Enumerate all tables in the database'),
('Column Digger', 'FLAG{c0lumn_3xtr4ct10n_pr0}', 'Medium', 'Extract column names from tables'),
('Data Exfiltration', 'FLAG{d4t4_3xf1ltr4t10n_c0mpl3t3}', 'Hard', 'Extract sensitive data from hidden tables'),
('File Reader', 'FLAG{l04d_f1l3_vuln3r4b1l1ty}', 'Hard', 'Use LOAD_FILE to read server files');

-- ============================================
-- EMPLOYEE DIRECTORY (Blind SQL Injection)
-- ============================================
CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emp_id VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    department VARCHAR(100),
    position_title VARCHAR(150),
    salary DECIMAL(12,2),
    phone VARCHAR(20),
    hire_date DATE,
    is_active TINYINT(1) DEFAULT 1
);

INSERT INTO employees (emp_id, first_name, last_name, department, position_title, salary, phone, hire_date) VALUES
('EMP001', 'Ahmad', 'Rizki', 'IT Security', 'Security Analyst', 15000000, '+62-812-3456-7890', '2022-01-15'),
('EMP002', 'Siti', 'Nurhaliza', 'Human Resources', 'HR Manager', 18000000, '+62-813-2345-6789', '2021-03-20'),
('EMP003', 'Budi', 'Santoso', 'Engineering', 'Senior Developer', 22000000, '+62-814-3456-7891', '2020-07-01'),
('EMP004', 'Dewi', 'Kartika', 'Finance', 'Financial Controller', 25000000, '+62-815-4567-8901', '2019-11-10'),
('EMP005', 'Reza', 'Pratama', 'IT Security', 'Penetration Tester', 20000000, '+62-816-5678-9012', '2023-02-28'),
('EMP006', 'FLAG', 'HOLDER', 'SECRET', 'FLAG{bl1nd_sql1_d4t4_3xtr4ct3d}', 99999999, 'FLAG-PHONE', '2024-01-01');

-- ============================================
-- ADMIN CREDENTIALS TABLE (Error-based injection)
-- ============================================
CREATE TABLE admin_credentials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_user VARCHAR(100),
    admin_hash VARCHAR(255),
    access_level INT,
    last_login TIMESTAMP,
    secret_note TEXT
);

INSERT INTO admin_credentials (admin_user, admin_hash, access_level, last_login, secret_note) VALUES
('superadmin', MD5('admin@ctf2024'), 10, NOW(), 'FLAG{3rr0r_b4s3d_1nj3ct10n_w1n}'),
('backup_admin', MD5('backup!pass'), 5, NOW(), 'Backup access for emergency'),
('dev_admin', MD5('devmode123'), 3, NOW(), 'Development admin account');

-- ============================================
-- NEWS / ARTICLES (Second-order injection)
-- ============================================
CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(300) NOT NULL,
    content TEXT,
    author VARCHAR(100),
    category VARCHAR(50),
    views INT DEFAULT 0,
    published_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_published TINYINT(1) DEFAULT 1
);

INSERT INTO articles (title, content, author, category, views) VALUES
('Keamanan Siber di Era Digital', 'Artikel tentang pentingnya keamanan siber dalam kehidupan sehari-hari. Serangan siber semakin meningkat setiap tahunnya dan organisasi perlu meningkatkan keamanan infrastruktur mereka.', 'Ahmad Rizki', 'Security', 1250),
('Panduan SQL untuk Pemula', 'SQL (Structured Query Language) adalah bahasa pemrograman yang digunakan untuk mengelola database relasional. Artikel ini membahas dasar-dasar SQL termasuk SELECT, INSERT, UPDATE, dan DELETE.', 'Budi Santoso', 'Tutorial', 3420),
('Top 10 Vulnerability Web Application', 'OWASP Top 10 adalah daftar kerentanan keamanan aplikasi web yang paling kritis. Injection, Broken Authentication, dan XSS merupakan tiga teratas dalam daftar tersebut.', 'Reza Pratama', 'Security', 5670),
('Pengenalan Penetration Testing', 'Penetration testing adalah metode evaluasi keamanan sistem komputer dengan mensimulasikan serangan dari pihak jahat. Proses ini meliputi reconnaissance, scanning, exploitation, dan reporting.', 'Reza Pratama', 'Security', 4100),
('Tips Mengamankan Database', 'Database merupakan aset penting yang harus dilindungi. Gunakan parameterized queries, least privilege principle, dan regular patching untuk mengamankan database Anda.', 'Ahmad Rizki', 'Tutorial', 2890);

-- ============================================
-- FEEDBACK TABLE (for stored/second-order SQLi)
-- ============================================
CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(150),
    subject VARCHAR(200),
    message TEXT,
    rating INT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_reviewed TINYINT(1) DEFAULT 0
);

-- ============================================
-- GRANT PERMISSIONS (intentionally weak)
-- ============================================
CREATE USER IF NOT EXISTS 'ctfuser'@'localhost' IDENTIFIED BY 'ctfpass123';
GRANT ALL PRIVILEGES ON ctf_company.* TO 'ctfuser'@'localhost';
GRANT FILE ON *.* TO 'ctfuser'@'localhost';
FLUSH PRIVILEGES;
