<?php
session_start();
include('server.php');
// Check if the user is logged in as an employee
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'employee' && $_SESSION['user_type'] !== 'employeur') {
    // If not logged in as an employee, redirect to the login page
    header("Location: login.php");
    exit();
}?>
<?php include('head.php') ?>

  <header class=" text-center py-5 mt-5 " >
    <div class="container">
      <br><h4 >Les nouvelles demandes reçues  !</h4>
    </div>
  </header>
  <div class="container">
    <table id="table_dmd" class="table table-striped table-hover custom-table mb-4" style="width:100%">
        <thead>
            <tr>
                <th data-order="true">N° Demande</th>
                <th data-search="true">Type de la demande</th>
                <th data-search="true">Date de la demande</th>
                <th data-orderable="false" >Consulter</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td> </td>
                <td> </td>
                <td>  </td>
                <td>  </td>

            </tr>
           
            <!-- Ajouter d'autres lignes ici si nécessaire -->
        </tbody>
    </table><br>
    <div class="d-flex justify-content-start mt-4">
                    <a href="demandes_recues.php" class="btn btn-primary" style="background-color:#FFA500; color:#000">Consulter tous les  demandes reçues </a>
                </div>
</div><br>


   <?php include('footer.php') ; ?>