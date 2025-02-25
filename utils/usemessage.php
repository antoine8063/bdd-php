<?php
require 'database.php';
session_start();
if (isset($_POST['submit'])) {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    $message = $_POST['message'];
    $id = 1;



    if (isset($_POST['send'])) {
        $insert = "INSERT INTO chat (message,user_id) VALUES (:message,user_id)";
        $stmt = $conn->prepare($insert);
        $stmt->execute([
            ':message' => $message,
            ':user_id' => $id
        ]);
    }
}