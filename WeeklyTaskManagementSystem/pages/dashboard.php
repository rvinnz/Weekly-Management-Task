<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

date_default_timezone_set('Asia/Manila'); 

include_once '../classes/database.php';
include_once '../classes/task.php';

$database = new Database();
$db = $database->connect();
$task = new Task($db);

// Handle Add Task
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_task'])) {
    $task->create($_SESSION['user_id'], $_POST['title'], $_POST['description'], $_POST['due_date']);
    header("Location: dashboard.php");
    exit();
}

// Handle Delete
if (isset($_GET['delete_id'])) {
    $task->delete($_GET['delete_id']);
    header("Location: dashboard.php");
    exit();
}

// Handle Status Updates
if (isset($_GET['complete_id'])) {
    $task->updateStatus($_GET['complete_id'], 'Complete');
    header("Location: dashboard.php");
    exit();
}
if (isset($_GET['start_id'])) {
    $task->updateStatus($_GET['start_id'], 'In Progress');
    header("Location: dashboard.php");
    exit();
}

// --- FILTER LOGIC ---
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$stmt = $task->getAllByUser($_SESSION['user_id'], $filter);

$today = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | WeeklyTask</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/dashboard.css?v=<?php echo time(); ?>">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body class="dashboard-page">

    <nav class="navbar">
        <div class="brand">
            <i class="ph-fill ph-kanban" style="color: var(--primary); margin-right: 8px;"></i> 
            Weekly Task Management
        </div>
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="text-align: right;">
                <span style="display: block; font-weight: 600; font-size: 14px; color: var(--text-main);">
                    <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                </span>
                <span style="display: block; font-size: 12px; color: var(--text-muted);">User Account</span>
            </div>
            <a href="logout.php" style="display: flex; align-items: center; gap: 5px; color: #ef4444; font-weight: 500;">
                <i class="ph-bold ph-sign-out"></i> Logout
            </a>
        </div>
    </nav>

    <div class="container">
        <div style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: end;">
            <div>
                <h2 style="font-size: 24px; color: var(--text-main);">Dashboard</h2>
                <p style="color: var(--text-muted); margin-top: 5px;">
                    Today is: <strong><?php echo date("l, F jS, Y", strtotime($today)); ?></strong>
                </p>
            </div>
            
            <!-- NEW: FILTER FORM -->
            <form method="GET" class="filter-form">
                <select name="filter" onchange="this.form.submit()">
                    <option value="all" <?php echo ($filter == 'all') ? 'selected' : ''; ?>>Show All Tasks</option>
                    <option value="pending" <?php echo ($filter == 'pending') ? 'selected' : ''; ?>>Pending Only</option>
                    <option value="complete" <?php echo ($filter == 'complete') ? 'selected' : ''; ?>>Completed Only</option>
                </select>
            </form>
        </div>

        <div class="task-card">
            <h3 style="display: flex; align-items: center; gap: 10px;">
                <i class="ph-duotone ph-plus-circle" style="color: var(--primary);"></i>
                Create New Task
            </h3>
            <form method="POST" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label>Task Title</label>
                        <input type="text" name="title" placeholder="e.g. Finish Project Report" required>
                    </div>
                    <div class="form-group">
                        <label>Due Date</label>
                        <input type="date" name="due_date" required>
                    </div>
                </div>
                <label>Description</label>
                <textarea name="description" rows="2" placeholder="Add details (Optional)..."></textarea>
                <div style="margin-top: 15px; text-align: right;">
                    <button type="submit" name="add_task">
                        <i class="ph-bold ph-paper-plane-right"></i> Add Task
                    </button>
                </div>
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Task Name</th>
                        <th>Details</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                        
                    <?php 
                        $dueDate = $row['due_date'];
                        $dateClass = ""; 
                        $alertText = ""; 

                        if ($row['status'] !== 'Complete') {
                            if ($dueDate < $today) {
                                $dateClass = "text-danger"; 
                                $alertText = "<span class='badge-overdue'>Overdue</span>";
                            } elseif ($dueDate == $today) {
                                $dateClass = "text-warning"; 
                                $alertText = "<span style='font-size:0.7rem; color:#f59e0b; margin-left:5px;'>(Due Today)</span>";
                            }
                        }
                    ?>

                    <tr>
                        <td style="font-weight: 600; color: var(--text-main);">
                            <?php echo htmlspecialchars($row['title']); ?>
                        </td>
                        <td style="color: var(--text-muted); font-size: 14px;">
                            <?php echo htmlspecialchars($row['description']); ?>
                        </td>
                        <td style="font-size: 14px;" class="<?php echo $dateClass; ?>">
                            <i class="ph-duotone ph-calendar-blank" style="margin-right: 5px;"></i>
                            <?php echo htmlspecialchars($row['due_date']); ?>
                            <?php echo $alertText; ?>
                        </td>
                        <td>
                            <?php 
                                $badgeClass = 'status-todo';
                                $iconClass = 'ph-circle';
                                if($row['status'] == 'In Progress') { $badgeClass = 'status-progress'; $iconClass = 'ph-spinner'; }
                                if($row['status'] == 'Complete') { $badgeClass = 'status-complete'; $iconClass = 'ph-check-circle'; }
                            ?>
                            <span class="status-badge <?php echo $badgeClass; ?>">
                                <i class="ph-fill <?php echo $iconClass; ?>" style="margin-right: 4px;"></i>
                                <?php echo htmlspecialchars($row['status']); ?>
                            </span>
                        </td>
                        <td style="text-align: right;">
                            
                            <!-- STATUS BUTTONS -->
                            <?php if($row['status'] == 'To Do'): ?>
                                <a href="dashboard.php?start_id=<?php echo $row['id']; ?>" 
                                   class="btn-action btn-progress" title="Start Task"><i class="ph-bold ph-play"></i> Start</a>
                            <?php endif; ?>

                            <?php if($row['status'] !== 'Complete'): ?>
                                <a href="dashboard.php?complete_id=<?php echo $row['id']; ?>" 
                                   class="btn-action btn-complete" title="Mark Done"><i class="ph-bold ph-check"></i> Done</a>
                            <?php endif; ?>

                            <!-- EDIT BUTTON -->
                            <a href="edit_task.php?id=<?php echo $row['id']; ?>" 
                               class="btn-action btn-edit" title="Edit Task"><i class="ph-bold ph-pencil-simple"></i></a>

                            <!-- DELETE BUTTON -->
                            <a href="dashboard.php?delete_id=<?php echo $row['id']; ?>" 
                               class="btn-action btn-delete" onclick="return confirm('Are you sure?')" title="Delete"><i class="ph-bold ph-trash"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <?php if($stmt->rowCount() == 0): ?>
                <div style="text-align:center; padding: 50px 20px; color: var(--text-muted);">
                    <i class="ph-duotone ph-clipboard-text" style="font-size: 48px; margin-bottom: 10px; opacity: 0.5;"></i>
                    <p>No tasks found for this filter.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>