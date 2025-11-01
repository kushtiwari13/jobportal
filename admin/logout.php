<?php
require_once __DIR__ . '/../includes/db.php';
session_unset();
session_destroy();
header('Location: ' . base_url() . 'admin/login.php');
exit;
