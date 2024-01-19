<?php
// Inclure votre fichier de configuration de la base de données ici
include('server.php');

// Récupérer l'ID de l'employé depuis la requête GET
if (isset($_GET['employee_id'])) {
    $employeeId = $_GET['employee_id'];

    // Échapper l'ID de l'employé pour éviter les injections SQL (si nécessaire)
    $employeeId = mysqli_real_escape_string($conn, $employeeId);
// Construire la requête SQL pour récupérer les informations de l'employé, de la CIN, du permis et de la banque
$query_empID = "SELECT e.*, c.*, p.*, b.*, ep.*
            FROM employe AS e
            LEFT JOIN (
                SELECT *
                FROM cin
                WHERE employe_id = ?
                ORDER BY date_update_cin DESC
                LIMIT 1
            ) AS c ON e.employe_id = c.employe_id
            LEFT JOIN (
                SELECT *
                FROM permis
                WHERE employe_id = ?
                ORDER BY date_update_permis DESC
                LIMIT 1
            ) AS p ON e.employe_id = p.employe_id
            LEFT JOIN (
                SELECT *
                FROM banque
                WHERE employe_id = ?
                ORDER BY date DESC LIMIT 1
            ) AS b ON e.employe_id = b.employe_id
            LEFT JOIN employepro AS ep ON e.employe_id = ep.employe_id
            WHERE e.employe_id = ? ORDER BY ep.date DESC LIMIT 1";

// Exécuter la requête SQL
$stmt_empID = mysqli_prepare($conn, $query_empID);
mysqli_stmt_bind_param($stmt_empID, "iiii", $employeeId, $employeeId, $employeeId, $employeeId);
mysqli_stmt_execute($stmt_empID);
$result_empID = mysqli_stmt_get_result($stmt_empID);
$employe = mysqli_fetch_assoc($result_empID);
include ('head.php');
?>
<header class=" text-center py-5 mt-5 " >
    <div class="container-fluid ">
        <div class="row mx-4">
            <div class="col-md-6">
            </div>
            <div class="col mt-4 text-right">
                <a href="list_emp.php" class="btn btn-warning "> Retourner à la liste des employés  </a>
            </div>
        </div>  
        </div>
    </div>
  </header>
<div class="items  mx-auto "style="background-color:#ffffff; width: 95%; ">
<div class="container-fluid mb-5">
    <div class="row">
        <div class="col-md-3 border-right">
            <div class="d-flex flex-column align-items-center text-center mt-2 "> <!-- Ajoutez mx-auto ici -->
                <?php if ($employe['profil_img']) { ?>
                    <img src="images/profile/<?php echo $employe['profil_img']; ?>" alt="Photo de profil" class="rounded-circle mt-2" width="150px" >
                <?php } else { ?>
                    <img src="images/profil.png" alt="Photo de profil par défaut" class="rounded-circle mt-2" width="150px">
                <?php } ?>      
                <span class="font-weight-bold mt-4 "><?php  echo $employe['Nom'] .' ' .$employe['prenom']; ?></span>  
            </div>
            <div class="form-group mb-4 mt-4">
                    <label  >Poste  :</label>
                    <input type="text" class="form-control" value="<?php  echo $employe['Poste']; ?>" Readonly>
            </div> 
            <div class="form-group mb-4">
                    <label  >Date d'embauche   :</label>
                    <input type="text" class="form-control" value="<?php  echo $employe['DateEmbauche']; ?>" Readonly>
            </div> 
            <div class="form-group mb-4">
                    <label  >Niveau d'études  :</label>
                    <input type="text" class="form-control" value="<?php  echo $employe['niveau_etude']; ?>" Readonly>
            </div> 
            <div class="form-group mb-4">
                    <label  >Diplome  :</label>
                    <input type="text" class="form-control" value="<?php  echo $employe['diplome']; ?>" Readonly>
            </div> 
            <div class="form-group mb-4">
                    <label  >Spécialité du diplome  :</label>
                    <input type="text" class="form-control" value="<?php  echo $employe['specialite']; ?>" Readonly>
            </div> 
        </div>
        <div class="col-md-6 border-right">
            <div class="p-3 ">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="text-center mx-auto mb-4">Informations personnelles :</h4>
                </div>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label  >CIN   :</label>
                            <input type="text" class="form-control" value="<?php  echo $employe['cin']; ?>" Readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label  >Date d'expération du CIN  :</label>
                            <input type="text" class="form-control" value="<?php  echo $employe['date_exp_cin']; ?>" Readonly>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label  >Type Permis :</label>
                            <input type="text" class="form-control" value="<?php  echo $employe['type_permis']; ?>" Readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label  >Permis  :</label>
                            <input type="text" class="form-control" value="<?php  echo $employe['permis']; ?>" Readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label  >Expération Permis  :</label>
                            <input type="text" class="form-control" value="<?php  echo $employe['date_exp_permis']; ?>" Readonly>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label  >Sexe  :</label>
                            <input type="text" class="form-control" value="<?php  echo $employe['sexe']; ?>" Readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label  >Civilité  :</label>
                            <input type="text" class="form-control" value="<?php  echo $employe['civilite']; ?>" Readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label  >Situation familiale :</label>
                            <input type="text" class="form-control" value="<?php  echo $employe['situation_fam']; ?>" Readonly>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label  >N° Téléphone  :</label>
                            <input type="text" class="form-control" value="<?php  echo $employe['tele']; ?>" Readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label  >Adresse Email :</label>
                            <input type="text" class="form-control" value="<?php  echo $employe['email']; ?>" Readonly>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label  >Ville  :</label>
                            <input type="text" class="form-control" value="<?php  echo $employe['ville']; ?>" Readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label  >Adresse  :</label>
                            <input type="text" class="form-control" value="<?php  echo $employe['adresse']; ?>" Readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-3 ">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="text-center mx-auto mb-4">Informations banquaires :</h4>
                </div>
                <div class="form-group mb-4">
                    <label  >Nom de la banque :   :</label>
                    <input type="text" class="form-control" value="<?php  echo $employe['nom_banque']; ?>" Readonly>
                </div> 
                <div class="form-group mb-4">
                        <label  >RIB  :</label>
                        <input type="text" class="form-control" value="<?php  echo $employe['rib']; ?>" Readonly>
                </div> 
                <div class="form-group mb-4">
                        <label  >Code IBAN  :</label>
                        <input type="text" class="form-control" value="<?php  echo $employe['iban']; ?>" Readonly>
                </div> 
            </div>
        </div>
    </div>
</div>
</div>
</div>


<?php     // Fermer la connexion à la base de données
    mysqli_close($conn);
} else {
    echo 'ID de l\'employé non spécifié.';
}
include('footer.php')
?>