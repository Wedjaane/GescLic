<?php
include('server.php');
// Vérifier si l'utilisateur est connecté en tant qu'employé ou employeur
if (!isset($_SESSION['user_type']) || ($_SESSION['user_type'] !== 'employee' && $_SESSION['user_type'] !== 'employeur')) {
    // Si l'utilisateur n'est pas connecté en tant qu'employé ou employeur, rediriger vers la page de connexion
    header("Location: login.php");
    exit();
}
 include('head.php') 
 ?>
<header class="text-center py-5 mt-5">
    <div class="container">
        <br>
        <h4>Les demandes reçues !</h4>
    </div>
</header>
<?php if ($_SESSION['user_type'] == 'employee') { ?>  
    <div class="container">
<?php } ?>
<div class="container-fluid">
<?php 
 if (isset($_SESSION['success_message'])) : ?>
     <div class="alert alert-success">
         <?php echo $_SESSION['success_message']; ?>
     </div>
     <?php unset($_SESSION['success_message']); // Clear the message after displaying it ?>
 <?php endif; ?>
 
 <!-- Check for and display error message -->
 <?php if (isset($_SESSION['error_message'])) : ?>
     <div class="error-message alert alert-danger">
         <?php echo $_SESSION['error_message']; ?>
     </div>
     <?php unset($_SESSION['error_message']); // Clear the message after displaying it ?>
 <?php endif; ?>
    <table id="table_dmd" class="table mb-4 table-bordered" style="width:100%;  vertical-align: middle;">
        <thead class="table-primary">
            <tr>
                <?php if ($role == 'employeur') { ?>
                <th data-orderable="false">Envoyé par :</th>
                <?php } ?>
                <th data-search="true">Date de la demande</th>
                <th data-search="true">Type de la demande</th>
                <th data-search="true">Déjà consulté</th>
                <th data-orderable="false">Consulter</th>   
                <th data-orderable="false">Répondre</th> 
                <?php if ($role == 'employeur') { ?>
                    <th data-orderable="false"> Statut</th>
                <?php } ?>
            </tr>
        </thead>
        <tbody >
            <?php
            if ($role == 'employeur') {
                while ($row = mysqli_fetch_assoc($rslt)) { ?>
                        <td><?php echo $row['nom_employe'] .' '.$row['prenom_employe']; ?></td>
                        <td><?php echo $row['DateSoumission']; ?></td>
                        <td><?php echo $row['type_dmd']; ?></td>
                        <td><?php echo $row['vu']; ?></td>
                        <td>
                            <button class="btn btn-link btn-details btn-mark-consulted" data-request-id="<?php echo $row['N_dmd']; ?>">
                                <i class="bi bi-eye-fill"></i>
                            </button>
                        </td>  
                        <td>
                        <?php
                            if ($row['reponse'] != NULL) {
                                echo '<button type="button" class="btn btn-warning" disabled>Réponse envoyée</button>';
                            } else {
                                echo '<button type="button" class="btn btn-warning btn-reply" data-request-id="' . $row['N_dmd'] . '" data-toggle="modal" data-target="#answerModal"> Répondre </button>';
                            }
                            ?>

                        </td>
                         <!-- Bouton pour ouvrir le modal de modification -->
                        <td>
                            <?php if ($row['Statut'] === 'prise' ) { ?>
                                <button type="button" class="btn btn-primary" disabled>Déjà prise </button>
                            <?php } else { ?>
                                    <a href="modifier_dmd.php?requestId=<?php echo $row['N_dmd']; ?>" class="btn btn-primary"><i class="bi bi-check"></i> Marquer comme prise </a>
                            <?php } ?>

                        </td>
                    </tr>
                <?php
                } 
            } else if ($role == 'employee') {
                while ($row = mysqli_fetch_assoc($res_rec)) {
                    ?>
                    <tr>
                        <td><?php echo $row['DateSoumission']; ?></td>
                        <td><?php echo $row['type_dmd']; ?></td>
                        <td><?php echo $row['vu']; ?></td>
                        <td>
                            <button class="btn btn-link btn-details btn-mark-consulted" data-request-id="<?php echo $row['N_dmd']; ?>">
                                <i class="bi bi-eye-fill"></i>
                            </button>
                        </td>  
                        <td>
                            <?php 
                              if ($row['reponse'] != NULL) {
                                echo '<button type="button" class="btn btn-warning" disabled>Réponse envoyée</button>';
                            } else {
                                echo '<button type="button" class="btn btn-warning btn-reply" data-request-id="' . $row['N_dmd'] . '" data-toggle="modal" data-target="#answerModal"> Répondre </button>';
                            }
                            ?>
                        </td>
                    </tr>
                <?php
                }
            }
            ?>
        </tbody>
    </table>
    <br>
</div>

<br><br><br>

<!-- Modal pour les détails de la demande -->
<div class="modal fade" id="requestDetailsModal" tabindex="-1" aria-labelledby="requestDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="requestDetailsModalLabel">Détails de votre demande</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="requestDetailsContent">
                <!-- Contenu des détails de la demande sera chargé ici -->
            </div>
        </div>
    </div>
</div>

<!-- Modale pour la réponse -->
<div class="modal fade" id="answerModal" tabindex="-1" role="dialog" aria-labelledby="answerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="answerModalLabel">Répondre à la demande</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Heure d'envoi : <?php echo date('H:i'); ?></p>
                <form action="add_answer.php" method="POST">
                    <div class="form-group">
                        <label for="response">Réponse :</label>
                        <textarea class="form-control" id="response" name="response" rows="3"></textarea>
                    </div>
                    <input type="hidden" id="requestId" name="requestId" value="">
                    <div class="modal-footer justify-content-center">
                        <button type="submit" class="btn btn-primary">Envoyer</button>
                        <a href="demandes_recues.php"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<br><br><br>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnDetails = document.querySelectorAll('.btn-details');

    btnDetails.forEach(btn => {
        btn.addEventListener('click', function () {
            const requestId = this.getAttribute('data-request-id');
            const row = this.closest('tr');
            const notConsulted = row.classList.contains('not-consulted');

           

            // Ouvrir la modale de détails de la demande
            const modal = new bootstrap.Modal(document.getElementById('requestDetailsModal'));
            const modalContent = document.getElementById('requestDetailsContent');

            fetch('fetch_request_details.php?id=' + requestId)
                .then(response => response.text())
                .then(data => {
                    modalContent.innerHTML = data;
                    modal.show();
                });
        });
    });

    // Gérer le clic sur le bouton "Répondre"
    const modal = new bootstrap.Modal(document.getElementById('answerModal'));
    const requestIdInput = document.getElementById('requestId');

    const btnReply = document.querySelectorAll('.btn-reply');

    btnReply.forEach(btn => {
        btn.addEventListener('click', function () {
            const requestId = this.getAttribute('data-request-id');
            requestIdInput.value = requestId;
            modal.show();
        });
    });
});
document.addEventListener('DOMContentLoaded', function () {
    const btnMarkConsulted = document.querySelectorAll('.btn-mark-consulted');

    btnMarkConsulted.forEach(btn => {
        btn.addEventListener('click', function () {
            const requestId = this.getAttribute('data-request-id');
            const row = this.closest('tr');

            fetch('vu_dmd.php?id=' + requestId)
                .then(response => response.text())
                .then(data => {
                    if (data === 'success') {
                        row.cells[3].textContent = 'Oui'; // Mise à jour du texte de la colonne "Déjà consulté"
                    }
                });
        });
    });

});


</script>


<?php include('footer.php'); ?>