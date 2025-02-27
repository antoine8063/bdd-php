<?php
// Démarrer la session PHP pour stocker les messages d'erreur ou de succès
session_start();

// Inclure la bibliothèque Stripe
require_once 'vendor/autoload.php';  // Assurez-vous que le chemin est correct

// Définir la clé secrète de l'API Stripe (utiliser votre clé secrète dans l'environnement de production)
\Stripe\Stripe::setApiKey('	sk_test_51Qx9HcAlH2S7sZiH6v8h0E2iHPqkbLyH0mm7VwXCZbUh2soKGFJV3hNZm5ELdoQDpC5KBnuoPsX1deNRNrgmTLh100xOWWSaTL');  // Remplacez avec votre clé secrète

// Vérifier si une demande a été envoyée
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Créer un paiement avec Stripe
        $amount = 1000;  // Montant en centimes (par exemple, 10.00€ -> 1000 centimes)
        $currency = 'eur';  // Devise : EUR pour euro

        // Créer un paiement Stripe
        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $amount,
            'currency' => $currency,
            'description' => 'Achat d\'article',
        ]);

        // Renvoyer le client_secret pour le paiement
        echo json_encode([
            'clientSecret' => $paymentIntent->client_secret
        ]);

    } catch (Exception $e) {
        // Gérer les erreurs
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo "Méthode de demande non autorisée.";
}
?>
