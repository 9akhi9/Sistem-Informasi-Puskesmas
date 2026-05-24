<?php
require_once '../config/koneksi.php';
wajibLogin();
$db = koneksi();

$dari   = $_GET['dari']   ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');

$where = "p.tanggal_kunjungan BETWEEN '".$db->real_escape_string($dari)."' AND '".$db->real_escape_string($sampai)."'";

// Ekspor CSV
if (isset($_GET['ekspor'])) {
    $data = $db->query("SELECT p.kode_daftar,p.nama_lengkap,p.nik,p.jenis_kelamin,p.no_telepon,pl.nama_poli,p.tanggal_kunjungan,p.sesi,p.nomor_antrian,p.status,p.created_at FROM pendaftaran p JOIN poli pl ON p.poli_id=pl.id WHERE $where ORDER BY p.tanggal_kunjungan,p.nomor_antrian")->fetch_all(MYSQLI_ASSOC);
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=laporan-pendaftaran-'.date('Ymd').'.csv');
    $out = fopen('php://output','w');
    fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM utf-8
    fputcsv($out, ['Kode','Nama','NIK','Kelamin','Telepon','Poli','Tanggal','Sesi','No.Antrian','Status','Didaftarkan']);
    foreach ($data as $r) { fputcsv($out, array_values($r)); }
    fclose($out); exit;
}

$rekap = $db->query("SELECT status, COUNT(*) AS jml FROM pendaftaran p WHERE $where GROUP BY status")->fetch_all(MYSQLI_ASSOC);
$perPoli = $db->query("SELECT pl.nama_poli, COUNT(p.id) AS jml FROM poli pl LEFT JOIN pendaftaran p ON pl.id=p.poli_id AND $where GROUP BY pl.id,pl.nama_poli ORDER BY jml DESC")->fetch_all(MYSQLI_ASSOC);
$harian  = $db->query("SELECT tanggal_kunjungan, COUNT(*) AS jml FROM pendaftaran p WHERE $where GROUP BY tanggal_kunjungan ORDER BY tanggal_kunjungan")->fetch_all(MYSQLI_ASSOC);
$total   = array_sum(array_column($rekap,'jml'));
?>
<!DOCTYPE html><html lang="id"><head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Laporan – <?php echo APP_NAME; ?></title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head><body class="admin-body">
<?php include 'includes/sidebar.php'; ?>
<div class="admin-konten">
  <?php include 'includes/topbar.php'; ?>
  <div class="admin-utama">
    <div class="admin-page-header">
      <h1 class="admin-judul">Laporan Pendaftaran</h1>
      <a href="?dari=<?php echo $dari; ?>&sampai=<?php echo $sampai; ?>&ekspor=1" class="tombol tombol-utama tombol-kecil">
        &#128196; Ekspor CSV
      </a>
    </div>

    <!-- Filter Periode -->
    <form method="GET" class="form-filter" style="margin-bottom:24px">
      <label style="font-size:13px;font-weight:600">Dari:</label>
      <input type="date" name="dari" value="<?php echo $dari; ?>">
      <label style="font-size:13px;font-weight:600">Sampai:</label>
      <input type="date" name="sampai" value="<?php echo $sampai; ?>">
      <button type="submit" class="tombol tombol-utama tombol-kecil">Tampilkan</button>
    </form>

    <!-- Ringkasan Status -->
    <div class="stat-grid" style="margin-bottom:24px">
      <div class="stat-kartu biru"><p>Total</p><h2><?php echo $total; ?></h2></div>
      <?php
      $warna = ['Menunggu'=>'oranye','Dikonfirmasi'=>'biru2','Selesai'=>'hijau','Dibatalkan'=>'merah'];
      $rekapMap = array_column($rekap, 'jml', 'status');
      foreach($warna as $st=>$kl): ?>
      <div class="stat-kartu <?php echo $kl; ?>"><p><?php echo $st; ?></p><h2><?php echo $rekapMap[$st]??0; ?></h2></div>
      <?php endforeach; ?>
    </div>

    <div class="dash-grid">
      <!-- Per Poli -->
      <div class="admin-kartu">
        <div class="kartu-header"><h3>Rekap Per Poli</h3></div>
        <table class="admin-tabel">
          <thead><tr><th>Poli</th><th style="text-align:center">Jumlah</th><th>Proporsi</th></tr></thead>
          <tbody>
          <?php foreach($perPoli as $pp): $pct=$total>0?round(($pp['jml']/$total)*100):0; ?>
          <tr>
            <td><?php echo htmlspecialchars($pp['nama_poli']); ?></td>
            <td style="text-align:center"><strong><?php echo $pp['jml']; ?></strong></td>
            <td style="width:40%">
              <div class="poli-bar-track">
                <div class="poli-bar-isi" style="width:<?php echo $pct; ?>%"></div>
              </div>
              <small><?php echo $pct; ?>%</small>
            </td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <!-- Harian -->
      <div class="admin-kartu">
        <div class="kartu-header"><h3>Rekap Harian</h3></div>
        <div class="tabel-scroll" style="max-height:360px">
        <table class="admin-tabel">
          <thead><tr><th>Tanggal</th><th style="text-align:center">Jumlah</th><th>Bar</th></tr></thead>
          <tbody>
          <?php
          $maks = $harian ? max(array_column($harian,'jml')) : 1;
          foreach($harian as $h): $pct=round(($h['jml']/$maks)*100); ?>
          <tr>
            <td><?php echo date('D, d M Y', strtotime($h['tanggal_kunjungan'])); ?></td>
            <td style="text-align:center"><strong><?php echo $h['jml']; ?></strong></td>
            <td><div class="poli-bar-track"><div class="poli-bar-isi" style="width:<?php echo $pct; ?>%;background:#3b82f6"></div></div></td>
          </tr>
          <?php endforeach; ?>
          <?php if(!$harian): ?><tr><td colspan="3" style="text-align:center;color:#9ca3af">Tidak ada data</td></tr><?php endif; ?>
          </tbody>
        </table></div>
      </div>
    </div>
  </div>
</div>
</body></html>