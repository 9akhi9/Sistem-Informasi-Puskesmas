<?php $halAktif = basename($_SERVER['PHP_SELF']); ?>
<aside class="admin-sidebar">
  <div class="sidebar-brand">
    <span class="brand-ikon">+</span>
    <div><span class="brand-nama">PUSKESMAS</span><span class="brand-sub" style="color:rgba(255,255,255,0.5)">Admin</span></div>
  </div>
  <nav class="sidebar-nav">
    <a href="dashboard.php"   class="<?php echo $halAktif=='dashboard.php'  ?'aktif':''; ?>">&#128202; Dashboard</a>
    <a href="pendaftaran.php" class="<?php echo $halAktif=='pendaftaran.php'?'aktif':''; ?>">&#128203; Pendaftaran</a>
    <a href="poli.php"        class="<?php echo $halAktif=='poli.php'       ?'aktif':''; ?>">&#128657; Manajemen Poli</a>
    <a href="laporan.php"     class="<?php echo $halAktif=='laporan.php'    ?'aktif':''; ?>">&#128196; Laporan</a>
  </nav>
  <div class="sidebar-bawah">
    <a href="../index.php">&#127968; Website</a>
    <a href="logout.php">&#128275; Logout</a>
  </div>
</aside>