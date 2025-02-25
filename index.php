<?php
// Inclure la connexion à la base de données
require_once 'utils/database.php';

// Récupérer l'instance de connexion
$db = Database::getInstance();
$connexion = $db->getConnection();

try {
    // Récupérer le nombre d'utilisateurs inscrits
    $stmt_users = $connexion->query("SELECT COUNT(*) AS total_users FROM users");
    $total_users = $stmt_users->fetchColumn();

    // Récupérer le nombre de comptes créés
    $stmt_accounts = $connexion->query("SELECT COUNT(*) AS total_accounts FROM users");
    $total_accounts = $stmt_accounts->fetchColumn();

    // Récupérer le nombre total de paris placés
    $stmt_bets = $connexion->query("SELECT COUNT(*) AS total_bets FROM bets_users");
    $total_bets = $stmt_bets->fetchColumn();

} catch (PDOException $e) {
    error_log("Erreur lors de la récupération des statistiques : " . $e->getMessage());
    $total_users = $total_accounts = $total_bets = 0; // Valeurs par défaut en cas d'erreur
}
?>
