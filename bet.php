<?php
// Démarrer la session pour stocker le solde
session_start();

// Initialiser le solde s'il n'existe pas
if (!isset($_SESSION['solde'])) {
    $_SESSION['solde'] = 0;
}

// Vérifier si un montant a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['amount'])) {
    $amount = floatval($_POST['amount']);
    if ($amount > 0) {
        $_SESSION['solde'] += $amount;
    } else {
        echo "<script>alert('Veuillez entrer un montant valide.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paris Sportifs</title>
    <link rel="stylesheet" href="bet.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Paris Sportifs</h1>
            <div class="balance">
                <span>Solde : </span>
                <span id="solde"><?php echo $_SESSION['solde']; ?> €</span>
                <button onclick="openAddMoneyModal()">+</button>
            </div>
        </header>

        <?php if ($_SESSION['solde'] > 0): ?>
            <!-- Paris 1 -->
            <div class="match-card" onclick="toggleDetails('details1')">
                <div class="match-summary">
                    <div class="teams">
                        <div class="team">
                            <img src="https://via.placeholder.com/30" alt="Équipe A">
                            <span>Équipe A</span>
                        </div>
                        <div class="vs">VS</div>
                        <div class="team">
                            <img src="https://via.placeholder.com/30" alt="Équipe B">
                            <span>Équipe B</span>
                        </div>
                    </div>
                </div>
                <div class="match-details" id="details1">
                    <div class="bet-options">
                        <div class="option" data-percentage="45%">
                            <span class="team">Équipe A</span>
                            <span class="odds">2.50</span>
                            <div class="percentage-bar"></div>
                            <span class="percentage">45%</span>
                        </div>
                        <div class="option" data-percentage="30%">
                            <span class="team">Nul</span>
                            <span class="odds">3.00</span>
                            <div class="percentage-bar"></div>
                            <span class="percentage">30%</span>
                        </div>
                        <div class="option" data-percentage="25%">
                            <span class="team">Équipe B</span>
                            <span class="odds">2.80</span>
                            <div class="percentage-bar"></div>
                            <span class="percentage">25%</span>
                        </div>
                    </div>
                    <div class="bet-buttons">
                        <button onclick="placeBet('Équipe A')">Parier Équipe A</button>
                        <button onclick="placeBet('Nul')">Parier Nul</button>
                        <button onclick="placeBet('Équipe B')">Parier Équipe B</button>
                    </div>
                </div>
            </div>

            <!-- Paris 2 -->
            <div class="match-card" onclick="toggleDetails('details2')">
                <div class="match-summary">
                    <div class="teams">
                        <div class="team">
                            <img src="https://via.placeholder.com/30" alt="Équipe C">
                            <span>Équipe C</span>
                        </div>
                        <div class="vs">VS</div>
                        <div class="team">
                            <img src="https://via.placeholder.com/30" alt="Équipe D">
                            <span>Équipe D</span>
                        </div>
                    </div>
                </div>
                <div class="match-details" id="details2">
                    <div class="bet-options">
                        <div class="option" data-percentage="60%">
                            <span class="team">Équipe C</span>
                            <span class="odds">1.80</span>
                            <div class="percentage-bar"></div>
                            <span class="percentage">60%</span>
                        </div>
                        <div class="option" data-percentage="25%">
                            <span class="team">Nul</span>
                            <span class="odds">3.50</span>
                            <div class="percentage-bar"></div>
                            <span class="percentage">25%</span>
                        </div>
                        <div class="option" data-percentage="15%">
                            <span class="team">Équipe D</span>
                            <span class="odds">4.00</span>
                            <div class="percentage-bar"></div>
                            <span class="percentage">15%</span>
                        </div>
                    </div>
                    <div class="bet-buttons">
                        <button onclick="placeBet('Équipe C')">Parier Équipe C</button>
                        <button onclick="placeBet('Nul')">Parier Nul</button>
                        <button onclick="placeBet('Équipe D')">Parier Équipe D</button>
                    </div>
                </div>
            </div>

            <!-- Paris 3 -->
            <div class="match-card" onclick="toggleDetails('details3')">
                <div class="match-summary">
                    <div class="teams">
                        <div class="team">
                            <img src="https://via.placeholder.com/30" alt="Équipe E">
                            <span>Équipe E</span>
                        </div>
                        <div class="vs">VS</div>
                        <div class="team">
                            <img src="https://via.placeholder.com/30" alt="Équipe F">
                            <span>Équipe F</span>
                        </div>
                    </div>
                </div>
                <div class="match-details" id="details3">
                    <div class="bet-options">
                        <div class="option" data-percentage="40%">
                            <span class="team">Équipe E</span>
                            <span class="odds">2.20</span>
                            <div class="percentage-bar"></div>
                            <span class="percentage">40%</span>
                        </div>
                        <div class="option" data-percentage="35%">
                            <span class="team">Nul</span>
                            <span class="odds">3.10</span>
                            <div class="percentage-bar"></div>
                            <span class="percentage">35%</span>
                        </div>
                        <div class="option" data-percentage="25%">
                            <span class="team">Équipe F</span>
                            <span class="odds">3.40</span>
                            <div class="percentage-bar"></div>
                            <span class="percentage">25%</span>
                        </div>
                    </div>
                    <div class="bet-buttons">
                        <button onclick="placeBet('Équipe E')">Parier Équipe E</button>
                        <button onclick="placeBet('Nul')">Parier Nul</button>
                        <button onclick="placeBet('Équipe F')">Parier Équipe F</button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal pour ajouter de l'argent -->
    <div id="addMoneyModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAddMoneyModal()">&times;</span>
            <h2>Ajouter de l'argent</h2>
            <form action="index.php" method="POST">
                <input type="number" id="amount" name="amount" placeholder="Montant en €" min="1" required>
                <button type="submit">Ajouter</button>
            </form>
        </div>
    </div>

    <script src="bet.js"></script>
</body>
</html>