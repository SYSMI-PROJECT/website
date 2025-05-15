function toggleFriendMenu(id) {
    document.querySelectorAll('.friend-menu').forEach(m => {
      if (m.id !== 'friend-menu-' + id) m.style.display = 'none';
    });
    const menu = document.getElementById('friend-menu-' + id);
    menu.style.display = (menu.style.display === 'flex') ? 'none' : 'flex';
  }

  document.addEventListener('click', function(e) {
    if (!e.target.closest('.friend-item')) {
      document.querySelectorAll('.friend-menu').forEach(m => m.style.display = 'none');
    }
  });