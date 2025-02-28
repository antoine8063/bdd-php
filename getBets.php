<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

// Informations de connexion à la base de données
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

// Connexion à la base de données
try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Si la requête est GET, récupérer les paris
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $pdo->query("SELECT * FROM bets");
        $bets = $stmt->fetchAll();
        
        // Ajouter les informations des équipes et des cotes
        foreach ($bets as &$bet) {
            $betId = $bet['id'];
            $stmt_teams = $pdo->prepare("SELECT * FROM bet_teams WHERE bet_id = :betId");
            $stmt_teams->execute(['betId' => $betId]);
            $teams = $stmt_teams->fetchAll();
            
            if ($teams) {
                foreach ($teams as $team) {
                    if ($team['team_name'] == 'Team A') {
                        $bet['team_a'] = $team['team_name'];
                        $bet['odds_a'] = $team['odds'];
                    } elseif ($team['team_name'] == 'Team B') {
                        $bet['team_b'] = $team['team_name'];
                        $bet['odds_b'] = $team['odds'];
                    } elseif ($team['team_name'] == 'Draw') {
                        $bet['draw_odds'] = $team['odds'];
                    }
                }
            } else {
                // Ajouter un message d'erreur si les équipes n'existent pas
                $bet['error'] = 'Aucune équipe trouvée pour ce pari';
            }
        }

        echo json_encode(['success' => true, 'bets' => $bets]);
    }

    // Si la requête est POST, placer un pari
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        $userId = $data['userId'];
        $betId = $data['betId'];
        $choice = $data['choice']; // Choix (ex: 'Team A', 'Draw', 'Team B')
        $amount = $data['amount'];

        // Validation des données
        if (empty($userId) || empty($betId) || empty($choice) || empty($amount)) {
            echo json_encode(['success' => false, 'message' => 'Données manquantes']);
            exit;
        }

        // Vérification du solde de l'utilisateur
        $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = :userId");
        $stmt->execute(['userId' => $userId]);
        $user = $stmt->fetch();

        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'Utilisateur introuvable']);
            exit;
        }

        if ($user['balance'] < $amount) {
            echo json_encode(['success' => false, 'message' => 'Solde insuffisant']);
            exit;
        }

        // Insérer le pari dans la table 'bets_users'
        $stmt = $pdo->prepare("
            INSERT INTO bets_users (user_id, bet_id, amount, choice) 
            VALUES (:userId, :betId, :amount, :choice)
        ");
        $stmt->execute([
            'userId' => $userId,
            'betId' => $betId,
            'amount' => $amount,
            'choice' => $choice
        ]);

        // Mettre à jour le solde de l'utilisateur
        $newBalance = $user['balance'] - $amount;
        $stmt = $pdo->prepare("UPDATE users SET balance = :balance WHERE id = :userId");
        $stmt->execute(['balance' => $newBalance, 'userId' => $userId]);

        // Enregistrer la transaction
        $stmt = $pdo->prepare("
            INSERT INTO transactions (user_id, amount, transaction_type) 
            VALUES (:userId, :amount, 'bet')
        ");
        $stmt->execute(['userId' => $userId, 'amount' => $amount]);

        echo json_encode(['success' => true, 'message' => 'Pari placé avec succès']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données : ' . $e->getMessage()]);
}
?>
