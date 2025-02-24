<?php
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePassword($password) {
    $errors = [];
    if (strlen($password) < 8) {
        $errors[] = "The password must be at least 8 characters long.";
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "The password must contain at least one digit.";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "The password must contain at least one uppercase letter.";
    }
    if (!preg_match('/[\W_]/', $password)) {
        $errors[] = "The password must contain at least one special character.";
    }
    return $errors;
}
?>