<?php include('head_lan.php') ?>

<div class="banner" style="background-image: linear-gradient(0deg, rgba(0, 0, 0, 0.60) 0%, rgba(0, 0, 0, 0.60) 100%), url('images/c.jpg')">
        <div class="banner-content">
            <div class="Headline " style="color: white">Une question ou une demande ?  <br>Notre équipe est toujours prête à vous aider</div>
        </div>
</div><hr>
<div class="container-fluid mb-3 mt-2" style="padding: 48px;" >
          <div class="Headline" style="color: black; font-size: 26px"> Contactez-nous pour tout renseignement supplémentaire !<br></div>
</div>
<div class="container-fluid" style="font-size: 18px;" >
        <div class="row  "style="margin-inline: 45px">
            <div class="col">
                 <div class="image-container mb-4"   >
                <img src='images/contactus.jpg' style="max-width: 100%; max-height: 100% ; border: 1px solid #000;"> 
            </div>   
           <h4 style="text-align: center;">Informations sur la société :</h4>
           <div class="info" style="margin-top:35px">
                <div class="row">
                        <div class="col" style="font-weight: bold;">Nom de la société:</div>
                        <div class="col">YanClic</div>
                    </div><br>
                   
                    <div class="row">
                        <div class="col" style="font-weight: bold;">Téléphone :</div>
                        <div class="col">00212.5.28.83.30.43</div>
                    </div><br>
                    <div class="row">
                        <div class="col" style="font-weight: bold;">Email :</div>
                        <div class="col">contact@yanclic.ma</div>
                    </div><br>
                    <section class="mb-4" style="text-align: center ">
                    <a class="btn btn-outline-dark btn-floating m-1" href="https://www.facebook.com/YanClicOfficial" role="button"><i class="bi bi-facebook"></i></a>
                    <a class="btn btn-outline-dark btn-floating m-1" href="https://www.linkedin.com/company/yanclic" role="button"><i class="bi bi-linkedin"></i></a>
                    <a class="btn btn-outline-dark btn-floating m-1" href="mailto:contact@yanclic.ma" role="button"><i class="bi bi-google"></i></a>
                    </section>
                </div>
            </div> 
             <!-- Form section -->
            <div class="col-md-8">
                 <div class="items mt-2 ml-4">
                <h4 style="text-align : center " >Dites-nous ce dont vous avez besoin !</h4><br>
                <?php
                if(isset($_POST['submit'])) {
                    if (empty($_POST["message"])) {
                        echo '<div class="alert alert-danger">Veuillez indiquer votre problème ou message ! .</div>';
                      } else {
                    $to = "contact@yanclic.ma"; // Adresse email pour recevoir le formulaire
                    $subject = "Formulaire de contact"; // Sujet du message
                    $name = $_POST['name'];
                    $prenom = $_POST['prenom'];
                    $email = $_POST['email'];
                    $message = $_POST['message'];
                    $headers = "From: ".$name." <".$email.">\r\n";
                    $headers .= "Reply-To: ".$email."\r\n";
                    $headers .= "Content-type: text/html\r\n";
                    // Message à envoyer
                    $message = "<html>
                    <head>
                    <title>".$subject."</title>
                    </head>
                    <body>
                    <p>Nouveau message de contact</p>
                    <table>
                    <tr><td>Nom:</td><td>".$name." ".$prenom."</td></tr>
                    <tr><td>Email:</td><td>".$email."</td></tr>
                    <tr><td>Message:</td><td>".$message."</td></tr>
                    </table>
                    </body>
                    </html>";
                    // Envoi du message
                    if(mail($to, $subject, $message, $headers)) {
                        echo '<div class="alert alert-success">Votre message a été envoyé avec succès.</div>';
                    } else {
                        echo '<div class="alert alert-danger">Erreur lors de l\'envoi du message. Veuillez réessayer plus tard.</div>';
                          }
                    }
                 }
                ?>
                <div class="form1">
                     <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <div class="row">
                            <div class="col">
                            <div class="form-group">
                                <label for="name">Nom:</label>
                                <input type="text" class="form-control" id="name" name="name">
                            </div>
                            </div>
                            <div class="col">
                            <div class="form-group">
                                <label for="name">Prénom:</label>
                                <input type="text" class="form-control" id="prenom" name="prenom">
                            </div>
                            </div>
                         </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                            <div class="form-group">
                                <label for="message">Message:</label>
                                <textarea class="form-control" id="message" name="message" rows="5"></textarea>
                            </div><br>
                            <div class="text-center">
                                <button type="submit" name="submit" class="btn btn-primary">Envoyer</button>
                                <button type="reset" name="reset" class="btn btn-secondary ml-4">Annuler</button>
                            </div>
                    </form>
                </div>
                </div>   
            </div>
        </div>
    </div>
</div>

    <?php include('footer.php') ?>
