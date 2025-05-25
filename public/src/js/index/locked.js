function unlockCard(el, cardId) {
  const icon = el.querySelector('i');
  const card = document.getElementById(cardId);
  el.disabled = true;

  fetch('/api/coin/update', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ amount: -10 }) // Prix à ajuster
  })
    .then(res => res.json())
    .then(data => {
      const counter = document.getElementById('scoin-count');

      if (data.erreur === true) {
        counter.textContent = 'erreur';
        el.disabled = false;
        return;
      }

      if (data.success) {
        el.classList.add('unlocked');
        icon.classList.replace('fa-lock', 'fa-lock-open');
        card.classList.remove('locked');
        card.style.pointerEvents = 'auto';
        updateScoinCounter(data.newEtoile, -10);
        setTimeout(() => el.remove(), 400);
      } else {
        const deniedSound = document.getElementById('scoin-denied-sound');
        deniedSound?.play?.();

        el.classList.add('shake-refuse');
        el.style.border = '2px solid red';
        setTimeout(() => {
          el.classList.remove('shake-refuse');
          el.style.border = '';
          el.disabled = false;
        }, 800);
      }
    })
    .catch(err => {
      console.error('Erreur unlock :', err);
      el.disabled = false;
      document.getElementById('scoin-count').textContent = 'erreur';
    });
}


// 🔢 Formater les grands nombres
function formatNumber(num) {
  if (num >= 1_000_000_000) return (num / 1_000_000_000).toFixed(1).replace(/\.0$/, '') + 'B';
  if (num >= 1_000_000) return (num / 1_000_000).toFixed(1).replace(/\.0$/, '') + 'M';
  if (num >= 1_000) return (num / 1_000).toFixed(1).replace(/\.0$/, '') + 'K';
  return num.toString();
}


// 🧮 Mise à jour animée du compteur S-Coins
function updateScoinCounter(newValue, variation = 0) {
  const counter = document.getElementById('scoin-count');
  const anim = document.getElementById('scoin-anim');
  const soundGain = document.getElementById('scoin-sound-gain');
  const soundLoss = document.getElementById('scoin-sound-loss');
  const oldValue = parseInt(counter.getAttribute('data-raw') || counter.textContent.replace(/[^\d]/g, ''), 10);
  const diff = newValue - oldValue;
  const step = diff / 10;
  let current = oldValue;
  let i = 0;

  counter.setAttribute('data-raw', newValue);

  const interval = setInterval(() => {
    current += step;
    counter.textContent = formatNumber(Math.round(current));
    i++;
    if (i >= 10) {
      counter.textContent = formatNumber(newValue);
      clearInterval(interval);
    }
  }, 30);

  if (variation !== 0) {
    anim.textContent = (variation > 0 ? '+' : '') + variation;
    anim.className = 'scoin-anim show ' + (variation > 0 ? 'positive' : 'negative');

    (variation > 0 ? soundGain : soundLoss)?.play?.();

    setTimeout(() => anim.classList.remove('show'), 600);
  }
}
