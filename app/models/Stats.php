<?php
// app/models/Stats.php
require_once 'Database.php';

class Stats {
    private $conn;

    public function __construct() {
        // Connexion à la base de données
        $db = Database::getInstance();
        $this->conn = $db->getConnection();
    }

    public function getTotalUsers() {
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) AS total_users FROM users");
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des utilisateurs : " . $e->getMessage());
            return 0;
        }
    }

    public function getTotalAccounts() {
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) AS total_accounts FROM users");
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des comptes : " . $e->getMessage());
            return 0;
        }
    }

    public function getTotalBets() {
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) AS total_bets FROM bets_users");
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des paris : " . $e->getMessage());
            return 0;
        }
    }
}
?>
