<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include_once '../classes/database.php';
include_once '../classes/task.php';

$database = new Database();
$db = $database->connect();
$task = new Task($db);

// Check if ID is set
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$taskId = $_GET['id'];
$currentTask = $task->getSingleTask($taskId);

// Update Logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($task->update($taskId, $_POST['title'], $_POST['description'], $_POST['due_date'])) {
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Something went wrong.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Task | WeeklyTask</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/dashboard.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body class="dashboard-page">
    <nav class="navbar">
        <div class="brand">
            <i class="ph-fill ph-kanban" style="color: var(--primary);"></i> WeeklyTask
        </div>
        <a href="dashboard.php" style="font-weight: 600;">&larr; Back to Dashboard</a>
    </nav>

    <div class="container" style="max-width: 600px;">
        <div class="task-card">
            <h3><i class="ph-bold ph-pencil-simple"></i> Edit Task</h3>
            
            <form method="POST" action="">
                <label>Task Title</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($currentTask['title']); ?>" required>
                
                <label>Due Date</label>
                <input type="date" name="due_date" value="<?php echo $currentTask['due_date']; ?>" required>
                
                <label>Description</label>
                <textarea name="description" rows="4"><?php echo htmlspecialchars($currentTask['description']); ?></textarea>
                
                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    <button type="submit">Save Changes</button>
                    <a href="dashboard.php" style="padding: 12px; color: #666;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>