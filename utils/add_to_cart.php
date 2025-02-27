<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    die("Veuillez vous connecter pour ajouter des articles au panier.");
}

$user_id = $_SESSION['user_id'];
$item_id = $_POST['item_id'];

// Connexion à la base de données
require_once __DIR__ . '/database.php';

$db = Database::getInstance(); // Récupérer l'instance de la BDD
$conn = $db->getConnection(); // Récupérer l'objet PDO

// Vérifier si l'utilisateur a déjà un panier
$stmt = $conn->prepare("SELECT id FROM carts WHERE user_id = ?");
$stmt->execute([$user_id]);
$cart = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cart) {
    // Créer un nouveau panier
    $stmt = $conn->prepare("INSERT INTO carts (user_id) VALUES (?)");
    $stmt->execute([$user_id]);
    $cart_id = $conn->lastInsertId();
} else {
    // Utiliser le panier existant
    $cart_id = $cart['id'];
}

// Vérifier si l'article est déjà dans le panier
$stmt = $conn->prepare("SELECT id, quantity FROM cart_items WHERE cart_id = ? AND item_id = ?");
$stmt->execute([$cart_id, $item_id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    // Ajouter un nouvel article au panier
    $stmt = $conn->prepare("INSERT INTO cart_items (cart_id, item_id, quantity) VALUES (?, ?, 1)");
    $stmt->execute([$cart_id, $item_id]);
} else {
    // Mettre à jour la quantité de l'article
    $new_quantity = $item['quantity'] + 1;
    $stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
    $stmt->execute([$new_quantity, $item['id']]);
}

// Redirection vers la boutique
header("Location: ../shop.php");
exit;
?>
