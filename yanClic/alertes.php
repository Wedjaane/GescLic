<?php
session_start();
include('server.php');
include('head.php');

// Vérifier si l'utilisateur est connecté en tant qu'employeur ou employé
if (!isset($_SESSION['user_type']) || ($_SESSION['user_type'] !== 'employee' && $_SESSION['user_type'] !== 'employeur')) {
    header("Location: login.php");
    exit();
}
?>
<header class="text-center py-5 mt-5">
    <div class="container">
        <br><h4>Les Alertes !</h4>
    </div>
</header>
<div class="container mb-4">
    <?php
    $user_type = $_SESSION['user_type'];
    $user_id = $_SESSION['user_id'];

if ($user_type == 'employeur' ) {
    // Récupérer les employés avec leurs alertes pour le CIN (une seule ligne par employé)
    $sqlCIN = "SELECT employe.nom AS employe_nom, employe.prenom AS employe_prenom, employe.cin AS employe_cin, MAX(cin.date_exp_cin) AS date_exp_cin
               FROM employe
               LEFT JOIN cin ON employe.employe_id = cin.employe_id
               WHERE employe.id_entreprise = '$entreprise_id'
               GROUP BY employe.employe_id
               ORDER BY MAX(cin.date_exp_cin) DESC";
    // Récupérer les employés avec leurs alertes pour le permis de conduire (une seule ligne par employé)
    $sqlPermis = "SELECT employe.nom AS employe_nom, employe.prenom AS employe_prenom, employe.cin AS employe_cin, MAX(permis.date_exp_permis) AS date_exp_permis
                    FROM employe
                    LEFT JOIN permis ON employe.employe_id = permis.employe_id
                    WHERE employe.id_entreprise = '$entreprise_id'
                    GROUP BY employe.employe_id
                    ORDER BY MAX(permis.date_exp_permis) DESC;
                ";
}elseif ($user_type == 'employee' ) {
    $sqlCIN = "SELECT date_exp_cin FROM cin WHERE employe_id = '$user_id ' ORDER BY date_update_cin DESC LIMIT 1";
    $sqlPermis = "SELECT date_exp_permis FROM permis WHERE employe_id = '$user_id' ORDER BY date_update_permis DESC LIMIT 1";
}
    $resultCIN = $conn->query($sqlCIN);
    $resultPermis = $conn->query($sqlPermis);
    $alertes = array(); // Tableau pour stocker les alertes

    // Traitement des alertes pour le CIN
    while ($rowCIN = $resultCIN->fetch_assoc()) { 
        $expirationCIN = $rowCIN['date_exp_cin'];
        $currentDate = date('Y-m-d');
        if ($user_type == 'employee' ) {
            if ($expirationCIN !== null) {
                if ($expirationCIN < $currentDate) {
                    $alertes[] = "<div class='alert alert-danger '>Attention ! Votre CIN a expiré le $expirationCIN. </div>";
                } elseif ($expirationCIN == $currentDate) {
                    $alertes[] = "<div class='alert alert-warning'>Attention ! Votre CIN expire aujourd'hui. </div>";
                } elseif ($expirationCIN > $currentDate) {
                    // Calculer le nombre de jours restants
                    $diff = strtotime($expirationCIN) - strtotime($currentDate);
                    $daysRemaining = floor($diff / (60 * 60 * 24));
        
                    if ($daysRemaining <= 30) {
                        $alertes[] = "<div class='alert alert-success'>Votre CIN est valide jusqu'au $expirationCIN. Il vous reste encore $daysRemaining jours. </div>";
                    }
                } 
            }else {
                    $alertes[] = "<div class='alert alert-warning'>Attention ! Vous devez définir une date d'expiration valable pour le CIN .</div>";
            }
        }elseif ( $user_type == 'employeur' ) {
        $employeNom = $rowCIN['employe_nom'];
        $employePrenom = $rowCIN['employe_prenom'];
        $employeCIN = $rowCIN['employe_cin'];
        $expirationCIN = $rowCIN['date_exp_cin'];
        if ($expirationCIN !== null) {
            if ($expirationCIN < $currentDate) {
                $alertes[] = "<div class='alert alert-danger'>Attention ! Le CIN de $employeNom $employePrenom (CIN: $employeCIN) a expiré le $expirationCIN.</div>";
            } elseif ($expirationCIN == $currentDate) {
                $alertes[] = "<div class='alert alert-warning'>Attention ! Le CIN de $employeNom $employePrenom (CIN: $employeCIN) expire aujourd'hui.</div>";
            } elseif ($expirationCIN > $currentDate) {
                $diff = strtotime($expirationCIN) - strtotime($currentDate);
                $daysRemaining = floor($diff / (60 * 60 * 24));

                if ($daysRemaining <= 30) {
                    $alertes[] = "<div class='alert alert-success'>Le CIN de $employeNom $employePrenom (CIN: $employeCIN) est valide jusqu'au $expirationCIN. Il reste encore $daysRemaining jours.</div>";
                }
            }
        } else {
            $alertes[] = "<div class='alert alert-warning'>Attention ! $employeNom $employePrenom (CIN: $employeCIN) doit définir une date d'expiration valable pour le CIN .</div>";
        }
    }
}
    // Traitement des alertes pour le permis de conduire
    while ($rowPermis = $resultPermis->fetch_assoc()) {
        $expirationPermis = $rowPermis['date_exp_permis'];
        $currentDate = date('Y-m-d');
        if ($user_type == 'employee' ) {
            if ($expirationPermis !== null) {
                if ($expirationPermis < $currentDate) {
                    $alertes[] = "<div class='alert alert-danger'>Attention ! Votre permis de conduire a expiré le $expirationPermis. </div>";
                } elseif ($expirationPermis == $currentDate) {
                    $alertes[] = "<div class='alert alert-warning'>Attention ! Votre permis de conduire expire aujourd'hui.</div>";
                } elseif ($expirationPermis > $currentDate) {
                    // Calculer le nombre de jours restants
                    $diff = strtotime($expirationPermis) - strtotime($currentDate);
                    $daysRemaining = floor($diff / (60 * 60 * 24));
                        if ($daysRemaining <= 30) {
                        $alertes[] = "<div class='alert alert-success'>Votre permis de conduire est valide jusqu'au $expirationPermis. Il vous reste encore $daysRemaining jours.</div>"; 
                        }
                }
            } else {
                    $alertes[] = "<div class='alert alert-warning'>Attention ! Vous devez définir une date d'expiration valable pour le CIN .</div>";
            }
        }elseif ( $user_type == 'employeur' ) {
        $employeNom = $rowPermis['employe_nom'];
        $employePrenom = $rowPermis['employe_prenom'];
        $employeCIN = $rowPermis['employe_cin'];
        if ($expirationPermis !== null) {
            if ($expirationPermis < $currentDate) {
                $alertes[] = "<div class='alert alert-danger'>Attention ! Le permis de conduire de $employeNom $employePrenom (CIN: $employeCIN) a expiré le $expirationPermis.</div>";
            } elseif ($expirationPermis == $currentDate) {
                $alertes[] = "<div class='alert alert-warning'>Attention ! Le permis de conduire de $employeNom $employePrenom (CIN: $employeCIN) expire aujourd'hui.</div>";
            } elseif ($expirationPermis > $currentDate) {
                $diff = strtotime($expirationPermis) - strtotime($currentDate);
                $daysRemaining = floor($diff / (60 * 60 * 24));

                if ($daysRemaining <= 30) {
                    $alertes[] = "<div class='alert alert-success'>Le permis de conduire de $employeNom $employePrenom (CIN: $employeCIN) est valide jusqu'au $expirationPermis. Il reste encore $daysRemaining jours.</div>";
                }
            } 
        } else {
            $alertes[] = "<div class='alert alert-warning'>Attention ! $employeNom $employePrenom (CIN: $employeCIN) doit définir une date d'expiration valable pour le permis de conduire.</div>";
        }
    }
}
    $conn->close();

    if (!empty($alertes)) {
        foreach ($alertes as $alerte) {
            echo "<div class='mb-4 mt-4'>$alerte</div>";
        }
    } else {
        echo "<div class='alert alert-info'>Il n'existe aucune alerte pour le moment.</div>";
    }
    $_SESSION['alertes'] = $alertes;
    ?>
</div>
<?php include('footer.php'); ?>
