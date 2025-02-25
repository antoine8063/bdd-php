<?php
class ProfileController {
    public function showProfile() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=home');
            exit();
        }

        // Inclure le modèle pour récupérer les informations de l'utilisateur
        require_once 'app/models/User.php';
        $userModel = new User();
        $user_info = $userModel->getUserById($_SESSION['user_id']);

        // Inclure la vue du profil
        require_once 'views/ProfileView.php';
    }

    public function updateAvatar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                require_once 'app/models/User.php';
                $userModel = new User();

                $user_id = $_SESSION['user_id'];
                $avatar_path = 'public/profile-picture/' . basename($_FILES['avatar']['name']);

                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $avatar_path)) {
                    $userModel->updateAvatar($user_id, basename($_FILES['avatar']['name']));
                    header('Location: index.php?action=profile&success=avatar_updated');
                    exit();
                } else {
                    header('Location: index.php?action=profile&error=upload_failed');
                    exit();
                }
            } else {
                header('Location: index.php?action=profile&error=no_file');
                exit();
            }
        } else {
            header('Location: index.php?action=profile&error=invalid_request');
            exit();
        }
    }

    public function updateUsername() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once 'app/models/User.php';
            $userModel = new User();

            $user_id = $_SESSION['user_id'];
            $new_username = $_POST['new_username'];

            if ($userModel->updateUsername($user_id, $new_username)) {
                header('Location: index.php?action=profile&success=username_updated');
                exit();
            } else {
                header('Location: index.php?action=profile&error=username_update_failed');
                exit();
            }
        } else {
            header('Location: index.php?action=profile&error=invalid_request');
            exit();
        }
    }

    public function updatePassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once 'app/models/User.php';
            $userModel = new User();

            $user_id = $_SESSION['user_id'];
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            $stored_password = $userModel->getPasswordById($user_id);

            if (password_verify($current_password, $stored_password)) {
                if ($new_password === $confirm_password) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    if ($userModel->updatePassword($user_id, $hashed_password)) {
                        header('Location: index.php?action=profile&success=password_updated');
                        exit();
                    } else {
                        header('Location: index.php?action=profile&error=password_update_failed');
                        exit();
                    }
                } else {
                    header('Location: index.php?action=profile&error=passwords_dont_match');
                    exit();
                }
            } else {
                header('Location: index.php?action=profile&error=invalid_current_password');
                exit();
            }
        } else {
            header('Location: index.php?action=profile&error=invalid_request');
            exit();
        }
    }
}
?>
