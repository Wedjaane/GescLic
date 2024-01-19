<?php
include('server.php');
if (isset($_GET['id'])) {
    $requestId = $_GET['id'];
    
    // Récupérez les détails de la demande à partir de la base de données
    $query = "SELECT * FROM demande WHERE N_dmd = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $requestId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $request = mysqli_fetch_assoc($result);

    // Générer le contenu HTML pour afficher les détails de la demande dans un formulaire en lecture seule
    $content = '<form>';
    $content .= '<div class="row">
                    <div class="col form-group">
                        <label>Type de demande :</label>
                    </div>
                    <div class="col form-group">
                        <input type="text" class="form-control" value="' . $request['type_dmd'] . '" readonly>
                    </div>
                </div>';
   
    $content .= '<div class="row">
                    <div class="col form-group">
                        <label>Description :</label>
                    </div>
                    <div class="col form-group">
                        <input type="text" class="form-control" value="' . $request['dscr_dmd'] . '" readonly>
                    </div>
                </div>';
    if ($_SESSION['user_type'] !== "employeur") { 
    $content .= '<div class="row">
                    <div class="col form-group">
                        <label>Statut :</label>
                    </div>
                    <div class="col form-group">
                        <input type="text" class="form-control" value="' . $request['Statut'] . '" readonly>
                    </div>
                </div>'; 
            }
    // Si le type de demande est "Congé", récupérez les détails du congé à partir de la table "congés"
    if ($request['type_dmd'] === 'Congé') {
        $congeId = $request['id_conge'];
        $queryConge = "SELECT * FROM conges WHERE id_conge = ?";
        $stmtConge = mysqli_prepare($conn, $queryConge);
        mysqli_stmt_bind_param($stmtConge, "i", $congeId);
        mysqli_stmt_execute($stmtConge);
        $resultConge = mysqli_stmt_get_result($stmtConge);
        $congeDetails = mysqli_fetch_assoc($resultConge);

         $content .= '<div class="row">
                        <div class="col form-group">
                            <label>Type congé :</label>
                        </div>
                        <div class="col form-group">
                            <input type="text" class="form-control" value="' . $congeDetails['type_conge'] . '" readonly>
                        </div>
                    </div>
                        <div class="row">
                        <div class="col form-group">
                            <label>Date de début :</label>
                        </div>
                        <div class="col form-group">
                            <input type="text" class="form-control" value="' . $congeDetails['DateDebut_conge'] . '" readonly>
                        </div>
                    </div>';
        $content .= '<div class="row">
                        <div class="col form-group">
                            <label>Date de fin :</label>
                        </div>
                        <div class="col form-group">
                            <input type="text" class="form-control" value="' . $congeDetails['DateFin_conge'] . '" readonly>
                        </div>
                    </div>';
        $content .= '<div class="row">
                        <div class="col form-group">
                            <label>Période :</label>
                        </div>
                        <div class="col form-group">
                            <input type="text" class="form-control" value="' . $congeDetails['periode'] . ' Jours" readonly>
                        </div>
                    </div>';
    } else if ($request['type_dmd'] === 'Bulletin de paie') {
        $bltnId = $request['id_bltn'];
        $queryBltn = "SELECT * FROM paie WHERE id_bltn = ?";
        $stmtBltn = mysqli_prepare($conn, $queryBltn);
        mysqli_stmt_bind_param($stmtBltn, "i", $bltnId);
        mysqli_stmt_execute($stmtBltn);
        $resultBltn = mysqli_stmt_get_result($stmtBltn);
        $BltnDetails = mysqli_fetch_assoc($resultBltn);

        $content .= '<div class="row">
                        <div class="col form-group">
                            <label>Mois de paie :</label>
                        </div>
                        <div class="col form-group">
                            <input type="text" class="form-control" value="' . $BltnDetails['mois'] . ' ' . $BltnDetails['annee'] . '" readonly>
                        </div>
                    </div>';

        // ... Ajoutez la logique pour afficher les détails du bulletin de paie si nécessaire ...
    }

    $content .= '<div class="row">
                    <div class="col form-group">
                        <label>Réponse reçue :</label>';
    if ($request['reponse'] != NULL) {
        $content .= '<textarea class="form-control" rows="3" readonly>' . $request['reponse'] . '</textarea>';
    } else {
        $content .= '<textarea class="form-control" rows="3" readonly>Vous n\'avez reçu aucune réponse pour le moment ! </textarea>';
    }
    $content .= '</div>
                </div>';
    $content .= '</form>';
    echo $content;
} else {
    echo "Erreur : ID de demande non fourni.";
}
?>