<?php
require 'database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

$message = $conn->prepare("SELECT chat.message, users.username FROM chat JOIN users ON chat.user_id = users.id ORDER BY chat.id ASC");
$message->execute();
$messages = $message->fetchAll(PDO::FETCH_ASSOC);

header("Content-Type: application/json");
echo json_encode($messages);