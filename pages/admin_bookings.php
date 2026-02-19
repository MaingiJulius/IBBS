<?php
/**
 * ADMIN_BOOKINGS.PHP (LEGACY REDIRECT)
 * Purpose: This file acts as a "Signpost" or "Router". 
 * In earlier versions of the project, this was the primary page for managing bookings.
 * Now, a more advanced version 'view_admin_bookings.php' is used.
 */

// header("Location: ...") sends a signal to the browser to immediately jump to the new page.
// This ensures that any old bookmarks or internal links still work correctly.
header("Location: view_admin_bookings.php");

// exit() is critical here. It tells PHP to stop processing anything else in this file
// now that the redirect command has been sent.
exit();
?>
