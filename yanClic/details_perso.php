<?php
include('server.php');
if (isset($_GET['id'])) {
    $requestId = $_GET['id'];
    
    // Récupérez les détails de la demande à partir de la base de données
    $query = "SELECT * FROM employe WHERE employe_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $requestId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $request = mysqli_fetch_assoc($result);

    // Générer le contenu HTML pour afficher les détails de la demande dans un formulaire en lecture seule
    $content = '<form>';
    $content .= '<div class="row">
                    <div class="col form-group">
                        <label>Nom de l\'employé :</label>
                    </div>
                    <div class="col form-group">
                        <input type="text" class="form-control" value="' . $request['Nom'] .' '. $request['prenom'] .'" readonly>
                    </div>
                </div>';
    $content .= '<div class="row">
                    <div class="col form-group">
                        <label>CIN :</label>
                    </div>
                    <div class="col form-group">
                    <input type="text" class="form-control" value="' . $request['cin'] . '" readonly>
                    </div>
                </div>';
    $content .= '<div class="row">
                    <div class="col form-group">
                        <label>Adresse :</label>
                    </div>
                    <div class="col form-group">
                        <input type="text" class="form-control" value="' . $request['adresse'] . '" readonly>
                    </div>
                </div>';
    $content .= '<div class="row">
                    <div class="col form-group">
                        <label>Téléphone :</label>
                    </div>
                    <div class="col form-group">
                        <input type="text" class="form-control" value="' . $request['tel'] . '" readonly>
                    </div>
                </div>';

    
$content .= '</form>';
echo $content;
}else {
    echo "Erreur : ID de l'employé non fourni.";
}
?>
