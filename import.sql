-- import.sql for Simple Event Volunteer Portal
DROP DATABASE IF EXISTS simple_event_portal;
CREATE DATABASE simple_event_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE simple_event_portal;

-- users
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(200) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','user') NOT NULL DEFAULT 'user',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- events
CREATE TABLE events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  event_date DATE,
  event_time TIME,
  venue VARCHAR(255),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- volunteers
CREATE TABLE volunteers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(200),
  phone VARCHAR(50),
  status ENUM('pending','approved','rejected') DEFAULT 'pending',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- tasks
CREATE TABLE tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  volunteer_id INT,
  event_id INT,
  status VARCHAR(50) DEFAULT 'assigned',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (volunteer_id) REFERENCES volunteers(id) ON DELETE SET NULL,
  FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- event_registrations
CREATE TABLE event_registrations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  event_id INT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
  UNIQUE KEY (user_id,event_id)
) ENGINE=InnoDB;

-- seed admin user
INSERT INTO users (name,email,password_hash,role) VALUES
('Admin User','admin@example.com', '$2y$10$g1J0d1Xb6z7hFf9l/8gMSeBrw2JkXl3YkqQKkzqzq0G9p7s9Zl8u', 'admin');
-- password for admin: Admin@123  (hash above; if not, create manually)

-- sample data
INSERT INTO events (title,description,event_date,event_time,venue) VALUES
('Community Clean Up','Help clean local park','2025-12-05','09:00:00','Central Park'),
('Food Drive','Collect and distribute food items','2025-11-30','10:00:00','Community Center');

INSERT INTO volunteers (name,email,phone,status) VALUES
('John Volunteer','john@example.com','+1234567890','approved'),
('Mary Volunteer','mary@example.com','+1987654321','pending');
