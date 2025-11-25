-- Database Hotel Management System
-- Buat database baru
CREATE DATABASE IF NOT EXISTS hotel_app;
USE hotel_app;

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
('Standard', 350000, 'Kamar standar dengan fasilitas lengkap, AC, TV, Wi-Fi gratis', 'standard.jpg'),
('Deluxe', 500000, 'Kamar deluxe dengan pemandangan kota, AC, TV, Wi-Fi gratis, kamar mandi dalam', 'deluxe.jpg'),
('Suite', 850000, 'Suite mewah dengan ruang tamu terpisah, AC, TV, Wi-Fi gratis, bathtub', 'suite.jpg');

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

-- Tabel bookings
CREATE TABLE IF NOT EXISTS bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    room_id INT NOT NULL,
    check_in_date DATE NOT NULL,
    check_out_date DATE NOT NULL,
    total_price DECIMAL(12,2) NOT NULL,
    status ENUM('Pending', 'Confirmed', 'Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (room_id) REFERENCES rooms(id)
);

-- Tabel payments
CREATE TABLE IF NOT EXISTS payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    payment_date TIMESTAMP NOT NULL,
    status ENUM('Pending', 'Success', 'Failed') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
);
