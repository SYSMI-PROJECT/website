function moveLeft() {
  const widgetItems = document.getElementById('widget-items');
  widgetItems.scrollBy({ left: -200, behavior: 'smooth' });
}

function moveRight() {
  const widgetItems = document.getElementById('widget-items');
  widgetItems.scrollBy({ left: 200, behavior: 'smooth' });
}

function showWidgetContent(type) {
  const widgetItems = document.getElementById('widget-items');

  widgetItems.innerHTML = "";

  const wrapper = document.createElement('div');
  wrapper.className = "widget-content fade-in";

  if (type === 'stats') {
    wrapper.innerHTML = `
      <div class="widget-item stats-layout">
        <div class="stats-header">
          <h3 class="stats-name"><i class="fas fa-user"></i> ${prenom}</h3>
        </div>
        <div class="stats-data">
          <ul class="stats-list">
            <li><strong>💬 Messages :</strong> 312</li>
            <div class="divider">/</div>
            <li><strong>⭐ XP :</strong> 840</li>
            <li><strong>⬆️ Niveau :</strong> 9</li>
          </ul>
          <button onclick="resetWidget()">↩ Retour</button>
        </div>
      </div>
    `;
  } else if (type === 'cadeau') {
    wrapper.innerHTML = `
      <div class="widget-item">
        <h3>🎁 Cadeau du jour</h3>
        <p>Tu as gagné <strong>+5 étoiles</strong> ✨</p>
        <button onclick="claimGift()">Réclamer</button>
        <button onclick="resetWidget()">Retour</button>
      </div>
    `;
  }

  widgetItems.appendChild(wrapper);
}

function resetWidget() {
  const widgetItems = document.getElementById('widget-items');
  widgetItems.innerHTML = "";

  const wrapper = document.createElement('div');
  wrapper.className = "widget-content fade-in";

  wrapper.innerHTML = `
    <div class="widget-item">
      <p>📊 Statistiques</p>
      <button onclick="showWidgetContent('stats')">Voir</button>
    </div>
    <div class="widget-item">
      <p>🎁 Cadeau du jour</p>
      <button onclick="showWidgetContent('cadeau')">Voir</button>
    </div>
  `;

  widgetItems.appendChild(wrapper);
}

function claimGift() {
  alert("🎉 Cadeau bien réclamé !");
}
