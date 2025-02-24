<?php
require_once 'config.php'; // Connexion à la BDD

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Vérification si tous les champs sont remplis
    if (empty($username) || empty($email) || empty($password)) {
        $error = "Tous les champs doivent être remplis.";
    } else {
        // Vérifier si l'utilisateur existe déjà
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email OR username = :username");
        $stmt->execute(['email' => $email, 'username' => $username]);
        
        if ($stmt->fetch()) {
            $error = "Ce pseudo ou cet email est déjà utilisé.";
        } else {
            // Hashage du mot de passe
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Insérer dans la base de données
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
            $success = $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword
            ]);

            if ($success) {
                header("Location: login.php"); // Redirection après inscription
                exit();
            } else {
                $error = "Erreur lors de l'inscription.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - BetFactory</title>
</head>
<body>
    <h2>Inscription</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST" action="">
        <input type="text" name="username" placeholder="Pseudo" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Mot de passe" required><br>
        <button type="submit">S'inscrire</button>
    </form>
</body>
</html>
