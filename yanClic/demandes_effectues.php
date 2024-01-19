<?php
include('server.php');
// Vérifier si l'utilisateur est connecté en tant qu'employé ou employeur
if (!isset($_SESSION['user_type']) || ($_SESSION['user_type'] !== 'employee' && $_SESSION['user_type'] !== 'employeur')) {
    header("Location: login.php");
    exit();
}
 include('head.php') 
 ?>
<header class="text-center py-5 mt-5">
    <div class="container">
        <br>
        <h4>Les demandes effectuées !</h4>    

    </div>
</header>
<div class="container">
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
    <table id="table" class="table  mb-4 table-bordered" style="width:100%">
        <thead class="table-primary">
            <tr>
                <th data-search="true">Date de la demande</th>
                <th data-search="true">Type de la demande</th>
                <th data-search="true">Etat</th>
                <?php
                if ($role == 'employee') { ?><th data-search="true">Statut</th><?php } ?>
                <th data-orderable="false">Consulter</th>
            </tr>
        </thead>
        <tbody>
           
                <?php
                if ($role == 'employee') {
                while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <tr>
                        <td><?php echo $row['DateSoumission']; ?></td>
                        <td><?php echo $row['type_dmd']; ?></td>
                        <td><?php echo $row['etat']; ?></td>
                        <td><?php echo $row['Statut']; ?></td>
                        <td>
                            <button class="btn btn-link btn-details " data-request-id="<?php echo $row['N_dmd']; ?>">
                                <i class="bi bi-eye-fill"></i>
                            </button>
                        </td>  
                    </tr>
                <?php                         
                }
            } elseif   ($role == 'employeur') {
            while ($row = mysqli_fetch_assoc($result_admin)) {
                ?>
                <tr>
                    <td><?php echo $row['DateSoumission']; ?></td>
                    <td><?php echo $row['type_dmd']; ?></td>
                    <td><?php echo $row['etat']; ?></td>
                    <td>
                        <button class="btn btn-link btn-details " data-request-id="<?php echo $row['N_dmd']; ?>">
                            <i class="bi bi-eye-fill"></i>
                        </button>
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
});
 
</script>


<?php include('footer.php'); ?>