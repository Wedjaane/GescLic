<?php
session_start();
include('server.php');

// Vérifier si l'utilisateur est connecté en tant qu'employé ou employeur
if (!isset($_SESSION['user_type']) || ($_SESSION['user_type'] !== 'chef' && $_SESSION['user_type'] !== 'employeur')) {
    // Si l'utilisateur n'est pas connecté en tant qu'employé, rediriger vers la page de connexion
    header("Location: login.php");
    exit();
}

include('head.php');
?>

<?php
$chef_id = $_SESSION['user_id'];
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$resultsPerPage = 10;
// Requête pour récupérer tous les employés avec leur chef

// Effectuer la requête de recherche avec pagination
if ($_SESSION['user_type'] == 'employeur') {
    $query = "SELECT e.*, ee.DateEmbauche, ee.DateFin, ee.fonction, c.nom AS chef_nom, c.prenom AS chef_prenom
    FROM employe e
    LEFT JOIN (
        SELECT employe_id, id_entreprise, MAX(update_date) AS MaxUpdateDate
        FROM employe_entreprise
        WHERE DateFin IS NULL
        GROUP BY employe_id, id_entreprise
    ) subquery ON e.employe_id = subquery.employe_id
    LEFT JOIN employe_entreprise ee ON e.employe_id = ee.employe_id AND subquery.id_entreprise = ee.id_entreprise AND subquery.MaxUpdateDate = ee.update_date
    LEFT JOIN employe c ON e.chef_id = c.employe_id
    WHERE e.id_entreprise = '$entreprise_id'
    LIMIT " . (($page - 1) * $resultsPerPage) . ", $resultsPerPage";
    $countQuery = "SELECT COUNT(*) AS total FROM employe ";
} else if ($_SESSION['user_type'] == 'chef') {
    $query = "SELECT * FROM employe WHERE chef_id = '$chef_id'  ORDER BY cin LIMIT " . (($page - 1) * $resultsPerPage) . ", $resultsPerPage";
    $countQuery = "SELECT COUNT(*) AS total FROM employe WHERE chef_id = '$chef_id' ";
}

$result = mysqli_query($conn, $query);
$countResult = mysqli_query($conn, $countQuery);
$rowCount = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($rowCount / $resultsPerPage);

?>

<header class="text-center py-5 mt-4">
    <div class="container">
        <br>
        <h3>La liste des employés</h3>
    </div>
</header>

<?php if ($_SESSION['user_type'] == 'employeur') { ?>  
<div class="container-fluid">
    <div class="row mx-4">
            <div class="col-md-6">
                <input type="text" id="myInput" onkeyup="myFunction()" placeholder=" Chercher Employé par CIN  ou Nom" title="Type in a name" style="width:50% ; margin-left: 40px">
                <i class="bi bi-search"></i>
            </div>
            <div class="col-md-6 text-right">
                         <button type="button" class="btn btn-primary mb-4" style="margin-right: 40px" data-toggle="modal" data-target="#addEmployeeModal">
                         <i class="bi bi-person-plus-fill"> </i>  Ajouter un employé
                        </button>
            </div>  
    </div>
    <?php }elseif ($_SESSION['user_type'] == 'chef') { ?> 
        <div class="container">
            <div class="row mx-4">
                <div class="col-md-6">
                <input type="text" id="myInput" onkeyup="myFunction()" placeholder=" Chercher Employé par CIN  ou Nom" title="Type in a name" style="width:70% ; margin-left: 40px">
                <i class="bi bi-search"></i>
                </div>
            </div>
    <?php } ?>
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
    <table id="myTable" class="table mt-4" style="width:100%">
        <thead class = "table-primary">
            <tr>
                <th > Image </th>
                <?php if ($_SESSION['user_type'] == 'chef') { ?>  
                    <th data-search="true">Nom complet</th>
                    <th data-order="true">Poste</th>
                    <th data-orderable="false">Email</th>
                    <th data-orderable="false">Téléphone</th>
                <?php }elseif ($_SESSION['user_type'] == 'employeur') { ?>  
                    <th data-search="true">CIN</th>
                    <th data-search="true">Nom complet</th>
                    <th data-order="true">Poste</th>
                    <th data-order="true">Date Embauche</th>
                    <th data-orderable="false">Email</th>
                    <th data-orderable="false">Téléphone</th>
                    <th data-orderable="false">Chef responsable </th>
                    <th data-orderable="false">Détails</th>

                <?php } ?>
                <?php if ($_SESSION['user_type'] == 'employeur') { ?>
                    <th data-orderable="false">Modifier</th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php
            // Parcourir les résultats de la requête et afficher chaque enregistrement dans une ligne du tableau
            while ($row = mysqli_fetch_assoc($result)) { ?>
             <tr>
                        <td> <?php if ($row['profil_img']) { ?>
                    <img src="images/profile/<?php echo $row['profil_img']; ?>" alt="Photo de profil" class="emp-image" >
                <?php } else { ?>
                    <img src="images/profil.png" alt="Photo de profil par défaut" class="emp-image">
                <?php } ?>   </td>
                        <?php if ($_SESSION['user_type'] == 'chef') { ?>
                            <td><?php echo $row['Nom'].' '.$row['prenom']; ?></td>
                            <td><?php echo $row['Poste']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['tele']; ?></td>

                        <?php }elseif ($_SESSION['user_type'] == 'employeur') { ?>
                            <td><?php echo $row['cin']; ?></td>
                            <td><?php echo $row['Nom'].' '.$row['prenom']; ?></td>
                            <td><?php echo $row['Poste']; ?></td>
                            <td><?php echo $row['DateEmbauche']; ?></td> 
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['tele']; ?></td>
                            <td><?php echo $row['chef_nom'].' '.$row['chef_prenom']; ?></td>
                            <td>
                                <a href="employee_profile.php?employee_id=<?php echo $row['employe_id']; ?>" class="btn btn-link btn-details">
                                    Voir profil
                                </a>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#editModal<?php echo $row['employe_id']; ?>">Modifier</button>
                                <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal<?php echo $row['employe_id']; ?>">Archiver</button>
                            </td>
                        </tr>

                        <!-- Modale de modification pour chaque employé -->
                        <div class="modal fade" id="editModal<?php echo $row['employe_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?php echo $row['employe_id']; ?>" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel<?php echo $row['employe_id']; ?>">Modifier l'employé - <?php echo $row['Nom'] . ' ' . $row['prenom']; ?></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" action="modifier_employe.php?id=<?php echo $row['employe_id']; ?>">
                                        <input type="hidden" name="id_entreprise" value ="<?php echo $row['id_entreprise']; ?>" >
                                            <div class="row">
                                                <div class="col">
                                                    <div class="form-group">
                                                        <label>Nom</label>
                                                        <input type="text" class="form-control" name="Nom" value="<?php echo $row['Nom']; ?>" readOnly>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="form-group">
                                                        <label>Prénom</label>
                                                        <input type="text" class="form-control" name="prenom" value="<?php echo $row['prenom']; ?>" readOnly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <div class="form-group">
                                                        <label>Poste</label>
                                                        <input type="text" class="form-control" name="Poste" value="<?php echo $row['Poste']; ?>" required>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="form-group">
                                                        <label>Date d'embauche</label>
                                                        <input type="date" class="form-control" name="date_embauche" value="<?php echo $row['DateEmbauche']; ?>" required>
                                                    </div>
                                                </div>
                                            </div> 
                                            <?php
                                                // Récupérer les autres employés pour la sélection du chef responsable
                                                $query_employes = "SELECT e.*, c.*, l.*
                                                FROM employe e
                                                JOIN chef c ON e.cin = c.cin
                                                JOIN login l ON c.email = l.email
                                                WHERE e.id_entreprise = '$entreprise_id'";
                                                $result_employes = mysqli_query($conn, $query_employes);
                                            ?>
                                            <div class="form-group">
                                                <label>Chef responsable</label>
                                                <select class="form-control" name="chef_id">
                                                    <option value="">Sélectionner un chef responsable</option>
                                                    <?php while ($row_employes = mysqli_fetch_assoc($result_employes)) { ?>
                                                        <option value="<?php echo $row_employes['chef_id']; ?>" <?php if ($row_employes['chef_id'] == $row['chef_id']) echo 'selected'; ?>>
                                                            <?php echo $row_employes['Nom'] . ' ' . $row_employes['prenom']; ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-primary" name="modifier_employe">Modifier</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modale de suppression pour chaque employé -->
                        <div class="modal fade" id="deleteModal<?php echo $row['employe_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel<?php echo $row['employe_id']; ?>" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel<?php echo $row['employe_id']; ?>">Archiver l'employé - <?php echo $row['Nom'] . ' ' . $row['prenom']; ?></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Êtes-vous sûr de vouloir supprimer cet employé de votre entreprise ?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <form method="POST" action="modifier_employe.php?id=<?php echo $row['employe_id']; ?>">
                                        <input type="hidden" name="id_entreprise" value ="<?php echo $row['id_entreprise']; ?>" >

                                            <button type="submit" class="btn btn-danger" name="supprimer_employe">Supprimer</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
          </tr>
            <?php }
            ?>
        </tbody>
    </table>

    <?php if ($totalPages > 1) { ?>
    <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
            <?php if ($page > 1) { ?>
                <li class="page-item">
                    <a class="page-link" href="?search=<?php echo $search; ?>&page=<?php echo $page - 1; ?>">Précédent</a>
                </li>
            <?php } ?>

            <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?search=<?php echo $search; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php } ?>

            <?php if ($page < $totalPages) { ?>
                <li class="page-item">
                    <a class="page-link" href="?search=<?php echo $search; ?>&page=<?php echo $page + 1; ?>">Suivant</a>
                </li>
            <?php } ?>
        </ul>
    </nav>
<?php } ?>
</div>


<!-- Modal pour les détails de l'employé -->
<div class="modal fade" id="requestDetailsModal" tabindex="-1" aria-labelledby="requestDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="requestDetailsModalLabel">Détails du personnel</h5>
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

<!-- Modal pour "ajouter employé"-->
<div class="modal fade" id="addEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addEmployeeModalLabel">Ajouter un employé</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="searchEmployeeForm">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="employeeEmail">Email de l'employé</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="employeeCIN">CIN de l'employé</label>
                        <input type="text" class="form-control" id="cin" name="cin" required>
                    </div>
                 </div>
            </div>
            <div class="text-center">
          <button type="submit" class="btn btn-primary mb-2">Chercher</button>
            </div>
        </form>
        <div id="searchResults" style="display: none;" class= "mt-2">
          <h5>Nom complet de l'employé cherché :</h5>
          <input type="hidden" id="employeeID" name="employeeID">
          <p id="employeeName"></p>
          <button type="button" class="btn btn-success" id="addEmployeeButton" name="addEmployeeButton">Cliquer ce boutton si vous voulez ajouter cet employé à votre entreprise </button>
        </div>
      </div>
    </div>
  </div>
</div>





<?php include('footer.php'); ?>
<script>
    // Ajouter employé 
$(document).ready(function() {
    // Soumettre le formulaire de recherche
    $('#searchEmployeeForm').submit(function(event) {
      event.preventDefault();
      var employeeEmail = $('#email').val();
      var employeeCIN = $('#cin').val();

      // Faire une requête AJAX pour rechercher l'employé dans la base de données
      $.ajax({
        url: 'search_employee.php',
        type: 'POST',
        data: {
          email: employeeEmail,
          cin: employeeCIN
        },
        success: function(response) {
          if (response.status === 'success') {
            // Afficher les résultats de la recherche
            $('#employeeID').val(response.employeeID);
            $('#employeeName').text(response.employeeName);
            $('#searchResults').show();
          } else {
            // Afficher un message d'erreur si l'employé n'est pas trouvé
            alert(response.message);
          }
        },
        error: function() {
          alert('Une erreur s\'est produite lors de la recherche de l\'employé.');
        }
      });
    });

    // Ajouter l'employé
    $('#addEmployeeButton').click(function() {
      // Faire une requête AJAX pour ajouter l'employé à la base de données
      $.ajax({
        url: 'add_employee.php',
        type: 'POST',
        data: {
          email: $('#email').val(),
          employeeID: $('#employeeID').val()

        },
        success: function(response) {
          if (response.status === 'success') {
            alert('L\'employé a été ajouté avec succès.');
             // Actualiser la page après l'alerte
             location.reload();
            // Masquer le modal
            $('#addEmployeeModal').modal('hide');
          } else {
            alert(response.message);
          }
        },
        error: function() {
          alert('Une erreur s\'est produite lors de l\'ajout de l\'employé.');
        }
      });
    });
  });


document.addEventListener('DOMContentLoaded', function () {
        const modal = new bootstrap.Modal(document.getElementById('requestDetailsModal'));
        const modalContent = document.getElementById('requestDetailsContent');

        const btnDetails = document.querySelectorAll('.btn-details');

        btnDetails.forEach(btn => {
            btn.addEventListener('click', function () {
                const requestId = this.getAttribute('data-request-id');
                fetch('details_perso.php?id=' + requestId)
                    .then(response => response.text())
                    .then(data => {
                        modalContent.innerHTML = data;
                        modal.show();
                    });
            });
        });
    });
 // Ajouter chef 
 

</script>