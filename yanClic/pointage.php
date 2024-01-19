<?php
include('server.php');
include('db.php');
$employeeEmail = $_SESSION['email'];
include('head.php');
?>

<header class="text-center py-5 mt-5">
    <div class="container">
        <!-- Contenu de l'en-tête si nécessaire -->
    </div>
</header>

<div class="container">
    <div class="menu">
        <button id="monthlyBtn">Pointage Mensuel</button>
        <button id="annualBtn">Pointage Annuel</button>
        <button id="hourlyBtn">Pointage Horaire</button>
    </div>
    <div class=" text-center ">
    <!-- Contenu des tables -->
      <?php include('monthly_table.php'); ?>
      <?php include('annual_table.php'); ?>
      <?php include('hourly_table.php'); ?>
      <div  style="columns: 4;">
      <ul  class=" mt-4">
        <li><strong>Ab :</strong> Absent</li>
        <li><strong>Co :</strong> Congé</li>
        <li><strong>Dé :</strong> Déplacement professionnel</li>
        <li><strong>Fo :</strong> Formation</li>
        <li><strong>Ma :</strong> Maladie</li>
        <li><strong>Mi :</strong> Mission</li>
        <li><strong>Té :</strong> Télétravail</li>
      </ul>
</div>
    </div>
    

</div>

<script>
       // Pointage table 
        // Variables globales pour les formulaires
      const monthlyForm = document.getElementById("monthlyForm");
      const annualForm = document.getElementById("annualForm");
      const hourlyForm = document.getElementById("hourlyForm");
      
document.addEventListener("DOMContentLoaded", function () {
      // Show the monthly attendance table by default
      showTable("monthly");
      setActiveButton("monthlyBtn");
      // Add event listeners to the menu buttons
      document.getElementById("monthlyBtn").addEventListener("click", function () {
        showTable("monthly");
        setActiveButton("monthlyBtn");
      });
      document.getElementById("annualBtn").addEventListener("click", function () {
        showTable("annual");
        setActiveButton("annualBtn");
      });
      document.getElementById("hourlyBtn").addEventListener("click", function () {
        showTable("hourly");
        setActiveButton("hourlyBtn");
      });

      // Function to show the selected table and hide the others
      function showTable(tableType) {
        const tables = document.querySelectorAll("table");
        const titles = document.querySelectorAll("h4");
        // Masquer tous les formulaires par défaut
          monthlyForm.style.display = "none";
          annualForm.style.display = "none";
          hourlyForm.style.display = "none";

          for (let i = 0; i < tables.length; i++) {
              if (tables[i].id === tableType + "Table") {
                tables[i].style.display = "table";
                titles[i].style.display = "block";
              } else {
                tables[i].style.display = "none";
                titles[i].style.display = "none";
              }
          }
          // Afficher le formulaire correspondant au type de pointage sélectionné
        if (tableType === "monthly") {
              monthlyForm.style.display = "block";
        } else if (tableType === "annual") {
              annualForm.style.display = "block";
        } else if (tableType === "hourly") {
              hourlyForm.style.display = "block";
        }
      }
        // Function to set the active button
      function setActiveButton(buttonId) {
        const buttons = document.querySelectorAll(".menu button");
        buttons.forEach((button) => {
            if (button.id === buttonId) {
              button.classList.add("active");
            } else {
              button.classList.remove("active");
            }
         });
      }
});
  </script>

<?php include('footer.php'); ?>






