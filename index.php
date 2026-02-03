<?php
    session_start();
    require_once('connect.php');

    if (!isset($_SESSION['user_email'])) {
        header('Location:login.php');
        exit;
    }
    
    $email = $_SESSION['user_email']; 

    $fetchemail = $conn->prepare("SELECT id,name FROM users WHERE email = ?");
    $fetchemail->bind_param("s",$email);
    $fetchemail->execute();
    $result = $fetchemail->get_result();

    if($result->num_rows > 0){
        $user = $result->fetch_assoc();
        $username = $user['name'];
        $userid = $user['id'];
        $_SESSION['id'] = $userid; 
   }else{
        die("user not found");
   }

    $msg = [];
    if (isset($_POST['addtask'])) {
         
            $title = $_POST['title'];
            $deadline = $_POST['deadline'];
            $categoryname = $_POST['category'];
            $priority = $_POST['priority'];
              
            if (empty($title)) {
             $msg['title'] = "please enter title";
            }else if(empty($deadline)){
              $msg['deadline'] =  "select deadline";
            }else if(empty($categoryname)){
              $msg['category'] =  "please select category";
            }else if(empty($priority)){
              $msg['priority'] =  "please select priority";
            }

            if (empty($msg)) {
                $catcheck = $conn->prepare("SELECT id FROM category WHERE user_id = ? AND name = ?");
                $catcheck->bind_param("is",$userid,$categoryname);
                $catcheck->execute();
                $catresult = $catcheck->get_result();
              
            if ($catresult->num_rows > 0) {
                $category_id = $catresult->fetch_assoc()['id'];
            }else {
                 $insertCat = $conn->prepare( "INSERT INTO category (user_id, name) VALUES (?, ?)");
                 $insertCat->bind_param("is", $userid, $categoryname);
                 $insertCat->execute();
                 $category_id = $conn->insert_id;
            }

            if(!empty($_POST['id'])) {
                  $id = $_POST['id'];
                  $query = $conn->prepare("UPDATE tasks SET title = ?, deadline = ? ,priority = ?, category_id = ? WHERE id = ? AND user_id = ?");
                  $query->bind_param("ssisii",$title,$deadline,$priority,$category_id,$id,$userid);
                  $query->execute();
            }else{
                  $query = $conn->prepare( "INSERT INTO tasks (title,deadline,priority,user_id,category_id) VALUES (?, ?, ?, ?, ?)");
                  $query->bind_param("sssii",$title,$deadline,$priority,$userid,$category_id);
                  $query->execute();
              }
        }
    }

    if (isset($_POST['delete'])) {
        
      $id = $_POST['id'];
      $query = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
      $query->bind_param("ii",$id,$userid);
      $query->execute();   
    }
    
     if (isset($_POST['ajax_complete'])) {

      $id = $_POST['id'];
      // print($id);  
      // exit;
       
       $query = $conn->prepare("UPDATE tasks SET status = 'complete'  WHERE id = ?  AND user_id = ?");
       $query->bind_param("ii",$id,$userid);
       $query->execute();
    }

    $editid = "";
    $edittitle = "";
    $editdeadline = "";
    $editcategory = "";
    $editpriority = "";

    if(isset($_POST['update'])){

          $editid = $_POST['id'];
          $query = $conn->prepare("SELECT title,deadline,category_id,priority FROM tasks WHERE id = ? AND user_id = ?");
          $query->bind_param("ii",$editid,$userid);
          $query->execute();

          $row = $query->get_result()->fetch_assoc(); 

          $edittitle = $row['title'];
          $editdeadline = $row['deadline'];
          $editpriority = $row['priority'];

          $catequery = $conn->prepare("SELECT name FROM category WHERE id = ?");
          $catequery->bind_param("i",$row['category_id']);
          $catequery->execute();
          $editcategory = $catequery->get_result()->fetch_assoc()['name'];
    }

    $fetchdata = $conn->prepare("SELECT tasks.id, tasks.title, tasks.deadline, tasks.status,tasks.priority, category.name FROM tasks LEFT JOIN category ON tasks.category_id = category.id
    WHERE tasks.user_id = ? ORDER BY tasks.deadline ASC");

    $fetchdata->bind_param("i",$userid);
    $fetchdata->execute();  
    $result = $fetchdata->get_result();
   
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - ToDo App</title>
<link rel="stylesheet" href="style.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
<div class="container">

    <div class="sidebar">
      <h2>ToDo App</h2>
      <a href="index.php">Tasks</a>
      <a href="profile.php">Profile</a>
      <a href="logout.php">Logout</a>
    </div>

  <div class="main">
   <div class="header-row">
    
    <h1 class="header-title">My Tasks</h1>

    <div class="filter-bar">
        <select id="filter_category" class="filter-select">
            <option value="">All Categories</option>
            <option value="Work">Work</option>
            <option value="Personal">Personal</option>
            <option value="Urgent">Urgent</option>
        </select>

        <select id="filter_priority" class="filter-select">
            <option value="">All Priorities</option>
            <option value="Low">Low</option>
            <option value="Medium">Medium</option>
            <option value="High">High</option>
        </select>

        <select id="filter_status" class="filter-select">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="complete">Completed</option>
        </select>
    </div>

    <div class="header-right">
        <!-- Search bar -->
        <!-- <form class="search-form" method="get"> -->
            <input type="text" id="search_query" name="search" placeholder="Search tasks..." autocomplete="off">
            <!-- <button type="submit" >🔍</button> -->
        <!-- </form> -->

        <!-- Welcome text -->
        <h3 class="welcome-text">Welcome <?= $username ?></h3>
    </div>
</div>

    <form class="add-task" style="margin-bottom:30px;" method="post">
         
         <?php if(isset($msg['title'])): ?>
          <b> <small style="color:red"><?= $msg['title'] ?></small></b>
         <?php endif; ?>  
      <input type="text" name="title" placeholder="Task Title" value = "<?= $edittitle ?>">
            
         <?php if(isset($msg['deadline'])): ?>
           <small style="color:red"><?= $msg['deadline'] ?></small>
         <?php endif; ?>  
      <input type="date" name="deadline" value = "<?= $editdeadline ?>">

      <select name="category">
          <option value="">Select Category</option>
          <option value="Work" <?= ($editcategory == 'Work') ? 'selected' : ''?>>Work</option>
          <option value="Personal" <?= ($editcategory == 'Personal') ? 'selected' : ''?>>Personal</option>
          <option value="Urgent" <?= ($editcategory == 'Urgent') ? 'selected' : ''?>>Urgent</option>
      </select>

      <select name="priority">
          <option value="">Select Priority</option>
          <option value="Low" <?= ($editcategory == 'Work') ? 'selected' : ''?>>Low</option>
          <option value="Medium" <?= ($editcategory == 'Personal') ? 'selected' : ''?>>Medium</option>
          <option value="High" <?= ($editcategory == 'Urgent') ? 'selected' : ''?>>High</option>
      </select>

      <?php if($editid): ?>  
        <input type="hidden" name="id" value=<?= $editid ?>>
        <button type="submit" name="addtask">Update Task</button>
      <?php else: ?>
        <button type="submit" name="addtask">Add Task</button>
      <?php endif; ?>
    </form>
        
  <div id="search_results"></div> 
  
  <div id="filter-data"></div> 
        
 <div id="all_tasks">
  <?php  while($row = $result->fetch_assoc()){  ?>  
        
    <div class="task-list" style="margin-top:20px;">
      <div class="task-card">

        <h3 style="<?= ($row['status'] == 'complete') ? 'text-decoration:line-through;color:gray;' : '' ?>"><?= $row['title']?></h3>
        <p>Deadline : <?= $row['deadline']?></p>
        <p>Category : <?= $row['name']?></p>
        <p>Priority : <?= $row['priority']?></p>
        
        <form method="post">
          <input type="hidden" name="id" value="<?= $row['id']?>">
            <div class="actions">
              <button  style="<?= ($row['status'] == 'complete') ? 'background:#bfbfbf;' : '' ?>" type="submit" class="editbtn edit" name="update" <?= ($row['status'] == 'complete') ? 'disabled' : '' ?>>Edit</button>
              <button style="<?= ($row['status'] == 'complete') ? 'background:#bfbfbf;' : '' ?>" type="button" class="completeBtn complete" name="complete" <?= ($row['status'] == 'complete') ? 'disabled' : '' ?>>Complete</button>
              <button style="<?= ($row['status'] == 'complete') ? 'background:#bfbfbf;' : '' ?>"  type="submit" class="deleteBtn delete" name="delete" <?= ($row['status'] == 'complete') ? 'disabled' : '' ?>>Delete</button>
            </div> 
        </form>  
    </div>
 </div>
     <?php } ?>
   </div>
  </div>
</div>
<script src="script.js"></script>
</body>
</html>
