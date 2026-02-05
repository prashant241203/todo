<?php
session_start();
require_once('connect.php');


if (!isset($_SESSION['user_email'])) {
    header('Location:login.php');
    exit;
}

$email = $_SESSION['user_email'];

$fetchemail = $conn->prepare("SELECT id, name FROM users WHERE email = ?");
$fetchemail->bind_param("s", $email);
$fetchemail->execute();
$user = $fetchemail->get_result()->fetch_assoc();

$userid   = $user['id'];
$username = $user['name'];


if (isset($_POST['restore'])) {

    $id = $_POST['id'];

    $restore = $conn->prepare( "UPDATE tasks SET is_deleted = 0 WHERE id = ? AND user_id = ? AND is_deleted = 1");
    $restore->bind_param("ii",$id,$userid);
    $restore->execute();

    header("Location:deletedtask.php");
    exit;
}


        $deletedTasks = $conn->prepare("SELECT tasks.id, tasks.title, tasks.deadline, tasks.priority, category.name FROM tasks LEFT JOIN category ON tasks.category_id = category.id 
                                    WHERE tasks.user_id = ? AND tasks.is_deleted = 1 ORDER BY tasks.deadline ASC");
        $deletedTasks->bind_param("i", $userid);
        $deletedTasks->execute();
        $deletedResult = $deletedTasks->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Deleted Tasks - ToDo App</title>
<link rel="stylesheet" href="style.css">
</head>

<body>
<div class="container">


    <div class="sidebar">
        <h2>ToDo App</h2>
        <a href="index.php">Tasks</a>
        <a href="deleted_tasks.php" class="active">Deleted Tasks</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>

   
    <div class="main">

        <div class="header-row">
            <h1>Deleted Tasks</h1>
            <h3>Welcome <?= $username ?></h3>
        </div>

        <?php if ($deletedResult->num_rows > 0): ?>

            <?php while ($row = $deletedResult->fetch_assoc()): ?>
                <div class="task-card" style="background:#f2f2f2;margin-top:20px;">
                    <h3><?= $row['title'] ?></h3>
                    <p><b>Deadline:</b> <?= $row['deadline'] ?></p>
                    <p><b>Category:</b> <?= $row['name'] ?></p>
                    <p><b>Priority:</b> <?= $row['priority'] ?></p>

                    <form method="post">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <button type="submit" name="restore" class="restoreBtn">
                            ♻️ Restore Task
                        </button>
                    </form>
                </div>
            <?php endwhile; ?>

        <?php else: ?>
            <p style="color:gray;margin-top:30px;">No deleted tasks found.</p>
        <?php endif; ?>

    </div>
</div>
</body>
</html>
