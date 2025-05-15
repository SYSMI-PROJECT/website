let isEditing = false;

document.getElementById("editToggle").addEventListener("click", () => {
  isEditing = !isEditing;
  document.querySelectorAll(".objet").forEach(obj => {
    obj.setAttribute("draggable", isEditing);
  });
});

document.querySelectorAll(".objet").forEach(obj => {
  obj.addEventListener("dragstart", e => {
    e.dataTransfer.setData("text/plain", e.target.dataset.nom);
    e.dataTransfer.setDragImage(e.target, 40, 40);
    e.target.classList.add("dragging");
  });

  obj.addEventListener("dragend", e => {
    e.target.classList.remove("dragging");
  });
});

document.getElementById("chambre").addEventListener("dragover", e => {
  if (!isEditing) return;
  e.preventDefault();
  const dragging = document.querySelector(".dragging");
  if (!dragging) return;
  const rect = e.currentTarget.getBoundingClientRect();
  dragging.style.left = `${e.clientX - rect.left - 40}px`;
  dragging.style.top = `${e.clientY - rect.top - 40}px`;
});

// Sécurité : vérifier si le bouton existe
const saveBtn = document.getElementById("saveRoom");
if (saveBtn) {
  saveBtn.addEventListener("click", () => {
    const objets = document.querySelectorAll(".objet");
    const data = {};

    objets.forEach(obj => {
      data[obj.dataset.nom] = {
        left: parseInt(obj.style.left || 0),
        top: parseInt(obj.style.top || 0)
      };
    });

    fetch('/chambre/save', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
    }).then(res => res.json())
      .then(data => {
        if (data.success) alert("✅ Chambre sauvegardée !");
        else alert("❌ Erreur lors de la sauvegarde");
      });
  });
}
