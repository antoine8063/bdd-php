<?php
session_start();

// Vérifie si l'utilisateur est connecté
if (isset($_SESSION['user_id'])) {
    // Détruire toutes les variables de session
    session_unset();

    // Détruire la session
    session_destroy();
}

// Redirige l'utilisateur vers la page d'accueil (index.php)
header('Location: /index.php'); // Assure-toi que le chemin vers index.php est correct
exit();
?>
