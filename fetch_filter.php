<?php 
    session_start();
    require_once('connect.php');

   $category = $_POST['category'] ?? '';
   $priority = $_POST['priority'] ?? '';
   $status = $_POST['status'] ?? '';
   $userid = $_SESSION['id'];

   $filterquery ="SELECT t.id ,t.title, t.deadline,t.priority, t.status,c.name FROM tasks t LEFT JOIN category c ON t.category_id = c.id
        WHERE t.user_id = ? ";
   
   $params = [$userid];
   $types = "i";
    
   if ($category !== "") {
        $filterquery .= " AND c.name = ?";
        $params[] = $category;
        $types .= "s";
   }

   if ($priority !== "") {
        $filterquery .= " AND t.priority = ?";
        $params[] = $priority;
        $types .= "s";
   }

   if ($status !== "") {
        $filterquery .= " AND  t.status = ?";
        $params[] = $status;
        $types .= "s";
   }

   $query = $conn->prepare($filterquery);
   $query->bind_param($types, ...$params);
   $query->execute();
   $result = $query->get_result();

   if ($result->num_rows > 0) {
   while ($row = $result->fetch_assoc()) {
        $style = ($row['status'] === 'complete')
            ? "text-decoration:line-through;color:gray;"
            : "";
        ?>
        <div class="task-card">
            <h3 style="<?= $style ?>"><?= ($row['title']) ?></h3>
            <p>Deadline: <?= $row['deadline'] ?></p>
            <p>Category: <?= $row['name'] ?></p>
            <p>Priority: <?= $row['priority'] ?></p>
            <form method="post">
            <input type="hidden" name="id" value="<?= $row['id']?>">
                <div class="actions">
                <button  style="<?= ($row['status'] == 'complete') ? 'background:#bfbfbf;' : '' ?>" type="submit" class="editbtn edit" name="update" <?= ($row['status'] == 'complete') ? 'disabled' : '' ?>>Edit</button>
                <button style="<?= ($row['status'] == 'complete') ? 'background:#bfbfbf;' : '' ?>" type="button" class="completeBtn complete" name="complete" <?= ($row['status'] == 'complete') ? 'disabled' : '' ?>>Complete</button>
                <button style="<?= ($row['status'] == 'complete') ? 'background:#bfbfbf;' : '' ?>"  type="submit" class="deleteBtn delete" name="delete" <?= ($row['status'] == 'complete') ? 'disabled' : '' ?>>Delete</button>
                </div> 
             </form>  
        </div>
        <?php
    }
   }else{
         echo "<p>No tasks found</p>";
   }
?>
