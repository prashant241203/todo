<?php 

    session_start();
    require_once('connect.php');

    $show_model = false;
    
    $emptyemail = "";
    $notfoundemail = "";

    if(isset($_POST['search'])) {
      
      $email = $_POST['email'];

      if(empty($email)){
           $emptyemail =  "Enter email address";
      }else{
          $query = $conn->prepare("SELECT id,email FROM users WHERE email = ?");
          $query->bind_param("s",$email);
          $query->execute();
              
          $result = $query->get_result();
          
          if($result->num_rows > 0) {
            $show_model = true;
            $user = $result->fetch_assoc();
            $_SESSION['reset_id'] = $user['id'];
          }else{
            $notfoundemail = "email id not found";
          }
      }
    }

    if(isset($_POST['resetpassword'])) {

         $password = $_POST['password'];
         $confirmpassword = $_POST['confirmpassword'];

         if ($password !== $confirmpassword) {
            echo "password do not match";
          }else if(empty($password)){
            echo "Please Enter password";
          }else if(strlen($password) < 8){
              echo "password must be 8 or more characters";
          }else if(!preg_match('/[A-Z]/',$password) || !preg_match('/[0-9]/',$password) || !preg_match('/[!@#$%^&*()_]/',$password)){
             echo "you password must have 1 Capital letter , digit and special character";
          }else if(empty($password)){
            echo "Enter your password";
         }
         else{
          $id = $_SESSION['reset_id'];  

          $query = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
          $query->bind_param("si",$password,$id);
          $query->execute();

          $_SESSION['success'] = "your password is updated";
          header('Location:login.php');
         }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password</title>
  <style>
    * {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: Arial, Helvetica, sans-serif;
}

body {
  background-color: #f0f2f5;
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
}

.container {
  width: 100%;
  display: flex;
  justify-content: center;
}

.card {
  background: #fff;
  width: 400px;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.card h2 {
  font-size: 20px;
  margin-bottom: 10px;
  color: #1c1e21;
}

.card p {
  font-size: 14px;
  color: #606770;
  margin-bottom: 15px;
}

.card input {
  width: 100%;
  padding: 12px;
  font-size: 15px;
  border: 1px solid #dddfe2;
  border-radius: 6px;
  margin-bottom: 20px;
}

.card input:focus {
  outline: none;
  border-color: #1877f2;
}

.buttons {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}
.buttons a{
    color:black;
    text-decoration:none;
}
.cancel {
  background: #e4e6eb;
  border: none;
  padding: 10px 16px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: bold;
}

.search {
  background: #1877f2;
  color: #fff;
  border: none;
  padding: 10px 18px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: bold;
}

.search:hover {
  background: #166fe5;
}

.modal {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  height: 100%;
  width: 100%;
  background: rgba(0,0,0,0.5);
  justify-content: center;
  align-items: center;
}

.modal-content {
  background: #fff;
  padding: 20px;
  width: 350px;
  border-radius: 8px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

.modal-content h3 {
  margin-bottom: 15px;
}

.modal-content input {
  width: 100%;
  padding: 10px;
  margin-bottom: 15px;
  border-radius: 6px;
  border: 1px solid #ddd;
}
  </style>
</head>
<body>

  <div class="container">
    <form action="" method="post">
      <div class="card">
      <h2>Find Your Account</h2>
      <p>Please enter your email address or mobile number to search for your account.</p>

      <small style="color:red"><?= $notfoundemail ?></small>
      <small style="color:red"><?= $emptyemail ?></small>
      <input type="email" placeholder="Email address" name="email">
    
      <div class="buttons">
        <button type="submit" class="cancel" ><a href="login.php">Cancel</a></button>
        <button class="search" name="search" id="openModal">Search</button>
      </div>
    </div>
    </form>
  </div>
      
  <form  method="post">
    <div id="passwordModal" class="modal">
      <div class="modal-content">
        <h3>Reset Password</h3>
        
        <input type="password" placeholder="New Password" name="password">
        <input type="password" placeholder="Confirm Password" name="confirmpassword">

        <button class="search" type="submit" name="resetpassword">Update Password</button>
      </div>
    </div>
  </form>

<script>
  
  const modal = document.getElementById("passwordModal");
    
  <?php if($show_model): ?>
    modal.style.display = "flex";
  <?php endif; ?>


</script>

</body>
</html>
