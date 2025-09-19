-- Création de la base de données
CREATE DATABASE IF NOT EXISTS gestion_etudiants CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE gestion_etudiants;

-- Table des utilisateurs
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des étudiants
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    departement VARCHAR(100) NOT NULL,
    filiere VARCHAR(100) NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insertion d'un utilisateur de test (mot de passe: admin123)
INSERT INTO users (nom, email, telephone, password) VALUES 
('Admin', 'admin@isep.edu', '771234567', '$2b$12$O2Ti79sB7p.KjyXxWxxBUOa9KV5Te/fSn67nGofO.7Tjarjnx0n0S');

-- Insertion d'étudiants de test
INSERT INTO students (nom, prenom, telephone, departement, filiere, user_id) VALUES 
('Diallo', 'Mamadou', '771111111', 'Informatique', 'Génie Logiciel', 1),
('Ndiaye', 'Fatou', '772222222', 'Télécommunications', 'Réseaux', 1),
('Fall', 'Moussa', '773333333', 'Informatique', 'Cybersécurité', 1);