  document.addEventListener("DOMContentLoaded", () => {
    const toggle = document.getElementById("inviteToggle");
    const colorBlocks = document.querySelectorAll(".color-block");
    const container = document.querySelector(".container");

    const applyColor = (color) => {
      if (toggle) {
        toggle.style.background = color;
        toggle.style.boxShadow = `0 0 10px ${color}`;
      }
      if (container) {
        container.style.background = color;
      }
      localStorage.setItem("inviteAccentColor", color);
    };

    if (toggle && colorBlocks.length > 0) {
      colorBlocks.forEach(block => {
        block.addEventListener("click", () => {
          const color = block.style.background;
          applyColor(color);
        });
      });

      // Appliquer la couleur sauvegardée si elle existe
      const savedColor = localStorage.getItem("inviteAccentColor");
      if (savedColor) {
        applyColor(savedColor);
      }
    }
  });

  function toggleMenu() {
    const menu = document.getElementById("menu");
    if (!menu) return;
    if (menu.classList.contains("active")) {
      menu.classList.remove("active");
      setTimeout(() => menu.style.display = "none", 300);
    } else {
      menu.style.display = "flex";
      setTimeout(() => menu.classList.add("active"), 10);
    }
  }