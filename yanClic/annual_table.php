<?php
// Vérifier si le formulaire a été soumis
if (isset($_POST['year'])) {
    // Récupérer l'année sélectionnée
    $currentYear = $_POST['year'];
    // Requête SQL pour récupérer les données de pointage pour l'année sélectionnée
    $sql = "SELECT date, statut FROM pointer WHERE YEAR(date) = $currentYear";
    $result = mysqli_query($conn, $sql);
} else {
    // Requête SQL pour récupérer les données de pointage pour l'année en cours
    $currentYear = date('Y');
    $sql = "SELECT date, statut FROM pointer WHERE YEAR(date) = $currentYear";
    $result = mysqli_query($conn, $sql);
}
?>
<h4 id="annualTitle" class="text-center hidden mb-4 ">Pointage Annuel</h4>
<!-- Formulaire de sélection de l'année -->
<form method="post" class="mb-4" id="annualForm" >
    <div class="form-row align-items">
        <div class="col-4 ">
            <div class="form-group">
                <label for="yearSelect">Sélectionnez une année:</label>
                <select class="form-control" id="yearSelect" name="year" onchange="this.form.submit()">
                    <?php
                    // Générer les options pour le select
                    for ($i = date('Y'); $i >= 2020; $i--) {
                        $selected = ($i == $currentYear) ? 'selected' : '';
                        echo "<option value='$i' $selected>$i</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>
</form>

<!-- Tableau de pointage annuel -->
<div class="table-responsive">
<table id="annualTable" class="hidden ">
    <tr>
        <th class="table-primary">Mois</th>
        <?php
        // Générer les en-têtes de colonne pour chaque jour du mois
        for ($day = 1; $day <= 31; $day++) {
            echo "<th class='table-primary'>$day</th>";
        }
        ?>
    </tr>
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
        echo "<tr >";
        echo "<td class='table-primary'>$monthName</td>";
        // Boucle à travers chaque jour du mois
        for ($day = 1; $day <= $daysInMonth; $day++) {
            // Format de la date courante en format 'Y-m-d' (Année-Mois-Jour)
            $currentDate = date('Y-m-d', mktime(0, 0, 0, $monthNumber, $day, $currentYear));
            // Effectuer une requête SQL pour obtenir le statut de présence pour chaque jour
            $sql = "SELECT statut FROM pointer WHERE date = '$currentDate'";
            $result = mysqli_query($conn, $sql);
            // Vérifier si la requête a réussi
            if ($result) {
                $row = mysqli_fetch_assoc($result);
                $statut = $row['statut'];

                // Définir une valeur par défaut si le statut n'est pas trouvé dans la base de données
                if (empty($statut)) {
                    $statut = "Ab"; // Absent par défaut
                }
            } else {
                // Définir une valeur par défaut si la requête échoue
                $statut = "-"; // Absent par défaut
            }
            // Afficher le statut dans la cellule du tableau
            // Ajouter un attribut de classe à la cellule du tableau en fonction du statut
            echo "<td class='$statut'>{$statut}</td>";
        }
        echo "</tr>";
    }
    ?>
</table>


