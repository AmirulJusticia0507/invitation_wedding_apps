let currentSlide = 1;
const totalSlides = 5;

// Sembunyikan semua slide kecuali yang pertama saat halaman dimuat
window.onload = function () {
  for (let i = 2; i <= totalSlides; i++) {
    const slide = document.getElementById(`slide-${i}`);
    if (slide) slide.style.display = 'none';
  }
};

// Fungsi untuk menampilkan slide berikutnya
function nextSlide() {
  if (currentSlide < totalSlides) {
    document.getElementById(`slide-${currentSlide}`).style.display = 'none';
    currentSlide++;
    document.getElementById(`slide-${currentSlide}`).style.display = 'block';
    scrollToSlide(currentSlide);
  }
}

// Fungsi untuk membuka undangan (dari slide 1 ke slide 2)
function openInvitation() {
    document.getElementById('slide-1').style.display = 'none';
    document.getElementById('slide-2').style.display = 'block';
    currentSlide = 2;
  }
  

// Fungsi untuk berpindah ke slide tertentu (jika diperlukan)
function goToSlide(slideNumber) {
  if (slideNumber >= 1 && slideNumber <= totalSlides) {
    document.getElementById(`slide-${currentSlide}`).style.display = 'none';
    document.getElementById(`slide-${slideNumber}`).style.display = 'block';
    currentSlide = slideNumber;
    scrollToSlide(currentSlide);
  }
}

// Fungsi smooth scroll ke slide
function scrollToSlide(slideNumber) {
  const slide = document.getElementById(`slide-${slideNumber}`);
  if (slide) {
    slide.scrollIntoView({ behavior: 'smooth' });
  }
}

// OPTIONAL: tombol keyboard panah untuk navigasi
document.addEventListener('keydown', function (e) {
  if (e.key === 'ArrowRight') {
    nextSlide();
  }
  if (e.key === 'ArrowLeft') {
    if (currentSlide > 2) {
      document.getElementById(`slide-${currentSlide}`).style.display = 'none';
      currentSlide--;
      document.getElementById(`slide-${currentSlide}`).style.display = 'block';
      scrollToSlide(currentSlide);
    }
  }
});
