<?php
require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']['email']);
}

function get_current_user_data() {
    return is_logged_in() ? $_SESSION['user'] : null;
}

function require_auth() {
    if (!is_logged_in()) {
        header('Location: index.php?login=required');
        exit;
    }
}

function attempt_login($email, $password) {
    global $AUTHORIZED_USERS;
    $email = strtolower(trim($email));
    
    if (isset($AUTHORIZED_USERS[$email])) {
        $userData = $AUTHORIZED_USERS[$email];
        if ($password === $userData['password']) {
            $_SESSION['user'] = [
                'email' => $email,
                'name' => $userData['name'],
                'role' => $userData['role']
            ];
            return true;
        }
    }
    return false;
}

function perform_logout() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}
