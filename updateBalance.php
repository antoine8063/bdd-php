<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['userId']) || !isset($data['amount'])) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit;
}

$userId = intval($data['userId']);
$amount = floatval($data['amount']);

$host = 'localhost';
$db   = 'betfactory';
$user = 'root'; // à adapter
$pass = 'root';     // à adapter
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Mettre à jour le solde
    $stmt = $pdo->prepare('UPDATE users SET balance = balance + ? WHERE id = ?');
    $stmt->execute([$amount, $userId]);

    // Récupérer le nouveau solde
    $stmt = $pdo->prepare('SELECT balance FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $userData = $stmt->fetch();

    if ($userData) {
        echo json_encode(['success' => true, 'newBalance' => $userData['balance']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non trouvé']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données : ' . $e->getMessage()]);
}