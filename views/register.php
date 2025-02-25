<?php
// views/registerView.php
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="/public/assets/css/register.css">
</head>
<body>

    <section>
        <h1>REGISTRATION</h1>

        <!-- Affichage des messages d'erreur ou de succès -->
        <?php
        if (isset($_SESSION['error'])) {
            echo '<div class="error">' . htmlspecialchars($_SESSION['error']) . '</div>';
            unset($_SESSION['error']);
        } elseif (isset($_SESSION['success'])) {
            echo '<div class="success">' . htmlspecialchars($_SESSION['success']) . '</div>';
            unset($_SESSION['success']);
        }
        ?>

        <!-- Formulaire d'inscription -->
        <form method="POST" action="index.php?action=submit_register">
            <input type="email" name="email" placeholder="  Email" required id="em"/>
            <input type="text" name="username" placeholder="  Username" required id="us"/>
            <input type="password" name="password" placeholder="  Password" required id="pas"/>
            <input type="password" name="confirm_password" placeholder="  Confirm password" required id="conn"/>
            <input type="submit" name="submit" value="Register" id="res"/>
        </form>
    </section>

</body>
</html>
