document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("chat-form");
    const messageInput = document.getElementById("message");
    const chatBox = document.getElementById("chat-box");
    const chatContainer = document.getElementById("chatcontainer");


    // Charger les messages
    function loadMessages() {
        fetch("utils/getmessage.php")
            .then(response => response.json())
            .then(data => {
                chatBox.innerHTML = "";
                data.forEach(msg => {
                    chatBox.innerHTML += `<p><strong>${msg.username}:</strong> ${msg.message}</p>`;

                });
                chatContainer.scrollTop = chatContainer.scrollHeight;//scroll vers le bas
            });
    }

    // Actualiser les messages toutes les 2 secondes
    setInterval(loadMessages, 2000);
    loadMessages();

    // Envoi du message
    form.addEventListener("submit", function (event) {
        event.preventDefault();
        let formData = new FormData(form);
        
        fetch("utils/usemessage.php", {
            method: "POST",
            body: formData
        })
            .then(response => response.text())
            .then(() => {
                messageInput.value = ""; // Efface le champ message
                loadMessages(); // Recharge les messages
            });
    });
});