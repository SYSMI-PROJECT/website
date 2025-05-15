function updateClock() {
    const now = new Date();
    const time = now.toLocaleTimeString('fr-FR');
    document.getElementById("clock").textContent = time;
  }
  setInterval(updateClock, 1000);
  updateClock();

  function collectReward() {
    alert("🎉 Tu as reçu 10 étoiles !");
  }