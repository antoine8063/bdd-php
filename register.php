<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jeu de Mémoire</title>
    <link rel="stylesheet" href="assets/css/register.css">
</head>

<?php
    include "partials/header.php"
?>

<body>

    <div>
        <h1>REGISTRATION</h1> 
        <?php
            session_start();
            if (isset($_SESSION['error'])) {
                echo '<p class="error-message">' . htmlspecialchars($_SESSION['error']) . '</p>';
                unset($_SESSION['error']);
            } elseif (isset($_SESSION['messagelogin'])) {
                echo '<p class="success-message">' . htmlspecialchars($_SESSION['messagelogin']) . '</p>';
                unset($_SESSION['messagelogin']);
            }
        ?>
        <!-- Formulaire d'inscription -->
        <form method="POST" action="utils/userRegister.php" enctype="multipart/form-data">
            <section class = container>    
                <input type="email" name="email" placeholder="  Email" required id="em"/>
            </section>
            <section class = container>
                <input type="text" name="username" placeholder="  Username" required id="us"/>
            </section>
            <section class = container>
                <input type="password" name="password" placeholder="  Password" required id="pas"/>
            </section>
            <section class = container>
                <input type="password" name="confirm_password" placeholder="  Confirm password" required id="conn"/>
            </section>
            <section class = container>
                <input type="submit" name="submit" value="Register" id="res"/>
            </section>
        </form>
    </div>

</body>
</html>
