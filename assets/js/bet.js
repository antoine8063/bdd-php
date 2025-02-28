document.addEventListener('DOMContentLoaded', function () {
    // Utiliser l'ID de l'utilisateur passé depuis PHP
    if (!userId) {
        console.error("Aucun utilisateur connecté.");
        return;
    }

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
        fetch('getUser.php?id=${userId}') // Pas besoin de passer l'ID dans l'URL
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
                if (data.success) {
                    const bets = data.bets;
                    sportsBets.innerHTML = '';
                    if(bets.length === 0){
                        sportsBets.innerHTML = '<p>Aucun pari disponible.</p>';
                    } else {
                        bets.forEach(bet => {
                            const betElement = document.createElement('div');
                            betElement.classList.add('bet');

                            const betHeader = document.createElement('div');
                            betHeader.classList.add('bet-header');
                            betHeader.innerHTML = `
                                <span>${bet.title}</span>
                                <span>${bet.category}</span>
                                <span>${new Date(bet.created_at).toLocaleTimeString()}</span>
                            `;

                            const betDetails = document.createElement('div');
                            betDetails.classList.add('bet-details');
                            betDetails.innerHTML = `
                                <p>Cote : ${parseFloat(bet.odds).toFixed(2)}</p>
                                <p>Status : ${bet.status}</p>
                            `;

                            const betButton = document.createElement('button');
                            betButton.textContent = 'Parier';
                            betButton.addEventListener('click', function () {
                                alert(`Vous avez parié sur : ${bet.title}`);
                            });

                            betDetails.appendChild(betButton);

                            // Ouvrir/fermer les détails en cliquant sur l'en-tête
                            betHeader.addEventListener('click', function () {
                                betDetails.style.display = betDetails.style.display === 'block' ? 'none' : 'block';
                            });

                            betElement.appendChild(betHeader);
                            betElement.appendChild(betDetails);
                            sportsBets.appendChild(betElement);
                        });
                    }
                } else {
                    sportsBets.innerHTML = `<p>${data.message}</p>`;
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
                    sportsBets.classList.remove('hidden');
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

    // Pour le test : décommenter la ligne ci-dessous pour forcer l'affichage des paris même sans ajout d'argent
    // sportsBets.classList.remove('hidden');
});