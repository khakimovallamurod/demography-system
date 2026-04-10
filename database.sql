-- Demography System Database
-- MySQL Database Structure

CREATE DATABASE IF NOT EXISTS demographydb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE demographydb;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Lectures table
CREATE TABLE IF NOT EXISTS lectures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    content LONGTEXT,
    file_path VARCHAR(255),
    file_name VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Practicals table
CREATE TABLE IF NOT EXISTS practicals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    content LONGTEXT,
    file_path VARCHAR(255),
    file_name VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tests table
CREATE TABLE IF NOT EXISTS tests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    duration INT DEFAULT 30,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Questions table
CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    test_id INT NOT NULL,
    question_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Options table
CREATE TABLE IF NOT EXISTS options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    option_text TEXT NOT NULL,
    is_correct TINYINT(1) DEFAULT 0,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Test Results table
CREATE TABLE IF NOT EXISTS test_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    test_id INT NOT NULL,
    score INT DEFAULT 0,
    total INT DEFAULT 0,
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Test Answers table
CREATE TABLE IF NOT EXISTS test_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    result_id INT NOT NULL,
    question_id INT NOT NULL,
    selected_option_id INT,
    FOREIGN KEY (result_id) REFERENCES test_results(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    FOREIGN KEY (selected_option_id) REFERENCES options(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default admin user (password: admin123)
INSERT INTO users (full_name, username, password, role) VALUES
('Administrator', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Test Foydalanuvchi', 'user1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');
-- Default password: password

-- Sample lectures
INSERT INTO lectures (title, description, content) VALUES
('Demografiya asoslari', 'Demografiya faniga kirish', 'Demografiya — aholini o''rganuvchi fan bo''lib, u aholi sonini, tarkibini, joylanishini va harakatini o''rganadi. Bu fan statistika va ijtimoiy fanlar bilan chambarchas bog''liq.'),
('Aholi o''sish nazariyalari', 'Malthus va zamonaviy nazariyalar', 'Tomas Malthus aholi geometrik progressiyada o''sishini, oziq-ovqat esa arifmetik progressiyada o''sishini ta''kidlagan. Zamonaviy demograflar bu nazariyani yanada rivojlantirgan.'),
('O''zbekiston demografiyasi', 'O''zbekiston aholi tahlili', 'O''zbekiston — Markaziy Osiyoning eng ko''p aholli davlati. 2024-yilga kelib aholi soni 37 million kishidan oshgan.');

-- Sample practicals
INSERT INTO practicals (title, description, content) VALUES
('Aholi o''sish sur''atini hisoblash', 'Amaliy mashg''ulot: formulalar va misollar', 'Aholi o''sish sur''ati quyidagi formula bilan hisoblanadi: r = (P2 - P1) / P1 × 100%. Bu yerda P1 - boshlang''ich aholi soni, P2 - yakuniy aholi soni.'),
('Yosh-jins piramidasi', 'Demografik piramidani qurish', 'Yosh-jins piramidasi aholining yoshga va jinsga ko''ra taqsimlanishini ko''rsatadi. Bu statistik vosita demografik tahlilning muhim qismi hisoblanadi.');

-- Sample test
INSERT INTO tests (title, description, duration) VALUES
('Demografiya asoslari testi', 'Demografiya fanining asosiy tushunchalarini tekshirish', 20);

INSERT INTO questions (test_id, question_text) VALUES
(1, 'Demografiya nima?'),
(1, 'O''zbekiston aholisi 2024-yilda necha million kishini tashkil etdi?'),
(1, 'Malthus qaysi asarda aholi nazariyasini bayon etgan?');

INSERT INTO options (question_id, option_text, is_correct) VALUES
(1, 'Aholini o''rganuvchi fan', 1),
(1, 'Geografiyaning bir bo''limi', 0),
(1, 'Iqtisodiyot fani', 0),
(1, 'Siyosat fani', 0),
(2, '37 million', 1),
(2, '25 million', 0),
(2, '45 million', 0),
(2, '30 million', 0),
(3, 'Aholi to''g''risidagi esse (1798)', 1),
(3, 'Kapital (1867)', 0),
(3, 'Boyliklar tabiati (1776)', 0),
(3, 'Iqtisodiy nazariya (1890)', 0);
