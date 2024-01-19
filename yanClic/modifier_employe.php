<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('server.php');
// Vérifier si l'ID de l'employé est fourni dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Si l'ID de l'employé n'est pas fourni, rediriger vers la liste des employés
    header("Location: list_emp.php");
    exit();
}

$employe_id = $_GET['id'];

// Vérifier si l'employé existe dans la base de données
$query = "SELECT * FROM employe WHERE employe_id = '$employe_id'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    // Si l'employé n'existe pas, rediriger vers la liste des employés
    header("Location: list_emp.php");
    exit();
}

// Vérifier si l'utilisateur a soumis le formulaire de modification
if (isset($_POST['modifier_employe'])) {
    $id_entreprise = $_POST['id_entreprise'];
    $chef_id = $_POST['chef_id'];
    $poste = $_POST['Poste'];
    $dateEmbauche = $_POST['date_embauche'];

    // Effectuer la mise à jour de l'employé dans la base de données
    // Mettre à jour la table employe
    $updateEmployeQuery = "UPDATE employe SET Poste = '$poste', chef_id = '$chef_id' WHERE employe_id = '$employe_id'";

// Mettre à jour la table employe_entreprise
    $updateEmployeEntrepriseQuery = "UPDATE employe_entreprise SET fonction = '$poste' , DateEmbauche='$dateEmbauche' WHERE employe_id = '$employe_id' AND id_entreprise = '$id_entreprise'";
    if (mysqli_query($conn, $updateEmployeQuery) && mysqli_query($conn, $updateEmployeEntrepriseQuery) )  {
        // Set a success message
        $_SESSION['success_message'] = "L'employé a été mis à jour avec succès.";
    } else {
        // Set an error message
        $_SESSION['error_message'] = "Une erreur s'est produite lors de la mise à jour de l'employé.";
    }    
    // Rediriger vers la liste des employés après la mise à jour
    header("Location: list_emp.php");
    exit();
}
if (isset($_POST['supprimer_employe'])) {
    $date = date('Y-m-d');
    $id_entreprise = $_POST['id_entreprise'];

    // Effectuer la mise à jour de l'employé dans la base de données
    $updateDelQuery = "UPDATE employe SET  chef_id= NULL, id_entreprise = NULL   WHERE employe_id='$employe_id'";
    $DelQuery = "UPDATE employe_entreprise SET  DateFin = NOW() , archive = 1   WHERE employe_id = '$employe_id' AND id_entreprise = '$id_entreprise'";

    if (mysqli_query($conn, $updateDelQuery) && mysqli_query($conn, $DelQuery) ) {
        // Set a success message
        $_SESSION['success_message'] = "L'employé a été supprimé avec succès.";
    } else {
        // Set an error message
        $_SESSION['error_message'] = "Une erreur s'est produite lors de la suppression de l'employé.";
    }
    // Rediriger vers la liste des employés après la mise à jour
    header("Location: list_emp.php");
    exit();
}

?>
