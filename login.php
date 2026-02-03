<?php

    session_start();
    require_once('connect.php');
    $emptymail = "";
    $invalmail = "";

    $success = false;
   
    if(isset($_POST['login'])){

        $email = $_POST['email'];
        $password = $_POST['password'];

          if (empty($email) && empty($password)) {
              $emptymail = "Enter email and password";
          }else if (empty($email)){
               $msg['email'] =  "Please Enter email"; 
          }
          else if (empty($password)){
              $msg['password'] = "Please Enter password";
          }
          else{
              $query = $conn->prepare("SELECT email FROM users WHERE email = ? AND  password = ?");
              $query->bind_param("ss",$email,$password);
              $query->execute();
              $result = $query->get_result();

              if ($result->num_rows > 0){
                $_SESSION['user_email'] = $email;
                header('Location:index.php');
                $success = true;
              }else{
                $invalmail = "Invalid email or password";
              }
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - ToDo App</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container" style="justify-content:center;align-items:center;display:flex;">
  <form  method="post" style="background:#fff;padding:30px;border-radius:15px;width:350px;box-shadow:0 5px 15px rgba(0,0,0,0.1);">
    <h2 style="text-align:center;margin-bottom:20px;">Login</h2>
    <small style="color:red;"><?=$emptymail ?></small>
    <small style="color:red;"><?=$invalmail ?></small>
      
     <?php if(isset($msg['email'])): ?>
          <small style="color:red"><?= $msg['email'] ?></small>
    <?php endif; ?>
    <input type="email" name="email" placeholder="Email" value="<?= ($success) ? '' : $_POST['email'] ?? '' ?>">

     <?php if(isset($msg['password'])): ?>
          <small style="color:red"><?= $msg['password'] ?></small>
     <?php endif; ?>
    <input type="password" name="password" placeholder="Password" value="<?= ($success) ? '' : $_POST['password'] ?? '' ?>">

    <button type="submit" name="login">Login</button>
    <p style="text-align:center;margin-top:10px;">
      <a href="register.php">Register</a> | <a href="forgot.php">Forgot Password?</a>
    </p>
  </form>
</div>
</body>
</html>
