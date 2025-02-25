<?php
// Démarrage de la session
session_start();

// Vérification si l'utilisateur est bien connecté et si les données sont présentes
if (!isset($_SESSION['user_id'])) {
    echo "L'utilisateur n'est pas connecté ou les informations sont manquantes.";
    exit();
}

// Récupération des informations utilisateur
$user_id = $_SESSION['user_id'];

// Inclure le modèle User
require_once '../app/models/User.php';

$userModel = new User();
$user_info = $userModel->getUserById($user_id);

if (!$user_info) {
    echo "L'utilisateur n'a pas été trouvé dans la base de données.";
    exit();
}
?>

<!-- Ajout du fichier CSS -->
<head>
    <link rel="stylesheet" type="text/css" href="/public/assets/css/profile.css">
</head>

<!-- Affichage du profil de l'utilisateur -->
<h2>PROFIL</h2>

<!-- Photo de profil -->
<div class="profile-picture">
    <?php 
    // Définir le chemin de l'avatar
    $avatar_path = 'public/profile-picture/' . $user_info['avatar'];
    if (file_exists($avatar_path)): 
    ?>
        <img src="/<?php echo $avatar_path; ?>" alt="Photo de profil">
    <?php else: ?>
        <img src="/public/profile-picture/default.png" alt="Photo de profil par défaut">
    <?php endif; ?>
</div>

<!-- Formulaire pour modifier la photo de profil -->
<h3>MODIFIER MA PHOTO DE PROFIL</h3>
<form action="profile.php?action=updateAvatar" method="POST" enctype="multipart/form-data">
    <input type="file" name="avatar" required />
    <button type="submit">Mettre à jour la photo</button>
</form>

<!-- Formulaire pour modifier le pseudo -->
<h3>MODIFIER MON PSEUDO</h3>
<form action="profile.php?action=updateUsername" method="POST">
    <input type="text" name="new_username" value="<?php echo htmlspecialchars($user_info['username']); ?>" required />
    <button type="submit">Mettre à jour le pseudo</button>
</form>

<!-- Formulaire pour changer le mot de passe -->
<h3>CHANGER MON MOT DE PASSE</h3>
<form action="profile.php?action=updatePassword" method="POST">
    <label for="current_password">Mot de passe actuel</label>
    <input type="password" name="current_password" required />
    
    <label for="new_password">Nouveau mot de passe</label>
    <input type="password" name="new_password" required />
    
    <label for="confirm_password">Confirmer le nouveau mot de passe</label>
    <input type="password" name="confirm_password" required />
    
    <button type="submit">Mettre à jour le mot de passe</button>
</form>

<!-- Affichage des informations du wallet -->
<h3>MON WALLET</h3>
<div>
    <strong>Solde actuel : </strong>
    <?php echo number_format($user_info['balance'], 2); ?> $
</div>

<!-- Affichage de la variation du solde -->
<div class="balance-variation">
    <?php 
    // Affichage de la variation du solde en pourcentage
    if (isset($percentage_change) && $percentage_change > 0) {
        echo '<span class="positive">↑ ' . number_format($percentage_change, 2) . '%</span>';
    } elseif (isset($percentage_change) && $percentage_change < 0) {
        echo '<span class="negative">↓ ' . number_format($percentage_change, 2) . '%</span>';
    } else {
        echo '<span class="neutral">= 0%</span>';
    }
    ?>
</div>

<!-- Messages d'erreur ou de succès -->
<?php if (isset($error_message)): ?>
    <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
<?php endif; ?>

<?php if (isset($success_message)): ?>
    <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
<?php endif; ?>
