<?php
session_start();
require 'security.php';
require 'validators.php';
require 'database.php';  // Assurez-vous que le fichier Database.php est correctement inclus

if (isset($_POST['submit'])) {
    // Récupérer l'instance de la connexion PDO via le Singleton
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // Récupérer les données du formulaire
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Vérifier que tous les champs sont remplis
    if (!empty($email) && !empty($username) && !empty($password) && !empty($confirm_password)) {
        // Vérifier l'adresse email
        if (!validateEmail($email)) {
            $_SESSION['error'] = "The email address is not valid.";
        } else {
            // Vérifier le pseudo
            if (strlen($username) < 4) {
                $_SESSION['error'] = "The username must be at least 4 characters long.";
            } else {
                // Vérifier si le pseudo existe déjà dans la table 'users'
                $pseudo = $conn->prepare("SELECT 1 FROM users WHERE username = :username");
                $pseudo->execute([':username' => $username]);
                if ($pseudo->rowCount() > 0) {
                    $_SESSION['error'] = "This username is already taken.";
                } else {
                    // Vérifier si l'email existe déjà dans la table 'users'
                    $emailCheck = $conn->prepare("SELECT 1 FROM users WHERE email = :email");
                    $emailCheck->execute([':email' => $email]);
                    if ($emailCheck->rowCount() > 0) {
                        $_SESSION['error'] = "This email is already registered.";
                    } else {
                        // Vérifier le mot de passe
                        $passwordErrors = validatePassword($password);
                        if (!empty($passwordErrors)) {
                            $_SESSION['error'] = nl2br(implode("\n", $passwordErrors));
                        } elseif ($password !== $confirm_password) {
                            $_SESSION['error'] = "The passwords do not match.";
                        } else {
                            // Hachage du mot de passe
                            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                            try {
                                // Préparation de la requête d'insertion dans la base de données
                                $insert = "INSERT INTO users (email, username, password) VALUES (:email, :username, :password)";
                                $stmt = $conn->prepare($insert);
                                $stmt->execute([
                                    ':email' => $email,
                                    ':username' => $username,
                                    ':password' => $hashedPassword
                                ]);
                                $_SESSION['success'] = "Registration successful! You can now log in."; 
                            } catch (PDOException $e) {
                                // Gestion de l'erreur si la requête échoue
                                $_SESSION['error'] = "Error: An unexpected error occurred. " . $e->getMessage();
                            }
                        }
                    }
                }
            }
        }
    } else {
        $_SESSION['error'] = "All fields are required.";
    }
    // Redirection vers la page d'inscription
    header("Location: ../register.php");
    exit();
}
?>
