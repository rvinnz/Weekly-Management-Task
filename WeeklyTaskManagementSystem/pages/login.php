<?php
include_once '../classes/database.php';
include_once '../classes/account.php';

$database = new Database();
$db = $database->connect();
$account = new Account($db);

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($account->login($email, $password)) {
        header("Location: dashboard.php");
        exit();
    } else {
        $message = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | WeeklyTask</title>
    
    <link rel="stylesheet" type="text/css" href="../assets/css/auth.css?v=<?php echo time(); ?>">
    
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>

    <div class="auth-card">
        <div class="brand-title">
            <i class="ph-fill ph-kanban"></i> WeeklyTask
        </div>
        <p class="subtitle">Welcome back! Please sign in.</p>

        <?php if(!empty($message)): ?>
            <div class="error-msg">
                <i class="ph-bold ph-warning-circle"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="you@example.com" required>

            <div style="display:flex; justify-content:space-between;">
                <label>Password</label>
                </div>
            <input type="password" name="password" placeholder="••••••••" required>

            <button type="submit">
                <i class="ph-bold ph-sign-in"></i> Sign In
            </button>
        </form>

        <div class="auth-footer">
            Don't have an account? <a href="register.php">Create one</a>
        </div>
    </div>

</body>
</html>