<?php
// Assurez-vous que le fichier 'checkout.php' génère correctement un clientSecret
// Nous avons besoin d'une page HTML avec un formulaire pour payer.

session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement BetFactory</title>
    <link rel="stylesheet" href="assets/css/payment.css">

    <!-- Inclure Stripe.js -->
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
    <h1>Paiement pour votre achat</h1>

    <!-- Formulaire de paiement -->
    <form id="payment-form">
        <div id="card-element">
            <!-- Élément Stripe pour le numéro de carte -->
        </div>

        <button id="submit">Payer</button>
    </form>

    <script>
        // Remplacez cette clé par votre clé publique Stripe
        var stripe = Stripe('pk_test_51Qx9HcAlH2S7sZiH7WoiiN39uh7rGhKC5PL9NLKmluIFv813qKOBOTZTyJhOS1Zxb8AbcaIyglGyqntdizC0fQIn00CtOdG32P');  // Utilisez votre propre clé publique
        var elements = stripe.elements();

        // Créer un élément de carte pour capturer les informations de la carte de paiement
        var card = elements.create('card');
        card.mount('#card-element');

        var form = document.getElementById('payment-form');
        var submitButton = document.getElementById('submit');

        form.addEventListener('submit', function (event) {
            event.preventDefault();

            // Créer un PaymentIntent via PHP
            fetch('checkout.php', {
                method: 'POST',
            })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                if (data.error) {
                    // Afficher l'erreur s'il y en a une
                    alert(data.error);
                } else {
                    // Si un clientSecret a été retourné, alors procéder à la confirmation du paiement
                    stripe.confirmCardPayment(data.clientSecret, {
                        payment_method: {
                            card: card,
                        },
                    }).then(function (result) {
                        if (result.error) {
                            // Si une erreur se produit, affichez-la
                            alert(result.error.message);
                        } else {
                            // Si le paiement est réussi, rediriger vers une page de confirmation ou d'autres actions
                            alert('Paiement réussi !');
                            // Vous pouvez aussi rediriger l'utilisateur ou gérer d'autres actions après le paiement
                        }
                    });
                }
            })
            .catch(function (error) {
                console.error('Erreur avec la requête : ', error);
            });
        });
    </script>
</body>
</html>
