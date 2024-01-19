<?php  include("server.php") ;
$role = $_SESSION['user_type'] ;

 ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-jBq2Ei5fYBCX+W8Ol0vlpSG6IkC8eAty6Kw0Kc/IfvrpBnfnwHNK1Bm/GeTuAxvfVXqQ1zC8JGu+QGnTtXryXQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.0/css/dataTables.bootstrap4.min.css"> 

    <link rel="stylesheet" type="text/css" href="css/landing.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <script src=" https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.0.0/chartjs-plugin-datalabels.minjs"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-annotation/1.0.2/chartjs-plugin-annotation.min.js"></script>
    
    <title>HR_GesClic</title>
   
</head>
<body style="background-color : #F7FDFE ; ">

    <!-- Barre de navigation -->
    <nav class="navbar navbar2 navbar-expand-lg navbar-dark fixed-top " style="background: #1D3461;padding : 0px;  font-size: 14px;">
        <a class="navbar-logo" href="index.php"><img src="images/logo2.png" alt="GesClic" style="width = 50px ; height = 60px"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav nav-pills ml-auto mr-auto">
            <?php if($role=="employee" ){?>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'pointer.php') ? 'active-link active-link-text' : ''; ?>" href="pointer.php">
                        <i class="bi bi-clock-history"></i> Pointer
                    </a>
                </li> 
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'Calendrier.php') ? 'active-link active-link-text' : ''; ?>" href="Calendrier.php">
                        <i class="bi bi-house-door-fill"></i> Calendrier
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'do_dmnd.php') ? 'active-link active-link-text' : ''; ?>" href="do_dmnd.php">
                    <i class="bi bi-pen"></i> Effectuer une demande
                    </a>
                </li>
                <?php } ?>
                <?php if($role=="employeur" ){?>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'employeur.php') ? 'active-link active-link-text' : ''; ?>" href="employeur.php">
                        <i class="bi bi-house-door-fill"></i> Statistiques
                    </a>
                </li> 
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'Calendrier.php') ? 'active-link active-link-text' : ''; ?>" href="Calendrier.php">
                        <i class="bi bi-house-door-fill"></i> Calendrier
                    </a>
                </li> 
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'do_dmnd.php') ? 'active-link active-link-text' : ''; ?>" href="do_dmnd.php">
                    <i class="bi bi-pen"></i> Envoyer une demande
                    </a>
                </li>
                <?php } ?>
                <?php if( $role=="chef" ){?>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'chef.php') ? 'active-link active-link-text' : ''; ?>" href="chef.php">
                        <i class="bi bi-house-door-fill"></i> Accueil
                    </a>
                </li> 
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'Calendrier.php') ? 'active-link active-link-text' : ''; ?>" href="Calendrier.php">
                        <i class="bi bi-house-door-fill"></i> Calendrier
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'valider_p.php') ? 'active-link active-link-text' : ''; ?>" href="valider_p.php">
                    <i class="bi bi-calendar-check"></i> Valider pointage  
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'pointer_perso.php') ? 'active-link active-link-text' : ''; ?>" href="pointer_perso.php">
                    <i class="bi bi-hand-index-thumb"></i> Pointer au personnel   
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'list_emp.php') ? 'active-link active-link-text' : ''; ?>" href="list_emp.php">
                        <i class="bi bi-person-vcard-fill"></i> Liste des employés 
                    </a>
                </li>
                <?php } ?>
                <?php if ($role=="employeur" ) {?>
                <li class="nav-item dropdown">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'list_emp.php' || basename($_SERVER['PHP_SELF']) == 'emp_archive.php') ? 'active-link active-link-text' : ''; ?> dropdown-toggle" data-toggle="dropdown" href="list_emp.php">Liste des employés</a> 
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="list_emp.php">Employés actives</a>
                            <a class="dropdown-item" href="emp_archive.php">Employés archivés</a>
                        </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'list_chef.php' || basename($_SERVER['PHP_SELF']) == 'archived_chef.php') ? 'active-link active-link-text' : ''; ?> dropdown-toggle" data-toggle="dropdown" href="list_chef.php">Liste des Chefs</a> 
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="list_chef.php">Liste des Chefs</a>
                            <a class="dropdown-item" href="archived_chef.php">comptes désactivés</a>
                        </div>
                </li>
                <?php } ?>
                <?php if ($role=="chef"|| $role=="employeur" )  {?>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'test_pointage.php') ? 'active-link active-link-text' : ''; ?>" href="test_pointage.php">
                        <i class="bi bi-calendar-check"></i> Pointage des employés 
                    </a>
                </li> 
                <?php } ?>
                <?php if ($role=="chef" )  {?>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'chef_p.php') ? 'active-link active-link-text' : ''; ?>" href="chef_p.php">
                        <i class="bi bi-person-circle"></i> Profil
                    </a>
                </li> 
                <?php } ?>
                
                <?php if($role=="employee" ){?>
                <!-- Ajoutez la classe active-link pour les autres liens de la barre de navigation de la même manière que ci-dessus pour les autres pages correspondantes -->
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'pointage.php') ? 'active-link active-link-text' : ''; ?>" href="pointage.php">
                         <i class="bi bi-calendar-check"></i> Pointage
                    </a>
                </li>
                <?php } ?>
                <?php if($role=="employeur" || $role=="employee" ){?>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'demandes_effectues.php') ? 'active-link active-link-text' : ''; ?>" href="demandes_effectues.php">
                        <i class="bi bi-card-checklist"></i> Mes demandes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'demandes_recues.php') ? 'active-link' : ''; ?>" href="demandes_recues.php">
                        <i class="bi bi-inbox"></i> Demandes reçues
                        <?php
                        $count = 0; // Initialisez la variable $count à zéro pour éviter des erreurs si la requête SQL échoue
                        
                        if ($_SESSION['user_type'] == 'employee') {
                            $employe_id = $_SESSION['user_id'];
                            $result_vu = mysqli_query($conn, "SELECT COUNT(*) AS count FROM demande WHERE vu = 'Non' AND employeID_destinataire = '$employe_id'");
                        } elseif ($_SESSION['user_type'] == 'employeur') {
                            $admin_id = $_SESSION['user_id'];
                            $result_vu = mysqli_query($conn, "SELECT COUNT(*) AS count FROM demande WHERE vu = 'Non' AND adminID_destinataire = '$admin_id'");
                        }

                        if ($result_vu && mysqli_num_rows($result_vu) > 0) {
                            $row = mysqli_fetch_assoc($result_vu);
                            $count = $row['count'];
                        }

                        // Affichez le nombre de demandes non vues
                        if ($count > 0) {
                            echo '<span class="badge badge-warning">' . $count . '</span>';
                        }
                        ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'alertes.php') ? 'active-link active-link-text' : ''; ?>" href="alertes.php">
                        <i class="bi bi-bell-fill"></i> Alertes
                          <?php if (isset($_SESSION['alertes'])) { $nombreAlertes = count($_SESSION['alertes']); 
                                    echo '<span class="badge badge-warning">' . $nombreAlertes . '</span>';
                                } ?>
                    </a>
                </li>
                <?php } ?>
                <?php if ($role=="employee" ) {?>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'employe.php') ? 'active-link active-link-text' : ''; ?>" href="employe.php">
                        <i class="bi bi-person-circle"></i> Mon profil
                    </a>
                </li> 
                <?php } ?>
                <?php if ($role=="employeur" ) {?>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'societe.php') ? 'active-link active-link-text' : ''; ?>" href="societe.php">
                        <i class="bi bi-person-workspace"></i> Infos société
                    </a>
                </li> 
                <?php } ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'logout.php') ? 'active-link active-link-text' : ''; ?>" href="logout.php" onclick="return confirm('Êtes-vous sûr de vouloir vous déconnecter ?')">
                        <i class="bi bi-box-arrow-right"></i> Déconnexion
                    </a>
                </li>
                <!-- Ajoutez d'autres liens avec la classe active-link pour les autres pages correspondantes -->
            </ul>
        </div>
    </nav>
  
