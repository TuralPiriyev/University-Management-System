<?php

session_start();

require_once "db.php"; 

function parseEmailRole(string $email): ?string {
    $email = strtolower(trim($email));
    if (strpos($email, '@') === false) return null;
    $local = explode('@', $email, 2)[0];
    if (preg_match('/\.(students|teacher|admin)$/i', $local, $m)) {
        $map = [
            'students' => 'student',
            'teacher'  => 'teacher',
            'admin'    => 'admin'
        ];
        $suffix = strtolower($m[1]);
        return $map[$suffix] ?? null;
    }
    return null;
}

function getUserByEmail(mysqli $conn, string $email, string $role): ?array {
    $map = [
        'student' => 'students',
        'teacher' => 'teachers',
        'admin'   => 'admins'
    ];
    if (!isset($map[$role])) return null;
    $table = $map[$role];

    $sql = "SELECT Id, username, email, password FROM $table WHERE email = ? LIMIT 1";
    if (!($stmt = $conn->prepare($sql))) return null;
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc() ?: null;
    $stmt->close();
    return $row;
}

function findUserAcrossTables(mysqli $conn, string $email): ?array {
    foreach (['student','teacher','admin'] as $role) {
        $row = getUserByEmail($conn, $email, $role);
        if ($row) {
            $row['__role_found'] = $role;
            return $row;
        }
    }
    return null;
}

function redirectByRole(string $role) {
    if ($role === 'student') {
        header('Location: Students/students_index.php'); exit;
    } elseif ($role === 'teacher') {
        header('Location: Teacher/teacher_index.php'); exit;
    } else {
        header('Location: Admin/admin_index.php'); exit;
    }
}

if (isset($_SESSION['role'])) {
    redirectByRole($_SESSION['role']);
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'], $_POST['pass'])) {
    $input = trim($_POST['email']);
    $password = $_POST['pass'];

    $emailRole = parseEmailRole($input); 

    if ($emailRole !== null) {
        $row = getUserByEmail($conn, $input, $emailRole);
        $detectedRole = $emailRole;
    } else {
        $row = findUserAcrossTables($conn, $input);
        $detectedRole = $row['__role_found'] ?? null;
    }

    if (!$row) {
        $error = 'İstifadəçi tapılmadı.';
    } else {
        $dbPass = $row['password'];

        $passwordOk = false;
        if (password_verify($password, $dbPass)) {
            $passwordOk = true;
        } else {
            if ($password === $dbPass) {
                $passwordOk = true;
            }
        }

        if ($passwordOk) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $row['Id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['role'] = $emailRole ?? $detectedRole ?? 'student';

            $redirect = $_SESSION['redirect_after_login'] ?? null;
            unset($_SESSION['redirect_after_login']);
            if ($redirect) {
                header("Location: $redirect"); exit;
            }
            redirectByRole($_SESSION['role']);
        } else {
            $error = 'Yanlış şifrə.';
        }
    }
}
?>

<!doctype html>
<html lang="az">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Giriş</title>
  <link rel="stylesheet" href="login.css">
</head>
<body>
       <div class="background-animation">
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
    </div>

  <div class="login-container">
    <div class="logo">
            <div class="logo-image"></div>
            <h1>AZƏRBAYCAN UNİVERSİTETİ</h1>
            <p>Hesabınıza daxil olun</p>
    <?php if(!empty($error)): ?>
      <div class="error" style="color:#ef4444; margin-bottom:12px; text-align:center;">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form id = "loginForm" method="POST">
      <div class="form-group">
        <label for="email">E-poçt və ya İstifadəçi adı</label>
        <input id="email" name="email" type="text" required placeholder="email@example.com">
      </div>
      <div class="form-group">
        <label for="pass">Şifrə</label>
        <input id="pass" name="pass" type="password" required placeholder="••••••">
      </div>
      <button class = "login-btn" type="submit">Daxil Ol</button>
    </form>
  </div>
</body>
</html>
