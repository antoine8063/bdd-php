<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jeu de Mémoire</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>

<body>
    <div>
        <h1>CONNEXION</h1>
        
        <!-- Affichage des messages d'erreur ou de succès -->
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
        
        <!-- Formulaire de connexion -->
        <form method="POST" action="utils/userConnexion.php">
                <section class = container>
                    <input type="email" name="email" placeholder="Email" required id="em"/>
                </section>
                <section class = container>
                    <input type="password" name="password" placeholder="Password" required id="pas"/>
                </section>
                <section class = container>
                    <input type="submit" name="submitForm" value="Login" id="log"/>
                    </section>

            <!-- Liens pour redirection -->
            <input type=button onclick=window.location.href='register.php'; value="pas de compte ?" />
        </form>
    </div>
    
</body>
</html>
