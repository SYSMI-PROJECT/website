function showForm() {
  const form = document.getElementById('edit-form-container');
  form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

function saveForm(event) {
  event.preventDefault();
  const mood = document.getElementById('newMood').value;
  const quote = document.getElementById('newQuote').value;

  if (mood) document.querySelector('.status').textContent = `🎭 Humeur : ${mood}`;
  if (quote) document.querySelector('.quote').textContent = `📝 Citation du jour : "${quote}"`;

  document.getElementById('edit-form-container').style.display = 'none';
}