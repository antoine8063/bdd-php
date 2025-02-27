<?php
session_start();
require_once 'utils/database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    die("Veuillez vous connecter pour voir votre panier.");
}

$user_id = $_SESSION['user_id'];
$db = Database::getInstance();
$conn = $db->getConnection();

// Récupérer le panier de l'utilisateur
$stmt = $conn->prepare("
    SELECT ci.id, si.name, si.price, ci.quantity 
    FROM cart_items ci
    JOIN shop_items si ON ci.item_id = si.id
    JOIN carts c ON ci.cart_id = c.id
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Panier</title>
    <link rel="stylesheet" href="assets/css/styles-shop.css">
</head>
<body>
    <h1>Mon Panier</h1>

    <?php if (empty($items)): ?>
        <p>Votre panier est vide.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Article</th>
                <th>Prix</th>
                <th>Quantité</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
            <?php $total = 0; ?>
            <?php foreach ($items as $item): ?>
                <?php $subtotal = $item['price'] * $item['quantity']; ?>
                <?php $total += $subtotal; ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= number_format($item['price'], 2) ?> €</td>
                    <td><?= $item['quantity'] ?></td>
                    <td><?= number_format($subtotal, 2) ?> €</td>
                    <td>
                        <form action="utils/remove_from_cart.php" method="post">
                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                            <button type="submit">Retirer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3"><strong>Total :</strong></td>
                <td colspan="2"><strong><?= number_format($total, 2) ?> €</strong></td>
            </tr>
        </table>
    <?php endif; ?>

    <a href="shop.php">Retour à la boutique</a>
</body>
</html>
