<?php
// admin/login.php
// Halaman login admin panel

require_once 'config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika sudah login, langsung ke dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = "Username dan password wajib diisi!";
    } else {
        try {
            // Prepared statement untuk keamanan dari SQL injection
            // Pastikan skema terbaru (role, team, classes)
            require_once __DIR__ . '/sql/migrate.php';

            $stmt = $pdo->prepare("SELECT id, username, password, role FROM admins WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password'])) {
                // Login sukses, simpan session
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_role'] = $admin['role'] ?? 'admin';
                
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Username atau password salah!";
            }
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan sistem: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - SDIT Al Fatah</title>
    <link href="../assets/img/logo afix.png" rel="icon">
    <link href="../assets/img/logo afix.png" rel="apple-touch-icon">
    <!-- Bootstrap 5 CSS -->
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            background-color: #f3f4f6;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            padding: 35px 30px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }
        .brand-logo {
            text-align: center;
            margin-bottom: 25px;
        }
        .brand-logo img {
            max-height: 80px;
        }
        .btn-brand {
            background-color: #1acc8d;
            border-color: #1acc8d;
            color: #fff;
            font-weight: 500;
        }
        .btn-brand:hover {
            background-color: #15b37b;
            border-color: #15b37b;
            color: #fff;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="brand-logo">
            <img src="../assets/img/logo afix.png" alt="Logo SDIT Al Fatah">
            <h4 class="mt-3 fw-bold text-dark"><span class="brand-font">SDIT Al Fatah</span></h4>
            <span class="text-muted small">Silakan login untuk masuk ke admin panel</span>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                <div>
                    <?= htmlspecialchars($error) ?>
                </div>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label fw-semibold text-secondary">Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-user text-muted"></i></span>
                    <input type="text" class="form-control bg-light border-start-0" id="username" name="username" placeholder="Masukkan username" required autofocus>
                </div>
            </div>
            
            <div class="mb-4">
                <label for="password" class="form-label fw-semibold text-secondary">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-lock text-muted"></i></span>
                    <input type="password" class="form-control bg-light border-start-0" id="password" name="password" placeholder="Masukkan password" required>
                </div>
            </div>

            <button type="submit" class="btn btn-brand w-100 py-2.5 rounded-lg"><i class="fa-solid fa-right-to-bracket me-2"></i> Masuk</button>
        </form>
    </div>

</body>
</html>
