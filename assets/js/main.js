// assets/js/main.js – JavaScript Murni

function toggleMenu() {
  var menu = document.getElementById('navMenu');
  if (menu) {
    if (menu.classList.contains('terbuka')) {
      menu.classList.remove('terbuka');
    } else {
      menu.classList.add('terbuka');
    }
  }
}

document.addEventListener('click', function(e) {
  var menu = document.getElementById('navMenu');
  var hamburger = document.querySelector('.hamburger');
  if (menu && hamburger && !hamburger.contains(e.target) && !menu.contains(e.target)) {
    menu.classList.remove('terbuka');
  }
});

var pesan = document.querySelectorAll('.pesan-sukses, .pesan-error');
pesan.forEach(function(el) {
  setTimeout(function() { el.style.display = 'none'; }, 4000);
});