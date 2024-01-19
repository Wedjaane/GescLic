<?php
session_start();
include('server.php');
Error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['modifier_p'])) {
    $point_id = $_POST['point_id'];
    $h_entree = $_POST['h_entree_chef'];
    $employe_id = $_SESSION['user_id'];

    // Vérifiez si l'employé est connecté en tant que chef
    if ($_SESSION['user_type'] === 'chef') {
        // Mise à jour des heures de pointage dans la base de données
        $update_query = "UPDATE pointer SET h_entree_chef = '$h_entree', chef_id= '$employe_id'  WHERE point_id = '$point_id'";
        $update_result = mysqli_query($conn, $update_query);

        if ($update_result) {
            // Redirigez l'utilisateur vers la page de pointage avec un message de succès
            echo "<script>alert('Pointage d'entrée modifié avec succès');</script>";
            echo "<script>window.location.href='valider_p.php'</script>";
        } else {
            // Redirigez l'utilisateur vers la page de pointage avec un message d'erreur
            echo "<script>alert('Erreur lors de la modification de pointage d'entrée !');</script>";
            echo "<script>window.location.href='valider_p.php'</script>";
        }
    }
} else if (isset($_POST['modifier_ps'])) {
    $point_id = $_POST['point_id'];
    $h_sortie = $_POST['h_sortie_chef'];
    $employe_id = $_SESSION['user_id'];

    // Vérifiez si l'employé est connecté en tant que chef
    if ($_SESSION['user_type'] === 'chef') {
        // Mise à jour des heures de pointage dans la base de données
        $update_query = "UPDATE pointer SET h_sortie_chef = '$h_sortie', chef_id= '$employe_id'  WHERE point_id = '$point_id'";
        $update_result = mysqli_query($conn, $update_query);

        if ($update_result) {
            // Redirigez l'utilisateur vers la page de pointage avec un message de succès
            echo "<script>alert('Pointage de sortie modifié avec succès');</script>";
            echo "<script>window.location.href='valider_p.php'</script>";
        } else {
            // Redirigez l'utilisateur vers la page de pointage avec un message d'erreur
            echo "<script>alert('Erreur lors de la modification de pointage de sortie !');</script>";
            echo "<script>window.location.href='valider_p.php'</script>";         
        }
    }
} 
else {
    // Redirigez l'utilisateur vers la page de pointage en cas d'accès incorrect
    echo "<script>alert('Erreur lors de modification de pointage . Veuillez réessayer plus tard ! ');</script>";
    echo "<script>window.location.href='valider_p.php'</script>";
    exit();
}

?>
