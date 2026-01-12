<?php
/**
 * PSM System - Entry Point
 * Redirects to login page
 */

require_once 'backend/config/database.php';

header("Location: " . BASE_URL . "/frontend/views/auth/login.php");
exit();

?>



