<?php
require_once 'database.php';

$db = Database::getInstance();
$connexion = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['betCategory'], $_POST['team1'], $_POST['odds1'], $_POST['team2'], $_POST['odds2'])) {
    // Récupération des données du formulaire
    $title = $_POST['title'];
    $betCategory = $_POST['betCategory'];
    $team1 = $_POST['team1'];
    $odds1 = floatval($_POST['odds1']);
    $team2 = $_POST['team2'];
    $odds2 = floatval($_POST['odds2']);

    // Validation des cotes
    if ($odds1 <= 0 || $odds2 <= 0) {
        echo json_encode(["success" => false, "message" => "Les cotes doivent être supérieures à 0."]);
        exit;
    }

    try {
        // 1. Insertion du pari dans la table bets
        $stmt = $connexion->prepare("INSERT INTO bets (title, category) VALUES (:title, :category)");
        $stmt->execute([
            ':title' => $title,
            ':category' => $betCategory
        ]);

        // Récupération de l'ID du pari inséré
        $betId = $connexion->lastInsertId();

        // 2. Insertion des équipes et de leurs cotes dans la table bet_teams
        $stmt = $connexion->prepare("INSERT INTO bet_teams (bet_id, team_name, odds) VALUES (:bet_id, :team_name, :odds)");

        // Insertion de l'équipe 1
        $stmt->execute([
            ':bet_id' => $betId,
            ':team_name' => $team1,
            ':odds' => $odds1
        ]);

        // Insertion de l'équipe 2
        $stmt->execute([
            ':bet_id' => $betId,
            ':team_name' => $team2,
            ':odds' => $odds2
        ]);

        // Si tout est ok, renvoyer une réponse JSON de succès
        echo json_encode(["success" => true]);

    } catch (PDOException $e) {
        error_log("Erreur lors de l'ajout du pari : " . $e->getMessage());
        echo json_encode(["success" => false, "message" => "Erreur lors de l'ajout du pari."]);
    }
}
?>
