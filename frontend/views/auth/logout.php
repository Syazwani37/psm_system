<?php
/**
 * PSM System - Logout
 * Frontend View: Auth Module
 */

session_start();
session_destroy();

header("Location: login.php");
exit();
?>
