<?php
// Assurez-vous d'avoir une connexion à la base de données établie avant cette étape
include('server.php');

// Vérifiez si l'ID de l'entreprise a été envoyé par la requête AJAX
    $entrepriseId = $entreprise_id;

    // Construire la requête SQL pour récupérer les employés associés à l'entreprise sélectionnée
    $query = "SELECT employe_id, Nom, prenom , cin FROM employe WHERE id_entreprise = ?";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $entrepriseId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Construire la liste des employés au format HTML
    $options = '';
    while ($row = mysqli_fetch_assoc($result)) {
        $employeId = $row['employe_id'];
        $employeName = $row['Nom'] ;
        $employepr = $row['prenom'];
        $cin = $row['cin'];
        $options .= '<option value="' . $employeId . '" data-cin="' . $cin . '">' . $employeName . ' '.$employepr.'</option>';
    }

    // Renvoyer la liste des employés au format HTML
    echo $options;


// Fermer la connexion à la base de données
mysqli_close($conn);
?>