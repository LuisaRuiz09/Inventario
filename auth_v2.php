<?php include_once('includes/load.php'); ?>
<?php
$req_fields = array('username','password' );
validate_fields($req_fields);
$username = remove_junk($_POST['username']);
$password = remove_junk($_POST['password']);

  if(empty($errors)){

    $user = authenticate_v2($username, $password);

        if($user):
           //crear secion con id
           $session->login($user['id']);
           //Actualizar tiempo de inicio
           updateLastLogIn($user['id']);
           // redirigir al usuario a la página de inicio del grupo por nivel de usuario
           if($user['user_level'] === '1'):
             $session->msg("s", "Hola".$user['username'].", Bienvenido a su inventario.");
             redirect('admin.php',false);
           elseif ($user['user_level'] === '2'):
              $session->msg("s", "Hola ".$user['username'].", Bienvenido a su inventario.");
             redirect('special.php',false);
           else:
              $session->msg("s", "Hola ".$user['username'].", Bienvenido a su inventario.");
             redirect('home.php',false);
           endif;

        else:
          $session->msg("d", "Lo sentimos, Usuario o contraseña incorrectos");
          redirect('index.php',false);
        endif;

  } else {

     $session->msg("d", $errors);
     redirect('login_v2.php',false);
  }

?>
