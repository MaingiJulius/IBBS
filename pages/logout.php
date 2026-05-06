<?php
/**
 * SYSTEM LOGOUT SCRIPT (logout.php)
 * Purpose: This script is used to securely end a user's session.
 * It clears all saved data so that no one else can use the same browser to access the account.
 */

// [1] ACCESS SESSION: We must first call session_start() to access the current 'User Notebook'.
// You cannot clear a session if you haven't opened it first.
session_start();

// [2] CLEAR VARIABLES: session_unset() removes all the keys we stored (like user_id, role, name).
// This makes the 'Notebook' blank, so the system no longer knows who the user is.
session_unset();

// [3] DESTROY SESSION: session_destroy() completely deletes the session file from the server.
// This is the final step to ensure the login session is totally killed.
session_destroy();

/**
 * [4] REDIRECT: Now that the user is officially logged out, we send them back 
 * to the landing page (login.html) so they can log in again if they want.
 */
header("Location: login.html");

// [5] HALT EXECUTION: We use exit() to make sure no more code runs after the redirect.
// This is a security best practice.
exit();
?>

