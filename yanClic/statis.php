<?php
include('server.php');

// Requête SQL pour récupérer les données du graphique 1
$result1 = $conn->query("SELECT date_naissance FROM employe WHERE id_entreprise = '$entreprise_id'");
$ages = array();
if ($result1->num_rows > 0) {
    while ($row = $result1->fetch_assoc()) {
        $dateNaissance = $row['date_naissance'];
        // Skip if date_naissance is NULL
        if ($dateNaissance === null) {
            continue;
        }
        $age = date_diff(date_create($dateNaissance), date_create('today'))->y;
        $ages[] = $age;
    }
}
// Calculer les intervalles d'âge
$ageIntervals = array();
for ($i = 0; $i <= 40; $i += 20) {
    $ageIntervals["$i - " . ($i + 19)." ans"] = 0;
}
// Compter les âges dans les intervalles
foreach ($ages as $age) {
    foreach ($ageIntervals as $interval => $count) {
        list($minAge, $maxAge) = explode(' - ', $interval);
        if ($age >= $minAge && $age <= $maxAge) {
            $ageIntervals[$interval]++;
            break;
        }
    }
}
// Préparation des données pour le graphique
$labels_age = array_keys($ageIntervals);
$data_age = array_values($ageIntervals);


// Requête SQL pour récupérer les données du graphique 2
$result2 = $conn->query("SELECT fonction FROM employe_entreprise WHERE id_entreprise = '$entreprise_id' AND archive='0'");
$poste = array();

if ($result2->num_rows > 0) {
    while ($row = $result2->fetch_assoc()) {
        $poste[] = $row['fonction'];
    }
}
$rep_poste = array_count_values($poste);
$labels_poste = array_keys($rep_poste);
$data_poste = array_values($rep_poste);
// Requête SQL pour récupérer les données du graphique 2
$result_dprt = $conn->query("SELECT departement FROM employe_entreprise WHERE id_entreprise = '$entreprise_id' AND archive='0'");
$depart = array();

if ($result_dprt->num_rows > 0) {
    while ($row = $result_dprt->fetch_assoc()) {
        $depart[] = $row['departement'];
    }
}
$rep_depart = array_count_values($depart);
$labels_depart = array_keys($rep_depart);
$data_depart = array_values($rep_depart);


$result3 = $conn->query("SELECT sexe  FROM employe WHERE id_entreprise = '$entreprise_id'");
$sexe = array();

if ($result3->num_rows > 0) {
    while ($row = $result3->fetch_assoc()) {
        $sexe[] = $row['sexe'];
    }
}

$sex = array_count_values($sexe);
$labels_sexe = array_keys($sex);
$data_sexe = array_values($sex);



// Requête SQL pour récupérer les données de la première source
$result5 = $conn->query("SELECT a.cause, COUNT(*) AS nombre_absences
                        FROM absence a
                        INNER JOIN employe e ON a.employe_id = e.employe_id
                        WHERE e.id_entreprise = '$entreprise_id'
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



$annee_actuelle = date("Y");

// Requête SQL pour obtenir le total d'enregistrements depuis la table "absence"
$resultTotalEnregistrements = $conn->query("SELECT COUNT(*) AS total_enregistrements FROM absence");
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
    YEAR(absence.date) = '$annee_actuelle' AND id_entreprise = '$entreprise_id'
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


// Requête SQL pour récupérer les données
$result_anc = $conn->query("SELECT ee.DateEmbauche, e.sexe FROM employe_entreprise ee
                        JOIN employe e ON ee.employe_id = e.employe_id WHERE e.id_entreprise = '$entreprise_id' AND ee.archive = '0'");

// Tableau associatif pour stocker la répartition des genres par ancienneté
$genreParAnciennete = array(
    "Moins de 1 an" => array("Masculin" => 0, "Féminin" => 0),
    "1-5 ans" => array("Masculin" => 0, "Féminin" => 0),
    "5-10 ans" => array("Masculin" => 0, "Féminin" => 0),
    "10-15 ans" => array("Masculin" => 0, "Féminin" => 0),
    "15-20 ans" => array("Masculin" => 0, "Féminin" => 0),
    "Plus de 20 ans" => array("Masculin" => 0, "Féminin" => 0),

);

// Parcourez les données pour compter la répartition des genres par ancienneté
foreach ($result_anc as $row) {
    $dateEmbauche = $row['DateEmbauche'];
    $genre = $row['sexe'];

    // Calculez l'ancienneté (par exemple, en années)
    $anciennete = floor((strtotime('now') - strtotime($dateEmbauche)) / (365 * 24 * 60 * 60));

    // Déterminez la tranche d'ancienneté
    if ($anciennete < 1) {
        $tranche = "Moins de 1 an";
    } elseif ($anciennete >= 1 && $anciennete <= 5) {
        $tranche = "1-5 ans";
    } elseif ($anciennete > 5 && $anciennete <= 10) {
        $tranche = "5-10 ans";
    }elseif ($anciennete > 10 && $anciennete <= 15) {
        $tranche = "10-15 ans";
    } elseif ($anciennete > 15 && $anciennete <= 20) {
        $tranche = "15-20 ans";
    }  
    else {
        $tranche = "Plus de 20 ans";
    }

    // Incrémentez le compteur de genre correspondant
    if ($genre === "Masculin") {
        $genreParAnciennete[$tranche]["Masculin"]++;
    } elseif ($genre === "Féminin") {
        $genreParAnciennete[$tranche]["Féminin"]++;
    }
}

// Préparation des données pour le graphique
$labels_anc = array_keys($genreParAnciennete);
$dataHomme = array_column($genreParAnciennete, "Masculin");
$dataFemme = array_column($genreParAnciennete, "Féminin");



$query = "SELECT ee.departement, DATE_FORMAT(a.date, '%M') AS mois, COUNT(a.id) AS nombre_absences
FROM employe_entreprise ee
LEFT JOIN absence a ON ee.employe_id = a.employe_id
WHERE ee.id_entreprise = '$entreprise_id' AND MONTH(a.date) = MONTH(CURRENT_DATE)  -- Pour le mois actuel
GROUP BY ee.departement";

$result = $conn->query($query);
// Tableaux pour stocker les données du graphique
$labels_departements = array();
$data_absences = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $departement = $row['departement'];
        $nombreAbsences = $row['nombre_absences'];
        if ($total_employe != 0) {
            $nombreAbsences = ($row["nombre_absences"] / $total_employe) * 100;
        } else {
            $nombreAbsences = 0;
        }

        $labels_departements[] = $departement;
        $data_absences[] = $nombreAbsences;
    }
}
// Obtenez la date du mois actuel au format 'YYYY-MM'
$moisActuelle = date('Y-m');

// Requête sql_ab pour récupérer les données d'absence pour le mois actue


function calculerTauxAbsenceActuelle($conn, $entreprise_id, $typePeriode) {
    // Requête SQL pour obtenir le taux d'absence par mois pour l'année actuelle
$resultTotalEmp = $conn->query("SELECT COUNT(*) AS total_employe FROM employe WHERE id_entreprise = '$entreprise_id'");
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
                WHERE DATE_FORMAT(a.date, '%Y-%m-%d') = '$dateActuelle' AND e.id_entreprise = '$entreprise_id'";
    } elseif ($typePeriode === 'mois') {
        // Calculer le taux d'absence pour le mois actuel
        $anneeActuelle = date('Y');
        $moisActuel = date('m');
        $sql_ab = "SELECT COUNT(a.id) AS nombre_absences, COUNT(DISTINCT e.employe_id) AS nombre_employes
                FROM employe e
                LEFT JOIN absence a ON e.employe_id = a.employe_id
                WHERE YEAR(a.date) = '$anneeActuelle' AND MONTH(a.date) = '$moisActuel' AND e.id_entreprise = '$entreprise_id'";
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
// Fonction pour obtenir la date en français
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




// Fermeture de la connexion à la base de données
$conn->close();