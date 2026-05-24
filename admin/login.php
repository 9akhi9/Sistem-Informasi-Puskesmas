<?php
require_once '../config/koneksi.php';
$error = '';
if (isAdminLogin()) { header('Location: dashboard.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = bersihkan($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $db   = koneksi();
    $stmt = $db->prepare("SELECT * FROM admin WHERE username=?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();
    if ($admin && $password === $admin['password']) {
        $_SESSION['admin_id']   = $admin['id'];
        $_SESSION['admin_nama'] = $admin['nama'];
        header('Location: dashboard.php'); exit;
    } else { $error = 'Username atau password salah!'; }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin – <?php echo APP_NAME; ?></title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="halaman-login">
<div class="login-wrapper">
  <div class="login-kartu">
    <div class="login-header">
      <span class="brand-ikon" style="font-size:2rem;width:56px;height:56px;margin:0 auto 12px">+</span>
      <h1><?php echo APP_NAME; ?></h1><p>Panel Administrasi</p>
    </div>
    <?php if($error): ?><div class="pesan-error"><?php echo $error; ?></div><?php endif; ?>
    <form method="POST">
      <div class="form-grup"><label>Username</label><input type="text" name="username" required autofocus></div>
      <div class="form-grup"><label>Password</label><input type="password" name="password" required></div>
      <button type="submit" class="tombol tombol-utama tombol-penuh">Masuk ke Dashboard</button>
    </form>
    <div class="login-catatan">
      <!-- <p>Default: <strong>admin</strong> / <strong>admin123</strong></p> -->
      <a href="../index.php">&larr; Kembali ke Website</a>
    </div>
  </div>
</div>
</body></html>