<?php
/**
 * destroy.php
 * -----------
 * Session termination handler for the Portalia web application.
 *
 * Destroys the current user session and redirects to the index page.
 * Called by the "Cerrar sesión" (Log out) button in profile.php.
 */

session_start();

// Destroy all session data for the current user
session_destroy();

// Redirect to the landing page
header("Location: index.php");
exit();
?>
