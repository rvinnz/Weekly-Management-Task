<?php
// Navigate up one level (..) to access the classes folder
include_once '../classes/database.php';
include_once '../classes/account.php';

$database = new Database();
$db = $database->connect();
$account = new Account($db);

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($account->emailExists($email)) {
        $message = "That email is already registered.";
    } else {
        if ($account->register($name, $email, $password)) {
            header("Location: login.php");
            exit();
        } else {
            $message = "Unable to register. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | WeeklyTask</title>
    
    <!-- Link to Updated Auth CSS with Cache Busting -->
    <link rel="stylesheet" type="text/css" href="../assets/css/auth.css?v=<?php echo time(); ?>">
    
    <!-- Load Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>

    <div class="auth-card">
        <!-- Logo / Header -->
        <div class="brand-title">
            <i class="ph-fill ph-kanban"></i> WeeklyTask
        </div>
        <p class="subtitle">Create your free account today.</p>

        <!-- Error Message -->
        <?php if(!empty($message)): ?>
            <div class="error-msg">
                <i class="ph-bold ph-warning-circle"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Register Form -->
        <form method="POST" action="">
            <label>Full Name</label>
            <input type="text" name="name" placeholder="John Doe" required>

            <label>Email Address</label>
            <input type="email" name="email" placeholder="you@example.com" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Create a strong password" required>

            <button type="submit">
                <i class="ph-bold ph-user-plus"></i> Create Account
            </button>
        </form>

        <!-- Footer -->
        <div class="auth-footer">
            Already have an account? <a href="login.php">Sign in here</a>
        </div>
    </div>

</body>
</html>