<?php
/**
 * ADMIN_ROUTES.PHP (LEGACY REDIRECT)
 * Purpose: This script serves as a "Bridge" between the old system structure 
 * and the improved routing architecture.
 * It ensures that any internal link or user bookmark pointing to the old 
 * 'admin_routes.php' is seamlessly sent to the new 'view_routes.php'.
 */

// header("Location: ...") is a PHP command that tells the web browser 
// to instantly jump to the new destination.
header("Location: view_routes.php");

// exit() is mandatory here. It prevents the server from executing 
// any hidden logic below this line once the redirect signal is sent.
exit();
?>
