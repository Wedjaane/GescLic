<?php
// Inclure la configuration de la base de données
include('server.php');

$successMessage = "";
$erreur_date = "";
// Traitement du formulaire d'ajout d'événement
if (isset($_POST['ajouter_evenement'])) {
    $titre = $_POST['titre'];
    $dateDebut = $_POST['date_debut'];
    if(empty($_POST['date_fin'])) {
        $dateFin = $_POST['date_debut'];
    }else {
        $dateFin = $_POST['date_fin'];
    }
    // Insérer les données dans la base de données
    $sql = "INSERT INTO repos (titre, date_debut, date_fin) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $titre, $dateDebut, $dateFin);

    if (mysqli_stmt_execute($stmt)) {
        $successMessage = "Date ajoutée avec succés . ";
    } else {
        // Gérer les erreurs d'insertion
        $erreur_date = "Erreur lors de l'ajout de l'événement.";
    }
    mysqli_stmt_close($stmt);
}
// Traitement du formulaire de modification d'événement
if (isset($_POST['modifier_evenement'])) {
    $id = $_POST['id_repos'];
    $titre = $_POST['titre'];
    $dateDebut = $_POST['date_debut'];
    $dateFin = $_POST['date_fin'];

    $sql = "UPDATE repos SET titre = ?, date_debut = ?, date_fin = ? WHERE id_repos = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $titre, $dateDebut, $dateFin, $id);

    if (mysqli_stmt_execute($stmt)) {
        $successMessage = "Date modifiée avec succés . ";
    } else {
        $erreur_date = "Erreur lors de la modification de l'événement.";
    }

    mysqli_stmt_close($stmt);
}

// Traitement du formulaire de suppression d'événement
if (isset($_POST['supprimer_evenement'])) {
    $id = $_POST['id_repos'];

    $sql_delete = "DELETE FROM repos WHERE id_repos = ?";
    $stmt_delete = mysqli_prepare($conn, $sql_delete);
    mysqli_stmt_bind_param($stmt_delete, "i", $id);

    if (mysqli_stmt_execute($stmt_delete)) {
        $successMessage = "Date supprimée avec succés . ";
    } else {
        $erreur_date= "Erreur lors de la suppression de l'événement.";
    }

    mysqli_stmt_close($stmt_delete);
}

// Récupérer les événements depuis la base de données
$sqlSelect = "SELECT id_repos, titre, date_debut, date_fin FROM repos";
$result = mysqli_query($conn, $sqlSelect);
$evenements = mysqli_fetch_all($result, MYSQLI_ASSOC);

include('head.php');
?>



<header class="text-center py-5 mt-5">
    <div class="container mb-4">
    <h4  class="text-center mt-2 mb-4">Liste des jours fériés </h4> 
    </div>
</header>
<div class="container">
<div class="row mx-4">
            <div class="col-md-6 mb-4">
                <input type="text" id="myInput" onkeyup="myFunction()" placeholder=" Chercher par titre ou date" title="Type in a name" style="width:50% ; margin-left: 40px">
                <i class="bi bi-search"></i>
            </div>
            <?php if ( $_SESSION['user_type'] == 'employeur' ) { ?>
            <div class="col-md-6 text-right">
                         <button type="button" class="btn btn-success mb-4" style="margin-right: 40px" data-toggle="modal" data-target="#ajouterModal">
                         <i class="bi bi-calendar-plus-fill"> </i>  Ajouter un événement
                        </button>
            </div> 
            <?php }?> 
    </div>
    <?php
        
               if (!empty($successMessage)) {
                   echo '<div class="alert alert-success">' . $successMessage . '</div>
                   <meta http-equiv="refresh" content="10;url=Calendrier.php">';


               } elseif (!empty($erreur_date)) {
                   echo '<div class="alert alert-danger">' . $erreur_date . '</div>
                   <meta http-equiv="refresh" content="10;url=Calendrier.php">';

               }
               ?>
             
<!-- Modal d'ajout d'événement -->
<div class="modal fade" id="ajouterModal" tabindex="-1" role="dialog" aria-labelledby="ajouterModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ajouterModalLabel">Ajouter un événement</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Formulaire d'ajout d'événement -->
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <div class="form-group">
                        <label for="titre">Titre</label>
                        <input type="text" class="form-control" id="titre" name="titre" required>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="date_debut">Date </label>
                                <input type="date" class="form-control" id="date_debut" name="date_debut" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="date_fin">Date de fin si nécessaire </label>
                                <input type="date" class="form-control" id="date_fin" name="date_fin" >
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-2">
                        <button type="submit" class="btn btn-success" name="ajouter_evenement">Ajouter</button>
                        <button type="button" class="btn btn-secondary " data-dismiss="modal">Annuler</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Tableau des événements -->
    <div class="row">
        <div class="col">
            <table class="table table-bordered mb-4 " id="myTable">
                <thead class= "table-primary" >
                    <tr>
                        <th>titre</th>
                        <th>Date de début</th>
                        <th>Date de fin</th>
                        <?php if ( $_SESSION['user_type'] == 'employeur' ) { ?>
                        <th>Actions</th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($evenements as $evenement) : ?>
                        <tr>
                            <td><?php echo $evenement['titre']; ?></td>
                            <td><?php echo $evenement['date_debut']; ?></td>
                            <td><?php echo $evenement['date_fin']; ?></td>
                            <?php if ( $_SESSION['user_type'] == 'employeur' ) { ?>
                            <td>
                                <button class="btn btn-primary btn-sm modifier-evenement" data-toggle="modal" data-target="#modifierModal" data-id="<?php echo $evenement['id_repos']; ?>" data-nom="<?php echo $evenement['titre']; ?>" data-date-debut="<?php echo $evenement['date_debut']; ?>" data-date-fin="<?php echo $evenement['date_fin']; ?>">Modifier</button>
                                <button class="btn btn-danger btn-sm supprimer-evenement" data-toggle="modal" data-target="#supprimerModal" data-id="<?php echo $evenement['id_repos']; ?>">Supprimer</button>
                            </td>
                            <?php } ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de suppression d'événement -->
<div class="modal fade" id="supprimerModal" tabindex="-1" role="dialog" aria-labelledby="supprimerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supprimerModalLabel">Supprimer un événement</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <input type="hidden" name="id_repos" id="id_repos">
                    <p>Êtes-vous sûr de vouloir supprimer cet événement ?</p>
                    <div class="text-center">
                        <button type="submit" class="btn btn-danger mb-4" name="supprimer_evenement">Supprimer</button>
                        <button type="button" class="btn btn-secondary mb-4" data-dismiss="modal">Annuler</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de modification d'événement -->
<div class="modal fade" id="modifierModal" tabindex="-1" role="dialog" aria-labelledby="modifierModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modifierModalLabel">Modifier un événement</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="id_repos" id="id_repos">

                    <div class="form-group">
                        <label for="titre">Titre</label>
                        <input type="text" class="form-control" id="titre" name="titre" value="" >
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="date_debut">Date </label>
                                <input type="date" class="form-control" id="date_debut" name="date_debut" value="" >
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="date_fin">Date de fin si nécessaire </label>
                                <input type="date" class="form-control" id="date_fin" name="date_fin" value="" >
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-2">
                        <button type="submit" class="btn btn-success" name="modifier_evenement">Modifier</button>
                        <button type="button" class="btn btn-secondary " data-dismiss="modal">Annuler</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ... code suivant ... -->
<!-- Ajoutez ce script à la fin de votre page HTML, juste avant la balise </body> -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Récupérer les boutons "Modifier" et "Supprimer"
        const modifierButtons = document.querySelectorAll('.modifier-evenement');
        const supprimerButtons = document.querySelectorAll('.supprimer-evenement');

        // Récupérer les modals de modification et de suppression
        const modifierModal = document.querySelector('#modifierModal');
        const supprimerModal = document.querySelector('#supprimerModal');

        // Récupérer les champs du formulaire dans les modals
        const modifierTitreInput = modifierModal.querySelector('#titre');
        const modifierDateDebutInput = modifierModal.querySelector('#date_debut');
        const modifierDateFinInput = modifierModal.querySelector('#date_fin');
        const supprimerIdInput = supprimerModal.querySelector('#id_repos');
        const modifierIdInput = modifierModal.querySelector('#id_repos');

        // Écouter les clics sur les boutons "Modifier"
        modifierButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Récupérer les données de l'événement depuis les attributs data-*
                const id = button.getAttribute('data-id');
                const titre = button.getAttribute('data-nom');
                const dateDebut = button.getAttribute('data-date-debut');
                const dateFin = button.getAttribute('data-date-fin');

                // Mettre à jour les champs du formulaire
                modifierIdInput.value = id;

                modifierTitreInput.value = titre;
                modifierDateDebutInput.value = dateDebut;
                modifierDateFinInput.value = dateFin;
            });
        });

        // Écouter les clics sur les boutons "Supprimer"
        supprimerButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Récupérer l'ID de l'événement à supprimer depuis l'attribut data-id
                const id = button.getAttribute('data-id');

                // Mettre à jour l'input hidden dans le formulaire de suppression
                supprimerIdInput.value = id;
            });
        });
    });
</script>


<!-- ... code suivant ... -->
<?php include ('footer.php') ;?>