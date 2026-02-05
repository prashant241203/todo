<?php
    session_start();
     require_once('connect.php');

    if (!isset($_SESSION['id'])) {  
        echo "Error";
        exit;
    }
    $id = $_SESSION['id'];

    $fetchedata = $conn->prepare("SELECT name,email,profile_pic FROM users WHERE id = ?");
    $fetchedata->bind_param("i",$id);
    $fetchedata->execute();
    $result = $fetchedata->get_result();

    if ($result->num_rows == 0) { 
      echo "No user found";
    }
    $row = $result->fetch_assoc();

    if(isset($_POST['update'])){

          $name = $_POST['name'];
          $email = $_POST['email'];
          $category_id = $_POST['category'];
          $profile_pic = $row['profile_pic'];

          if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
                
                if (!is_dir("upload")) mkdir("upload",0777,true);
                $profile_pic = basename($_FILES['profile_pic']['name']);
                move_uploaded_file($_FILES['profile_pic']['tmp_name'],'upload/'. $profile_pic);
          }
          
            $query = $conn->prepare("UPDATE users SET name = ?,email = ? ,profile_pic = ?  WHERE id = ?"); 
            $query->bind_param("sssi",$name,$email,$profile_pic,$id);
            
            if ($query->execute()){
              // echo "<script>alert('record updated.')</script>";
              header('Location:index.php');
            }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profile - ToDo App</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">

  <div class="sidebar">
    <h2>ToDo App</h2>
    <a href="index.php">Tasks</a>
    <a href="profile.php">Profile</a>
    <a href="logout.php">Logout</a>
    <a href="deletedtask.php">Deleted Tasks</a>
  </div>
  
  <div class="main">
    <h1>Edit Profile</h1>
    <form class="profile-section" method="post" enctype="multipart/form-data">
      
      <input type="text" placeholder="Full Name" name="name" value=<?= $row['name'] ?>>
      <input type="email" placeholder="Email"  name="email" value=<?= $row['email'] ?>>

      <?php if(!empty($row['profile_pic'])): ?>
      <img src="upload/<?= ($row['profile_pic'])?>" alt="" style="width:100px;height:100px;">
      <?php endif; ?>
      <input type="file" name="profile_pic">  

      <button type="submit" name="update">Update Profile</button>
       
    </form>
  </div>

</div>
</body>
</html>
