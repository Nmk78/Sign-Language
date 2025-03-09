<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
unset($_SESSION['user']);
session_destroy();
echo json_encode(['success' => true]);
?>
