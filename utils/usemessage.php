<?php
require 'database.php';

if (!empty($_POST['message'])) {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    $message = trim($_POST['message']);
    $id = 1; // Modifier selon l'utilisateur connecté

    try {
        $insert = "INSERT INTO chat (message, user_id) VALUES (:message, :user_id)";
        $stmt = $conn->prepare($insert);
        $stmt->execute([
            ':message' => $message,
            ':user_id' => $id
        ]);

        echo json_encode(["success" => true, "message" => "Message envoyé"]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
}
