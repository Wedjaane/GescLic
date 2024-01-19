<?php
include('server.php');
// Connexion à la base de données
include('db.php');

// Récupérer les informations de l'utilisateur depuis la base de données
$userId = $_SESSION['user_id'];
$sql = "SELECT * FROM employe WHERE id = $userId";
$result = mysqli_query($conn, $sql);
$userData = mysqli_fetch_assoc($result);

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données soumises
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    // ... récupérer d'autres données du formulaire ...

    // Vérifier si l'utilisateur a déjà des informations dans la base de données
    if (!$userData) {
        // Si l'utilisateur n'a pas d'informations, insérer les données dans la base de données
        $insertQuery = "INSERT INTO employe (nom, prenom) VALUES ('$nom', '$prenom')";
        mysqli_query($conn, $insertQuery);
        // Vous pouvez ajouter d'autres champs à insérer ici
    } else {
        // Si l'utilisateur a déjà des informations, mettre à jour les données dans la base de données
        $updateQuery = "UPDATE employe SET nom = '$nom', prenom = '$prenom' WHERE id = $userId";
        mysqli_query($conn, $updateQuery);
        // Vous pouvez ajouter d'autres champs à mettre à jour ici
    }

    // Rediriger l'utilisateur vers la page de profil
    header("Location: employe.php");
    exit();
}

?>

<!-- Le reste de votre code HTML et formulaire -->
