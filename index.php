<?php
/**
 * PSM System - Entry Point
 */

require_once __DIR__ . '/backend/config/database.php';

header("Location: " . BASE_URL . "/frontend/views/auth/login.php");
exit();
?>




