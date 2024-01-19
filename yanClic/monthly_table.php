<h4 id="monthlyTitle" class="text-center mb-4">Pointage Mensuel</h4>
<form method="post" class="ml-2 mb-4 "  id="monthlyForm">
  <div class="form-row align-items-center">
    <div class="col-auto">
      <label class="sr-only" for="month">Mois</label>
        <select class="form-control mb-2" id="month" name="month">
                <?php
                    $frenchMonths = array(
                        1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin',
                        7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
                    );
                    // Générer les options pour le select des mois
                    for ($m = 1; $m <= 12; $m++) {
                        $selected = "";
                        if (isset($_POST['month']) && $_POST['month'] == $m) {
                            $selected = "selected";
                        } elseif (!isset($_POST['month']) && $m == date('n')) {
                            $selected = "selected";
                        }
                        echo "<option value='$m' $selected>{$frenchMonths[$m]}</option>";
                    }
                ?>
        </select>
    </div>
    <div class="col-auto">
      <label class="sr-only" for="year">Année</label>
        <select class="form-control mb-2" id="year" name="year">
            <?php
                // Generate options for the year select box
                $current_year = date('Y');
                for ($y = $current_year; $y >= 2020; $y--) {
                    $selected = "";
                    if (isset($_POST['year']) && $_POST['year'] == $y) {
                        $selected = "selected";
                    } elseif (!isset($_POST['year']) && $y == date('Y')) {
                        $selected = "selected";
                    }
                    echo "<option value='$y' $selected>$y</option>";
                }
            ?>
        </select>
    </div>
    <div class="col-auto">
      <button type="submit" class="btn btn-primary mb-2">Filtrer</button>
    </div>
  </div>
</form>

<table id="monthlyTable" class="table table-bordered " style="width:100%">
<tr class="table-primary">
        <th style="width: 20%;">Date</th>
        <th>H.E employé </th>
        <th>H.E système </th>
        <th>H.E responsable</th>
        <th>H.S employé</th>
        <th>H.S système</th>
        <th>H.S responsable</th>
        <th>Total H.tavaillés </th>
        <th>Responsable</th>
    </tr>
  <!-- Monthly attendance table content -->
<?php
          // Get the selected month and year from the form, or use the current month and year if not set
          $selected_month = isset($_POST['month']) ? $_POST['month'] : date('n');
          $selected_year = isset($_POST['year']) ? $_POST['year'] : date('Y');
          // Check if the selected month and year are valid
          if (!checkdate($selected_month, 1, $selected_year)) {
              // If the selected month and year are not valid, use the current month and year
              $selected_month = date('n');
              $selected_year = date('Y');
          }
          // Calculate the start and end dates of the selected month
          $start_date = date('Y-m-d', strtotime("$selected_year-$selected_month-01"));
          $end_date = date('Y-m-t', strtotime("$selected_year-$selected_month-01"));
           // Check if the user is logged in as an employee or employeur
            if (isset($_SESSION['user_id'])) {
                    $employe_id = $_SESSION['user_id'];
                    $sql = "SELECT p.*, e.nom AS chef_nom FROM pointer p
                    LEFT JOIN employe e ON p.chef_id = e.employe_id
                    WHERE p.employe_id = '$employe_id' AND p.date BETWEEN '$start_date' AND '$end_date'";
                    $result_point = mysqli_query($conn, $sql);
                if ($result_point && mysqli_num_rows($result_point) > 0) {
                    foreach ($result_point as $row) {
                        $heure_arrivee = new DateTime($row['h_entree_chef']);
                        $heure_depart = $row['h_sortie_chef'] ? new DateTime($row['h_sortie_chef']) : null;
                        
                        if ($heure_depart !== null) {
                            $interval = $heure_arrivee->diff($heure_depart);
                            $heures_travaillees = $interval->format('%h h %i min');
                        } else {
                            $heures_travaillees = '--';
                        }
                        echo "<tr style='display: " . ($rowIndex > $rowsPerPage ? 'none' : 'table-row') . ";'>";
                            echo "<td>{$row['date']}</td>";
                            echo "<td>{$row['h_entree']}</td>";
                            echo "<td>{$row['h_entree_sys']}</td>";
                            echo "<td>{$row['h_entree_chef']}</td>";
                            echo "<td>{$row['h_sortie']}</td>";
                            echo "<td>{$row['h_sortie_sys']}</td>";
                            echo "<td>{$row['h_sortie_chef']}</td>";
                            echo "<td>$heures_travaillees</td>";
                            echo "<td>" . ($row['chef_nom'] ? $row['chef_nom'] : 'Non assigné') . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' class='text-center'>Aucun enregistrement de pointage disponible pour ce mois</td></tr>";
                }
                $month_name = $frenchMonths[$selected_month];
            } else {
                echo "<tr><td colspan='8' class='text-center'>Vous devez vous connecter pour accéder à cette page</td></tr>";
            }
?>
</table>
  