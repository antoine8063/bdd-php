<?php
// Démarrer la session
session_start();

// Inclusion des fichiers nécessaires
require_once '/xampp/htdocs/bdd-php/app/controllers/HomeController.php';
require_once '/xampp/htdocs/bdd-php/app/controllers/AuthController.php';
require_once '/xampp/htdocs/bdd-php/app/controllers/ProfileController.php'; // Ajout du contrôleur de profil

// Logique de routage
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} else {
    $action = 'home'; // Par défaut, on affiche la page d'accueil
}

// Déterminer quel contrôleur utiliser en fonction de l'action
switch ($action) {
    case 'home':
        // Afficher la page d'accueil
        $controller = new HomeController();
        $controller->index();
        break;

    case 'register':
        // Afficher le formulaire d'inscription
        $controller = new AuthController();
        $controller->showRegisterForm();
        break;

    case 'submit_register':
        // Soumettre le formulaire d'inscription
        $controller = new AuthController();
        $controller->registerUser();
        break;

    case 'profile':
        // Afficher le profil de l'utilisateur
        $controller = new ProfileController();
        $controller->showProfile();
        break;

    case 'updateAvatar':
        // Mettre à jour l'avatar de l'utilisateur
        $controller = new ProfileController();
        $controller->updateAvatar();
        break;

    case 'updateUsername':
        // Mettre à jour le pseudo de l'utilisateur
        $controller = new ProfileController();
        $controller->updateUsername();
        break;

    case 'updatePassword':
        // Mettre à jour le mot de passe de l'utilisateur
        $controller = new ProfileController();
        $controller->updatePassword();
        break;

    default:
        // Par défaut, afficher la page d'accueil
        $controller = new HomeController();
        $controller->index();
        break;
}
?>
