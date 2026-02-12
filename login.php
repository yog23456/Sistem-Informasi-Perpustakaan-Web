<?php
// Secure session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // 1 in production with HTTPS
ini_set('session.cookie_path', '/');
ini_set('session.use_strict_mode', 1);
session_start();
if (isset($_SESSION['user_id']) && empty($_SESSION['role'])) {
    // Incomplete session â€ clear it
    session_destroy();
    // Or just unset invalid parts: unset($_SESSION['user_id'], $_SESSION['role']);
}
// Include functions FIRST (so auth.php can use sanitize(), etc.)
require_once 'lib/functions.php';
require_once 'lib/auth.php';
// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    redirectBasedOnRole($_SESSION['role']);
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Check
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        die('Invalid request. CSRF token mismatch.');
    }
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    if (empty($username) || empty($password)) {
        $error = "Both fields are required.";
    } else {
        $role = login($username, $password);
        if ($role) {
            redirectBasedOnRole($role);
        } else {
            $error = "Invalid username or password.";
        }
    }
}
$csrfToken = generateCSRFToken();
?>
<!-- HTML -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Aplikasi Perpustakaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #F3F4F6;
            color: #2c2c2c;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 30px;
            max-width: 420px;
            width: 100%;
        }

        .login-title {
            color: #2c2c2c;
            font-weight: 600;
            margin-bottom: 25px;
        }

        .form-label {
            color: #2c2c2c;
            font-weight: 500;
        }

        .form-control {
            border: 1px solid #9CA3AF;
            border-radius: 8px;
            padding: 10px 15px;
            background-color: #F9FAFB;
        }

        .form-control:focus {
            border-color: #6B7280;
            box-shadow: 0 0 0 0.2rem rgba(107, 114, 128, 0.25);
        }

        .btn-login {
            background-color: #2c2c2c;
            border: none;
            color: white;
            padding: 12px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background-color: #1a1a1a;
            transform: translateY(-2px);
        }

        .alert {
            border-radius: 8px;
        }

        .register-link {
            color: #6B7280;
            text-decoration: none;
            font-weight: 500;
        }

        .register-link:hover {
            color: #2c2c2c;
            text-decoration: underline;
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center min-vh-100">
    <div class="login-container">
        <h3 class="text-center login-title">📚 Login Aplikasi Perpustakaan</h3>
        <?php if ($error): ?>
            <?php showAlert($error); ?>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES) ?>">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-login w-100">Login</button>
            <div class="text-center mt-3">
                <a href="register.php" class="register-link">Don't have an account?</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>