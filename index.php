<?php
/**
 * PSM System - Entry Point
 */

require_once dirname(__FILE__, 1) . '/backend/config/database.php';

header("Location: " . BASE_URL . "/frontend/views/auth/login.php");
exit();
?>




