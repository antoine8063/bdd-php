<?php
session_start();
require_once 'database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    die("Veuillez vous connecter pour modifier votre panier.");
}

$user_id = $_SESSION['user_id'];
$item_id = $_POST['item_id'];

// Connexion à la base de données
$db = Database::getInstance();
$conn = $db->getConnection();

// Vérifier si l'article est dans le panier
$stmt = $conn->prepare("
    SELECT ci.id, ci.quantity 
    FROM cart_items ci
    JOIN carts c ON ci.cart_id = c.id
    WHERE c.user_id = ? AND ci.item_id = ?
");
$stmt->execute([$user_id, $item_id]);
$cartItem = $stmt->fetch(PDO::FETCH_ASSOC);

if ($cartItem) {
    if ($cartItem['quantity'] > 1) {
        // Diminuer la quantité de 1
        $stmt = $conn->prepare("UPDATE cart_items SET quantity = quantity - 1 WHERE id = ?");
        $stmt->execute([$cartItem['id']]);
    } else {
        // Supprimer l'article du panier s'il n'en reste qu'un
        $stmt = $conn->prepare("DELETE FROM cart_items WHERE id = ?");
        $stmt->execute([$cartItem['id']]);
    }
}

header("Location: ../cart.php");
exit;
