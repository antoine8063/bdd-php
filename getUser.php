<?php
// Inclure la connexion à la base de données
require_once 'utils/database.php';

// Récupérer l'instance de connexion
$db = Database::getInstance();
$connexion = $db->getConnection();

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $stmt = $pdo->prepare('SELECT balance FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $userData = $stmt->fetch();

    if ($userData) {
        echo json_encode(['success' => true, 'balance' => $userData['balance']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non trouvé']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données : ' . $e->getMessage()]);
}
