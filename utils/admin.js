document.addEventListener('DOMContentLoaded', function () {
    const ajouterPariBtn = document.getElementById("ajouterPariBtn");
    const betFormModal = document.getElementById("betFormModal");
    const closeModalBtn = document.getElementById("closeModal");
    const betForm = document.getElementById("betForm");

    if (ajouterPariBtn) {
        ajouterPariBtn.addEventListener("click", () => {
            betFormModal.style.display = "flex";
        });
    } else {
        console.error("Le bouton #ajouterPariBtn n'a pas été trouvé.");
    }

    if (closeModalBtn) {
        closeModalBtn.addEventListener("click", () => {
            betFormModal.style.display = "none";
        });
    } else {
        console.error("Le bouton #closeModal n'a pas été trouvé.");
    }

    if (betForm) {
        betForm.addEventListener("submit", function (event) {
            event.preventDefault();

            const odds1 = parseFloat(document.getElementById("odds1").value);
            const odds2 = parseFloat(document.getElementById("odds2").value);

            if (odds1 <= 0 || odds2 <= 0) {
                alert("Les cotes doivent être supérieures à 0.");
                return;
            }

            const formData = new FormData(betForm);

            fetch("utils/add_bet.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Pari ajouté avec succès !");
                    betFormModal.style.display = "none";
                    betForm.reset();
                } else {
                    alert("Erreur : " + data.message);
                }
            })
            .catch(error => {
                console.error("Erreur lors de l'envoi du formulaire :", error);
                alert("Une erreur est survenue.");
            });
        });
    } else {
        console.error("Le formulaire #betForm n'a pas été trouvé.");
    }
});
