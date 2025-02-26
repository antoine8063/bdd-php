<head>
    <link rel="stylesheet" href="assets/css/chat.css">
    <script src="assets/js/chat.js"></script>
</head>


<body>
    <div id='chat-box'>
        <?php
            session_start();
            require_once 'utils/database.php';

            $db = Database::getInstance();
            $conn = $db->getConnection();

            $message = $conn->prepare("SELECT message, user_id FROM chat ");
            $message->execute();

            $messages = $message->fetchAll(PDO::FETCH_ASSOC);

            foreach ($messages as $msg) {
                echo "utilisateur " . $msg['user_id']. " : " . $msg['message'] . "<br>";
            }
        ?>
    </div>
    <form id='chat-form'>
        <section class=container>
            <input type="text" name='message' id="message" placeholder="Entrez votre message...">
        </section>
        <section class=container>
            <input type="submit" name="submit" value="connexion" id="conn"/>
        </section>
    </form>
</body>



