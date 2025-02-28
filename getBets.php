<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

// Informations de connexion
$host    = 'localhost';
$db      = 'betfactory';
$user    = 'root';
$pass    = 'root';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // ----------------------------
    // Requête GET : Récupérer les paris et leurs équipes (via team_bets)
    // ----------------------------
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Récupérer tous les paris depuis la table bets
        $stmt = $pdo->query("SELECT * FROM bets");
        $bets = $stmt->fetchAll();
        
        if (empty($bets)) {
            echo json_encode(['success' => false, 'message' => 'Aucun pari trouvé dans la base de données.']);
            exit;
        }
        
        // Pour chaque pari, récupérer la ligne associée dans team_bets (on suppose qu'il y a une seule ligne par pari)
        foreach ($bets as &$bet) {
            $betId = $bet['id'];
            $stmtTeams = $pdo->prepare("SELECT * FROM bet_teams WHERE bet_id = :betId");
            $stmtTeams->execute(['betId' => $betId]);
            $teamData = $stmtTeams->fetch(); // On attend une seule ligne par pari
            
            if ($teamData) {
                // On remplit l'objet $bet avec les informations de team_bets
                $bet['team_name']       = $teamData['team_name'];  // option générique si besoin
                $bet['team_a']          = $teamData['team_a'];
                $bet['odds_a']          = $teamData['odds_a'];
                $bet['percentage_a']    = $teamData['percentage_a'];
                $bet['team_b']          = $teamData['team_b'];
                $bet['odds_b']          = $teamData['odds_b'];
                $bet['percentage_b']    = $teamData['percentage_b'];
                $bet['draw_odds']       = $teamData['draw_odds'];
                $bet['percentage_draw'] = $teamData['percentage_draw'];
                // Optionnellement, on peut conserver la colonne 'odds' qui vient de bets si besoin
            } else {
                $bet['error'] = 'Aucune équipe trouvée pour ce pari';
            }
        }
        
        echo json_encode(['success' => true, 'bets' => $bets]);
        exit;
    }

    // ----------------------------
    // Requête POST : Placer un pari
    // ----------------------------
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        $userId = $data['userId'];
        $betId  = $data['betId'];
        $choice = $data['choice'];  // Exemple : 'Team A', 'Draw' ou 'Team B'
        $amount = $data['amount'];

        // Validation des données
        if (empty($userId) || empty($betId) || empty($choice) || empty($amount)) {
            echo json_encode(['success' => false, 'message' => 'Données manquantes']);
            exit;
        }

        // Vérifier le solde de l'utilisateur
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

        // Insérer le pari dans la table bets_users
        $stmt = $pdo->prepare("
            INSERT INTO bets_users (user_id, bet_id, amount, choice) 
            VALUES (:userId, :betId, :amount, :choice)
        ");
        $stmt->execute([
            'userId' => $userId,
            'betId'  => $betId,
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
        exit;
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur de base de données : ' . $e->getMessage()]);
}
?>
