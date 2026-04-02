CREATE DATABASE IF NOT EXISTS freelance_marketplace;
USE freelance_marketplace;

DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS conversations;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS bids;
DROP TABLE IF EXISTS projects;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  role ENUM('client', 'freelancer', 'admin') NOT NULL DEFAULT 'freelancer',
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  title VARCHAR(160) DEFAULT NULL,
  bio TEXT DEFAULT NULL,
  skills VARCHAR(255) DEFAULT NULL,
  location VARCHAR(120) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  client_id INT NOT NULL,
  title VARCHAR(180) NOT NULL,
  description TEXT NOT NULL,
  category VARCHAR(80) NOT NULL,
  budget_min DECIMAL(10,2) NOT NULL,
  budget_max DECIMAL(10,2) NOT NULL,
  status ENUM('open', 'in_progress', 'completed') DEFAULT 'open',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (client_id) REFERENCES users(id)
);

CREATE TABLE bids (
  id INT AUTO_INCREMENT PRIMARY KEY,
  project_id INT NOT NULL,
  freelancer_id INT NOT NULL,
  bid_amount DECIMAL(10,2) NOT NULL,
  delivery_days INT NOT NULL,
  cover_letter TEXT NOT NULL,
  status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (project_id) REFERENCES projects(id),
  FOREIGN KEY (freelancer_id) REFERENCES users(id)
);

CREATE TABLE conversations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  project_id INT NOT NULL,
  client_id INT NOT NULL,
  freelancer_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (project_id) REFERENCES projects(id),
  FOREIGN KEY (client_id) REFERENCES users(id),
  FOREIGN KEY (freelancer_id) REFERENCES users(id)
);

CREATE TABLE messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  conversation_id INT NOT NULL,
  sender_id INT NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (conversation_id) REFERENCES conversations(id),
  FOREIGN KEY (sender_id) REFERENCES users(id)
);

CREATE TABLE payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  project_id INT NOT NULL,
  payer_id INT NOT NULL,
  payee_id INT NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  currency VARCHAR(8) NOT NULL DEFAULT 'USD',
  method ENUM('card', 'bank', 'wallet') DEFAULT 'card',
  status ENUM('escrow', 'released', 'refunded') DEFAULT 'escrow',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (project_id) REFERENCES projects(id),
  FOREIGN KEY (payer_id) REFERENCES users(id),
  FOREIGN KEY (payee_id) REFERENCES users(id)
);

INSERT INTO users (role, full_name, email, password_hash, title, bio, skills, location) VALUES
('admin', 'admin123', 'admin123@gmail.com', '$2y$10$1eGC0ToUtyvgVDHzFhzPCewnFQ9/a/6QnsFfggwEr1m5nZnDhJPki', 'Platform Administrator', 'Manages users, projects, bids, and payments across the platform.', 'Platform Operations, Trust & Safety', 'Douala, CM'),
('client', 'Jordan Blake', 'client@taskweave.test', '$2y$10$MhQix4guSn5KCtjqNFOq5Ox7mShuNflMQRdYlXNMJe7tY7OKAXLyS', 'Product Lead', 'Building a portfolio of digital experiences for emerging brands.', 'Product, UX, Strategy', 'Austin, TX'),
('freelancer', 'Chloe Park', 'freelancer@taskweave.test', '$2y$10$MhQix4guSn5KCtjqNFOq5Ox7mShuNflMQRdYlXNMJe7tY7OKAXLyS', 'Full-stack Developer', 'Specializes in fast MVP builds, clean UI, and scalable backends.', 'PHP, MySQL, HTML, CSS', 'Seoul, KR'),
('freelancer', 'Tariq Ncube', 'tariq@taskweave.test', '$2y$10$MhQix4guSn5KCtjqNFOq5Ox7mShuNflMQRdYlXNMJe7tY7OKAXLyS', 'Brand Designer', 'Creates bold brand systems and digital identities.', 'Branding, Visual Design', 'Cape Town, ZA');

INSERT INTO projects (client_id, title, description, category, budget_min, budget_max, status) VALUES
(2, 'Marketplace landing page refresh', 'Redesign a hero section and onboarding flow for a freelance marketplace. Deliver responsive HTML/CSS and a new visual direction.', 'Design', 1200, 2200, 'open'),
(2, 'PHP project dashboard', 'Build a PHP dashboard to manage projects, bids, and chat threads. Should include admin reporting and payment status views.', 'Development', 2400, 4200, 'open'),
(2, 'Brand tone and messaging system', 'Develop a messaging kit, tagline options, and microcopy for project and payment flows.', 'Content', 800, 1400, 'in_progress');

INSERT INTO bids (project_id, freelancer_id, bid_amount, delivery_days, cover_letter, status) VALUES
(1, 3, 1850, 10, 'I will deliver a modern, conversion-focused hero and flow, with reusable CSS components and a style guide.', 'pending'),
(2, 3, 3900, 18, 'I can implement the dashboard with clean PHP structure, seed data, and admin summary panels.', 'accepted'),
(3, 4, 1200, 7, 'I will craft a tone guide with microcopy for bids, payments, and trust signals.', 'pending');

INSERT INTO conversations (project_id, client_id, freelancer_id) VALUES
(2, 2, 3),
(3, 2, 4);

INSERT INTO messages (conversation_id, sender_id, message) VALUES
(1, 2, 'Thanks for the bid! Can you share a quick milestone plan?'),
(1, 3, 'Absolutely. I will send a 3-step plan with weekly check-ins.'),
(2, 4, 'Drafting tone options now. Do you prefer friendly or premium?');

INSERT INTO payments (project_id, payer_id, payee_id, amount, currency, method, status) VALUES
(2, 2, 3, 1300.00, 'USD', 'card', 'escrow'),
(3, 2, 4, 500.00, 'USD', 'wallet', 'released');
