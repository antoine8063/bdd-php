<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Paris Sportifs</title>
  <link rel="stylesheet" href="assets/css/bet.css">
</head>
<body>
  <div id="app">
    <!-- En-tête avec le bouton pour ajouter de l'argent et le solde -->
    <div id="header">
      <div id="balanceDisplay">Solde : 0€</div>
      <button id="addMoneyButton">Ajouter de l'argent</button>
    </div>

    <!-- Section des paris sportifs (initialement masquée) -->
    <div id="sportsBets" class="hidden">
      <!-- Les paris sportifs seront injectés ici par JavaScript -->
    </div>

    <!-- Boîte de dialogue pour ajouter de l'argent -->
    <div id="addMoneyModal" class="modal hidden">
      <div class="modal-content">
        <span id="closeModalButton">&times;</span>
        <h2>Ajouter de l'argent</h2>
        <input type="number" id="moneyInput" placeholder="Montant à ajouter" min="1">
        <button id="confirmAddMoneyButton">Confirmer</button>
      </div>
    </div>
  </div>

  <script src="assets/js/bet.js"></script>
</body>
</html>
