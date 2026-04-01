-- First drop existing tables
DROP TABLE IF EXISTS results;
DROP TABLE IF EXISTS questions;
DROP TABLE IF EXISTS users;

-- Create fresh tables
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_text TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,
    correct_option CHAR(1) NOT NULL,
    marks INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    score INT,
    total_questions INT,
    percentage DECIMAL(5,2),
    exam_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert admin (password: admin123)
INSERT INTO users (username, email, password, role) 
VALUES ('admin', 'admin@exam.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample questions
INSERT INTO questions (question_text, option_a, option_b, option_c, option_d, correct_option) VALUES
('What does PHP stand for?', 'Personal Home Page', 'Preprocessed Hypertext Page', 'PHP: Hypertext Preprocessor', 'Public Hosting Page', 'C'),
('What is the correct way to start a session in PHP?', 'session_start()', 'start_session()', 'Session::start()', 'begin_session()', 'A'),
('Which of the following is a valid PHP variable?', '$var_name', 'var_name', '&var_name', 'var-name', 'A'),
('What does SQL stand for?', 'Structured Question Language', 'Structured Query Language', 'Simple Query Language', 'Strong Question Language', 'B'),
('Which function is used to connect to MySQL in PHP?', 'mysql_connect()', 'mysqli_connect()', 'pdo_connect()', 'db_connect()', 'B');