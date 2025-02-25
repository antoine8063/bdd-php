<head>
    <link rel="stylesheet" href="assets/css/chat.css">
    <script src="assets/js/chat.js"></script>
</head>


<body>
    <?php
        session_start();
        require_once 'utils/database.php';

        $db = Database::getInstance();

        $conn = $db->getConnection();

        $message = $conn->prepare("SELECT message FROM chat ");

        $message->execute();

        echo $message->fetchColumn();
    ?>
    <form method="POST" action="utils/usemessage.php" enctype="multipart/form-data">
        <section class=container>
            <input type="text" id="message" placeholder="Entrez votre message...">
            <button type="submit" id="send">Envoyer</button>
        </section>
    </form>
</body>



