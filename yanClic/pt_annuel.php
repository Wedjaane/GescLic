<?php
session_start();
include('server.php');
include('head.php');

// Vérifier si l'utilisateur est connecté en tant qu'employeur
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
$sql = "SELECT pointer.*, employe.nom AS employe_nom, employe.prenom AS prenom 
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
                <button id="monthly">Pointage journalier</button>
            </a>
            <a href="pt_mensuel.php">
                <button id="monthly">Pointage Mensuel</button>
            </a>
            <a href="pt_annuel.php">
                <button id="Annual" class="active">Pointage Annuel</button>
            </a>
            <a href="pt_horaire.php">
                <button id="hourly">Pointage Horaire</button>
            </a>
        </div>

        <h4 id="hourlyTitle" class="text-center mt-2 mb-4">Pointage Annuel</h4>

        <!-- Formulaire de sélection de l'année et de l'employé -->
        <form method="post" class="ml-2 mb-4" id="monthlyForm">
            <div class="form-row align-items-center">
                <!-- Sélectionner l'employé -->
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
            <!-- Tableau de pointage annuel -->
            <table id="annualTable">
                <thead>
                    <tr class="table-primary">
                        <th class="table-primary">Mois</th>
                        <?php
                        // Générer les en-têtes de colonne pour chaque jour du mois
                        for ($day = 1; $day <= 31; $day++) {
                            echo "<th>$day</th>";
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Tableau pour stocker les noms des mois
                    $months = array(
                        1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin',
                        7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
                    );

                    // Boucle à travers chaque mois de l'année
                    for ($monthNumber = 1; $monthNumber <= 12; $monthNumber++) {
                        $monthName = $months[$monthNumber];
                        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $monthNumber, $currentYear); // Nombre de jours dans le mois en cours
                        echo "<tr>";
                        echo "<td class='table-primary'>$monthName</td>";
                        // Boucle à travers chaque jour du mois
                        for ($day = 1; $day <= $daysInMonth; $day++) {
                            // Format de la date courante en format 'Y-m-d' (Année-Mois-Jour)
                            $currentDate = date('Y-m-d', mktime(0, 0, 0, $monthNumber, $day, $selectedYear));
                            $statut = 'Ab'; // Par défaut, absent
                            // Vérifier si la date correspond à une colonne vide
                            if (($day > $daysInMonth) || ($monthNumber == 2 && $day > 28 && !date('L', mktime(0, 0, 0, 1, 1, $currentYear)))) {
                                $statut = 'empty'; // Colonne vide (colorer la colonne)
                            } else {
                                $reposSql = "SELECT * FROM repos WHERE '$currentDate' BETWEEN date_debut AND date_fin";
                                $reposResult = mysqli_query($conn, $reposSql);
                                if (mysqli_num_rows($reposResult) > 0) {
                                    // La date existe dans la table "repos"
                                    $statut = 'R'; // Repos (colorer la colonne)
                                } else {
                                    // Effectuer une requête SQL pour obtenir le statut de présence pour chaque jour
                                    $attendanceSql = "SELECT Statut FROM pointer WHERE employe_id = '$selectedEmployee' AND date = '$currentDate'";
                                    $attendanceResult = mysqli_query($conn, $attendanceSql);
                                    // Vérifier si la requête a réussi
                                    if ($attendanceResult) {
                                        $row = mysqli_fetch_assoc($attendanceResult);
                                        $statut = $row['Statut'];
                                        if ( $statut == 'present'){
                                            $statut = 'Pr';
                                          } // Afficher le statut dans la cellule du tableau
                                        if (empty($statut)) {
                                            $statut = 'Ab'; // Définir une valeur par défaut si le statut n'est pas trouvé dans la base de données
                                            $absenceSql = "SELECT cause FROM absence WHERE employe_id = '$selectedEmployee' AND date = '$currentDate'";
                                            $absenceResult = mysqli_query($conn, $absenceSql);
                                            if (mysqli_num_rows($absenceResult) > 0) {
                                                // La date existe dans la table "absence"
                                                $absenceRow = mysqli_fetch_assoc($absenceResult);
                                                $statut = $absenceRow['cause']; // 
                                                if ( $statut == 'Maladie'){
                                                    $statut = 'Ma';
                                                  }else if ($statut == 'Congé') {
                                                    $statut = 'Co';
                                                  }else if ($statut == 'Mission') {
                                                    $statut = 'Mi';
                                                  }else if ($statut == 'Formation') {
                                                    $statut = 'Fo';
                                                  }else if ($statut == 'Télétravail') {
                                                    $statut = 'Té';
                                                  }else if ($statut == 'Déplacement') {
                                                    $statut = 'Dé';
                                                  }
                                            }
                                        }
                                    } else {
                                        // Définir une valeur par défaut si la requête échoue
                                        $statut = "--"; // Absent par défaut
                                    }
                                }
                            }
                            // Ajouter un attribut de classe à la cellule du tableau en fonction du statut
                            echo "<td class='$statut'>$statut</td>";
                        }
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="mt-4" style="columns: 3;">
            <ul>
                <li><strong>Ab :</strong> Absent</li>
                <li><strong>Co :</strong> Congé</li>
                <li><strong>Dé :</strong> Déplacement professionnel</li>
                <li><strong>Fo :</strong> Formation</li>
                <li><strong>Ma :</strong> Maladie</li>
                <li><strong>Mi :</strong> Mission</li>
                <li><strong>Pr :</strong> Présent</li>
                <li><strong>R :</strong> Jours fériés</li>
                <li><strong>Té :</strong> Télétravail</li>
            </ul>
        </div>
    </div>
    <?php include('footer.php'); ?>

