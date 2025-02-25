<?php
// controllers/RegisterController.php
require_once 'models/UserModel.php';

class RegisterController {

    public function showRegisterForm() {
        // Affiche le formulaire d'inscription
        include 'views/registerView.php';
    }

    public function registerUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupération des données du formulaire
            $email = trim($_POST['email']);
            $username = trim($_POST['username']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            // Création du modèle utilisateur
            $userModel = new UserModel();

            // Appel de la méthode d'enregistrement de l'utilisateur et gestion des erreurs
            $result = $userModel->registerUser($email, $username, $password, $confirm_password);

            if ($result === true) {
                $_SESSION['success'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                header('Location: login.php');
                exit();
            } else {
                // En cas d'erreur, afficher les messages d'erreur
                $_SESSION['error'] = $result;
                $this->showRegisterForm();
            }
        }
    }
}
?>
