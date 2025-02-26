<?php
require 'database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

$message = $conn->prepare("SELECT message, user_id FROM chat ORDER BY id DESC");
$message->execute();
$messages = $message->fetchAll(PDO::FETCH_ASSOC);

header("Content-Type: application/json");
echo json_encode($messages);
