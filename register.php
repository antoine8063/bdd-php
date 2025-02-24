<?php
session_start();

// Affichage des messages d'erreur ou de succès
if (isset($_SESSION['error'])) { 
    echo '<div class="error">' . htmlspecialchars($_SESSION['error']) . '</div>'; 
    unset($_SESSION['error']);
} elseif (isset($_SESSION['success'])) {
    echo '<div class="success">' . htmlspecialchars($_SESSION['success']) . '</div>'; 
    unset($_SESSION['success']); 
}

?>

<body>

    <section>
        <h1>REGISTRATION</h1> 

        <!-- Formulaire d'inscription -->
        <form method="POST" action="utils/userRegister.php" enctype="multipart/form-data">
            <input type="email" name="email" placeholder="  Email" required id="em"/>
            <input type="text" name="username" placeholder="  Username" required id="us"/>
            <input type="password" name="password" placeholder="  Password" required id="pas"/>
            <input type="password" name="confirm_password" placeholder="  Confirm password" required id="conn"/>
            <input type="submit" name="submit" value="Register" id="res"/>
        </form>
    </section>

</body>
</html>
