CREATE DATABASE IF NOT EXISTS gdi;
USE gdi;

CREATE TABLE incidents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reporter VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('pendiente', 'en proceso', 'resuelto') NOT NULL DEFAULT 'pendiente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);