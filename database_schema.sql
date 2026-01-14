-- Elimo Real Estate Property Management System Database Schema
-- Created based on HTML template analysis

-- Create database
CREATE DATABASE IF NOT EXISTS elimo_real_estate;
USE elimo_real_estate;

-- Users table for authentication and roles
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Properties table
CREATE TABLE properties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category ENUM('Residential', 'Commercial', 'Developments', 'Land') NOT NULL,
    property_type ENUM('Apartment', 'House', 'Townhouse', 'Semi Detached', 'Office', 'Retail', 'Industrial', 'Land') NOT NULL,
    status ENUM('for-rent', 'for-sale', 'under-construction', 'sold', 'rented') DEFAULT 'for-rent',
    price DECIMAL(12,2),
    location VARCHAR(100) NOT NULL,
    province VARCHAR(50),
    district VARCHAR(50),
    bedrooms INT,
    bathrooms INT,
    garage INT DEFAULT 0,
    size_sqm DECIMAL(8,2),
    year_built INT,
    image_main VARCHAR(255),
    images JSON,
    features JSON,
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Team members table
CREATE TABLE team_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    position VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    bio TEXT,
    image VARCHAR(255),
    social_links JSON,
    listed_properties INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Blog posts table
CREATE TABLE blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    content TEXT NOT NULL,
    excerpt TEXT,
    category VARCHAR(50) DEFAULT 'creative',
    image VARCHAR(255),
    author_id INT,
    status ENUM('published', 'draft') DEFAULT 'published',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id)
);

-- FAQs table
CREATE TABLE faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question VARCHAR(255) NOT NULL,
    answer TEXT NOT NULL,
    category ENUM('selling', 'renting', 'developments', 'general') DEFAULT 'general',
    order_index INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Contact messages table
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Newsletter subscribers table
CREATE TABLE newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Property inquiries table
CREATE TABLE property_inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    message TEXT,
    status ENUM('new', 'contacted', 'closed') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
);

-- Site settings table
CREATE TABLE site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, email, password, full_name, role) VALUES 
('admin', 'admin@elimo.rw', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin');

-- Insert default site settings
INSERT INTO site_settings (setting_key, setting_value, description) VALUES
('site_name', 'Elimo Real Estate', 'Website name'),
('site_description', 'Your trusted resourceful companion on your real estate journey', 'Site description'),
('contact_email', 'info@elimo.rw', 'Contact email'),
('contact_phone', '+250-789-517-737', 'Contact phone'),
('contact_address', 'KG 622, Street 19 P.O. BOX 4566 Rugando - Kigali Rwanda', 'Contact address');

-- Insert sample team members based on template
INSERT INTO team_members (name, position, email, phone, bio, image, social_links, listed_properties) VALUES
('Aristide Yves Horimbere', 'Founder', 'aristide@elimo.rw', '+250-789-517-737', 'Founder and lead real estate expert at Elimo Real Estate', 'aristide.jpeg', '{"twitter":"#", "facebook":"#", "linkedin":"#"}', 5),
('Jean Dedieu Karangwa', 'Real Estate Broker', 'jean@elimo.rw', '+250-789-517-737', 'Experienced real estate broker specializing in residential properties', 'jean.jpeg', '{"twitter":"#", "facebook":"#", "linkedin":"#"}', 5),
('Nina Habonimana', 'Sales Executive', 'nina@elimo.rw', '+250-789-517-737', 'Sales executive focused on client satisfaction and property matching', 'nina.jpeg', '{"twitter":"#", "facebook":"#", "linkedin":"#"}', 5);

-- Insert sample FAQs based on template
INSERT INTO faqs (question, answer, category, order_index) VALUES
('How can we help?', 'Elimo Real Estate provides comprehensive property management, valuation, and consulting services to help you find your perfect property in Rwanda.', 'general', 1),
('How do I delete my account?', 'To delete your account, please contact our support team at info@elimo.rw. We will process your request within 24 hours.', 'general', 2),
('Do you store any of my information?', 'We only store necessary information required to provide our services. Your data is protected according to our privacy policy.', 'general', 3),
('I\'ve got a problem, how do I contact support?', 'You can contact our support team via email at info@elimo.rw or call us at +250-789-517-737 for immediate assistance.', 'general', 4),
('What is cloud backup?', 'Cloud backup is a secure way to store your property documents and information online, ensuring they are safe and accessible from anywhere.', 'general', 5);

-- Insert sample blog posts based on template
INSERT INTO blog_posts (title, slug, content, excerpt, category, image) VALUES
('The Interior Design of houses in Kigali City', 'interior-design-kigali-city', 'Lorem ipsum dolor sit amet, consectetur cing elit. Suspe ndisse suscipit sagittis leo sit met condimentum estibulum issim', 'Lorem ipsum dolor sit amet, consectetur cing elit. Suspe ndisse suscipit sagittis leo sit met condimentum estibulum issim', 'creative', 'post-15.jpg'),
('Ten Benefits Of Rentals That May Change Your Perspective', 'benefits-rentals-perspective', 'Lorem ipsum dolor sit amet, consectetur cing elit. Suspe ndisse suscipit sagittis leo sit met condimentum estibulum issim', 'Lorem ipsum dolor sit amet, consectetur cing elit. Suspe ndisse suscipit sagittis leo sit met condimentum estibulum issim', 'rental', 'post-16.jpg'),
('Future Office Buildings Intelligent by Design', 'future-office-buildings-design', 'Lorem ipsum dolor sit amet, consectetur cing elit. Suspe ndisse suscipit sagittis leo sit met condimentum estibulum issim', 'Lorem ipsum dolor sit amet, consectetur cing elit. Suspe ndisse suscipit sagittis leo sit met condimentum estibulum issim', 'creative', 'post-17.jpg');

-- Insert sample properties based on template
INSERT INTO properties (title, description, category, property_type, status, price, location, province, district, bedrooms, bathrooms, garage, size_sqm, year_built, image_main, features) VALUES
('Cluster Homes in Kazungu', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut', 'Developments', 'House', 'under-construction', NULL, 'Northen Province', 'Northern', 'Kazungu', 3, 3, 1, 345, 2020, 'properties-list-01.jpg', '["Air Conditioning", "Laundry", "WiFi", "Gym"]'),
('Modern apartments in city center', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut', 'Residential', 'Apartment', 'under-construction', NULL, 'Kigali', 'Kigali', 'Nyarugenge', 3, 3, 1, 230, 2020, 'properties-list-02.jpg', '["Air Conditioning", "WiFi", "Gym", "Swimming Pool"]'),
('4 bedroom house for rent', 'Stay in this modern furnished house located in Kiyovu, a quiet and safe neighborhood very close to the town and Kiyovu area. Its bright living room opens on a fancy dining room and a wide kitchen.', 'Residential', 'House', 'for-rent', 1500.00, 'Gaju', 'Kigali', 'Gasabo', 4, 4, 1, NULL, NULL, 'property-1-search.jpg', '["Air Conditioning", "WiFi", "Gym", "Garage"]'),
('2 bed apartment to let', 'Stay in this modern furnished house located in Kiyovu, a quiet and safe neighborhood very close to the town and Kiyovu area. Its bright living room opens on a fancy dining room and a wide kitchen.', 'Residential', 'Apartment', 'for-rent', 1200.00, 'Kigali City', 'Kigali', 'Kicukiro', 2, 2, 1, NULL, NULL, 'property-2-search.jpg', '["Air Conditioning", "WiFi", "Laundry"]');
