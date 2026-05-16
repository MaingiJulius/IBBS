<?php
// <?php (opening tag) tells the server to start interpreting the code as PHP.

/**
 * SYSTEM LOGOUT SCRIPT (logout.php)
 */

// session_start (session start) opens the current user notebook.
session_start();

// session_unset (session unset) clears all data inside the notebook.
session_unset();

// session_destroy (session destroy) throws away the notebook completely.
session_destroy();

// header (header) sends the user back to the login page.
header("Location: login.html");

// exit (exit) stops the script immediately.
exit();
?>
