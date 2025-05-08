<?php
session_start();

// Load language system
require_once '../languages/language.php';

// Check if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

// Default admin credentials (in a real application, these would be stored securely)
$adminUsername = 'admin';
$adminPassword = 'admin123'; // In a real application, use password_hash() and password_verify()

$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if ($username === $adminUsername && $password === $adminPassword) {
        // Successful login
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;

        // Redirect to dashboard
        header('Location: dashboard.php');
        exit;
    } else {
        $error = __('admin_error');
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang->getCurrentLang(); ?>" dir="<?php echo $lang->getDirection(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('admin_login'); ?></title>

    <!-- CSS -->
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="admin-styles.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
</head>
<body class="admin-body">
    <div class="admin-login-container">
        <div class="admin-login-box">
            <div class="admin-login-header">
                <h1><?php echo __('admin_login'); ?></h1>
                <p><?php echo __('admin_login_desc'); ?></p>
            </div>

            <?php if ($error): ?>
            <div class="admin-alert admin-alert-danger">
                <?php echo $error; ?>
            </div>
            <?php endif; ?>

            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="admin-login-form">
                <div class="admin-form-group">
                    <label for="username"><?php echo __('admin_username'); ?></label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="admin-form-group">
                    <label for="password"><?php echo __('admin_password'); ?></label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="admin-button admin-button-primary admin-button-block"><?php echo __('admin_login_button'); ?></button>
            </form>

            <div class="admin-login-footer">
                <a href="../index.php"><?php echo __('admin_back_to_site'); ?></a>
            </div>
        </div>
    </div>
</body>
</html>
