<?php
$halaman = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($judul) ? $judul . ' - ' : ''; echo APP_NAME; ?></title>
  <!-- Path relatif dari root folder puskesmas/ -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<nav class="navbar">
  <div class="container nav-dalam">
    <a href="<?php echo BASE_URL; ?>/index.php" class="brand">
      <span class="brand-ikon">+</span>
      <div>
        <span class="brand-nama">PUSKESMAS</span>
        <span class="brand-sub">Sehat Sejahtera</span>
      </div>
    </a>
    <button class="hamburger" onclick="toggleMenu()">&#9776;</button>
    <ul class="nav-menu" id="navMenu">
      <li><a href="<?php echo BASE_URL; ?>/index.php"            class="<?php echo $halaman=='index.php'           ?'aktif':''; ?>">Beranda</a></li>
      <li><a href="<?php echo BASE_URL; ?>/tentang.php"          class="<?php echo $halaman=='tentang.php'         ?'aktif':''; ?>">Tentang</a></li>
      <li><a href="<?php echo BASE_URL; ?>/layanan.php"          class="<?php echo $halaman=='layanan.php'         ?'aktif':''; ?>">Layanan Poli</a></li>
      <li><a href="<?php echo BASE_URL; ?>/pendaftaran.php"      class="<?php echo $halaman=='pendaftaran.php'     ?'aktif':''; ?>">Daftar Online</a></li>
      <li><a href="<?php echo BASE_URL; ?>/cek-pendaftaran.php"  class="<?php echo $halaman=='cek-pendaftaran.php' ?'aktif':''; ?>">Cek Pendaftaran</a></li>
    </ul>
  </div>
</nav>