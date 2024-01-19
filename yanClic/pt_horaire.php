<?php
session_start();
include('server.php');
include('head.php');

// Vérifier si l'utilisateur est connecté en tant qu'employeur ou employé
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'employeur' && $_SESSION['user_type'] !== 'chef') {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// Récupérer le mois actuel et l'année actuelle
$currentYear = date('Y');

// Variables pour filtrer les données
$selectedYear = isset($_POST['year']) ? $_POST['year'] : $currentYear;
$selectedEmployee = isset($_POST['employee']) ? $_POST['employee'] : '';

// Requête SQL de base pour récupérer les données
$sql = "SELECT pointer.*, employe.nom AS employe_nom , employe.prenom AS prenom 
        FROM pointer 
        JOIN employe ON pointer.employe_id = employe.employe_id
        WHERE YEAR(pointer.date) = $selectedYear ";

// Ajouter le filtre d'employé si sélectionné
if (!empty($selectedEmployee)) {
    $selectedEmployee = mysqli_real_escape_string($conn, $selectedEmployee);
    $sql .= " AND employe.employe_id = '$selectedEmployee'";
}

// Exécuter la requête SQL
$result = mysqli_query($conn, $sql);

?>

<header class="text-center py-5">
    <div class="container">
        <!-- Contenu de l'en-tête si nécessaire -->
    </div>
</header>

<div class="container">
    <div class="menu">
        <a href="test_pointage.php">
            <button id="daily">Pointage journalier</button>
        </a>
        <a href="pt_mensuel.php">
            <button id="monthly">Pointage Mensuel</button>
        </a>
        <a href="pt_annuel.php">
            <button id="Annual">Pointage Annuel</button>
        </a>
        <a href="pt_horaire.php">
            <button id="hourly" class="active">Pointage Horaire</button>
        </a>
    </div>

    <h4 id="hourlyTitle" class="text-center mt-2 mb-4">Pointage Horaire</h4>

    <!-- Formulaire de sélection de l'année et de l'employé -->
    <form method="post" class="ml-2 mb-4 " id="hourlyForm">
        <div class="form-row align-items-center">
            <!-- Sélectionner l'employé (si l'utilisateur est un employeur) -->
            <div class="col-md-4">
                <label class="sr-only" for="employee">Employé</label>
                <select class="form-control mb-2" id="employee" name="employee" onchange="this.form.submit()">
                    <option value="">Tous les employés</option>
                    <?php
                    if ( $_SESSION['user_type'] == 'employeur') {
                        $employeeSql = "SELECT nom, employe_id FROM employe WHERE employe.id_entreprise = '$entreprise_id'";
                    } elseif ($_SESSION['user_type'] == 'chef') {
                        $employeeSql = "SELECT nom, employe_id FROM employe WHERE employe.chef_id = '$user_id'";
                    }
                    $employeeResult = mysqli_query($conn, $employeeSql);
                    while ($employeeRow = mysqli_fetch_assoc($employeeResult)) {
                        $employeeId = $employeeRow['employe_id'];
                        $employeeName = $employeeRow['nom'];
                        $selected = ($selectedEmployee == $employeeId) ? 'selected' : '';
                        echo "<option value='$employeeId' $selected>$employeeName</option>";
                    }
                    ?>
                </select>
            </div>
                 <!-- Sélectionner l'année -->
            <div class="col-md-2">
                <label class="sr-only" for="year">Année</label>
                <select class="form-control mb-2" id="year" name="year" onchange="this.form.submit()">
                    <?php
                    $currentYear = date('Y');
                    for ($year = $currentYear; $year >= 2020; $year--) {
                        $selected = ($selectedYear == $year) ? 'selected' : '';
                        echo "<option value='$year' $selected>$year</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
    </form>
    <div class="table-responsive text-center">
    <table id="hourlyTable" class=" table-bordered">
        <!-- Hourly attendance table content -->
        <thead>
            <tr class="table-primary">
                <th>Mois</th>
                <?php
                // Boucle à travers chaque jour du mois (supposons 31 jours pour l'exemple)
                for ($day = 1; $day <= 31; $day++) {
                    echo "<th>{$day}</th>";
                }
                ?>
                <th>Total heures travaillées</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Tableau pour stocker les noms des mois
            $months = array(
                1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin',
                7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
            );

            // Tableau pour stocker les totaux des heures travaillées par mois
            $monthlyTotals = array();

            // Initialiser les totaux à zéro pour chaque mois
            foreach ($months as $monthNumber => $monthName) {
                $monthlyTotals[$monthNumber] = 0;
            }

            // Boucle à travers chaque mois
            foreach ($months as $monthNumber => $monthName) {
                echo "<tr>";
                echo "<td class='table-primary'>{$monthName}</td>";

                // Boucle à travers chaque jour du mois
                for ($day = 1; $day <= 31; $day++) {
                    // Format de la date courante en format 'Y-m-d' (Année-Mois-Jour)
                    $currentDate = date('Y-m-d', mktime(0, 0, 0, $monthNumber, $day, $selectedYear));
                    $statut = 'A'; // Par défaut, absent
                    $reposSql = "SELECT * FROM repos WHERE '$currentDate' BETWEEN date_debut AND date_fin";
                    $reposResult = mysqli_query($conn, $reposSql);
                    if (mysqli_num_rows($reposResult) > 0) {
                        // La date existe dans la table "repos"
                        echo "<td class='R'>R</td>";

                    } else {
                    // Si l'utilisateur est un employé, obtenir l'enregistrement de pointage pour cet employé uniquement
                    $sql = "SELECT h_entree_chef, h_sortie_chef FROM pointer WHERE date = '$currentDate' AND employe_id = '$selectedEmployee'";
                    $result = mysqli_query($conn, $sql);
                    // Vérifier si la requête a réussi et qu'il y a des enregistrements
                        if ($result && mysqli_num_rows($result) > 0) {
                            $row = mysqli_fetch_assoc($result);
                            $heureEntree = $row['h_entree_chef'];
                            $heureSortie = $row['h_sortie_chef'];

                            // Calculer les heures travaillées pour la journée
                            $heureEntree = strtotime($heureEntree);
                            $heureSortie = strtotime($heureSortie);
                            $dureeTravaillee = round(($heureSortie - $heureEntree) / 3600, 2); // Convertir en heures
                            if ($dureeTravaillee < 0) {
                                $dureeTravaillee = 0;
                            }

                            // Ajouter les heures travaillées du jour au total du mois correspondant
                            $monthlyTotals[$monthNumber] += $dureeTravaillee;

                            // Afficher les heures travaillées pour la journée
                            echo "<td>{$dureeTravaillee}</td>";
                        } else {
                            // Afficher "N/A" si aucune heure n'est disponible pour la journée
                            echo "<td class='na-cell'>-</td>";
                        }
                }
            }
                // Afficher le total des heures travaillées pour le mois en cours
                    echo "<td>{$monthlyTotals[$monthNumber]} h</td>";
            }
        
                echo "</tr>";
          
            ?>
           
        </tbody>
    </table>
</div>
</div></div>
<?php include('footer.php'); ?>
