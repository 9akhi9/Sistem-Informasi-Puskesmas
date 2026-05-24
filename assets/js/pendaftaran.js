// assets/js/pendaftaran.js – JavaScript Murni

function gantilangkah(n) {
  for (var i = 1; i <= 6; i++) {
    var kotak = document.getElementById("kotak" + i);
    if (kotak) kotak.style.display = i === n ? "block" : "none";
  }
  document.getElementById("inputLangkah").value = n;
  updateIndikator(n);
  if (n === 4) buatTanggal();
  window.scrollTo(0, 0);
}

function updateIndikator(aktif) {
  var items = document.querySelectorAll(".langkah-item");
  var garis = document.querySelectorAll(".langkah-garis");
  items.forEach(function (el, i) {
    var n = i + 1;
    el.classList.remove("aktif", "selesai");
    if (n < aktif) el.classList.add("selesai");
    else if (n === aktif) el.classList.add("aktif");
  });
  garis.forEach(function (el, i) {
    if (i < aktif - 1) el.classList.add("selesai");
    else el.classList.remove("selesai");
  });
}

function pilihPoli(radio) {
  document.querySelectorAll(".poli-pilih-item").forEach(function (el) {
    el.classList.remove("terpilih");
  });
  radio.closest(".poli-pilih-item").classList.add("terpilih");
  document.getElementById("namaPoliDisplay").value =
    radio.getAttribute("data-nama");
}

function pilihanSesi(radio) {
  document.querySelectorAll(".sesi-item").forEach(function (el) {
    el.classList.remove("terpilih");
  });
  radio.closest(".sesi-item").classList.add("terpilih");
}

function validasiLanjut(langkah) {
  if (langkah === 2) {
    if (!document.querySelector('[name="poli_id"]:checked')) {
      alert("Silakan pilih poli!");
      return;
    }
  }
  if (langkah === 3) {
    var wajib = [
      "nama_lengkap",
      "nik",
      "tanggal_lahir",
      "jenis_kelamin",
      "no_telepon",
      "alamat",
    ];
    for (var i = 0; i < wajib.length; i++) {
      var el = document.querySelector('[name="' + wajib[i] + '"]');
      if (el && el.value.trim() === "") {
        alert("Lengkapi semua field wajib!");
        el.focus();
        return;
      }
    }
    var nik = document.querySelector('[name="nik"]');
    if (nik && nik.value.length !== 16) {
      alert("NIK harus 16 digit!");
      nik.focus();
      return;
    }
  }
  if (langkah === 4) {
    if (!document.getElementById("tanggalKunjungan").value) {
      alert("Pilih tanggal kunjungan!");
      return;
    }
  }
  if (langkah === 5) {
    if (!document.querySelector('[name="sesi"]:checked')) {
      alert("Pilih sesi kunjungan!");
      return;
    }
    isiRingkasan();
  }
  gantilangkah(langkah + 1);
}

function isiRingkasan() {
  function val(name) {
    var el = document.querySelector('[name="' + name + '"]');
    return el ? el.value : "-";
  }
  function set(id, v) {
    var el = document.getElementById(id);
    if (el) el.textContent = v || "-";
  }
  set("rNama", val("nama_lengkap"));
  set("rNik", val("nik"));
  set("rKelamin", val("jenis_kelamin"));
  set("rTelp", val("no_telepon"));
  set("rJaminan", val("jenis_jaminan"));
  set("rPoli", val("nama_poli_display"));
  set("rSesi", val("sesi"));
  var tl = val("tanggal_lahir");
  if (tl) set("rLahir", formatTanggal(tl));
  var tk = document.getElementById("tanggalKunjungan");
  if (tk && tk.value) set("rTanggal", formatTanggal(tk.value));
}

function formatTanggal(str) {
  if (!str) return "-";
  var bagian = str.split("-");
  var bulan = [
    "Januari",
    "Februari",
    "Maret",
    "April",
    "Mei",
    "Juni",
    "Juli",
    "Agustus",
    "September",
    "Oktober",
    "November",
    "Desember",
  ];
  return (
    parseInt(bagian[2]) + " " + bulan[parseInt(bagian[1]) - 1] + " " + bagian[0]
  );
}

function buatTanggal() {
  var grid = document.getElementById("tanggalGrid");
  if (!grid) return;
  var hari = ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"];
  var bulan = [
    "Jan",
    "Feb",
    "Mar",
    "Apr",
    "Mei",
    "Jun",
    "Jul",
    "Agu",
    "Sep",
    "Okt",
    "Nov",
    "Des",
  ];
  var terpilih = document.getElementById("tanggalKunjungan").value;
  var d = new Date();
  d.setDate(d.getDate() + 1);
  var jumlah = 0;
  grid.innerHTML = "";
  while (jumlah < 14) {
    if (d.getDay() !== 0) {
      var y = d.getFullYear(),
        m = String(d.getMonth() + 1).padStart(2, "0"),
        t = String(d.getDate()).padStart(2, "0");
      var nilai = y + "-" + m + "-" + t;
      var btn = document.createElement("button");
      btn.type = "button";
      btn.setAttribute("data-nilai", nilai);
      btn.className = "tanggal-btn" + (terpilih === nilai ? " terpilih" : "");
      btn.innerHTML =
        '<div class="tgl-hari">' +
        hari[d.getDay()] +
        '</div><div class="tgl-angka">' +
        t +
        '</div><div class="tgl-bulan">' +
        bulan[d.getMonth()] +
        "</div>";
      btn.onclick = function () {
        document.querySelectorAll(".tanggal-btn").forEach(function (b) {
          b.classList.remove("terpilih");
        });
        this.classList.add("terpilih");
        document.getElementById("tanggalKunjungan").value =
          this.getAttribute("data-nilai");
      };
      grid.appendChild(btn);
      jumlah++;
    }
    d.setDate(d.getDate() + 1);
  }
}

document.addEventListener("DOMContentLoaded", function () {
  buatTanggal();
  var l = document.getElementById("inputLangkah");
  updateIndikator(l ? parseInt(l.value) : 1);
});
