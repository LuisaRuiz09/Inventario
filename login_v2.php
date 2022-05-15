<?php
  ob_start();
  require_once('includes/load.php');
  if($session->isUserLoggedIn(true)) { redirect('home.php', false);}
?>

<div class="login-page">
    <div class="text-center">
       <h1>Bienvenido</h1>
       <p>Sign in para empezar</p>
     </div>
     <?php echo display_msg($msg); ?>
      <form method="post" action="auth_v2.php" class="clearfix">
        <div class="form-group">
              <label for="username" class="control-label">Nombre de usuario</label>
              <input type="name" class="form-control" name="username" placeholder="Nombre de usuario">
        </div>
        <div class="form-group">
            <label for="Password" class="control-label">Contraseña</label></label>
            <input type="password" name= "password" class="form-control" placeholder="password">
        </div>
        <div class="form-group">
                <button type="submit" class="btn btn-info  pull-right">Ingresar</button>
        </div>
    </form>
</div>
<?php include_once('layouts/header.php'); ?>
