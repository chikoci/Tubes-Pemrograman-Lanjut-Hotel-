-- Active: 1761218254352@@127.0.0.1@3306@kluwa_hotel
-- Database Kluwa
-- Buat database baru
CREATE DATABASE IF NOT EXISTS kluwa_hotel;
USE kluwa_hotel;
-- Tabel roles
CREATE TABLE IF NOT EXISTS roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(50) NOT NULL UNIQUE
);

INSERT INTO roles (role_name) VALUES ('Admin'), ('Tamu');

-- Tabel users
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- Data admin (password: admin123)
INSERT INTO users (name, age, email, phone, password, role_id) 
VALUES ('Administrator', 30, 'admin@hotel.com', '081234567890', '$2y$10$hr9nd7bohGa62saPMhOWwOh0dLlRUYuUrw0kIN05XNGY5DthSrH/e', 1);

-- Tabel room_types
CREATE TABLE IF NOT EXISTS room_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(12,2) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Data tipe kamar
INSERT INTO room_types (name, price, description, image) VALUES
('Standard', 500000, 'Kamar standar dengan fasilitas lengkap, AC, TV, Wi-Fi gratis', 'rooms/standard.jpg'),
('Deluxe', 800000, 'Kamar deluxe dengan pemandangan kota, AC, TV, Wi-Fi gratis, Bathub', 'rooms/deluxe.jpg'),
('Suite', 1200000, 'Suite mewah dengan ruang tamu terpisah, AC, TV, Wi-Fi gratis, Bathub', 'rooms/suite.jpg');
-- Tabel rooms
CREATE TABLE IF NOT EXISTS rooms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    room_type_id INT NOT NULL,
    room_number VARCHAR(20) NOT NULL UNIQUE,
    status ENUM('Available', 'Maintenance') DEFAULT 'Available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_type_id) REFERENCES room_types(id)
);

-- Data kamar
INSERT INTO rooms (room_type_id, room_number, status) VALUES
(1, '101', 'Available'),
(1, '102', 'Available'),
(1, '103', 'Available'),
(2, '201', 'Available'),
(2, '202', 'Available'),
(2, '203', 'Available'),
(3, '301', 'Available'),
(3, '302', 'Available');

-- Tabel payment_types
CREATE TABLE IF NOT EXISTS payment_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Data tipe pembayaran
INSERT INTO payment_types (name, description) VALUES
('QRIS', 'Pembayaran menggunakan QR Code Indonesia Standard'),
('Transfer Bank', 'Transfer ke rekening bank hotel'),
('Debit Bank', 'Pembayaran menggunakan kartu debit'),
('Cash', 'Pembayaran tunai di hotel');

-- Tabel bookings (dengan payment fields digabung)
CREATE TABLE IF NOT EXISTS bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    room_id INT NOT NULL,
    check_in_date DATE NOT NULL,
    check_out_date DATE NOT NULL,
    total_price DECIMAL(12,2) NOT NULL,
    status ENUM('Pending', 'Confirmed', 'Cancelled') DEFAULT 'Pending',
    payment_type_id INT DEFAULT NULL,
    payment_proof VARCHAR(255) DEFAULT NULL,
    payment_date TIMESTAMP NULL,
    payment_status ENUM('Pending', 'Success', 'Failed') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (room_id) REFERENCES rooms(id),
    FOREIGN KEY (payment_type_id) REFERENCES payment_types(id)
);