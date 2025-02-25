<?php
// Démarrage de la session
session_start();

// Vérification si l'utilisateur est connecté
if (isset($_SESSION['user_id'])) {
    // Récupérer l'ID de l'utilisateur
    $user_id = $_SESSION['user_id'];

    // Exemple de fonction pour récupérer les informations utilisateur depuis la base de données
    $user_info = getUserInfo($user_id);

    // Vérifiez si l'utilisateur existe et les informations sont valides
    if (!$user_info) {
        echo "L'utilisateur n'a pas été trouvé dans la base de données.";
        exit();
    }

    // Si les données sont valides, afficher la vue
    require('views/ProfileView.php');
} else {
    // Si l'utilisateur n'est pas connecté
    echo "L'utilisateur n'est pas connecté.";
    exit();
}

// Fonction pour récupérer les informations de l'utilisateur depuis la base de données
function getUserInfo($user_id) {
    // Connexion à la base de données
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=betfactory', 'root', ''); // Remplacez par vos infos de connexion
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Préparer la requête
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :user_id');
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        
        // Récupérer l'utilisateur
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        exit();
    }
}
