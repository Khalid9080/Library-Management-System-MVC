<?php
// MVC/Controller/guard.php

// Make sure the session is started
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/** Return true if someone is logged in */
function is_logged_in(): bool {
    return !empty($_SESSION['user_id']);
}

/** Map role_id (1..3) -> role name */
function role_name_from_id(?int $rid): string {
    switch ((int)$rid) {
        case 3: return 'admin';
        case 2: return 'librarian';
        default: return 'member'; // 1 or anything else defaults to member
    }
}

/** Ensure the user is authenticated; else redirect to login */
function ensure_auth(): void {
    if (!is_logged_in()) {
        // IMPORTANT: this path is RELATIVE to the URL of the page calling ensure_auth().
        // From /MVC/View/Reusable_Components/dashboard.php, '../Authentication/login.php' is correct.
        header('Location: ../Authentication/login.php');
        exit;
    }
}

/** Current user as an array: ['id','username','email','role_id','role'] or null */
function auth_user(): ?array {
    if (!is_logged_in()) return null;

    $id       = (int)($_SESSION['user_id'] ?? 0);
    $username = (string)($_SESSION['username'] ?? '');
    $email    = (string)($_SESSION['email'] ?? '');
    $role_id  = isset($_SESSION['role_id']) ? (int)$_SESSION['role_id'] : 1;
    $role     = role_name_from_id($role_id);

    return [
        'id'       => $id,
        'username' => $username,
        'email'    => $email,
        'role_id'  => $role_id,
        'role'     => $role,
    ];
}

/** Convenience accessor for just the role string */
function user_role(): string {
    return role_name_from_id(isset($_SESSION['role_id']) ? (int)$_SESSION['role_id'] : 1);
}
