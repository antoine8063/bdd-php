<?php
session_start();
require_once 'database.php'; // Connexion à la base de données
require_once 'security.php'; // Contient les fonctions de sécurité (par exemple, hachage)

if (isset($_POST['submitForm'])) {
    // Connexion à la base de données via PDO
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // Récupération des données du formulaire
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Vérification si l'email existe dans la base de données (table 'users')
        $query = "SELECT id, username, password, role FROM users WHERE email = :email"; // Ajout du champ `role`
        $stmt = $conn->prepare($query);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si l'utilisateur est trouvé
        if ($user) {
            // Vérification du mot de passe avec password_verify
            if (password_verify($password, $user['password'])) {
                // Enregistrement des informations de l'utilisateur dans la session
                $_SESSION['messagelogin'] = "Connexion réussie ! Bienvenue " . $user['username'];
                $_SESSION['user_username'] = $user['username'];
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];  // Enregistrer le rôle de l'utilisateur

                // Redirection vers la page admin si l'utilisateur est un admin
                if ($user['role'] == 'admin') {
                    header("Location: ../admin.php");  // Redirection vers le panel admin
                } else {
                    header("Location: ../profile.php"); // Redirection vers la page utilisateur
                }
                exit();
            } else {
                // Mot de passe incorrect
                $_SESSION['error'] = "Mot de passe incorrect.";
            }
        } else {
            // L'email n'existe pas dans la base de données
            $_SESSION['error'] = "Aucun compte trouvé avec cet email.";
        }
    } catch (PDOException $e) {
        // Gestion des erreurs de connexion à la base de données
        $_SESSION['error'] = "Erreur lors de la connexion à la base de données : " . $e->getMessage();
    }

    // Redirection vers la page de connexion en cas d'erreur
    header("Location: ../login.php");
    exit();
}
?>
