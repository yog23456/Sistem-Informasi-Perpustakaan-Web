<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Set to 1 in production with HTTPS
ini_set('session.use_strict_mode', 1);
session_start();
require_once 'lib/auth.php';
require_once 'lib/functions.php';
$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Check
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        die('Invalid request. CSRF token mismatch.');
    }
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $nama_lengkap = $_POST['nama_lengkap'] ?? '';
    // Ambil role dari POST, default ke 'petugas' jika tidak dipilih
    $role = $_POST['role'] ?? 'petugas';

    if (empty($username) || empty($password) || empty($nama_lengkap)) {
        $error = "All fields are required.";
    } else {
        // Validate password strength
        $passwordErrors = validatePassword($password, false); // ganti menjadi: false agar bebas membuat password
        if (!empty($passwordErrors)) {
            $error = implode('', $passwordErrors);
        } else {
            if (registerUser($username, $password, $role, $nama_lengkap)) {
                $success = "Registration successful! You can now log in.";
            } else {
                $error = "Username already exists or registration failed.";
            }
        }
    }
}

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #F3F4F6;
            color: #2c2c2c;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .register-container {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 30px;
            max-width: 420px;
            width: 100%;
        }

        .register-title {
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

        .btn-register {
            background-color: #2c2c2c;
            border: none;
            color: white;
            padding: 12px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-register:hover {
            background-color: #1a1a1a;
            transform: translateY(-2px);
        }

        .alert {
            border-radius: 8px;
        }

        .login-link {
            color: #6B7280;
            text-decoration: none;
            font-weight: 500;
        }

        .login-link:hover {
            color: #2c2c2c;
            text-decoration: underline;
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center min-vh-100">
    <div class="register-container">
        <h3 class="text-center register-title">📝 Register</h3>
        <?php if ($error): ?>
            <?php showAlert($error); ?>
        <?php endif; ?>
        <?php if ($success): ?>
            <?php showAlert($success, 'success'); ?>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES) ?>">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
                <div class="form-text">
                    Must be 8+ chars, with uppercase, lowercase, and number.
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role" class="form-control">
                    <option value="admin">Admin</option>
                    <option value="petugas">Petugas</option>
                </select>
            </div>
            <button type="submit" class="btn btn-register w-100">Register</button>
            <div class="text-center mt-3">
                <a href="login.php" class="login-link">Already have an account?</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>