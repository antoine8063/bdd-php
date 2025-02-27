<?php
session_start();
require_once 'utils/database.php';

// Créer une instance de la classe Database et obtenir la connexion
$db = Database::getInstance();
$conn = $db->getConnection();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo "Veuillez vous connecter pour accéder à la boutique.";
    exit;
}

$user_id = $_SESSION['user_id'];

// Récupérer les articles de la boutique
$query = $conn->query("SELECT * FROM shop_items");
$items = $query->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les articles du panier
$stmt = $conn->prepare("
    SELECT si.id, si.name, si.price, ci.quantity 
    FROM cart_items ci
    JOIN shop_items si ON ci.item_id = si.id
    JOIN carts c ON ci.cart_id = c.id
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>BetFactory Shop</title>
    <link rel="stylesheet" href="assets/css/shop.css">
</head>
<body>

<?php
        include "partials/header.php"
    ?>


    <h1>Bienvenue sur la boutique BetFactory</h1>

    <div class="shop-items">
        <?php if (empty($items)): ?>
            <p>Aucun article disponible.</p>
        <?php else: ?>
            <?php foreach ($items as $item): ?>
                <div class="item">
                    <h2><?= htmlspecialchars($item['name']) ?></h2>
                    <p>Prix : <?= number_format($item['price'], 2) ?> €</p>
                    <form action="utils/add_to_cart.php" method="post">
                        <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                        <button type="submit">Ajouter au panier</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <h2>Mon Panier</h2>
    <div class="cart">
        <?php if (empty($cartItems)): ?>
            <p>Votre panier est vide.</p>
        <?php else: ?>
            <ul>
                <?php $total = 0; ?>
                <?php foreach ($cartItems as $item): ?>
                    <?php $subtotal = $item['price'] * $item['quantity']; ?>
                    <?php $total += $subtotal; ?>
                    <li>
                        <?= htmlspecialchars($item['name']) ?> - 
                        <?= $item['quantity'] ?> x <?= number_format($item['price'], 2) ?>€ 
                        = <?= number_format($subtotal, 2) ?>€
                        <form action="utils/remove_from_cart.php" method="post" style="display:inline;">
                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                            <button type="submit">Retirer</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p><strong>Total : <?= number_format($total, 2) ?>€</strong></p>
            <a href="cart.php">Voir mon panier</a>
        <?php endif; ?>
    </div>
</body>
</html>
