<?php
session_start();
require_once 'utils/database.php'; // Connexion à la base de données

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Si l'utilisateur n'est pas connecté, rediriger vers la page de login
    exit();
}

$user_id = $_SESSION['user_id'];

// Connexion à la base de données
$db = Database::getInstance();
$conn = $db->getConnection();

// Récupère les informations de l'utilisateur
$query = "SELECT username, email, avatar, balance FROM users WHERE id = :user_id";
$stmt = $conn->prepare($query);
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // Si l'utilisateur n'existe pas dans la base de données
    $_SESSION['error'] = "Utilisateur introuvable.";
    header('Location: login.php');
    exit();
}

// Calcul du pourcentage de variation du solde sur 7 jours
$query = "SELECT SUM(amount) as total_change 
          FROM transactions 
          WHERE user_id = :user_id AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
$stmt = $conn->prepare($query);
$stmt->execute(['user_id' => $user_id]);
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);

// Calcul du pourcentage de variation
$percentage_change = 0;
if ($transaction['total_change'] !== null) {
    // Si le solde précédent était nul ou négatif et qu'il y a eu une augmentation, afficher 100%
    if ($user['balance'] - $transaction['total_change'] <= 0 && $transaction['total_change'] > 0) {
        $percentage_change = 100;
    } elseif ($user['balance'] - $transaction['total_change'] > 0) {
        $percentage_change = ($transaction['total_change'] / ($user['balance'] - $transaction['total_change'])) * 100;
    }
}

// Traitement de la mise à jour de la photo de profil
if (isset($_POST['update_avatar']) && isset($_FILES['avatar'])) {
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    $file_name = $_FILES['avatar']['name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    if (in_array($file_ext, $allowed_extensions)) {
        $new_filename = uniqid('avatar_') . '.' . $file_ext;
        $upload_path = 'user/profile-picture/' . $new_filename;
        
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $upload_path)) {
            $query = "UPDATE users SET avatar = :avatar WHERE id = :user_id";
            $stmt = $conn->prepare($query);
            $stmt->execute([ 
                'avatar' => $new_filename,
                'user_id' => $user_id
            ]);
            
            $user['avatar'] = $new_filename;
            $_SESSION['success'] = "Photo de profil mise à jour avec succès!";
            header('Location: profile.php');
            exit();
        } else {
            $_SESSION['error'] = "Erreur lors du téléchargement de l'image.";
        }
    } else {
        $_SESSION['error'] = "Format de fichier non autorisé. Utilisez JPG, JPEG, PNG ou GIF.";
    }
}

// Traitement de la mise à jour du pseudo
if (isset($_POST['update_username']) && !empty($_POST['new_username'])) {
    $new_username = trim($_POST['new_username']);
    
    // Vérifie si le pseudo existe déjà
    $query = "SELECT id FROM users WHERE username = :username AND id != :user_id";
    $stmt = $conn->prepare($query);
    $stmt->execute([ 
        'username' => $new_username, 
        'user_id' => $user_id 
    ]);
    
    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "Ce pseudo est déjà utilisé.";
    } else {
        $query = "UPDATE users SET username = :username WHERE id = :user_id";
        $stmt = $conn->prepare($query);
        $stmt->execute([ 
            'username' => $new_username,
            'user_id' => $user_id
        ]);
        
        $user['username'] = $new_username;
        $_SESSION['success'] = "Pseudo mis à jour avec succès!";
        header('Location: profile.php');
        exit();
    }
}

// Traitement de la mise à jour du mot de passe
if (isset($_POST['update_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Récupère le mot de passe actuel
    $query = "SELECT password FROM users WHERE id = :user_id";
    $stmt = $conn->prepare($query);
    $stmt->execute([ 'user_id' => $user_id ]);
    $current_hashed_password = $stmt->fetchColumn();
    
    if (password_verify($current_password, $current_hashed_password)) {
        if ($new_password === $confirm_password) {
            if (strlen($new_password) >= 8) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                $query = "UPDATE users SET password = :password WHERE id = :user_id";
                $stmt = $conn->prepare($query);
                $stmt->execute([ 
                    'password' => $hashed_password, 
                    'user_id' => $user_id 
                ]);
                
                $_SESSION['success'] = "Mot de passe mis à jour avec succès!";
                header('Location: profile.php');
                exit();
            } else {
                $_SESSION['error'] = "Le mot de passe doit contenir au moins 8 caractères.";
            }
        } else {
            $_SESSION['error'] = "Les mots de passe ne correspondent pas.";
        }
    } else {
        $_SESSION['error'] = "Mot de passe actuel incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - BetFactory</title>
    <link rel="stylesheet" href="assets/css/profile.css">
</head>




<body class="profile-page">


<?php include 'partials/header.php'; ?>

    <div class="profile-container">
        <h1>PROFIL</h1>
        
        <?php
        // Affichage des messages d'erreur ou de succès
        if (isset($_SESSION['error'])) {
            echo '<p class="error-message">' . htmlspecialchars($_SESSION['error']) . '</p>';
            unset($_SESSION['error']);
        } elseif (isset($_SESSION['success'])) {
            echo '<p class="success-message">' . htmlspecialchars($_SESSION['success']) . '</p>';
            unset($_SESSION['success']);
        }
        ?>
        
        <div class="profile-picture">
        <?php if ($user['avatar'] && file_exists('user/profile-picture/' . $user['avatar'])): ?>
            <img src="user/profile-picture/<?php echo htmlspecialchars($user['avatar']); ?>" alt="Photo de profil">
        <?php else: ?>
            <img src="user/profile-picture/default.png" alt="Photo de profil par défaut">
        <?php endif; ?>
        </div>
        
        <h3 class="section-title">MODIFIER MA PHOTO DE PROFIL</h3>
        
        <form method="post" enctype="multipart/form-data" class="form-container">
            <input type="file" name="avatar" accept="image/*" required>
            <button type="submit" name="update_avatar">Mettre à jour la photo</button>
        </form>
        
        <h3 class="section-title">MODIFIER MON PSEUDO</h3>
        
        <form method="post" class="form-container">
            <input type="text" name="new_username" placeholder="Nouveau pseudo" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            <button type="submit" name="update_username">Mettre à jour le pseudo</button>
        </form>
        
        <h3 class="section-title">CHANGER MON MOT DE PASSE</h3>
        
        <form method="post" class="form-container">
            <input type="password" name="current_password" placeholder="Mot de passe actuel" required>
            <input type="password" name="new_password" placeholder="Nouveau mot de passe" required>
            <input type="password" name="confirm_password" placeholder="Confirmer le nouveau mot de passe" required>
            <button type="submit" name="update_password">Mettre à jour le mot de passe</button>
        </form>
        
        <!-- Formulaire de déconnexion -->
        <form method="post" action="utils/logout.php">
            <button type="submit" name="logout">Se déconnecter</button>
        </form>
    </div>
    
    <h2>MON WALLET</h2>
    
    <div class="wallet-container">
        <div class="wallet-label">CURRENT BALANCE</div>
        <div class="wallet-balance">
            <div class="balance-amount"><?php echo number_format($user['balance'], 2); ?> $</div>
            <div class="<?php echo $percentage_change >= 0 ? 'balance-change-positive' : 'balance-change-negative'; ?>">
                <?php if ($percentage_change > 0): ?>
                    ↑<?php echo number_format(abs($percentage_change), 1); ?>%
                <?php elseif ($percentage_change < 0): ?>
                    ↓<?php echo number_format(abs($percentage_change), 1); ?>%
                <?php else: ?>
                    0.0%
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
