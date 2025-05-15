    function toggleMenu() {
      const menu = document.getElementById("menu");
      if (menu.classList.contains("active")) {
        menu.classList.remove("active");
        setTimeout(() => menu.style.display = "none", 300);
      } else {
        menu.style.display = "flex";
        setTimeout(() => menu.classList.add("active"), 10);
      }
    }