<?php
session_start();
include('server.php');
// Check if the user is logged in as an employee
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'employee') {
    // If not logged in as an employee, redirect to the login page
    header("Location:login.php");
    exit();
}?>
<?php include('head.php') ?>

  <header class=" text-center py-5 mt-5 " >
    <div class="container">
      <br><h1 >Bienvenue dans l'application GesClic !</h1>  <br>
      <p class="lead">L'outil parfait pour gérer vos tâches en ligne.</p>
    </div>
  </header>

    <div class="container py-5">
      <div class="row">
      <div class="col-md-6">
          <div class="card mb-3">
            <div class="card-body">
              <h5 class="card-title">Pointer votre entrée</h5>
              <p class="card-text">Cliquez sur le bouton ci-dessous pour enregistrer votre heure d'entrée.</p>
              <button type="button" class="btn btn-orange" data-toggle="modal" data-target="#clockInModal">Pointer maintenant !</button>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card mb-3">
            <div class="card-body">
              <h5 class="card-title">Pointer votre sortie</h5>
              <p class="card-text">Cliquez sur le bouton ci-dessous pour enregistrer votre heure de sortie.</p>
              <button type="button" class="btn btn-orange" data-toggle="modal" data-target="#clockOutModal">Pointer maintenant !</button>
            </div>
          </div>
        </div>
      </div>
    </div>

   <!-- Modal pour pointer l'entrée -->
<div class="modal fade" id="clockInModal" tabindex="-1" role="dialog" aria-labelledby="clockInModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="clockInModalLabel">Pointer votre entrée</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Formulaire pour enregistrer l'heure d'entrée -->
        <form action="enregistrer_heures.php" method="post">
        <div class='row'>
              <div class="col">
                  <div class="form-group">
                    <label>Date : </label>
                    <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" readonly>
                  </div>
              </div>
              <div class="col">
                  <div class="form-group">
                    <label>Heure actuelle : </label>
                    <input type="time" name="heure_actuelle" class="form-control current-time" readonly>
                  </div>
              </div>
            </div>
            <div class="form-group">
                    <label>Heure d'entrée' : </label>
                    <input type="time" name="h_entree" class="form-control current-time" >
            </div>
           <!-- Ajouter l'input caché pour l'action -->
           <input type="hidden" name="action" value="clock_in">
           <div class="text-center">
              <button type="submit" class="btn btn-primary">Enregistrer l'entrée</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal pour pointer la sortie -->
<div class="modal fade" id="clockOutModal" tabindex="-1" role="dialog" aria-labelledby="clockOutModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="clockOutModalLabel">Pointer votre sortie</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Formulaire pour enregistrer l'heure de sortie -->
        <form action="enregistrer_heures.php" method="post">
            <div class='row'>
              <div class="col">
                  <div class="form-group">
                    <label>Date : </label>
                    <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" readonly>
                  </div>
              </div>
              <div class="col">
                  <div class="form-group">
                    <label>Heure actuelle : </label>
                    <input type="time" name="heure_actuelle" class="form-control current-time" readonly>
                  </div>
              </div>
            </div>
            <div class="form-group">
                    <label>Heure de sortie: </label>
                    <input type="time" name="h_sortie" class="form-control current-time" >
            </div>
            <!-- Ajouter l'input caché pour l'action -->
            <input type="hidden" name="action" value="clock_out">
            <div class="text-center">
              <button type="submit" class="btn btn-primary">Enregistrer la sortie</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  // Fonction pour obtenir l'heure locale du navigateur et la mettre à jour dans les champs de l'heure actuelle
  function updateCurrentTimeFields() {
    const currentTime = new Date().toLocaleTimeString('fr-FR', { hour12: false, timeZone: 'Africa/Casablanca', hour: '2-digit', minute: '2-digit' });
    const currentInputFields = document.querySelectorAll('.current-time');
    currentInputFields.forEach(input => (input.value = currentTime));
  }

  // Mettre à jour l'heure actuelle au chargement de la page
  updateCurrentTimeFields();

  // Mettre à jour l'heure actuelle toutes les 60 secondes pour qu'elle reste à jour
  setInterval(updateCurrentTimeFields, 60000);
</script>
<?php include('footer.php') ?>
