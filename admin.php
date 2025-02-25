<?php
// Inclure la connexion à la base de données
require_once 'utils/database.php';

// Récupérer l'instance de connexion
$db = Database::getInstance();
$connexion = $db->getConnection();

// Récupérer tous les utilisateurs
try {
    $stmt = $connexion->query("SELECT id, username, email, role, balance, created_at FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erreur lors de la récupération des utilisateurs : " . $e->getMessage());
    $users = [];  // Tableau vide en cas d'erreur
}

// Traitement de l'ajout d'un pari via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['betCategory'], $_POST['team1'], $_POST['odds1'], $_POST['team2'], $_POST['odds2'])) {
    // Validation et ajout du pari
    $title = $_POST['title'];
    $betCategory = $_POST['betCategory'];
    $team1 = $_POST['team1'];
    $odds1 = $_POST['odds1'];
    $team2 = $_POST['team2'];
    $odds2 = $_POST['odds2'];

    // Validation des cotes
    if ($odds1 <= 0 || $odds2 <= 0) {
        $error = "Les cotes doivent être supérieures à 0.";
    } else {
        try {
            // Insertion dans la base de données
            $stmt = $connexion->prepare("INSERT INTO bets (title, odds, category) VALUES (:title, :odds, :category)");
            $stmt->execute([':title' => $team1 . " vs " . $team2, ':odds' => "$odds1/$odds2", ':category' => $betCategory]);
            $successMessage = "Pari ajouté avec succès!";
        } catch (PDOException $e) {
            error_log("Erreur lors de l'ajout du pari : " . $e->getMessage());
            $error = "Erreur lors de l'ajout du pari.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - Liste des Utilisateurs</title>
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>

<h1>Liste des Utilisateurs Inscrits</h1>

<!-- Affichage du message de succès ou erreur -->
<?php if (isset($successMessage)): ?>
    <div class="alert success"><?php echo htmlspecialchars($successMessage); ?></div>
<?php endif; ?>
<?php if (isset($error)): ?>
    <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<!-- Tableau des utilisateurs -->
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nom d'utilisateur</th>
            <th>Email</th>
            <th>Rôle</th>
            <th>Solde (€)</th>
            <th>Date d'Inscription</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo htmlspecialchars($user['id']); ?></td>
            <td><?php echo htmlspecialchars($user['username']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td><?php echo htmlspecialchars($user['role']); ?></td>
            <td><?php echo number_format($user['balance'], 2, ',', ' '); ?> €</td>
            <td><?php echo htmlspecialchars($user['created_at']); ?></td>
            <td>
                <a href="delete_user.php?id=<?php echo $user['id']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">Supprimer</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Bouton pour ajouter un pari -->
<button id="ajouterPariBtn">Ajouter un pari</button>

<!-- Fenêtre modale pour ajouter un pari -->
<div id="betFormModal">
    <div id="betFormContainer">
        <form id="betForm" action="ajouter_pari.php" method="POST">
            <label for="title">Nom du Pari</label>
            <input type="text" id="title" name="title" required>

            <label for="betCategory">Catégorie du Pari</label>
            <select id="betCategory" name="betCategory" required>
                <option value="Football">Football</option>
                <option value="Basketball">Basketball</option>
                <!-- Ajouter d'autres catégories ici -->
            </select>

            <label for="team1">Équipe 1</label>
            <input type="text" id="team1" name="team1" required>

            <label for="odds1">Cote de l'Équipe 1</label>
            <input type="number" id="odds1" name="odds1" required step="0.01">

            <label for="team2">Équipe 2</label>
            <input type="text" id="team2" name="team2" required>

            <label for="odds2">Cote de l'Équipe 2</label>
            <input type="number" id="odds2" name="odds2" required step="0.01">

            <button type="submit">Ajouter le Pari</button>
        </form>

        <button id="closeModal">Fermer</button>
    </div>
</div>

<script src="/assets/js/admin.js"></script>

</body>
</html>
