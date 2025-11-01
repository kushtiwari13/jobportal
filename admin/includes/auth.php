<?php
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

function require_admin(): void {
    if (empty($_SESSION['admin_user_id'])) {
        header('Location: ' . base_url() . 'admin/login.php');
        exit;
    }
}
