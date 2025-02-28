<?php
include 'log_errors.php'; // Pour journaliser les erreurs sans les afficher

header('Content-Type: application/json');

$userId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'ID utilisateur invalide']);
    exit;
}

$host = 'localhost';
$db   = 'betfactory';
$user = 'root'; 
$pass = 'root';     
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $stmt = $pdo->prepare('SELECT balance FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$userId]);
    $userData = $stmt->fetch();

    if ($userData) {
        echo json_encode(['success' => true, 'balance' => $userData['balance']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non trouvé']);
    }
} catch (PDOException $e) {
    error_log("Erreur BDD: " . $e->getMessage()); // Journalisation
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données']);
}
