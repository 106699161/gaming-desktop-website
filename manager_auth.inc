<?php
/* Enhancement 1: Manager authentication helper */
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function require_manager_login() {
    if (empty($_SESSION["manager_logged_in"])) {
        header("Location: manager_login.php");
        exit;
    }
}

function require_super_manager() {
    require_manager_login();

    if (((isset($_SESSION["manager_role"]) ? $_SESSION["manager_role"] : "")) !== "SUPER") {
        header("Location: manager.php?error=super_only");
        exit;
    }
}
?>
