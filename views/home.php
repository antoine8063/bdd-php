<?php
// Inclure la connexion à la base de données
require_once '../config/database.php';

// Récupérer l'instance de connexion
$db = Database::getInstance();
$connexion = $db->getConnection();

try {
    // Récupérer le nombre d'utilisateurs inscrits
    $stmt_users = $connexion->query("SELECT COUNT(*) AS total_users FROM users");
    $total_users = $stmt_users->fetchColumn();

    // Récupérer le nombre de comptes créés (par le même nombre d'utilisateurs)
    $total_accounts = $total_users;

    // Récupérer le nombre total de paris effectués
    $stmt_bets = $connexion->query("SELECT COUNT(*) AS total_bets FROM bets_users");
    $total_bets = $stmt_bets->fetchColumn();

} catch (PDOException $e) {
    error_log("Erreur lors de la récupération des statistiques : " . $e->getMessage());
    $total_users = $total_accounts = $total_bets = 0; // Valeurs par défaut en cas d'erreur
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BetFactory</title>
    <link rel="stylesheet" href="/public/assets/css/index.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

    <!-- Barre de navigation -->
    <nav>
        <div class="logo">BETFACTORY</div>
        <ul class="nav-links">
            <li><a href="#">ACCUEIL</a></li>
            <li><a href="ProfileView.php">COMPTE</a></li>
            <li><a href="#">À PROPOS</a></li>
        </ul>
        <a href="#" class="bet-now">BET NOW</a>
    </nav>

    <!-- Bouton central -->
    <section class="intro">
        <button class="how-it-works">COMMENT ÇA MARCHE ?</button>
    </section>

    <!-- Deux blocs d'informations -->
    <section class="info-section">
        <div class="info-box"></div>
        <div class="info-box"></div>
    </section>

    <!-- Statistiques -->
    <section class="stats">
        <div class="stat">UTILISATEURS INSCRITS<br><span class="stat-value"><?php echo $total_users; ?></span></div>
        <div class="stat">COMPTES CRÉÉS<br><span class="stat-value"><?php echo $total_accounts; ?></span></div>
        <div class="stat">PARIS EFFECTUÉS<br><span class="stat-value"><?php echo $total_bets; ?></span></div>
    </section>

</body>
</html>
