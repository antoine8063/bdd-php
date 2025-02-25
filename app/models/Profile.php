<?php
// app/models/Profile.php
class Profile {
    private $conn;

    public function __construct() {
        $db = Database::getInstance();
        $this->conn = $db->getConnection();
    }

    public function getUserInfo($user_id) {
        $query = "SELECT id, username, avatar, balance FROM users WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getBalanceChange($user_id) {
        $query = "SELECT SUM(amount) AS total_change FROM transactions WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateAvatar($user_id, $new_filename) {
        $query = "UPDATE users SET avatar = :avatar WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':avatar' => $new_filename, ':user_id' => $user_id]);
    }

    public function updateUsername($user_id, $new_username) {
        $query = "UPDATE users SET username = :username WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':username' => $new_username, ':user_id' => $user_id]);
    }

    public function updatePassword($user_id, $new_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET password = :password WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':password' => $hashed_password, ':user_id' => $user_id]);
    }
}
?>
