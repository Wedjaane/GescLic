<?php
include('server.php');

// Récupérer les valeurs du formulaire de recherche
$employeeEmail = $_POST['email'];
$employeeCIN = $_POST['cin'];

// Effectuer la recherche dans la base de données
$query = "SELECT employe_id, Nom, prenom, cin, email, id_entreprise FROM employe WHERE cin = '$employeeCIN' OR email = '$employeeEmail' ";

// Exécuter la requête de recherche
$result = mysqli_query($conn, $query);

if ($result) {
  if (mysqli_num_rows($result) > 0) {
    // L'employé est trouvé
    $row = mysqli_fetch_assoc($result);
    $employeeID = $row['employe_id'];
    $employeeName = $row['Nom'] . ' ' . $row['prenom'];
    $employeeEnterpriseID = $row['id_entreprise'];

    if ($employeeID == $entreprise_id) {
        // L'employé appartient à l'entreprise de l'administrateur
        $response = array(
          'status' => 'error',
          'message' => 'Cet employé existe déjà dans votre entreprise.'
        );
    
    } elseif (!is_null($employeeEnterpriseID)) {
        // L'employé a déjà une entreprise associée
        $response = array(
          'status' => 'error',
          'message' => 'Cet employé est déjà dans une entreprise.'
        );
    } else {
      // L'employé peut être ajouté à l'entreprise
      $response = array(
        'status' => 'success',
        'employeeName' => $employeeName,
        'employeeID' => $employeeID 
      );
    }
  } else {
    // L'employé n'est pas trouvé
    $response = array(
      'status' => 'error',
      'message' => 'Cet employé n\'a pas un compte dans GesClic.'
    );
  }
} else {
  // Erreur lors de l'exécution de la requête
  $response = array(
    'status' => 'error',
    'message' => 'Une erreur s\'est produite lors de la recherche de l\'employé : ' . mysqli_error($conn)
  );
}

// Fermer la connexion à la base de données
mysqli_close($conn);

// Retourner la réponse au format JSON
header('Content-Type: application/json');
echo json_encode($response);
?>