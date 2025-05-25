let videoStates = {};
  
function saveVideoStates() {
  document.querySelectorAll('video').forEach(video => {
    const publicationId = video.id.split('-')[1];
    videoStates[publicationId] = {
      currentTime: video.currentTime,
      playbackRate: video.playbackRate,
      paused: video.paused,
      muted: video.muted
    };
  });
}

function restoreVideoStates() {
  document.querySelectorAll('video').forEach(video => {
    const publicationId = video.id.split('-')[1];
    if (videoStates[publicationId]) {
      const state = videoStates[publicationId];
      video.currentTime = state.currentTime;
      video.playbackRate = state.playbackRate;
      if (!state.paused) {
        video.play().catch(err => console.error('Erreur lecture:', err));
      }
      video.muted = state.muted;
    }
  });
}

function refreshPosts() {
  const homeIcon = document.getElementById('home-icon');
  homeIcon.classList.remove('fa-home');
  homeIcon.classList.add('fa-spinner', 'fa-spin');

  saveVideoStates();

  var xhr = new XMLHttpRequest();
  xhr.open('GET', window.location.href, true);
  xhr.onload = function() {
    if (xhr.status === 200) {
      var tempDiv = document.createElement('div');
      tempDiv.innerHTML = xhr.responseText;
      var newPosts = tempDiv.querySelector('#posts-container');
      if (newPosts) {
        document.getElementById('posts-container').innerHTML = newPosts.innerHTML;
      }
      attachInteractions();
      restoreVideoStates();
      window.scrollTo({ top: 0, behavior: 'smooth' });
      homeIcon.classList.remove('fa-spinner', 'fa-spin');
      homeIcon.classList.add('fa-home');
    }
  };
  xhr.send();
}

function attachInteractions() {
  // --- Gestion du menu ellipse pour les commentaires ---
  document.querySelectorAll('.comment-options .menu-button').forEach(button => {
    button.addEventListener('click', event => {
      event.stopPropagation();
      const dropdown = button.nextElementSibling;
      dropdown.classList.toggle('show');
    });
  });
  
  // --- Affichage / fermeture de la modale des commentaires ---
  document.querySelectorAll('.comment-button').forEach(button => {
    button.addEventListener('click', event => {
      event.stopPropagation();
      const publicationId = button.dataset.publicationId;
      const modal = document.getElementById(`comment-modal-${publicationId}`);
      if (modal) {
        modal.style.display = 'block';
      }
    });
  });
  
  document.querySelectorAll('.close-modal').forEach(button => {
    button.addEventListener('click', event => {
      event.stopPropagation();
      const publicationId = button.dataset.publicationId;
      const modal = document.getElementById(`comment-modal-${publicationId}`);
      if (modal) {
        modal.style.display = 'none';
      }
    });
  });
  
  // --- Fermeture de la modale si clic en dehors ---
  document.addEventListener('click', function(event) {
    // Si le clic ne provient pas d'un élément à l'intérieur d'une modale ou du bouton d'ouverture des commentaires
    if (!event.target.closest('.comment-modal') && !event.target.closest('.comment-button')) {
      document.querySelectorAll('.comment-modal').forEach(modal => {
        modal.style.display = 'none';
      });
    }
  });
  
  // --- Fermeture de la modale lors du scroll ou d'un glissement ---
  window.addEventListener('scroll', function() {
    document.querySelectorAll('.comment-modal').forEach(modal => {
      modal.style.display = 'none';
    });
  });
  window.addEventListener('touchmove', function() {
    document.querySelectorAll('.comment-modal').forEach(modal => {
      modal.style.display = 'none';
    });
  });
  
  // --- Gestion AJAX des commentaires ---
  document.querySelectorAll('form.ajax-comment-form').forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(form);
      const publicationId = formData.get('publication_id');
      const commentInput = form.querySelector('input[name="comment"]');
      if (!commentInput.value.trim()) {
        alert("Le commentaire ne peut pas être vide.");
        return;
      }
      fetch('traitements/Formulaires/comments.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const commentList = document.getElementById(`comment-list-${publicationId}`);
          const commentCount = document.getElementById(`comment-count-${publicationId}`);
          if (commentList && data.commentHtml) {
            const newComment = document.createElement('div');
            newComment.classList.add('comment');
            newComment.innerHTML = data.commentHtml;
            commentList.prepend(newComment);
          }
          if (commentCount) {
            commentCount.textContent = parseInt(commentCount.textContent) + 1;
          }
          commentInput.value = '';
        } else {
          if (data.redirect) {
            window.location.href = data.redirect;
          } else {
            alert("Erreur : " + (data.message || "Une erreur s'est produite."));
          }
        }
      })
      .catch(error => {
        console.error('Erreur AJAX :', error);
        alert("Une erreur s'est produite. Veuillez vérifier la console.");
      });
    });
  });
  
  // --- Gestion du like/unlike ---
document.querySelectorAll('.like-button').forEach(button => {
  button.addEventListener('click', function () {
    const btn = this;
    const publicationId = btn.getAttribute('data-publication-id');
    const action = btn.getAttribute('data-action');

    fetch('/api/like', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ publication_id: publicationId, action })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const likeCountElem = btn.closest('.like-container').querySelector('.like-count');
        likeCountElem.textContent = data.likes;

        if (action === 'like') {
          btn.setAttribute('data-action', 'unlike');
          btn.classList.add('liked');
        } else {
          btn.setAttribute('data-action', 'like');
          btn.classList.remove('liked');
        }
      } else {
        alert("Erreur : " + data.message);
      }
    })
    .catch(error => {
      console.error('Erreur AJAX :', error);
      alert("Une erreur s'est produite. Veuillez réessayer.");
    });
  });
});

  // --- Lecture/Pause des vidéos ---
  document.querySelectorAll('.play-pause-button').forEach(button => {
    button.addEventListener('click', function() {
      const publicationId = this.id.split('-')[2];
      togglePlayPause(publicationId);
    });
  });
  
  // --- Barre de progression pour les vidéos ---
  document.querySelectorAll('.progress-container').forEach(container => {
    const publicationId = container.getAttribute('data-publication-id');
    const video = document.getElementById('video-' + publicationId);
    const handle = document.getElementById('handle-' + publicationId);
    if (video) {
      video.addEventListener('timeupdate', () => {
        const progressElem = container.querySelector('.progress');
        const percentage = (video.currentTime / video.duration) * 100;
        progressElem.style.width = percentage + '%';
        if (handle) { handle.style.left = percentage + '%'; }
      });
      video.addEventListener('progress', () => {
        const bufferedElem = document.getElementById('buffered-' + publicationId);
        if(video.buffered.length > 0) {
          let bufferedEnd = video.buffered.end(video.buffered.length - 1);
          let bufferedPercentage = (bufferedEnd / video.duration) * 100;
          if(bufferedElem) {
            bufferedElem.style.width = bufferedPercentage + '%';
          }
        }
      });
      function updateTime(e) {
        let clientX;
        if (typeof e.clientX !== 'undefined') {
          clientX = e.clientX;
        } else if (e.touches && e.touches.length > 0) {
          clientX = e.touches[0].clientX;
        } else if (e.changedTouches && e.changedTouches.length > 0) {
          clientX = e.changedTouches[0].clientX;
        } else {
          return;
        }
        const rect = container.getBoundingClientRect();
        let percentage = (clientX - rect.left) / rect.width;
        percentage = Math.min(Math.max(percentage, 0), 1);
        if (video && video.duration) {
          video.currentTime = percentage * video.duration;
        }
      }
      container.addEventListener('mousedown', function(e) {
        e.preventDefault();
        if (handle) { handle.style.display = 'block'; }
        updateTime(e);
        function onMouseMove(e) { updateTime(e); }
        function onMouseUp(e) {
          updateTime(e);
          if (handle) { handle.style.display = 'none'; }
          document.removeEventListener('mousemove', onMouseMove);
          document.removeEventListener('mouseup', onMouseUp);
        }
        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);
      });
      container.addEventListener('touchstart', e => { updateTime(e); if(handle) handle.style.display = 'block'; });
      container.addEventListener('touchmove', updateTime);
      container.addEventListener('touchend', e => { updateTime(e); if(handle) handle.style.display = 'none'; });
      container.addEventListener('mousemove', function(e) {
        const rect = container.getBoundingClientRect();
        const offsetX = e.clientX - rect.left;
        let percentage = offsetX / rect.width;
        percentage = Math.min(Math.max(percentage, 0), 1);
        const hoveredTime = percentage * video.duration;
        const tooltip = document.getElementById('tooltip-' + publicationId);
        if (tooltip) {
          tooltip.style.display = 'block';
          tooltip.style.left = offsetX + 'px';
          tooltip.innerText = formatTime(hoveredTime) + ' / ' + formatTime(video.duration);
        }
      });
      container.addEventListener('mouseleave', function() {
        const tooltip = document.getElementById('tooltip-' + publicationId);
        if (tooltip) {
          tooltip.style.display = 'none';
        }
      });
    }
  });
  
  // --- Autoplay/Pause via IntersectionObserver ---
const videoObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    const video = entry.target;
    const publicationId = video.id.split('-')[1];  // Assure-toi que l'ID de la vidéo suit ce format
    const playButton = document.getElementById('play-pause-' + publicationId);
    
    if (entry.isIntersecting && entry.intersectionRatio >= 0.5) {
      // Pause les autres vidéos si la vidéo courante est visible
      document.querySelectorAll('video').forEach(v => {
        if (v !== video) { 
          v.pause();  // Pauser les autres vidéos
          const otherPlayButton = document.getElementById('play-pause-' + v.id.split('-')[1]);
          if (otherPlayButton) otherPlayButton.style.display = 'block';  // Afficher les autres boutons
        }
      });

      // Démute et joue la vidéo
      video.muted = false;
      video.play().then(() => {
        if (playButton) {
          playButton.style.display = 'none'; // Cacher le bouton play/pause si la vidéo joue
        }
      }).catch(error => {
        console.error('Erreur en lecture automatique:', error);
      });
    } else {
      // Pause la vidéo si elle n'est plus dans la vue
      video.pause();
      if (playButton) {
        playButton.style.display = 'block'; // Afficher le bouton play/pause
      }
    }
  });
}, { threshold: 0.5 });

document.querySelectorAll('video').forEach(video => {
  videoObserver.observe(video); // Observer toutes les vidéos présentes
});

document.addEventListener("visibilitychange", function() {
  if (document.hidden) {
    // Met en pause toutes les vidéos si l'onglet devient invisible
    document.querySelectorAll('video').forEach(video => video.pause());
  }
});


  
  // --- Afficher plus / Afficher moins ---
  document.querySelectorAll('.show-more').forEach(button => {
    button.addEventListener('click', function(e) {
      e.stopPropagation();
      const contenu = this.parentElement.querySelector("p.contenu");
      if (contenu.classList.contains("expanded")) {
        contenu.style.maxHeight = "60px";
        contenu.classList.remove("expanded");
        this.innerText = "Afficher plus";
      } else {
        contenu.style.maxHeight = contenu.scrollHeight + "px";
        contenu.classList.add("expanded");
        this.innerText = "Afficher moins";
      }
    });
  });
  
  // --- Bouton mute/unmute ---
  document.querySelectorAll('.mute-button').forEach(button => {
    button.addEventListener('click', function() {
      const publicationId = this.getAttribute('data-publication-id');
      const video = document.getElementById('video-' + publicationId);
      if (video) {
        video.muted = !video.muted;
        const icon = this.querySelector('i');
        if (video.muted) {
          icon.classList.remove('fa-volume-low');
          icon.classList.add('fa-volume-mute');
        } else {
          icon.classList.remove('fa-volume-mute');
          icon.classList.add('fa-volume-low');
        }
      }
    });
  });
  
  // --- Menu déroulant pour les contrôles médias ---
  document.querySelectorAll('.menu-button').forEach(button => {
    button.addEventListener('click', function(e) {
      e.stopPropagation();
      const dropdown = this.closest('.media-controls').querySelector('.dropdown-menu');
      dropdown.classList.toggle('show');
    });
  });
  
  // Fermer le dropdown en cliquant en dehors
  document.addEventListener('click', () => {
    document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
      menu.classList.remove('show');
    });
  });
  
  // Empêcher la fermeture du dropdown lorsqu'on clique dedans
  document.querySelectorAll('.dropdown-menu').forEach(menu => {
    menu.addEventListener('click', function(e) {
      e.stopPropagation();
    });
  });
  
  // --- Fullscreen, Download et Share ---
  document.querySelectorAll('.fullscreen-button').forEach(button => {
    button.addEventListener('click', function(e) {
      e.stopPropagation();
      const publicationId = this.closest('.dropdown-menu').parentElement.querySelector('.publication').id.split('-')[1];
      toggleFullscreen(publicationId, e);
    });
  });
  
  document.querySelectorAll('.download-button').forEach(button => {
    button.addEventListener('click', function(e) {
      e.stopPropagation();
      const mediaUrl = this.closest('.dropdown-menu').querySelector('a')?.href || '';
      downloadMedia(mediaUrl, e);
    });
  });
  
  document.querySelectorAll('.share-media-button').forEach(button => {
    button.addEventListener('click', function(e) {
      e.stopPropagation();
      const mediaUrl = this.closest('.dropdown-menu').querySelector('a')?.href || '';
      shareMedia(mediaUrl, e);
    });
  });
  
  // --- Picture-in-Picture ---
  document.querySelectorAll('.pip-button').forEach(button => {
    button.addEventListener('click', function(e) {
      e.stopPropagation();
      const publicationId = this.closest('.dropdown-menu').parentElement.querySelector('.publication').id.split('-')[1];
      togglePictureInPicture(publicationId, e);
    });
  });
  
  // --- Contrôle de la vitesse de lecture ---
  document.querySelectorAll('.speed-button').forEach(button => {
    button.addEventListener('click', function(e) {
      e.stopPropagation();
      const publicationId = this.closest('.interactions').getAttribute('data-publication-id');
      cyclePlaybackSpeed(publicationId, e);
    });
  });
  
  // --- Contrôles de reculer/avancer ---
  document.querySelectorAll('.rewind-button').forEach(button => {
    button.addEventListener('click', function(e) {
      e.stopPropagation();
      const publicationId = this.closest('.dropdown-menu').parentElement.querySelector('.publication').id.split('-')[1];
      rewindVideo(publicationId, e);
    });
  });
  
  document.querySelectorAll('.forward-button').forEach(button => {
    button.addEventListener('click', function(e) {
      e.stopPropagation();
      const publicationId = this.closest('.dropdown-menu').parentElement.querySelector('.publication').id.split('-')[1];
      forwardVideo(publicationId, e);
    });
  });
  
  // --- Bookmark ---
  document.querySelectorAll('.bookmark-button').forEach(button => {
    button.addEventListener('click', function(e) {
      e.stopPropagation();
      const publicationId = this.closest('.dropdown-menu').parentElement.querySelector('.publication').id.split('-')[1];
      toggleBookmark(publicationId, e);
    });
  });
  
  // Boutons de scroll globaux
  const scrollContainer = document.querySelector('.scroll-container');
  document.getElementById('scroll-up').addEventListener('click', () => {
    scrollContainer.scrollBy({ top: -300, behavior: 'smooth' });
  });
  document.getElementById('scroll-down').addEventListener('click', () => {
    scrollContainer.scrollBy({ top: 300, behavior: 'smooth' });
  });
}

document.addEventListener('DOMContentLoaded', attachInteractions);

// --- Fonction utilitaire pour formater le temps ---
function formatTime(seconds) {
  const minutes = Math.floor(seconds / 60);
  const secs = Math.floor(seconds % 60);
  return (minutes < 10 ? '0' + minutes : minutes) + ':' + (secs < 10 ? '0' + secs : secs);
}

// --- Lecture/Pause des vidéos ---
async function togglePlayPause(publicationId) {
  const video = document.getElementById('video-' + publicationId);
  const playButton = document.getElementById('play-pause-' + publicationId);
  if (!video) return;

  if (video.paused) {
    const allVideos = document.querySelectorAll('video');
    for (const v of allVideos) {
      if (v !== video) {
        try {
          v.pause();
        } catch (e) {
          console.warn("Erreur pause sur autre vidéo:", e);
        }

        const otherId = v.id.split('-')[1];
        const otherBtn = document.getElementById('play-pause-' + otherId);
        if (otherBtn) otherBtn.style.display = 'block';
      }
    }
    try {
      await video.play();
      playButton.style.display = 'none';
    } catch (err) {
      console.error('Erreur lecture:', err);
    }

  } else {
    video.pause();
    playButton.style.display = 'block';
  }
}

// --- Fullscreen ---
function toggleFullscreen(publicationId, e) {
  e.stopPropagation();
  const elem = document.getElementById('media-wrapper-' + publicationId);
  if (!document.fullscreenElement) {
    elem.requestFullscreen().catch(err => {
      alert("Erreur en plein écran: " + err.message);
    });
  } else {
    document.exitFullscreen();
  }
}

// --- Télécharger le média ---
function downloadMedia(mediaUrl, e) {
  e.stopPropagation();
  const a = document.createElement('a');
  a.href = mediaUrl;
  a.download = mediaUrl.substring(mediaUrl.lastIndexOf('/') + 1);
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
}

// --- Partager le média ---
function shareMedia(mediaUrl, e) {
  e.stopPropagation();
  if (navigator.share) {
    navigator.share({
      title: document.title,
      url: mediaUrl
    }).then(() => {
      console.log('Media shared successfully');
    }).catch(error => console.error('Erreur partage:', error));
  } else {
    navigator.clipboard.writeText(mediaUrl).then(() => {
      alert("Lien copié dans le presse-papier");
    });
  }
}

// --- Picture-in-Picture ---
function togglePictureInPicture(publicationId, e) {
  e.stopPropagation();
  const video = document.getElementById('video-' + publicationId);
  if (!document.pictureInPictureElement) {
    video.requestPictureInPicture().catch(error => console.error('Erreur PiP:', error));
  } else {
    document.exitPictureInPicture().catch(error => console.error('Erreur sortie PiP:', error));
  }
}

// --- Changement de la vitesse de lecture ---
function cyclePlaybackSpeed(publicationId, e) {
  e.stopPropagation();
  const video = document.getElementById('video-' + publicationId);
  const speedDisplay = document.getElementById('speed-display-' + publicationId);
  if (!video) {
    console.error("Vidéo introuvable pour l'ID:", publicationId);
    return;
  }
  const speeds = [1, 1.25, 1.5, 2];
  let currentSpeed = video.playbackRate;
  let index = speeds.indexOf(currentSpeed);
  if (index === -1) index = 0;
  index = (index + 1) % speeds.length;
  video.playbackRate = speeds[index];
  if (speedDisplay) {
    speedDisplay.innerText = speeds[index] + 'x';
  }
}

// --- Reculer dans la vidéo ---
function rewindVideo(publicationId, e) {
  e.stopPropagation();
  const video = document.getElementById('video-' + publicationId);
  if (video) {
    video.currentTime = Math.max(0, video.currentTime - 10);
  }
}

// --- Avancer dans la vidéo ---
function forwardVideo(publicationId, e) {
  e.stopPropagation();
  const video = document.getElementById('video-' + publicationId);
  if (video) {
    video.currentTime = Math.min(video.duration, video.currentTime + 10);
  }
}

// --- Bookmark ---
function toggleBookmark(publicationId, e) {
  e.stopPropagation();
  const btn = e.currentTarget;
  const icon = btn.querySelector('i');
  if(icon.classList.contains('fa-regular')) {
    icon.classList.remove('fa-regular');
    icon.classList.add('fa-solid');
    alert("Publication bookmarkée");
  } else {
    icon.classList.remove('fa-solid');
    icon.classList.add('fa-regular');
    alert("Publication retirée des bookmarks");
  }
}

// --- Fonctions pour les options des commentaires ---
function copyComment(commentId, event) {
  event.preventDefault();
  const button = event.currentTarget;
  const commentElement = button.closest('.comment');
  const commentTextElem = commentElement.querySelector('.comment-text');
  if (commentTextElem) {
    const textToCopy = commentTextElem.innerText;
    navigator.clipboard.writeText(textToCopy)
      .then(() => alert("Commentaire copié !"))
      .catch(err => {
        console.error("Erreur lors de la copie :", err);
        alert("Erreur lors de la copie.");
      });
  }
}

function reportComment(commentId, event) {
  event.preventDefault();
  // Vous pouvez ajouter ici une requête AJAX pour signaler le commentaire
  alert("Commentaire signalé !");
}

function deleteComment(commentId, event) {
  event.preventDefault();
  if (confirm("Êtes-vous sûr de vouloir supprimer ce commentaire ?")) {
    const commentElement = event.currentTarget.closest('.comment');
    commentElement.style.opacity = 0;
    setTimeout(() => commentElement.remove(), 300);
  }
}

function editComment(commentId, event) {
  event.preventDefault();
  const commentElement = event.currentTarget.closest('.comment');
  const commentTextElem = commentElement.querySelector('.comment-text');
  const originalText = commentTextElem.innerText.replace(/^.*?:\s*/, '');
  const input = document.createElement('input');
  input.type = 'text';
  input.value = originalText;
  input.style.width = '80%';
  commentElement.replaceChild(input, commentTextElem);
  input.focus();
  input.addEventListener('blur', () => {
    const newText = input.value;
    const newDiv = document.createElement('div');
    newDiv.className = 'comment-text';
    newDiv.innerHTML = '<strong>Utilisateur1</strong>: ' + newText;
    commentElement.replaceChild(newDiv, input);
  });
}

function replyComment(commentId, event) {
  event.preventDefault();
  const commentElement = event.currentTarget.closest('.comment');
  if (commentElement.querySelector('.reply-input')) return;
  const replyInput = document.createElement('div');
  replyInput.className = 'reply-input';
  replyInput.innerHTML = `
    <input type="text" placeholder="Votre réponse">
    <button onclick="sendReply(event)">Envoyer</button>
  `;
  commentElement.appendChild(replyInput);
}

function sendReply(event) {
  event.preventDefault();
  const replyInputContainer = event.currentTarget.parentElement;
  const input = replyInputContainer.querySelector('input');
  const replyText = input.value.trim();
  if (replyText !== '') {
    // Vous pouvez ajouter ici l'envoi AJAX de la réponse
    alert("Réponse envoyée : " + replyText);
    replyInputContainer.remove();
  }
}