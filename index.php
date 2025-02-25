<?php
// index.php
session_start();

// Inclusion des fichiers nécessaires
require_once 'controllers/RegisterController.php';
require_once 'controllers/HomeController.php';  // Ajouter l'inclusion du contrôleur d'accueil

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
        $controller = new RegisterController();
        $controller->showRegisterForm();
        break;
    
    case 'submit_register':
        // Soumettre le formulaire d'inscription
        $controller = new RegisterController();
        $controller->registerUser();
        break;

    default:
        // Par défaut, afficher la page d'accueil
        $controller = new HomeController();
        $controller->index();
        break;
}
?>
