<?php
    require_once('connect.php');
    $allfields = "";
    $success = false;

    if(isset($_POST['signup'])) {

        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        $target_dir = "upload/";
        $target_file = $target_dir . basename($_FILES['profile_pic']['name']);
        // $imagefiltype = strtolower(pathinfo($target_file , PATHINFO_EXTENSION));
        // echo $target_file;
        // exit();

            if(move_uploaded_file($_FILES['profile_pic']['tmp_name'],$target_file)) {
                $file = $target_file;
            }
            if(empty($name) && empty($email) && empty($password) && empty($file)){
                $allfields =  "Some Fields are Empty";
            }else if(empty($name)){
                 $msg['name'] =  "Please Enter Name";
            }else if(empty($email)){
                 $msg['email'] =  "Please Enter email";
            }else if(empty($password)){
                 $msg['password'] = "Please Enter password";
            }else if(empty($file)){
                 $msg['profile_pic'] =  "Please select file";
            }else{
                $query = $conn->prepare("INSERT INTO users (name,email,password,profile_pic) VALUES (? ,?, ?, ?)"); 
                $query->bind_param("ssss",$name,$email,$password,$file);
                $query->execute();
                $success = true;
                header('Location:login.php');
            }      
        }                                                                
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register - ToDo App</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container" style="justify-content:center;align-items:center;display:flex;">
  <form  method="post" style="background:#fff;padding:30px;border-radius:15px;width:350px;box-shadow:0 5px 15px rgba(0,0,0,0.1);" enctype="multipart/form-data">
    <h2 style="text-align:center;margin-bottom:20px;">Register</h2>

    <small style="color:red"><?= $allfields ?></small>

    <?php if(isset($msg['name'])): ?>
          <small style="color:red"><?=  $msg['name'] ?></small>
    <?php endif; ?>  
    <input type="text" name="name" placeholder="Full Name" value="<?= ($success) ? '' : $_POST['name'] ?? '' ?>">

    <?php if(isset($msg['email'])): ?>
          <small style="color:red"><?= $msg['email'] ?></small>
    <?php endif; ?>
    
    <input type="email" name="email" placeholder="Email" value="<?= ($success) ? '' : $_POST['email'] ?? '' ?>">
    
      <?php if(isset($msg['password'])): ?>
          <small style="color:red"><?= $msg['password'] ?></small>
      <?php endif; ?>
    <input type="password" name="password" placeholder="Password" value="<?= ($success) ? '' : $_POST['password'] ?? '' ?>">

    <?php if(isset($msg['profile_pic'])): ?>
          <small style="color:red"><?= $msg['profile_pic'] ?></small>
    <?php endif; ?>
    <input type="file" name="profile_pic" value="<?= ($success) ? '' : $_POST['profile_pic'] ?? '' ?>">

    <button type="submit" name="signup">Register</button>
    
    <p style="text-align:center;margin-top:10px;">
      Already have an account? <a href="login.php">Login</a>
    </p>
  </form>
</div>
</body>
</html>
