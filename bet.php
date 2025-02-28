<?php
// Inclure la connexion à la base de données
require_once 'utils/database.php';

// Récupérer l'instance de connexion
$db = Database::getInstance();
$connexion = $db->getConnection();

// Récupérer tous les paris ouverts
try {
    $stmt = $connexion->query("SELECT id, title, category, odds1, odds2, team1, team2 FROM bets WHERE status = 'open'");
    $bets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Erreur lors de la récupération des paris : " . $e->getMessage());
    $bets = [];  // Tableau vide en cas d'erreur
}
?>

<?php
        include "partials/header.php"
    ?>

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

    <!-- Section des paris sportifs -->
    <div id="sportsBets">
      <h2>Paris disponibles</h2>
      <?php if (!empty($bets)): ?>
        <ul>
          <?php foreach ($bets as $bet): ?>
            <li>
              <h3><?php echo htmlspecialchars($bet['title']); ?></h3>
              <p>Catégorie: <?php echo htmlspecialchars($bet['category']); ?></p>
              <p><?php echo htmlspecialchars($bet['team1']); ?> vs <?php echo htmlspecialchars($bet['team2']); ?></p>
              <p>Cote 1: <?php echo htmlspecialchars($bet['odds1']); ?> | Cote 2: <?php echo htmlspecialchars($bet['odds2']); ?></p>
              <a href="place_bet.php?id=<?php echo $bet['id']; ?>">Parier</a>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p>Aucun pari disponible actuellement.</p>
      <?php endif; ?>
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