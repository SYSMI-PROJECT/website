document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    
    form.addEventListener('submit', function(event) {
      event.preventDefault(); // Empêche l'envoi classique du formulaire
  
      const email = form.querySelector('input[name="email"]').value;
      const password = form.querySelector('input[name="password"]').value;
  
      // Envoi des données au serveur via Fetch
      fetch('/login', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email, password })
      })
      .then(response => {
        if (response.ok) {
          window.location.href = '/';  // Redirige après une connexion réussie
        } else {
          alert('Identifiants incorrects');
        }
      })
      .catch(error => {
        console.error('Erreur lors de la connexion', error);
      });
    });
  });
  