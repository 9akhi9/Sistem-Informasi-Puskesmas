<?php // admin/includes/topbar.php ?>
<div class="admin-topbar">
  <span class="topbar-info">Sistem Informasi Puskesmas &rsaquo; Admin</span>
  <div class="topbar-pengguna">
    <div class="topbar-avatar"><?php echo strtoupper(substr($_SESSION['admin_nama']??'A',0,1)); ?></div>
    <span><?php echo htmlspecialchars($_SESSION['admin_nama']??'Admin'); ?></span>
  </div>
</div>