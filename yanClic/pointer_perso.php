<?php
session_start();
include('server.php');
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'chef') {
    header("Location: login.php");
    exit();
}
$chef_id = $_SESSION['user_id'] ;
// Si le formulaire de recherche a été soumis

if (isset($_POST['modifier_statut'])) {
    // Get the selected employee ID and new status from the form
    $employe_id = $_POST['employe_id'];
    $date = $_POST['date'];
    $nouveau_statut = $_POST['nouveau_statut'];
    if ($_POST['nouveau_statut'] == 'Pr') {
        $employeId = $_POST['employe_id'];
        $timeOption = $_POST['time_option'];
        $date = $_POST['date'];
        $heure = $_POST['heure'];
        $heure_sys = $_POST['manual_time'];
         // Insérer l'entrée dans la table "pointer"
         $insertQuery = "INSERT INTO pointer (employe_id, chef_id, h_entree_chef, h_entree_sys, date, statut) VALUES ('$employeId', '$chef_id', '$heure', '$heure_sys', '$date', 'present')";
         $insertResult = mysqli_query($conn, $insertQuery);
         // Vérifier si l'insertion et la mise à jour ont réussi
        if ($insertResult ) {
            // Rediriger vers la même page avec un message de succès
            header("Location: ".$_SERVER['PHP_SELF']."?successMsg=".urlencode("Pointage d'entrée ajouté avec succès."));
            exit;
        } else {
            $errorMsg = "Erreur lors de l'ajout du pointage d'entrée.";
        }
    }else
    $sqly = "INSERT INTO absence (employe_id, chef_id, cause, date) VALUES ('$employe_id', '$chef_id', '$nouveau_statut', '$date')";
        // Execute the SQL query
         $resulty = mysqli_query($conn, $sqly);
        // Check if the update was successful and display a message to the user
        if ($resulty) {
            header("Location: ".$_SERVER['PHP_SELF']."?successMsg=".urlencode("Statut bien modifié ."));
            exit;
        } else {
            $errorMsg = "Erreur lors de modification du statut.";
        }
}  
// Récupérer la date sélectionnée dans la recherche
$dateExp = isset($_POST['dateExp']) ? $_POST['dateExp'] : null;

if (!is_null($dateExp)) {
    // Modifier la requête pour utiliser la date sélectionnée
    $valider = mysqli_query($conn, "SELECT e.*, p.point_id, p.h_entree_chef, p.statut
        FROM employe e
        LEFT JOIN pointer p ON e.employe_id = p.employe_id AND DATE(p.date) = '$dateExp'
        LEFT JOIN absence a ON e.employe_id = a.employe_id AND DATE(a.date) = '$dateExp'
        WHERE p.date IS NULL AND e.chef_id = '$chef_id' AND a.employe_id IS NULL");
} else {
    $valider = mysqli_query($conn, "SELECT e.*, p.point_id, p.h_entree_chef, p.statut
        FROM employe e
        LEFT JOIN pointer p ON e.employe_id = p.employe_id AND p.date = CURDATE()
        LEFT JOIN absence a ON e.employe_id = a.employe_id AND a.date = CURDATE()
        WHERE p.date IS NULL AND e.chef_id = '$chef_id' AND a.employe_id IS NULL");
}
include('head.php');
?>

<header class="text-center py-5 mt-5">
    <div class="container">
   
        <br><h2>Pointer l'entrée aux employés ! </h2>
        
    </div>
</header>

<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="heure">Heure actuelle :</label>
                <input type="text" class="form-control" value="<?php echo date('H:i:s'); ?>" disabled>
            </div>
        </div>
        <div class ="col"></div>
        <div class="col-md-6">
            <form method="post" class="ml-2 mb-4 mt-4">
                <div class="form-row align-items-center">
                    <div class="col-8 mt-2">
                        <input type="date" id="dateExp" name="dateExp" class="form-control" value="<?php echo isset($_POST['dateExp']) ? $_POST['dateExp'] : date('Y-m-d'); ?>" >
                    </div>
                    <div class="col mt-2">
                        <input type="submit" value="Filtrer" class="btn btn-primary">
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php if (isset($_GET['successMsg'])) {
    $successMsg = $_GET['successMsg'];
    echo '<div class="alert alert-success">'.htmlentities($successMsg).'</div>';
} elseif (isset($errorMsg)) {
    echo '<div class="alert alert-danger">'.$errorMsg.'</div>';
} ?>
    <table id="table_dmd" class="table table-bordered" style="width:100% ; ">
        <!-- En-têtes du tableau -->
        <thead class="table-primary">
            <tr>
                <th>CIN</th>
                <th>Nom complet</th>
                <th>Statut</th>
                <th>Heure actuelle :</th>
                <th>Pointer</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($valider)) { ?>
                <tr>
                    <td><?php echo $row['cin']; ?></td>
                    <td><?php echo $row['Nom'] . ' ' . $row['prenom']; ?></td>
                    <td><?php echo isset($row['statut']) ? $row['statut'] : 'Non défini'; ?></td>                    
                    <td>
                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <input type="hidden" name="employe_id" value="<?php echo $row['employe_id']; ?>">
                        <input type="hidden" name="point_id" value="<?php echo $row['point_id']; ?>">
                        <input type="hidden" name="date" class="form-control" value="<?php echo isset($_POST['dateExp']) ? $_POST['dateExp'] : date('Y-m-d'); ?>"> 
                        <div class="row">
                            <div class="col">
                                <input type="time" class="form-control "  name="heure" value="<?php echo date('H:i:s'); ?>">
                                <input type="time"  name="manual_time" style="display:none;" value="<?php echo date('H:i:s'); ?>">
                            </div>
                        </div>
                    </td>
                    <td>
                            <div class="row">
                                <div class="col">
                            <select class="form-control" name="nouveau_statut">
                                <option value="Pr" Selected>Présent</option>
                                <option value="Ab">Absent</option>
                                <option value="Maladie">Malade</option>
                                <option value="Congé">Congé</option>
                                <option value="Mission">Mission</option>
                                <option value="Formation">Formation</option>
                                <option value="Télétravail">Télétravail</option>
                                <option value="Déplacement">Déplacement professionnel</option>

                            </select>
                            </div>
                            <div class="col">
                            <button type="submit" name="modifier_statut" class="btn btn-success">Valider</button>
                            </div>  </div>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <div class="col mt-4">
             <a href="pointer_persoS.php" class="btn btn-warning "> Cliquer ici pour pointer la sortie des personnelles </a>
        </div>
</div>

<?php include('footer.php'); ?>
