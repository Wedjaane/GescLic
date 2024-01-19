<?php
session_start();
include('server.php');

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'chef') {
    header("Location: login.php");
    exit();
}
$chef_id = $_SESSION['user_id'] ;
// Si le formulaire de recherche a été soumis

if (isset($_POST['pointer_sortie'])) {
    $employeId = $_POST['employe_id'];
    $timeOption = $_POST['time_option'];
    $heure = $_POST['heure'];
    $date = $_POST['date'];
    $point_id = $_POST['point_id'];
    $heure_sys = $_POST['manual_time'];

    // Mettre à jour le pointage dans la table "pointer"
    $update_query = "UPDATE pointer SET h_sortie_chef = '$heure', chef_id= '$chef_id', h_sortie_sys = '$heure_sys'   WHERE point_id = '$point_id' AND employe_id = '$employeId'";
    $update_queryResult = mysqli_query($conn, $update_query);

    // Vérifier si la mise à jour a réussi
    if ($update_queryResult) {
        // Rediriger vers la même page avec un message de succès
        header("Location: " . $_SERVER['PHP_SELF'] . "?successMsg=" . urlencode("Pointage de sortie ajouté avec succès."));
        exit;
    } else {
        $errorMsg = "Erreur lors de l'ajout du pointage de sortie.";
    }
}
    
// Récupérer la date sélectionnée dans la recherche
$dateExp = isset($_POST['dateExp']) ? $_POST['dateExp'] : null;

if (!is_null($dateExp)) {
    // Modifier la requête pour utiliser la date sélectionnée
    $valider = mysqli_query($conn, "SELECT e.*, p.point_id, p.h_sortie_chef , p.statut
    FROM employe e
    LEFT JOIN pointer p ON e.employe_id = p.employe_id AND p.date = '$dateExp'
    WHERE  e.chef_id = '$chef_id' AND (p.h_sortie_chef IS NULL OR p.h_sortie_chef = '00:00:00')
    AND p.h_entree_chef IS NOT NULL ");
   
} else {
    $valider = mysqli_query($conn, "SELECT e.*, p.point_id, p.h_sortie_chef , p.statut
    FROM employe e
    LEFT JOIN pointer p ON e.employe_id = p.employe_id AND p.date = CURDATE()
    WHERE  e.chef_id = '$chef_id' AND (p.h_sortie_chef IS NULL OR p.h_sortie_chef = '00:00:00')
    AND p.h_entree_chef IS NOT NULL ");
   
}
include('head.php');
?>

<header class="text-center py-5 mt-5">
    <div class="container">
   
        <br><h2>Pointer la sortie aux employés ! </h2>
        
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
                <th>Pointer</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($valider)) { ?>
                <tr>
                    <td><?php echo $row['cin']; ?></td>
                    <td><?php echo $row['Nom'] . ' ' . $row['prenom']; ?></td>
                    <td><?php echo $row['statut']; ?></td>
                    <td>
                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <input type="hidden" name="employe_id" value="<?php echo $row['employe_id']; ?>">
                        <input type="hidden" name="point_id" value="<?php echo $row['point_id']; ?>">
                        <input type="hidden" name="date" class="form-control" value="<?php echo isset($_POST['dateExp']) ? $_POST['dateExp'] : date('Y-m-d'); ?>"> 
                        <input type="hidden" name="time_option" value="manual" checked>
                        <div class="row">
                            <div class="col">
                                <input type="time" name="heure"  class="form-control " value="<?php echo date('H:i:s'); ?>">
                                <input type="time" name="manual_time" style="display:none;" value="<?php echo date('H:i:s'); ?>">
                            </div>
                            <div class="col">
                                <button type="submit" name="pointer_sortie" class="btn btn-success ">Pointer sortie</button>
                            </div>
                        </div>
                    </form>
                    </td>
                    
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <div class="col mt-4">
             <a href="pointer_perso.php" class="btn btn-warning "> Cliquer ici pour pointer l'entrée des personnelles </a>
        </div>
</div>

<?php include('footer.php'); ?>

