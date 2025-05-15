const video = document.getElementById("video");
const recordBtn = document.getElementById("recordBtn");
const recordInner = document.querySelector(".record-inner");
const recordingIndicator = document.getElementById("recordingIndicator");
const recordingTimer = document.getElementById("recordingTimer");
const modeButtons = document.querySelectorAll(".mode-btn");
const filterButtons = document.querySelectorAll(".filter-btn");
const filterPanel = document.getElementById("filterPanel");
const effectsPanel = document.getElementById("effectsPanel");
const customFilter = document.getElementById("customFilter");
const brightnessRange = document.getElementById("brightnessRange");
const effBrightness = document.getElementById("effBrightness");
const effContrast = document.getElementById("effContrast");
const effSaturation = document.getElementById("effSaturation");
const iphonePreviewContainer = document.getElementById("iphonePreviewContainer");
const iphonePreviewMedia = document.getElementById("iphonePreviewMedia");
const closeIphonePreview = document.getElementById("closeIphonePreview");
const flipBtn = document.getElementById("flipBtn");
const backBtn = document.getElementById("backBtn");
const mirrorBtn = document.getElementById("mirrorBtn");
const pauseBtn = document.getElementById("pauseBtn");
const closeFilterPanel = document.getElementById("closeFilterPanel");
const closeEffectsPanel = document.getElementById("closeEffectsPanel");
const publicationForm = document.getElementById("publicationForm");
const mediaInput = document.getElementById("media");
const uploadOption = document.getElementById("uploadOption");

// Variables pour l'enregistrement segmenté
let segments = [];
let currentRecorder = null;
let recording = false;
let recordingPaused = false;
let currentMode = "video";
let useFrontCamera = true;
// Par défaut, l'effet miroir est activé pour le flux live
let mirrorEffect = true;
let recordingStartTime;
let elapsedTime = 0;
let capturedMedia = null; // pour stocker le média capturé
let recordingTimerInterval;

// Création d'un canevas caché servant à enregistrer (rendu du flux vidéo)
const canvas = document.createElement("canvas");
const ctx = canvas.getContext("2d");

// Démarrer la caméra
async function startCamera() {
  try {
    if(window.currentStream) {
      window.currentStream.getTracks().forEach(track => track.stop());
    }
    window.currentStream = await navigator.mediaDevices.getUserMedia({
      video: { facingMode: useFrontCamera ? "user" : "environment" },
      audio: { echoCancellation: true, noiseSuppression: true, autoGainControl: true }
    });
    video.srcObject = window.currentStream;
    video.muted = true; // Le flux live reste en sourdine (pour éviter la récursivité audio)
    updateMirror();
  } catch (error) {
    console.error("Erreur d'accès à la caméra :", error);
  }
}

// Dès que les métadonnées sont chargées, on initialise le canevas et démarre la boucle de dessin
video.addEventListener("loadedmetadata", () => {
  canvas.width = video.videoWidth;
  canvas.height = video.videoHeight;
  drawCanvas();
});

// Boucle de dessin sur le canevas (on capture le flux brut, sans transformation miroir)
function drawCanvas() {
  ctx.filter = video.style.filter || "none";
  ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
  requestAnimationFrame(drawCanvas);
}

// Mise à jour de l'effet miroir pour le flux live
function updateMirror() {
  video.style.transform = mirrorEffect ? "scaleX(-1)" : "none";
}
mirrorBtn.addEventListener("click", () => {
  mirrorEffect = !mirrorEffect;
  updateMirror();
});

// Changer de caméra
flipBtn.addEventListener("click", () => {
  useFrontCamera = !useFrontCamera;
  startCamera();
});
backBtn.addEventListener("click", () => {
  window.location.href = "/";
});

// Gestion du changement de mode
function changeMode(mode) {
  currentMode = mode;
  modeButtons.forEach(btn => btn.classList.remove("active"));
  document.querySelector(`.mode-btn[data-mode="${mode}"]`).classList.add("active");

  // Affichage ou masquage des panneaux en fonction du mode
  if (mode === "filters") {
    filterPanel.classList.add("active");
    effectsPanel.classList.remove("active");
  } else if (mode === "effects") {
    effectsPanel.classList.add("active");
    filterPanel.classList.remove("active");
  } else {
    filterPanel.classList.remove("active");
    effectsPanel.classList.remove("active");
  }

  // Si mode upload, on affiche le champ file
  if(mode === "upload") {
    mediaInput.style.display = "block";
  } else {
    mediaInput.style.display = "none";
  }
  console.log("Mode sélectionné :", mode);
}
modeButtons.forEach(btn => {
  btn.addEventListener("click", () => {
    changeMode(btn.getAttribute("data-mode"));
  });
});

// Gestion des filtres
function changeFilter(filterValue) {
  if (filterValue === "custom") {
    customFilter.style.display = "flex";
    video.style.filter = `brightness(${brightnessRange.value}%)`;
  } else {
    customFilter.style.display = "none";
    video.style.filter = filterValue;
  }
  filterButtons.forEach(btn => btn.classList.remove("active"));
  document.querySelector(`.filter-btn[data-filter="${filterValue}"]`).classList.add("active");
}
filterButtons.forEach(btn => {
  btn.addEventListener("click", () => {
    changeFilter(btn.getAttribute("data-filter"));
  });
});
brightnessRange.addEventListener("input", () => {
  video.style.filter = `brightness(${brightnessRange.value}%)`;
});

// Gestion des effets via CSS
function updateEffects() {
  const b = effBrightness.value;
  const c = effContrast.value;
  const s = effSaturation.value;
  video.style.filter = `brightness(${b}%) contrast(${c}%) saturate(${s}%)`;
}
[effBrightness, effContrast, effSaturation].forEach(slider => {
  slider.addEventListener("input", updateEffects);
});

// Capture photo
function capturePhoto() {
  const photoDataURL = canvas.toDataURL("image/png");
  showPreview("photo", photoDataURL);
}

// Enregistrement segmenté avec ajout de l'audio depuis la caméra
function startSegmentRecording() {
  // Capture du flux vidéo depuis le canvas
  const canvasStream = canvas.captureStream(30);
  // Récupération des pistes audio du flux de la caméra
  const audioTracks = window.currentStream.getAudioTracks();
  audioTracks.forEach(track => {
    canvasStream.addTrack(track);
  });
  try {
    currentRecorder = new MediaRecorder(canvasStream, { mimeType: "video/webm; codecs=vp9" });
  } catch (error) {
    console.error("Erreur lors de l'initialisation du MediaRecorder :", error);
    return;
  }
  currentRecorder.ondataavailable = event => {
    if (event.data.size > 0) {
      segments.push(event.data);
    }
  };
  currentRecorder.start();
  recordingStartTime = Date.now();
  recordingTimerInterval = setInterval(updateRecordingTimer, 1000);
}

// Démarrer l'enregistrement global
function startRecording() {
  segments = [];
  recording = true;
  recordingPaused = false;
  startSegmentRecording();
  recordInner.classList.add("active");
  recordingIndicator.classList.add("active");
  recordingTimer.classList.add("active");
  pauseBtn.style.display = "flex";
  pauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
}

// Mise à jour du timer d'enregistrement
function updateRecordingTimer() {
  let currentElapsed = Date.now() - recordingStartTime;
  let totalElapsed = elapsedTime + currentElapsed;
  const seconds = Math.floor((totalElapsed / 1000) % 60);
  const minutes = Math.floor((totalElapsed / (1000 * 60)) % 60);
  recordingTimer.textContent = String(minutes).padStart(2, "0") + ":" + String(seconds).padStart(2, "0");
}

// Arrêter l'enregistrement global
function stopRecording() {
  if (currentRecorder && currentRecorder.state !== "inactive") {
    currentRecorder.stop();
  }
  clearInterval(recordingTimerInterval);
  recordInner.classList.remove("active");
  recordingIndicator.classList.remove("active");
  recordingTimer.classList.remove("active");
  recording = false;
  recordingPaused = false;
  const blob = new Blob(segments, { type: "video/webm" });
  const blobURL = URL.createObjectURL(blob);
  showPreview("video", blobURL, blob);
}

// Pause / Reprise de l'enregistrement
function togglePauseRecording() {
  if (!recording) return;
  if (!recordingPaused) {
    currentRecorder.stop();
    recordingPaused = true;
    elapsedTime += Date.now() - recordingStartTime;
    clearInterval(recordingTimerInterval);
    pauseBtn.innerHTML = '<i class="fas fa-play"></i>';
  } else {
    startSegmentRecording();
    recordingPaused = false;
    recordingStartTime = Date.now();
    recordingTimerInterval = setInterval(updateRecordingTimer, 1000);
    pauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
  }
}
recordBtn.addEventListener("click", () => {
  if (currentMode === "photo") {
    capturePhoto();
  } else if (currentMode === "upload") {
    mediaInput.click();
  } else {
    if (!recording) {
      startRecording();
    } else {
      stopRecording();
    }
  }
});
pauseBtn.addEventListener("click", togglePauseRecording);

// Gestion du téléversement via la barre d'options
uploadOption.addEventListener("click", () => {
  changeMode("upload");
  mediaInput.click();
});

// Fonction d'affichage de la prévisualisation directement dans la modal iPhone
function showPreview(type, source, blob = null) {
  iphonePreviewMedia.innerHTML = "";
  let mediaElement;
  if (type === "video") {
    mediaElement = document.createElement("video");
    mediaElement.src = source;
    mediaElement.autoplay = false;
    mediaElement.controls = false; // Désactivation des contrôles natifs
    if (currentMode === "slowmo") {
      mediaElement.playbackRate = 0.5;
    } else if (currentMode === "timelapse") {
      mediaElement.playbackRate = 4;
    }
    mediaElement.style.transform = mirrorEffect ? "scaleX(-1)" : "none";
    
    // Boutons personnalisés
    const controlsDiv = document.createElement("div");
    controlsDiv.className = "custom-video-controls";
    const playBtn = document.createElement("button");
    playBtn.innerHTML = '<i class="fas fa-play"></i>';
    playBtn.addEventListener("click", () => { mediaElement.play(); });
    const pauseBtnCustom = document.createElement("button");
    pauseBtnCustom.innerHTML = '<i class="fas fa-pause"></i>';
    pauseBtnCustom.addEventListener("click", () => { mediaElement.pause(); });
    controlsDiv.appendChild(playBtn);
    controlsDiv.appendChild(pauseBtnCustom);
    
    iphonePreviewMedia.appendChild(mediaElement);
    iphonePreviewMedia.appendChild(controlsDiv);
    capturedMedia = blob;
  } else if (type === "photo") {
    mediaElement = document.createElement("img");
    mediaElement.src = source;
    mediaElement.alt = "Photo capturée";
    mediaElement.style.transform = mirrorEffect ? "scaleX(-1)" : "none";
    iphonePreviewMedia.appendChild(mediaElement);
    capturedMedia = source;
  }
  iphonePreviewContainer.style.display = "flex";
}

// Fermeture de la modal iPhone et arrêt de la vidéo si elle est lue
closeIphonePreview.addEventListener("click", () => {
  const previewVideo = iphonePreviewMedia.querySelector("video");
  if (previewVideo) {
    previewVideo.pause();
    previewVideo.src = "";
    previewVideo.load();
  }
  iphonePreviewContainer.style.display = "none";
});

// Gestion du téléversement : prévisualisation dès qu'un fichier est sélectionné
mediaInput.addEventListener("change", function(e) {
  const file = e.target.files[0];
  if (file) {
    let fileType = file.type.startsWith("video") ? "video" : "photo";
    const fileURL = URL.createObjectURL(file);
    showPreview(fileType, fileURL, file);
  }
});

// Conversion d'un Blob en base64
function blobToBase64(blob) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onloadend = () => resolve(reader.result);
    reader.onerror = reject;
    reader.readAsDataURL(blob);
  });
}

// Intercepter la soumission du formulaire pour gérer les modes
publicationForm.addEventListener("submit", function(e) {
  if (currentMode === "upload") {
     document.getElementById("media_mode").value = "upload";
     return;
  }
  if (capturedMedia instanceof Blob) {
    e.preventDefault();
    blobToBase64(capturedMedia).then((base64Data) => {
       document.getElementById("recorded_media").value = base64Data;
       document.getElementById("media_mode").value = "record";
       publicationForm.submit();
    });
  } else {
     document.getElementById("media_mode").value = "record";
     document.getElementById("recorded_media").value = capturedMedia;
  }
});

// Démarrer la caméra au chargement
startCamera();