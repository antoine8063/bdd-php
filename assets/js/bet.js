document.addEventListener('DOMContentLoaded', function () {
    const userId = 1; // ID de l'utilisateur connecté
    const addMoneyButton = document.getElementById('addMoneyButton');
    const addMoneyModal = document.getElementById('addMoneyModal');
    const closeModalButton = document.getElementById('closeModalButton');
    const confirmAddMoneyButton = document.getElementById('confirmAddMoneyButton');
    const moneyInput = document.getElementById('moneyInput');
    const sportsBets = document.getElementById('sportsBets');
    const balanceDisplay = document.getElementById('balanceDisplay');

    let userBalance = 0;

    // Met à jour l'affichage du solde
    function updateBalanceDisplay() {
        balanceDisplay.textContent = `Solde : ${userBalance.toFixed(2)}€`;
    }

    // Récupère le solde de l'utilisateur
    function fetchUserBalance() {
        fetch(`getUser.php?id=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    userBalance = parseFloat(data.balance);
                    updateBalanceDisplay();
                } else {
                    console.error("Erreur lors de la récupération du solde :", data.message);
                }
            })
            .catch(error => console.error('Erreur:', error));
    }

    // Charge les paris depuis getBets.php (mode GET)
    function loadSportsBets() {
        fetch('getBets.php')
            .then(response => response.json())
            .then(data => {
                console.log("Données reçues :", data);
                sportsBets.innerHTML = ''; // Réinitialise l'affichage

                if (data.success && Array.isArray(data.bets) && data.bets.length > 0) {
                    data.bets.forEach(bet => {
                        // Vérifie que les informations essentielles sont présentes
                        if (!bet.team_a || !bet.odds_a || !bet.team_b || !bet.odds_b || !bet.draw_odds) {
                            console.error("Données manquantes pour le pari:", bet);
                            return;
                        }

                        const betElement = document.createElement('div');
                        betElement.classList.add('bet');

                        const betHeader = document.createElement('div');
                        betHeader.classList.add('bet-header');
                        betHeader.innerHTML = `
                            <span>${bet.title}</span>
                            <span>${bet.category}</span>
                            <span>${new Date(bet.created_at).toLocaleString()}</span>
                        `;

                        const betDetails = document.createElement('div');
                        betDetails.classList.add('bet-details');
                        betDetails.innerHTML = `
                            <div class="bet-options">
                                <button class="bet-option" data-bet-id="${bet.id}" data-team="${bet.team_a}" data-odds="${bet.odds_a}" data-choice="Team A">
                                    ${bet.team_a} - Cote: ${bet.odds_a} (${bet.percentage_a}%)
                                </button>
                                <button class="bet-option" data-bet-id="${bet.id}" data-team="Nul" data-odds="${bet.draw_odds}" data-choice="Draw">
                                    Nul - Cote: ${bet.draw_odds} (${bet.percentage_draw}%)
                                </button>
                                <button class="bet-option" data-bet-id="${bet.id}" data-team="${bet.team_b}" data-odds="${bet.odds_b}" data-choice="Team B">
                                    ${bet.team_b} - Cote: ${bet.odds_b} (${bet.percentage_b}%)
                                </button>
                            </div>
                        `;

                        // Clique sur l'en-tête pour afficher ou masquer les détails
                        betHeader.addEventListener('click', function () {
                            betDetails.style.display = (betDetails.style.display === 'block') ? 'none' : 'block';
                        });

                        betElement.appendChild(betHeader);
                        betElement.appendChild(betDetails);
                        sportsBets.appendChild(betElement);
                    });

                    // Ajoute les écouteurs aux boutons de pari
                    const betButtons = document.querySelectorAll('.bet-option');
                    betButtons.forEach(button => {
                        button.addEventListener('click', function () {
                            const betId = this.getAttribute('data-bet-id');
                            const team = this.getAttribute('data-team');
                            const odds = this.getAttribute('data-odds');
                            placeBet(betId, team, odds);
                        });
                    });
                } else {
                    sportsBets.innerHTML = '<p>Aucun pari disponible.</p>';
                }
            })
            .catch(error => console.error('Erreur lors du chargement des paris:', error));
    }

    // Fonction pour placer un pari en mode POST vers getBets.php
    function placeBet(betId, choice, odds) {
        const amount = prompt(`Vous pariez sur ${choice} avec une cote de ${odds}. Entrez le montant (€) :`);
        if (!amount || isNaN(amount) || amount <= 0) {
            alert("Montant invalide.");
            return;
        }
        if (parseFloat(amount) > userBalance) {
            alert("Solde insuffisant pour ce pari.");
            return;
        }

        // Envoie la requête POST à getBets.php
        fetch('getBets.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                userId: userId,
                betId: betId,
                choice: choice,
                amount: parseFloat(amount)
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                userBalance -= parseFloat(amount); // Met à jour le solde
                updateBalanceDisplay();
                alert(`Pari placé avec succès sur ${choice} (${odds}).`);
            } else {
                alert(`Erreur lors du pari: ${data.message}`);
            }
        })
        .catch(error => console.error('Erreur lors du placement du pari:', error));
    }

    // Gestion du modal pour l'ajout d'argent
    addMoneyButton.addEventListener('click', function () {
        addMoneyModal.classList.remove('hidden');
    });
    closeModalButton.addEventListener('click', function () {
        addMoneyModal.classList.add('hidden');
    });
    confirmAddMoneyButton.addEventListener('click', function () {
        const amount = parseFloat(moneyInput.value);
        if (amount > 0) {
            fetch('updateBalance.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ userId: userId, amount: amount })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    userBalance = parseFloat(data.newBalance);
                    updateBalanceDisplay();
                    addMoneyModal.classList.add('hidden');
                    alert(`Vous avez ajouté ${amount}€. Votre solde est maintenant de ${userBalance}€.`);
                } else {
                    alert(`Erreur lors de la mise à jour du solde: ${data.message}`);
                }
            })
            .catch(error => console.error('Erreur lors de l\'ajout d\'argent:', error));
        } else {
            alert('Veuillez entrer un montant valide.');
        }
    });

    // Initialiser l'affichage
    fetchUserBalance();
    loadSportsBets();
});