document.addEventListener('DOMContentLoaded', function () {
    // Vérifie si le bouton pour ajouter un pari existe
    const ajouterPariBtn = document.getElementById("ajouterPariBtn");
    const betFormModal = document.getElementById("betFormModal");
    const closeModalBtn = document.getElementById("closeModal");
    const betForm = document.getElementById("betForm");

    // Vérifie l'existence du bouton pour ajouter un pari
    if (ajouterPariBtn) {
        ajouterPariBtn.addEventListener("click", () => {
            // Afficher la modale quand on clique sur le bouton
            betFormModal.style.display = "flex";
        });
    } else {
        console.error("Le bouton #ajouterPariBtn n'a pas été trouvé.");
    }

    // Vérifie l'existence du bouton pour fermer la modale
    if (closeModalBtn) {
        closeModalBtn.addEventListener("click", () => {
            // Fermer la modale
            betFormModal.style.display = "none";
        });
    } else {
        console.error("Le bouton #closeModal n'a pas été trouvé.");
    }

    // Soumission du formulaire
    if (betForm) {
        betForm.addEventListener("submit", function (event) {
            event.preventDefault();
            
            // Récupère les données du formulaire
            const formData = new FormData(betForm);

            // Envoi des données vers le serveur via fetch
            fetch("add_bet.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())  // La réponse du serveur en JSON
            .then(data => {
                if (data.success) {
                    alert("Pari enregistré avec succès !");
                    betFormModal.style.display = "none";
                    betForm.reset();
                } else {
                    alert("Erreur lors de l’enregistrement du pari : " + data.message);
                }
            })
            .catch(error => {
                console.error("Erreur lors de la requête :", error);
                alert("Une erreur est survenue lors de l'ajout du pari.");
            });
        });
    } else {
        console.error("Le formulaire #betForm n'a pas été trouvé.");
    }
});
