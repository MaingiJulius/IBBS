<?php
/**
 * ADMIN_USERS.PHP (LEGACY REDIRECT)
 * Purpose: This file acts as a "Router" to maintain path compatibility.
 * In the latest version of IBBS, 'view_users_sorted.php' provides a superior 
 * interface for managing the user database. This script ensures we always 
 * land on that better page.
 */

// The 'header' function performs a 302 redirect by default, 
// sending the user to the specialized user management view.
header("Location: view_users_sorted.php");

// Stop further execution of this file to save server resources.
exit();
?>
