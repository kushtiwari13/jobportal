-- ProJobs database schema (MySQL 8+/MariaDB)
CREATE TABLE IF NOT EXISTS jobs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  company VARCHAR(255),
  location VARCHAR(100),
  experience VARCHAR(50),
  skills VARCHAR(255),
  description TEXT,
  apply_link VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  expires_at DATE,
  is_active TINYINT(1) DEFAULT 1,
  is_approved TINYINT(1) DEFAULT 1
);

CREATE TABLE IF NOT EXISTS admin_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) UNIQUE,
  password_hash VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS reports (
  id INT AUTO_INCREMENT PRIMARY KEY,
  job_id INT,
  reason TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create initial admin (replace PASSWORD_HASH with output of PHP password_hash)
-- INSERT INTO admin_users (email, password_hash) VALUES ('admin@example.com', 'PASSWORD_HASH');

