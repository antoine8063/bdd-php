<?php
session_start();
require_once '../app/controllers/AuthController.php';

$authController = new AuthController();

// Si la connexion est soumise, appeler le contrôleur
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $authController->login();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <div>
        <h1>CONNEXION</h1>
        
        <!-- Messages d'erreur ou de succès -->
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error-message"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php elseif (isset($_SESSION['messagelogin'])): ?>
            <p class="success-message"><?php echo $_SESSION['messagelogin']; unset($_SESSION['messagelogin']); ?></p>
        <?php endif; ?>

        <!-- Formulaire de connexion -->
        <form method="POST" action="login.php">
            <section class="container">
                <input type="email" name="email" placeholder="Email" required id="em" />
            </section>
            <section class="container">
                <input type="password" name="password" placeholder="Password" required id="pas" />
            </section>
            <section class="container">
                <input type="submit" name="submitForm" value="Login" id="log" />
            </section>

            <!-- Liens pour redirection -->
            <input type="button" onclick="window.location.href='register.php';" value="Pas de compte ?" />
        </form>
    </div>
</body>
</html>
