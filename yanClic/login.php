<?php include('server.php') ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-jBq2Ei5fYBCX+W8Ol0vlpSG6IkC8eAty6Kw0Kc/IfvrpBnfnwHNK1Bm/GeTuAxvfVXqQ1zC8JGu+QGnTtXryXQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="css/landing.css">
    <title>HR_GESCLIC </title>
</head>
<body style="background-color: #eee;">

<section class="gradient-form" >
  <div class="container mt-5 ">
    <div class="row d-flex justify-content-center align-items-center ">
      <div class="col-xl-10">
        <div class="card rounded-3 text-black">
          <div class="row ">
          <div class="col-lg-4 d-flex align-items-center gradient-custom-2" style="justify-content: center;">
              <div class="text-white text-center mb-3 py-4 p-md-2 mx-md-4">
                <h4 class="mb-4">Bienvenue <br> <br>sur HR GesClic</h4>
              </div>
            </div>
            <div class="col-lg-8">
              <div class="card-body p-md-4 mx-md-4">
                <div class="text-center">
                    <div class="navbar-logo"  href="index.php"><img  src="images/logo2.png" alt="GesClic"></a></div>
                    <div class="log1 mb-4" style="text-align: center">Ges<span style="color: #FFA500">Clic</span></div>
                </div>
                  <p class="text-center fw-bold p-0 m-0" style="font-size: 20px">Veuillez vous connecter pour accéder à votre compte :</p> 
                  <form method="POST"  action="<?php echo $_SERVER['PHP_SELF']; ?>"  class="form1" >
                     <?php if (isset($errors['login'])): ?>
                          <span id="loginError" class="error-message"><?php echo $errors['login']; ?></span>
                      <?php endif; ?>
                      <div class="mb-3">
                            <div class="form-group">
                                <label for="formGroupExampleInput" class="form-label">Email :</label>
                                <div class="input-container">
                                  <i class="bi bi-envelope icon"></i>
                                  <input type="text" class="form-control" id="email" name="email"  placeholder="Email" value="<?php if(isset($_POST["email"])) echo  $_POST["email"];?>" aria-label="email"  aria-describedby="emailError" required >
                                </div>
                            <?php if (isset($errors['email'])): ?>
                                <span id="emailError" class=" error-message"><?php echo $errors['email']; ?></span>
                            <?php endif; ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-group">
                                <label for="formGroupExampleInput" class="form-label">Mot de passe :</label>
                            <div class="input-container">
                                <i class="bi bi-lock icon"></i>
                                <input type="password" class="form-control " name="password" id="password" placeholder="Mot de passe" value="<?php if(isset($_POST["password"])) echo  $_POST["password"];?>"  aria-describedby="passwordError"required>
                            </div>
                            <?php if (isset($errors['password'])): ?>
                                <div id="passwordError" class="error-message"><?php echo $errors['password']; ?></div>
                            <?php endif; ?>
                            </div>
                        </div>
                        <div class="row">
                          <div class="col">
                            <div class="form-check">
                                <!-- Ajoutez un attribut "checked" si le cookie existe pour "Se souvenir de moi" -->
                                <input type="checkbox" id="rememberMe" class="form-check-input" name="rememberMe" <?php if (isset($_COOKIE['remembered_user'])) echo 'checked'; ?>>
                                <label class="form-check-label" for="rememberMe">Se souvenir de moi</label>
                            </div>
                          </div>
                          <div class="col justify-content-md-end  d-md-flex ">
                            <a  href="#!">Mot de passe oublié ?</a>
                          </div>
                        </div>
                        <div class=" text-center pt-1  pb-1">
                             <button name="login_button" class="btn btn-primary   mb-3" type="submit" style="background: #1D3461;border-radius: 39px ;width: 164px ">Se connecter </button>
                        </div>
                        <div class="text-center mt-3">
                            <span class="create-account-text">Vous ne disposez pas d’un compte ?</span>
                            <a  class="create-account-link" href="signup.php">Créer un maintenant !</span>
                        </div>
                 </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

      <?php include('footer.php') ?>


