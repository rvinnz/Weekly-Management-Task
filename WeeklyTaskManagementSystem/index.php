<?php
// Check if user is already logged in
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: pages/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome | WeeklyTask</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Reusing your Auth CSS for consistency -->
    <link rel="stylesheet" type="text/css" href="assets/css/auth.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        /* Simple Landing Page Overrides */
        body { flex-direction: column; text-align: center; padding: 20px; }
        .hero-section { max-width: 600px; margin-top: -50px; }
        h1 { font-size: 3rem; color: var(--primary); margin-bottom: 10px; }
        p { font-size: 1.2rem; color: var(--text-muted); margin-bottom: 30px; }
        .btn-group { display: flex; gap: 15px; justify-content: center; }
        .btn-outline { background: transparent; border: 2px solid var(--primary); color: var(--primary); }
        .btn-outline:hover { background: #eff6ff; }
    </style>
</head>
<body>

    <div class="hero-section">
        <i class="ph-duotone ph-kanban" style="font-size: 80px; color: var(--primary);"></i>
        <h1>WeeklyTask</h1>
        <p>Organize your week, stay productive, and never miss a deadline again.</p>
        
        <div class="btn-group">
            <a href="pages/login.php" style="text-decoration: none;">
                <button class="btn-outline" style="width: 150px;">Sign In</button>
            </a>
            <a href="pages/register.php" style="text-decoration: none;">
                <button style="width: 150px;">Get Started</button>
            </a>
        </div>
    </div>

    <footer style="margin-top: 50px; color: #999; font-size: 0.9rem;">
        &copy; <?php echo date('Y'); ?> WeeklyTask System. All rights reserved.
    </footer>

</body>
</html> 