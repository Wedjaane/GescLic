<?php
session_start();
include('server.php');
// Vérifier si l'utilisateur est connecté en tant qu'employeur
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'employeur') {
    header("Location: login.php");
    exit();
}

// Récupérer l'ID de l'administrateur connecté
$admin_id = $_SESSION['user_id'];

// Requête SQL pour récupérer toutes les entreprises de cet administrateur
$query = "SELECT e.*, b.*, ae.* , a.email as email_admin
FROM entreprises e
LEFT JOIN banque b ON e.id_entreprise = b.id_entreprise
LEFT JOIN admin_entreprise ae ON e.id_entreprise = ae.id_entreprise
LEFT JOIN administrateurs a ON a.id_admin = ae.id_admin
WHERE ae.id_admin = ?  LIMIT 1";

// Exécuter la requête SQL
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $admin_id); 
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Récupérer les informations sur les entreprises
$entreprises = mysqli_fetch_assoc($result);

include('head.php');

?>
<div class="profile-container py-2 mt-2">
        <div class="sidebar ">
        <div class="user-profile">
                <?php if ($entreprises['ste_img']) { ?>
                    <img src="images/societe/<?php echo $entreprises['ste_img']; ?>" alt="Photo de profil" class="profile-image">
                <?php } else { ?>
                    <img src="images/profil.png" alt="Photo de profil par défaut" class="profile-image">
                <?php } ?>           
            </div>
            <button class="nav-button " onclick="showSection('society_info')">Informations de l'entreprise </button>
            <button class="nav-button" onclick="showSection('bank_info')">Informations Bancaires</button>
            <button class="nav-button" onclick="showSection('password')">Changer le mot de passe </button>
            <!-- Ajoutez d'autres boutons de navigation au besoin -->
        </div>
    <div class="container mt-4 main-content">
    
<!-- Section pour les informations sur la société -->
<div id="society_info" class=" mb-4">
<h3 class="text-center mt-2 mb-4" >Informations sur la Société : </h3>
    <div class="items  mx-auto "style="background-color:#ffffff; width: 100%; ">
        <div class="container ">  
                        <form method="post" class="form1" action="add_ste.php" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label  >N° Id fiscale  :</label>
                                        <input type="text" id="fiscale" name="fiscale" class="form-control" value="<?php echo  $entreprises['fiscale'] ; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label  >N° I.C.E  :</label>
                                        <input type="text" id="ice" name="ice" class="form-control" value="<?php echo  $entreprises['ice'] ; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label  >N° Patente   :</label>
                                        <input type="text" id="patente" name="patente" class="form-control" value="<?php echo  $entreprises['patente'] ; ?>" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="address">N° CNSS :</label>
                                        <input type="text" id="cnss" name="cnss" class="form-control" value="<?php echo $entreprises['cnss']; ?>" readonly>
                                    </div>
                                </div> 
                                <div class="col">
                                    <div class="form-group">
                                        <label >N° TP :</label>
                                        <input type="text" id="tp" name="tp" class="form-control" value="<?php echo $entreprises['tp']; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label >N° R.C :</label>
                                        <input type="text" id="rc" name="rc" class="form-control" value="<?php echo $entreprises['rc']; ?>" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label  >Campanie Assurance  :</label>
                                        <input type="text" id="comp_assurance" name="comp_assurance" class="form-control" value="<?php echo  $entreprises['comp_assurance'] ; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label >Caisse de retraite :</label>
                                        <input type="text" id="caisse_retraite" name="caisse_retraite" class="form-control" value="<?php echo $entreprises['caisse_retraite']; ?>" readonly>
                                    </div>
                                </div>   
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label  >Raison sociale   :</label>
                                        <input type="text" id="Nom_Entreprise" name="Nom_Entreprise" class="form-control" value="<?php echo  $entreprises['Nom_Entreprise'] ; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label >Forme juridique :</label>
                                        <input type="text" id="forme" name="forme" class="form-control" value="<?php echo $entreprises['forme']; ?>" readonly>
                                    </div>
                                </div>  
                                <div class="col">
                                    <div class="form-group">
                                        <label >Activité :</label>
                                        <input type="text" id="activite" name="activite" class="form-control" value="<?php echo $entreprises['activite']; ?>" readonly>
                                    </div>
                                </div>  
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label  >Délégation   :</label>
                                        <input type="text" id="delegation" name="delegation" class="form-control" value="<?php echo  $entreprises['delegation'] ; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label >Région :</label>
                                        <input type="text" id="region" name="region" class="form-control" value="<?php echo $entreprises['region']; ?>" readonly>
                                    </div>
                                </div>  
                                <div class="col">
                                    <div class="form-group">
                                        <label >Ville :</label>
                                        <input type="text" id="ville" name="ville" class="form-control" value="<?php echo $entreprises['ville']; ?>" readonly>
                                    </div>
                                </div>    
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label  >E-mail   :</label>
                                        <input type="text" id="email" name="email" class="form-control" value="<?php echo  $entreprises['email'] ; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label >Fax :</label>
                                        <input type="text" id="fax" name="fax" class="form-control" value="<?php echo $entreprises['fax']; ?>" readonly>
                                    </div>
                                </div>  
                                <div class="col">
                                    <div class="form-group">
                                        <label >N° Téléphone :</label>
                                        <input type="text" id="Contact" name="Contact" class="form-control" value="<?php echo $entreprises['Contact']; ?>" readonly>
                                    </div>
                                </div>  
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label  >Adresse 1   :</label>
                                        <input type="text" id="adress1" name="adress1" class="form-control" value="<?php echo  $entreprises['adress1'] ; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label >Adresse 2 :</label>
                                        <input type="text" id="adress2" name="adress2" class="form-control" value="<?php echo $entreprises['adress2']; ?>" readonly>
                                    </div>
                                </div>  
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label  >Site Web   :</label>
                                        <input type="text" id="web" name="web" class="form-control" value="<?php echo  $entreprises['web'] ; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="ste_img">Télécharger logo de votre entreprise :</label>
                                        <input type="file" id="ste_img" name="ste_img" value=" <?php $entreprises['ste_img'] ?>" >
                                        <?php if (!empty($entreprises['ste_img'])): ?>
                                                <img src="images/societe/<?php echo $entreprises['ste_img']; ?>" alt="Société logo" class="cin-image">
                                        <?php endif; ?>

                                    </div>
                                </div>
                            </div>
                            <div class="text-center mt-4">
                            <button type="button" class="btn btn-primary edit-button" data-section="society_info">Modifier</button>
                            <button type="submit" class="btn btn-success save-button mx-auto" name = "add_ste" style="display: none;">Enregistrer les modifications</button>
                             </div>
                        </form>
              
        </div>
    </div>
</div>

       <!-- Section pour les informations bancaires -->
<div id="bank_info" style="display: none;">
    <h3 class="text-center mt-2 mb-4" >Informations Bancaires de l'Entreprise : </h3>
    <div class="items  mx-auto "style="background-color:#ffffff; width: 80%; ">
        <div class="container ">  
            <form method="post" class="form1" action="add_ste.php">
                        <div class="form-group">
                            <label>Nom de la banque :</label>
                            <input type="text" name="nom_banque" class="form-control" value="<?php echo $entreprises['nom_banque']; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="address">RIB :</label>
                            <input type="text" name="rib" class="form-control" value="<?php echo $entreprises['rib']; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Code IBAN :</label>
                            <input type="text" name="iban" class="form-control" value="<?php echo $entreprises['iban']; ?>" readonly>
                        </div>
                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-primary edit-button" data-section="bank_info">Modifier mes informations</button>
                            <button type="submit" name="add_banq" class="btn btn-success save-button mx-auto" style="display: none;">Enregistrer</button>
                        </div>    
            </form> 
        </div>
    </div>
</div>
      
<div id="password" >
        <h4 class="text-center mt-4 mb-4">Changer le mot de passe :</h4>
                <div class="items mx-auto" style="background-color:#ffffff; width: 80%;">
                    <div class="container">
                        <!-- Afficher les messages d'erreur uniquement si la section password est active -->
                        <?php if (isset($_GET['error']) && $_GET['section'] === 'password') { echo $_GET['error']; } ?>
                        <form action="change-p.php" method="post" id="password-form">
                        <?php
                            if (isset($_GET['error'])) {
                                $error_message = urldecode($_GET['error']);
                                echo '<div class="alert alert-danger">' . $error_message . '</div>';
                            } elseif (isset($_GET['success'])) {
                                $success_message = urldecode($_GET['success']);
                                echo '<div class="alert alert-success">' . $success_message . '</div>';
                            }
                        ?>
                            <div class="row form-group">
                            <input type="hidden" name="email_admin" id= "email_admin">
                                <div class="col-sm-6">
                                    <label>Ancien mot de passe :</label>
                                </div>
                                <div class="col-sm-6">
                                    <input class="form-control" type="password" name="op" placeholder="Ancien mot de passe" required>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-6">
                                    <label>Nouveau mot de passe :</label>
                                </div>
                                <div class="col-sm-6">
                                    <input class="form-control" type="password" name="np" placeholder="Nouveau mot de passe" >
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-6">
                                    <label>Confirmer le mot de passe :</label>
                                </div>
                                <div class="col-sm-6">
                                    <input class="form-control" type="password" name="c_np" placeholder="Confirmer le nouveau mot de passe:" >
                                </div>
                            </div>
                            <div class="modal-footer justify-content-center mt-2">
                                <button type="submit" name="change_p" class="btn btn-primary">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>

    <?php include('footer.php'); ?>

    <script>
// Fonction pour activer l'édition des champs
document.addEventListener('DOMContentLoaded', function () {
    const editButtons = document.querySelectorAll(".edit-button");
    const saveButtons = document.querySelectorAll(".save-button");
    const inputFields = document.querySelectorAll(".form-control");

    // Vérifier si les champs sont déjà remplis
    const iceField = document.querySelector("#ice");
    const fiscaleField = document.querySelector("#fiscale");

  

    editButtons.forEach((editButton, index) => {
        editButton.addEventListener("click", () => {
            inputFields.forEach((field, fieldIndex) => {
                if (fieldIndex !== 0 && fieldIndex !== 1) {
                    if (iceField.value.trim() !== '' && fiscaleField.value.trim() !== '') {
                        iceField.setAttribute("readonly", "readonly");
                        fiscaleField.setAttribute("readonly", "readonly");
                    } else {
                        iceField.removeAttribute("readonly");
                        fiscaleField.removeAttribute("readonly");
                    }
                    field.removeAttribute("readonly");
                }
            });
            editButton.style.display = "none";
            saveButtons[index].style.display = "block";
        });
    });
});

</script>


