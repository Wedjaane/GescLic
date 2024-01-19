<?php
session_start();
include('server.php');
// Check if the user is logged in as an employee
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'chef') {
    // If not logged in as an employee, redirect to the login page
    header("Location:login.php");
    exit();
}
$chef_id = $_SESSION['user_id'];
$annee_actuelle = date("Y");

function date_fr($format, $timestamp = 'now') {
    $months = array(
        'January' => 'Janvier', 'February' => 'Février', 'March' => 'Mars', 'April' => 'Avril',
        'May' => 'Mai', 'June' => 'Juin', 'July' => 'Juillet', 'August' => 'Août',
        'September' => 'Septembre', 'October' => 'Octobre', 'November' => 'Novembre', 'December' => 'Décembre'
    );
    $days = array(
        'Monday' => 'Lundi', 'Tuesday' => 'Mardi', 'Wednesday' => 'Mercredi', 'Thursday' => 'Jeudi',
        'Friday' => 'Vendredi', 'Saturday' => 'Samedi', 'Sunday' => 'Dimanche'
    );

    $translated_date = strtr(date($format, strtotime($timestamp)), $months);
    $translated_date = strtr($translated_date, $days);

    return $translated_date;
}
function calculerTauxAbsenceActuelle($conn, $chef_id, $typePeriode) {
    // Requête SQL pour obtenir le taux d'absence par mois pour l'année actuelle
$resultTotalEmp = $conn->query("SELECT COUNT(*) AS total_employe FROM employe WHERE chef_id = '$chef_id'");
// Vérification des erreurs de la requête
if (!$resultTotalEmp) {
    die("Erreur lors de l'exécution de la requête : " . $conn->error);
}

// Récupération du total d'enregistrements
$rowTotalemp = $resultTotalEmp->fetch_assoc();
$employes = $rowTotalemp["total_employe"];
    $sql_ab = "";

    if ($typePeriode === 'jour') {
        // Calculer le taux d'absence pour la journée actuelle
        $dateActuelle = date('Y-m-d');
        $sql_ab = "SELECT COUNT(a.id) AS nombre_absences, COUNT(DISTINCT e.employe_id) AS nombre_employes
                FROM employe e
                LEFT JOIN absence a ON e.employe_id = a.employe_id
                WHERE DATE_FORMAT(a.date, '%Y-%m-%d') = '$dateActuelle' AND e.chef_id = '$chef_id'";
    } elseif ($typePeriode === 'mois') {
        // Calculer le taux d'absence pour le mois actuel
        $anneeActuelle = date('Y');
        $moisActuel = date('m');
        $sql_ab = "SELECT COUNT(a.id) AS nombre_absences, COUNT(DISTINCT e.employe_id) AS nombre_employes
                FROM employe e
                LEFT JOIN absence a ON e.employe_id = a.employe_id
                WHERE YEAR(a.date) = '$anneeActuelle' AND MONTH(a.date) = '$moisActuel' AND e.chef_id = '$chef_id'";
    } else {
        return 0; // Type de période non pris en charge
    }
    
    $result_ab = $conn->query($sql_ab);

    $tauxAbsence_emp = 0;
    $nombreAbsences_emp = 0;
    $nombreEmployes = 0;

    if ($result_ab->num_rows > 0) {
        $row = $result_ab->fetch_assoc();
        $nombreAbsences_emp = $row['nombre_absences'];
        $nombreEmployes = $row['nombre_employes'];

        if ($nombreEmployes > 0) {
            $tauxAbsence_emp = ($nombreEmployes / $employes) * 100;
        }
    }

    return $tauxAbsence_emp;
}
// Requête SQL pour récupérer les données de la première source
$result5 = $conn->query("SELECT a.cause, COUNT(*) AS nombre_absences
                        FROM absence a
                        INNER JOIN employe e ON a.employe_id = e.employe_id
                        WHERE e.chef_id = '$chef_id'
                        GROUP BY a.cause");

// Création des tableaux de données pour le graphique
$labels_a = array();
$data_a = array();

if ($result5->num_rows > 0) {
    while ($row = $result5->fetch_assoc()) {
        $cause = $row["cause"];
        $nombre_absences = $row["nombre_absences"];
        
        $labels_a[] = $cause;
        $data_a[] = $nombre_absences;
        // Exécuter la requête $result5 uniquement si $cause est égal à "Congé"
    }
}

// Requête SQL pour obtenir le total d'enregistrements depuis la table "absence"
$resultTotalEnregistrements = $conn->query("SELECT COUNT(*) AS total_enregistrements FROM absence a  INNER JOIN employe e ON a.employe_id = e.employe_id
WHERE e.chef_id = '$chef_id'
GROUP BY a.cause");
$rowTotalEnregistrements = $resultTotalEnregistrements->fetch_assoc();
$total_employe = $rowTotalEnregistrements["total_enregistrements"];
// Requête SQL pour obtenir le nombre d'absences uniques par mois acuel
$query_eff = "SELECT
    DATE_FORMAT(absence.date, '%Y-%m') AS mois,
    COUNT(DISTINCT absence.employe_id) AS absences
FROM
    absence
LEFT JOIN
    employe ON employe.employe_id = absence.employe_id 
WHERE
    YEAR(absence.date) = '$annee_actuelle' AND employe.chef_id = '$chef_id'
GROUP BY
    mois";

// Exécution de la requête pour obtenir le nombre d'absences par mois
$result_eff = $conn->query($query_eff);

$mois = array();
$taux_mensuel = array();
$mois_fr = array(
    'January' => 'Janvier',
    'February' => 'Février',
    'March' => 'Mars',
    'April' => 'Avril',
    'May' => 'Mai',
    'June' => 'Juin',
    'July' => 'Juillet',
    'August' => 'Août',
    'September' => 'Septembre',
    'October' => 'Octobre',
    'November' => 'Novembre',
    'December' => 'Décembre'
);

if ($result_eff->num_rows > 0) {
    while ($row = $result_eff->fetch_assoc()) {
        $date_mois = $mois_fr[date('F', strtotime($row["mois"] . "-01"))] . ' ' . date('Y', strtotime($row["mois"] . "-01"));
        
        $mois[] = $date_mois;
        // Vérifiez si le dénominateur (total_employes) est différent de zéro pour éviter une division par zéro
        if ($total_employe != 0) {
            $taux = ($row["absences"] / $total_employe) * 100;
        } else {
            $taux = 0;
        }

        $taux_mensuel[] = $taux;
    }
}


include('head.php');
?>

<header class="text-center py-4 " style = "color: #000; text-align: center;">
    <div class="container ">
    </div>
</header>

<section >
    <div class="container mb-4 ">
        <div class="row mb-4  " style="margin-top : 80px">
            <div class="col-md-3">
                <div class="chart-card text-center" style="height : 88% ">
                  <?php $total = $conn->query("SELECT DISTINCT COUNT(employe_id) As total from employe where chef_id = '$chef_id'");
                   if ($total && mysqli_num_rows($total) > 0) {
                        $row = mysqli_fetch_assoc($total);
                        $total = $row['total'];
                    ?>
                  <h5>Nombre des effectifs <br> <br><span class="total-number"><?php  echo $total ; ?> </span> </h5>
                    <?php  } else {
                    // Gérer le cas où l'id_entreprise n'est pas trouvé
                    echo '<h5>Aucun employé </h5>' ;
                            }?>
                </div>
            </div>
                <?php   $tauxAbsenceJourActuel = calculerTauxAbsenceActuelle($conn, $chef_id, 'jour');
                        $tauxAbsenceMoisActuel = calculerTauxAbsenceActuelle($conn, $chef_id, 'mois');
                ?>
                <div class="col"> </div>
            <div class="col-md-4">
                <div class="chart-card text-center">
                <h5>Absentéisme par mois </h5> 
                <span class="total-number "><?php echo number_format($tauxAbsenceMoisActuel, 2); ?> %</span>
                <p style="color: #555555;"><?php echo date_fr('F Y'); ?></p>
                </div>
            </div>
            <div class="col"> </div>
            <div class="col-md-4">
                <div class="chart-card text-center">
                <h5>Taux d'Absentéisme   </h5> 
                <span class="total-number "><?php echo number_format($tauxAbsenceJourActuel, 2); ?> %</span>
                <p style="color: #555555;"><?php echo date_fr('l j F Y'); ?></p>
                </div>
            </div>
        </div>
        <div class="row " style="margin-top : 80px">
            <div class="col-md-4">
                <div class="chart-card ">
                    <h6>Répartition par les causes d'absence  </h6>
                    <canvas id="absence" class="mb-4 mt-2"></canvas>
                </div>
            </div>
            <div class="col-md-8">
                <div class="chart-card">
                <h6>Taux d'absence durant l'année actuelle : </h6>
                    <canvas id="graphiqueTauxAbsence"></canvas>
                </div>
            </div>
        </div>
       

    </div>
</section>

<?php include('footer.php') ?>

<script>
     

   
    var absence = document.getElementById('absence').getContext('2d');
    var chart_a = new Chart(absence, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($labels_a); ?>,
            datasets: [{
                label: 'Types absences  ',
                data: <?php echo json_encode($data_a); ?>,
                backgroundColor: [
                    'rgb(55, 58, 109)',
                    'rgb(255, 130, 70)',
                    'rgb(234, 84, 85)',
                    'rgb(190, 238, 247)',
                    'rgb(111, 194, 208)',
                    'rgb(235, 228, 209)',
                    'rgb(180, 180, 179)',
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    // ... Ajoutez des couleurs supplémentaires si nécessaire
                ]
            }]
        }
    });
   
        var ctx = document.getElementById('graphiqueTauxAbsence').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($mois); ?>,
                datasets: [{
                    label: '',
                    data: <?php echo json_encode($taux_mensuel);?>,
                    backgroundColor: 'rgb(0, 43, 91)'
                }]
            },
            options: {
                plugins: {
                    title: {
                    display: false,
                    
                },
                legend: {
                    display: false, // Afficher la légende
                
                }},
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100, // Définissez la plage maximale du graphique en pourcentage
                        ticks: {
                    callback: function (value) {
                        return value + ' %'; // Ajoutez le symbole '%' ici
                    }
                }
                    }
                }
            }
        });
       
</script>