<?php
// This is a simple script used to log a user out of the system.

// First, we need to access the current session (our virtual notebook).
session_start();

// session_unset() clears all the information we stored in the session variables.
// It's like erasing everything written in the notebook.
session_unset();

// session_destroy() completely throws away the session.
// It's like shredding the notebook so no one can read it later.
session_destroy();

// After cleaning up, we send the user back to the login page.
header("Location: login.html");

// We use exit() to make sure the script stops running immediately.
exit();
?>
