<?php
// Inclure la connexion à la base de données
require_once 'utils/database.php';

// Récupérer l'instance de connexion
$db = Database::getInstance();
$connexion = $db->getConnection();

try {
    // Requête pour récupérer toutes les informations des utilisateurs
    $stmt = $connexion->query("SELECT id, username, email, role, created_at FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupère tous les utilisateurs
} catch (PDOException $e) {
    error_log("Erreur lors de la récupération des utilisateurs : " . $e->getMessage());
    $users = []; // Tableau vide en cas d'erreur
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - Liste des Utilisateurs</title>
    <link rel="stylesheet" href="styles.css"> <!-- Lien vers ta feuille de style -->
</head>
<body>

<h1>Liste des Utilisateurs Inscrits</h1>

<!-- Tableau pour afficher les utilisateurs -->
<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nom d'utilisateur</th>
            <th>Email</th>
            <th>Rôle</th>
            <th>Date d'Inscription</th>
            <th>Actions</th>  <!-- Colonne pour les actions à effectuer sur chaque utilisateur -->
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo htmlspecialchars($user['id']); ?></td>
            <td><?php echo htmlspecialchars($user['username']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td><?php echo htmlspecialchars($user['role']); ?></td>
            <td><?php echo htmlspecialchars($user['created_at']); ?></td>
            <td>

                <a href="delete_user.php?id=<?php echo $user['id']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">Supprimer</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
