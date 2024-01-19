<?php
// Initialisation de la session
session_start();

// Destruction de toutes les variables de session
$_SESSION = array();

// Destruction de la session
session_destroy();
// Supprimer les cookies d'authentification, s'ils existent
if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
    setcookie('email', '', time() - 3600);
    setcookie('password', '', time() - 3600);
}
// Redirection vers la page de connexion
header("location: login.php");
exit;
?>