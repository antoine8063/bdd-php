<?php
// Inclure les modèles nécessaires
require_once '../app/models/User.php';

class AuthController {

    // Affiche la page de connexion
    public function showLoginPage() {
        require_once '../views/login.php';
    }

    // Affiche la page d'inscription
    public function showRegisterPage() {
        require_once '../views/register.php';
    }

    // Méthode pour l'inscription de l'utilisateur
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            // Vérification des données du formulaire
            if (empty($email) || empty($username) || empty($password) || empty($confirm_password)) {
                $_SESSION['error'] = "Tous les champs sont obligatoires.";
            } elseif ($password !== $confirm_password) {
                $_SESSION['error'] = "Les mots de passe ne correspondent pas.";
            } else {
                // Utiliser le modèle User pour vérifier si l'email existe déjà
                $userModel = new User();
                if ($userModel->emailExists($email)) {
                    $_SESSION['error'] = "Cet email est déjà utilisé.";
                } else {
                    // Hachage du mot de passe
                    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                    // Enregistrer le nouvel utilisateur
                    if ($userModel->createUser($email, $username, $hashedPassword)) {
                        $_SESSION['success'] = "Inscription réussie, vous pouvez maintenant vous connecter.";
                        header("Location: login.php");
                        exit();
                    } else {
                        $_SESSION['error'] = "Erreur lors de l'inscription. Veuillez réessayer.";
                    }
                }
            }

            // Rediriger vers la page d'inscription en cas d'erreur
            header("Location: register.php");
            exit();
        }
    }

    // Méthode pour la connexion de l'utilisateur
    public function login() {
        // Vérification si la session est bien démarrée
        if(session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            if (empty($email) || empty($password)) {
                $_SESSION['error'] = "Tous les champs sont obligatoires.";
            } else {
                // Vérification de l'utilisateur via le modèle User
                $userModel = new User();
                $user = $userModel->getUserByEmail($email);

                if ($user) {
                    if (password_verify($password, $user['password'])) {
                        // Connexion réussie, stocker les informations de l'utilisateur dans la session
                        $_SESSION['messagelogin'] = "Connexion réussie ! Bienvenue " . $user['username'];
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_username'] = $user['username'];

                        // Rediriger vers la page de profil
                        header("Location: profile.php");
                        exit();
                    } else {
                        $_SESSION['error'] = "Mot de passe incorrect.";
                    }
                } else {
                    $_SESSION['error'] = "Aucun compte trouvé avec cet email.";
                }
            }

            // Rediriger vers la page de connexion en cas d'erreur
            header("Location: login.php");
            exit();
        }
    }

    // Méthode pour la déconnexion de l'utilisateur
    public function logout() {
        if(session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        session_unset(); // Supprimer toutes les variables de session
        session_destroy(); // Détruire la session
        $_SESSION['messagelogin'] = "Vous êtes déconnecté avec succès.";
        
        // Rediriger vers la page d'accueil ou de connexion
        header("Location: login.php");
        exit();
    }
}
?>
