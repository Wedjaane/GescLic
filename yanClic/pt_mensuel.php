<?php
session_start();
include('server.php');
include('head.php');

// Vérifier si l'utilisateur est connecté en tant qu'employé
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'employeur' && $_SESSION['user_type'] !== 'chef') {
  header("Location: login.php");
    exit();
} 
$user_id = $_SESSION['user_id'];

?>

<header class="text-center py-5 ">
    <div class="container">
        <!-- Contenu de l'en-tête si nécessaire -->
    </div>
</header>

<div class="container">
    <div class="menu">
        <a href="test_pointage.php" >
            <button id="monthly" >Pointage journalier</button>
        </a>
        <a href="pt_mensuel.php" >
            <button id="monthly" class="active">Pointage Mensuel</button>
        </a>
        <a href="pt_annuel.php">
            <button id="Annual" >Pointage Annuel</button>
        </a>
        <a href="pt_horaire.php">
            <button id="hourly">Pointage Horaire</button>
        </a>
    </div>

    <?php
    // Vérifier si le formulaire a été soumis
    if (isset($_POST['year']) && isset($_POST['month'])) {
        // Récupérer l'année et le mois sélectionnés
        $currentYear = $_POST['year'];
        $currentMonth = $_POST['month'];

        // Requête SQL pour récupérer les données de pointage pour l'année et le mois sélectionnés
        $sql = "SELECT date, employe_id, Statut FROM pointer WHERE YEAR(date) = $currentYear AND MONTH(date) = $currentMonth";
        $result = mysqli_query($conn, $sql);
    } else {
        // Requête SQL pour récupérer les données de pointage pour l'année et le mois en cours
        $currentYear = date('Y');
        $currentMonth = date('m');
        $sql = "SELECT date, employe_id, Statut FROM pointer WHERE YEAR(date) = $currentYear AND MONTH(date) = $currentMonth";
        $result = mysqli_query($conn, $sql);
    }
    ?>
    <h4  class="text-center  mb-4 mt-4">Pointage Mensuel</h4>
    
    <!-- Formulaire de sélection de l'année et du mois -->
<form method="post" class="mb-4">
  <div class="form-row align-items">
    <div class="col-4">
      <div class="form-group">
        <label for="monthSelect">Sélectionnez un mois:</label>
        <select class="form-control" id="monthSelect" name="month" onchange="this.form.submit()">
          <?php
            $frenchMonths = array(
              1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin',
              7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
            );
            foreach ($frenchMonths as $monthNumber => $monthName) {
              $selected = ($monthNumber == $_POST['month']) ? 'selected' : '';
              echo "<option value='$monthNumber' $selected>$monthName</option>";
            }
          ?>
        </select>
      </div>
    </div>
    <div class="col-4">
      <div class="form-group">
        <label for="yearSelect">Sélectionnez une année:</label>
        <select class="form-control" id="yearSelect" name="year" onchange="this.form.submit()">
          <?php
            // Générer les options pour le select
            for ($i = date('Y'); $i >= 2020; $i--) {
              $selected = ($i == $_POST['year']) ? 'selected' : '';
              echo "<option value='$i' $selected>$i</option>";
            }
          ?>
        </select>
      </div>
    </div>
  </div>
</form>

    <!-- Tableau de pointage annuel -->
<div class="table-responsive text-center">

    <table id="annualTable" >
    <tr class="table-primary">
        <th class="table-primary">Employé</th>
            <?php
            // Générer les en-têtes de colonne pour chaque jour du mois
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $currentYear);
            for ($day = 1; $day <= $daysInMonth; $day++) {
                echo "<th>$day</th>";
            }
            ?>
        </tr>
        <?php
        // Récupérer la liste des employés
        if ( $_SESSION['user_type'] == 'employeur') {
          $sql = "SELECT * FROM employe WHERE id_entreprise = '$entreprise_id'";
        } elseif ($_SESSION['user_type'] == 'chef') { 
          $sql = "SELECT * FROM employe WHERE chef_id = '$user_id'";

        }     
        $result = mysqli_query($conn, $sql);
        $employees = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $employees[$row['employe_id']] = $row['Nom'];
        }

        // Boucle à travers chaque employé
        foreach ($employees as $id => $name) {
            echo "<tr>";
            echo "<td class='table-primary'>$name</td>";

            // Boucle à travers chaque jour du mois
            for ($day = 1; $day <= $daysInMonth; $day++) {
                // Format de la date courante en format 'Y-m-d' (Année-Mois-Jour)
                $currentDate = date('Y-m-d', mktime(0, 0, 0, $currentMonth, $day, $currentYear));

                $reposSql = "SELECT * FROM repos WHERE '$currentDate' BETWEEN date_debut AND date_fin";
                $reposResult = mysqli_query($conn, $reposSql);
                
                if (mysqli_num_rows($reposResult) > 0) {
                    // La date existe dans la table "repos"
                    $statut = 'R'; // Repos (colorer la colonne)
                }else {
                // Effectuer une requête SQL pour obtenir le statut de présence pour chaque jour
                    $attendanceSql = "SELECT Statut FROM pointer WHERE employe_id = '$id' AND date = '$currentDate'";
                    $attendanceResult = mysqli_query($conn, $attendanceSql);
                    // Vérifier si la requête a réussi
                    if ($attendanceResult) {
                        $row = mysqli_fetch_assoc($attendanceResult);
                        $statut = $row['Statut'];
                        if ( $statut == 'present'){
                          $statut = 'Pr';
                        }  // Afficher le statut dans la cellule du tableau
                        if (empty($statut)) {
                            $statut = 'Ab';       // Définir une valeur par défaut si le statut n'est pas trouvé dans la base de données  
                            $absenceSql = "SELECT cause FROM absence WHERE employe_id = '$id' AND date = '$currentDate'";
                            $absenceResult = mysqli_query($conn, $absenceSql);
                            if (mysqli_num_rows($absenceResult) > 0) {
                                // La date existe dans la table "absence"
                                $absenceRow = mysqli_fetch_assoc($absenceResult);
                                $statut = $absenceRow['cause']; 
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

                // Afficher le Statut dans la cellule du tableau
                // Ajouter un attribut de classe à la cellule du tableau en fonction du Statut
                echo "<td class='$statut'>$statut</td>";
            }
            echo "</tr>";
        }
        ?>
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