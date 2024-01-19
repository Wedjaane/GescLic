<h4 id="hourlyTitle" class="text-center mt-2 mb-4">Pointage Horaire</h4>

    <!-- Formulaire de sélection de l'année et de l'employé -->
    <form method="post" class="ml-2 mb-4 " id="hourlyForm">
        <div class="form-row align-items-center">
            <!-- Sélectionner l'année -->
            <div class="col-4">
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
            <!-- Sélectionner l'employé (si l'utilisateur est un employeur) -->
        </div>
    </form>

    <table id="hourlyTable" class="table table-bordered">
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

                    // Si l'utilisateur est un employé, obtenir l'enregistrement de pointage pour cet employé uniquement
                    if ($_SESSION['user_type'] === 'employee') {
                        $employe_id = $_SESSION['user_id'];
                        $sql = "SELECT h_entree_chef, h_sortie_chef FROM pointer WHERE date = '$currentDate' AND employe_id = '$employe_id'";
                    } else {
                        // Ajoutez ici votre code pour d'autres types d'utilisateurs ou scénarios
                        // Par exemple, si l'utilisateur est un administrateur, obtenez les enregistrements de pointage de tous les employés
                    }

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

                // Afficher le total des heures travaillées pour le mois en cours
                echo "<td>{$monthlyTotals[$monthNumber]} h</td>";

                echo "</tr>";
            }
            ?>
            
        </tbody>
    </table>
</div>

