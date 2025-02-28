    document.addEventListener('DOMContentLoaded', function () {
        const userId = 1; // ID de l'utilisateur connecté (à gérer via session dans une appli réelle)
        const addMoneyButton = document.getElementById('addMoneyButton');
        const addMoneyModal = document.getElementById('addMoneyModal');
        const closeModalButton = document.getElementById('closeModalButton');
        const confirmAddMoneyButton = document.getElementById('confirmAddMoneyButton');
        const moneyInput = document.getElementById('moneyInput');
        const sportsBets = document.getElementById('sportsBets');
        const balanceDisplay = document.getElementById('balanceDisplay');

        let userBalance = 0;

        // Mettre à jour l'affichage du solde
        function updateBalanceDisplay() {
            balanceDisplay.textContent = `Solde : ${userBalance.toFixed(2)}€`;
        }

        // Récupérer le solde de l'utilisateur
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

        // Charger les paris sportifs depuis la base
        function loadSportsBets() {
            fetch('getBets.php')
                .then(response => response.json())
                .then(data => {
                    console.log("Données reçues :", data);
                    sportsBets.innerHTML = ''; // Nettoie l'affichage avant d'ajouter les nouveaux paris

                    if (data.success && Array.isArray(data.bets) && data.bets.length > 0) {
                        data.bets.forEach(bet => {
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
                                    <button class="bet-option" data-bet-id="${bet.id}" data-team="${bet.team_a}" data-odds="${bet.odds_a}" data-choice="team_a">
                                        ${bet.team_a} - Cote: ${bet.odds_a} (${bet.percentage_a}%)
                                    </button>
                                    <button class="bet-option" data-bet-id="${bet.id}" data-team="Nul" data-odds="${bet.draw_odds}" data-choice="draw">
                                        Nul - Cote: ${bet.draw_odds} (${bet.percentage_draw}%)
                                    </button>
                                    <button class="bet-option" data-bet-id="${bet.id}" data-team="${bet.team_b}" data-odds="${bet.odds_b}" data-choice="team_b">
                                        ${bet.team_b} - Cote: ${bet.odds_b} (${bet.percentage_b}%)
                                    </button>
                                </div>
                            `;

                            // Ouvrir/fermer les détails en cliquant sur l'en-tête
                            betHeader.addEventListener('click', function () {
                                betDetails.style.display = betDetails.style.display === 'block' ? 'none' : 'block';
                            });

                            betElement.appendChild(betHeader);
                            betElement.appendChild(betDetails);
                            sportsBets.appendChild(betElement);
                        });

                        // Ajouter des événements aux boutons de pari
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
                .catch(error => console.error('Erreur:', error));
        }

        // Fonction pour placer un pari
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

            fetch('placeBet.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ userId: userId, betId: betId, choice: choice, amount: parseFloat(amount) })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    userBalance -= parseFloat(amount); // Déduire le montant du pari
                    updateBalanceDisplay();
                    alert(`Pari placé avec succès sur ${choice} (${odds}).`);
                } else {
                    alert(`Erreur lors du pari: ${data.message}`);
                }
            })
            .catch(error => console.error('Erreur:', error));
        }

        // Ouvrir le modal d'ajout d'argent
        addMoneyButton.addEventListener('click', function () {
            addMoneyModal.classList.remove('hidden');
        });

        // Fermer le modal
        closeModalButton.addEventListener('click', function () {
            addMoneyModal.classList.add('hidden');
        });

        // Confirmer l'ajout d'argent
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
                .catch(error => console.error('Erreur:', error));
            } else {
                alert('Veuillez entrer un montant valide.');
            }
        });

        // Initialiser l'affichage
        fetchUserBalance();
        loadSportsBets();
    });
