<?php
// Inclure votre fichier de configuration de la base de données ici
include('server.php');

// Vérifier si l'ID de la demande est passé en POST
if (isset($_GET['requestId'])) {
    $requestId = $_GET['requestId'];

    // Échapper l'ID de la demande pour éviter les injections SQL (si nécessaire)
    $requestId = mysqli_real_escape_string($conn, $requestId);

    // Effectuer une requête SQL pour obtenir le statut de la demande
    $query = "SELECT Statut FROM demande WHERE N_dmd = '$requestId'";

    $result = mysqli_query($conn, $query);

    if ($result) {
        if ($row = mysqli_fetch_assoc($result)) {
            $statut = $row['Statut'];

            if ($statut === 'prise') {
                // Si le statut est "prise", renvoyer un message d'erreur
                echo "<script>alert('La demande est déjà marquée comme prise.');</script>";
                echo "<script>window.location.href='demandes_recues.php'</script>";
        
            } else {
                // Sinon, effectuer la mise à jour du statut ici
                // Exemple : Mettre à jour le statut de la demande à "prise"
                $updateQuery = "UPDATE demande SET Statut = 'prise' WHERE N_dmd = '$requestId'";
                $updateResult = mysqli_query($conn, $updateQuery);

                if ($updateResult) {
                    // La demande a été mise à jour avec succès
                    echo "<script>alert('Demande mis à jour avec succès');</script>";
                    echo "<script>window.location.href='demandes_recues.php'</script>";
            
                } else {
                    // En cas d'erreur lors de la mise à jour, renvoyer un message d'erreur
                    echo "<script>alert('Erreur lors de la mise à jour de la demande :');</script>";
                    echo "<script>window.location.href='demandes_recues.php'</script>";
            
                }
            }
        } else {
            // Si la demande n'a pas été trouvée, renvoyer un message d'erreur
            echo "<script>alert('Demande non trouvée');</script>";
            echo "<script>window.location.href='demandes_recues.php'</script>";
    
        }
    } else {
        // En cas d'erreur lors de la requête SQL, renvoyer un message d'erreur
        echo "<script>alert('Erreur  :');</script>";
        echo "<script>window.location.href='demandes_recues.php'</script>";

    }

    // Fermer la connexion à la base de données
    mysqli_close($conn);
} else {
    // Si l'ID de la demande n'est pas passé en POST, renvoyer un message d'erreur
    echo "<script>alert('ID de demande non spécifié');</script>";
    echo "<script>window.location.href='demandes_recues.php'</script>";

}
?>
