<?php
require_once 'database.php'; // Inclure la connexion à la base de données

// Récupérer l'instance de connexion
$db = Database::getInstance();
$connexion = $db->getConnection();

$response = [];

try {
    // Vérifier si les données sont envoyées par POST
    if (isset($_POST['title']) && isset($_POST['betCategory']) && isset($_POST['team1']) && isset($_POST['odds1']) && isset($_POST['team2']) && isset($_POST['odds2'])) {
        $title = $_POST['title']; // Nom du pari
        $betCategory = $_POST['betCategory']; // Catégorie
        $team1 = $_POST['team1']; // Équipe 1
        $odds1 = $_POST['odds1']; // Cote Équipe 1
        $team2 = $_POST['team2']; // Équipe 2
        $odds2 = $_POST['odds2']; // Cote Équipe 2

        // Validation des cotes
        if ($odds1 <= 0 || $odds2 <= 0) {
            $response = [
                'success' => false,
                'message' => 'Les cotes doivent être supérieures à 0.'
            ];
        } else {
            // Insertion du pari dans la base de données
            $stmt = $connexion->prepare("INSERT INTO bets (title, odds, category) VALUES (?, ?, ?)");
            $stmt->execute([$team1 . " vs " . $team2, $odds1 . '/' . $odds2, $betCategory]);

            // Réponse en cas de succès
            $response = [
                'success' => true,
                'message' => 'Le pari a été ajouté avec succès.'
            ];
        }
    } else {
        // Si des champs manquent
        $response = [
            'success' => false,
            'message' => 'Des champs sont manquants.'
        ];
    }
} catch (PDOException $e) {
    // Erreur dans l'insertion
    $response = [
        'success' => false,
        'message' => 'Erreur lors de l’enregistrement du pari : ' . $e->getMessage()
    ];
}

// Retourner la réponse au format JSON
echo json_encode($response);
?>
