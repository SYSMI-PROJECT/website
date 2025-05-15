document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    
    form.addEventListener('submit', function(event) {
      event.preventDefault();
  
      const email = form.querySelector('input[name="email"]').value;
      const password = form.querySelector('input[name="password"]').value;
  
      fetch('/login', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email, password })
      })
      .then(response => {
        if (response.ok) {
          window.location.href = '/';
        } else {
          alert('Identifiants incorrects');
        }
      })
      .catch(error => {
        console.error('Error while connecting', error);
      });
    });
  });
  