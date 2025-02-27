<?php
session_start();
require_once 'utils/database.php'; // Assurez-vous que le chemin est correct

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    die("Veuillez vous connecter pour voir votre panier.");
}

$user_id = $_SESSION['user_id'];

// Connexion à la base de données
$db = Database::getInstance();
$conn = $db->getConnection();

// Récupérer le panier de l'utilisateur
$query = $conn->prepare("
    SELECT si.id, si.name, si.price, ci.quantity 
    FROM cart_items ci
    JOIN shop_items si ON ci.item_id = si.id
    JOIN carts c ON ci.cart_id = c.id
    WHERE c.user_id = ?
");
$query->execute([$user_id]);
$cart_items = $query->fetchAll(PDO::FETCH_ASSOC);

// Calcul du total
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Panier</title>
    <link rel="stylesheet" href="assets/css/cart.css">
</head>
<body>
    <h1>Votre Panier</h1>
    <div class="cart-items">
        <?php if (empty($cart_items)): ?>
            <p>Votre panier est vide.</p>
        <?php else: ?>
            <?php foreach ($cart_items as $item): ?>
                <div class="cart-item">
                    <h2><?= htmlspecialchars($item['name']) ?></h2>
                    <p>Prix : <?= htmlspecialchars($item['price']) ?> €</p>
                    <p>Quantité : <?= $item['quantity'] ?></p>
                    
                    <!-- Bouton pour retirer un article -->
                    <form action="utils/remove_from_cart.php" method="post" style="display:inline;">
                        <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                        <button type="submit">Retirer</button>
                    </form>
                </div>
            <?php endforeach; ?>
            <hr>
            <h3>Total : <?= $total ?> €</h3>
        <?php endif; ?>
    </div>

    <!-- Bouton pour revenir à la boutique -->
    <a href="shop.php">
        <button type="button">Retour à la boutique</button>
    </a>

    <!-- Formulaire de paiement avec Stripe -->
    <?php if (!empty($cart_items)): ?>
        <form action="payment.php" method="POST">
            <input type="hidden" name="total" value="<?= $total ?>"> <!-- Passer le total pour le paiement -->
            <a>
            <button type="submit">Payer avec Stripe</button>
            </a>
        </form>
    <?php endif; ?>
</body>
</html>
