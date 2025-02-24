<body>
    <section>
        <h1>LOGIN</h1>
        
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
            <input type="email" name="email" placeholder="Email" required id="em"/>
            <input type="password" name="password" placeholder="Password" required id="pas"/>
            <input type="submit" name="submitForm" value="Login" id="log"/>
            
            <!-- Liens pour redirection -->
            <a href="myAccount.php">My account</a>
            <a href="register.php">Register</a>
        </form>
    </section>
    
</body>
</html>
