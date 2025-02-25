<?php
// models/User.php

// Inclure correctement le fichier de base de données, en utilisant un chemin relatif correct
require_once __DIR__ . '/../../config/database.php'; // Utilisation du bon chemin relatif à ce fichier

class User {
    private $conn;

    public function __construct() {
        // S'assurer que la connexion à la base de données fonctionne
        $db = Database::getInstance();
        $this->conn = $db->getConnection();
    }

    // Méthode pour créer un utilisateur
    public function createUser($email, $username, $hashedPassword) {
        try {
            $query = "INSERT INTO users (email, username, password) VALUES (:email, :username, :password)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':email' => $email,
                ':username' => $username,
                ':password' => $hashedPassword
            ]);
            return true;
        } catch (PDOException $e) {
            // Gestion des erreurs lors de l'insertion
            return false;
        }
    }

    // Vérifier si un email existe déjà
    public function emailExists($email) {
        $query = "SELECT 1 FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':email' => $email]);
        return $stmt->rowCount() > 0;
    }

    // Vérifier si un username existe déjà
    public function usernameExists($username) {
        $query = "SELECT 1 FROM users WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':username' => $username]);
        return $stmt->rowCount() > 0;
    }

    // Ajouter une méthode pour récupérer un utilisateur par son id
    public function getUserById($userId) {
        $query = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);  // Retourne les données de l'utilisateur
    }

    // Ajouter une méthode pour récupérer un utilisateur par son email
    public function getUserByEmail($email) {
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);  // Retourne les données de l'utilisateur
    }
}
?>
