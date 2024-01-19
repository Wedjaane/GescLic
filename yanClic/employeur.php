<?php
session_start();
include('statis.php');
// Check if the user is logged in as an employee
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'employeur') {
    // If not logged in as an employee, redirect to the login page
    header("Location:login.php");
    exit();
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
                  <?php $total = $conn->query("SELECT DISTINCT COUNT(employe_id) As total from employe where id_entreprise = '$entreprise_id'");
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
                <?php   $tauxAbsenceJourActuel = calculerTauxAbsenceActuelle($conn, $entreprise_id, 'jour');
                        $tauxAbsenceMoisActuel = calculerTauxAbsenceActuelle($conn, $entreprise_id, 'mois');
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
            <div class="col-md-3">
            <div class="chart-card text-center" >
                     <h6 class="mb-3">Répartition par genre  </h6>
                <canvas id="sex" ></canvas>
                </div>
            </div>
            <div class="col-md-3">
                <div class="chart-card ">
                    <h6>Répartition des employés par département  </h6>
                    <canvas id="departement"  ></canvas>
                </div>
            </div>
            <div class="col-md-3">
                <div class="chart-card ">
                    <h6>Répartition par statut professionnel  </h6>
                    <canvas id="poste" ></canvas>
                </div>
            </div>
            <div class="col-md-3">
                <div class="chart-card">
                    <h6>Répartition par les causes d'absence  </h6>
                    <canvas id="absence"></canvas>
                </div>
            </div>
        </div>
        <div class="row mb-2" style="margin-top : 80px">
            <div class="col-md-6">
                <div class="chart-card">
                    <h6>Répartition par age  </h6>
                    <canvas id="age"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-card">
                    <h6>Répartition des genres par ancienneté  </h6>
                    <canvas id="chartCanvas" ></canvas>
                </div>
            </div>
            </div>
        <div class="row " style="margin-top : 40px">
            <div class="col-md-6">
                <div class="chart-card">
                <h6>Taux d'absence durant l'année actuelle : </h6>
                    <canvas id="graphiqueTauxAbsence"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-card">
                <h6>Taux d'absence par département  </h6>
                <canvas id="absencesDepartement" ></canvas>
                </div>
            </div>
            
        </div>
       

    </div>
</section>

<?php include('footer.php') ?>

<script>
     
    var age = document.getElementById('age').getContext('2d');

    var chart_age = new Chart(age, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($labels_age); ?>,
            datasets: [{
                label: '',
                data: <?php echo json_encode($data_age); ?>,
                backgroundColor: 'rgb(0, 43, 91)'
            }]
        },
        options: {
            plugins: {
            legend: {
                display: false, // Afficher la légende
                position: 'bottom', // Position de la légende (peut être 'top', 'bottom', 'left' ou 'right')
                labels: {
                    font: {
                        size: 12 // Taille de la police de la légende
                    }
                }
            }}
        }
    });

var poste = document.getElementById('poste').getContext('2d');
var chart_poste = new Chart(poste, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($labels_poste); ?>,
        datasets: [{
            label: 'Répartition par statut professionnel',
            data: <?php echo json_encode($data_poste); ?>,
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
    },
    options: {
        plugins: {
            legend: {
                display: true, // Afficher la légende
                position: 'top', // Position de la légende (peut être 'top', 'bottom', 'left' ou 'right')
                labels: {
                    font: {
                        textAlign : 'left',
                        size: 12 // Taille de la police de la légende
                    }
                }
            },
            datalabels: {
                display: true, // Activer l'affichage des labels de données
                color: 'black', // Couleur du texte des labels
                font: {
                    weight: 'bold' // Style de police
                },
                formatter: function(value, context) {
                    // Afficher le nombre
                    return value;
                }
            }
        },
       
        legend: {
            position: 'left', // Position de la légende (à gauche)
            labels: {
                fontColor: 'black', // Couleur du texte de la légende
                fontSize: 12 // Taille du texte de la légende
            }
        }
    }
});

    var departement = document.getElementById('departement').getContext('2d');

    var chart_depart = new Chart(departement, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($labels_depart); ?>,
            datasets: [{
                label: 'Répartition par département',
                data: <?php echo json_encode($data_depart); ?>,
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
        },
        options: {
            plugins: {
                datalabels: {
                    display: true, // Activer l'affichage des labels de données
                    color: 'black', // Couleur du texte des labels
                    font: {
                        weight: 'bold' // Style de police
                    },
                    
                    formatter: function(value, context) {
                        // Afficher le nombre
                        return value;
                    }
                }
            }
        }
    });
    var sexe = document.getElementById('sex').getContext('2d');
    var chart_sexe = new Chart(sexe, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($labels_sexe); ?>,
            datasets: [{
                label: 'Répartition par sexe',
                data: <?php echo json_encode($data_sexe); ?>,
                backgroundColor: ['rgb(234, 84, 85)', 'rgb(0, 43, 91)'
            ]
            }]
        },
        options: {
             
        }
    });
   
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
        // Récupérez le contexte du canvas
// Récupération du contexte du graphique
var ctx = document.getElementById('chartCanvas').getContext('2d');

// Données pour le graphique
var data = {
    labels: <?php echo json_encode($labels_anc); ?>,
    datasets: [
        {
            label: 'Hommes',
            data: <?php echo json_encode($dataHomme); ?>,
            backgroundColor: 'rgb(0, 43, 91)',
        },
        {
            label: 'Femmes',
            data: <?php echo json_encode($dataFemme); ?>,
            backgroundColor: 'rgb(234, 84, 85)',
        }
    ]
};

// Configuration du graphique
var options = {
    scales: {
        y: {
            beginAtZero: true
        }
    }
    
};

// Création du graphique
var myChart = new Chart(ctx, {
    type: 'bar',
    data: data,
    options: options
});

var ctx_abdepart = document.getElementById('absencesDepartement').getContext('2d');
var chart_abdepart = new Chart(ctx_abdepart, {
    type: 'bar', // Utilisez le type de graphique approprié (bar, pie, etc.)
    data: {
        labels: <?php echo json_encode($labels_departements); ?>,
        datasets: [{
            label: '',
            data: <?php echo json_encode($data_absences); ?>,
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
    },
    options: {
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
        },
        plugins: {
        legend: {
            display: false, // Afficher la légende
            position: 'bottom', // Position de la légende (peut être 'top', 'bottom', 'left' ou 'right')
            labels: {
                font: {
                    size: 12 // Taille de la police de la légende
                }
            }
        }
        
    }
    }
});


    // Sélectionnez tous les canvas avec la classe "chart-canvas"
var canvasElements = document.querySelectorAll('.chart-canvas');

// Définissez la hauteur minimale pour tous les canvas
var minHeight = '100px';

// Parcourez tous les canvas et définissez leur hauteur minimale
canvasElements.forEach(function (canvas) {
    canvas.style.minHeight = minHeight;
});
   

</script>