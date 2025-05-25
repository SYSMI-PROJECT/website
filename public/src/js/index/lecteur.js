  const lecteur = document.getElementById('lecteur');
  const barre = document.getElementById('barre-progression');
  const temps = document.getElementById('temps-actuel');
  const titreMorceau = document.getElementById('titre-morceau');
  let enLecture = false;

  function toggleLecture() {
    if (lecteur.paused) {
      lecteur.volume = 1.0;
      lecteur.play();
      enLecture = true;
    } else {
      lecteur.pause();
      enLecture = false;
    }
  }

  lecteur.addEventListener('timeupdate', () => {
    const pourcentage = (lecteur.currentTime / lecteur.duration) * 100;
    barre.style.width = pourcentage + '%';

    const minutes = Math.floor(lecteur.currentTime / 60);
    const secondes = Math.floor(lecteur.currentTime % 60).toString().padStart(2, '0');
    temps.textContent = `${minutes}:${secondes}`;
  });

  // 🎶 Mettre à jour le titre dans le <marquee>
  lecteur.addEventListener('loadedmetadata', () => {
    const src = lecteur.src;
    const nomFichier = decodeURIComponent(src.split('/').pop());
    const titre = nomFichier.replace(/\.[^/.]+$/, '');
    titreMorceau.textContent = `🎶 En lecture : ${titre} 🎶`;
  });