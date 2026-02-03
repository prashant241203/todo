<?php 
    session_start();
    require_once('connect.php');

    if (!isset($_POST['query']) || !isset($_SESSION['id'])) {
        exit;
    }
        $search = '%' . $_POST['query'] . '%';
        $userid = $_SESSION['id'];
        
        $searchquery = $conn->prepare("SELECT t.title, t.deadline,t.priority, t.status,c.name FROM tasks t LEFT JOIN category c ON t.category_id = c.id
        WHERE t.user_id = ? AND t.title LIKE ?");
        
        $searchquery->bind_param("is",$userid,$search);
        $searchquery->execute();  
        $searchresult = $searchquery->get_result();

if ($searchresult->num_rows > 0) {
    while ($row = $searchresult->fetch_assoc()) {
        $style = ($row['status'] === 'complete')
            ? "text-decoration:line-through;color:gray;"
            : "";
        ?>
        <div class="task-card">
            <h3 style="<?= $style ?>"><?= htmlspecialchars($row['title']) ?></h3>
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
} else {
    echo "<p>No tasks found</p>";
}
?>