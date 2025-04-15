if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/Import/assets/sw.js').then((registration) => {
        console.log('Service Worker enregistré :', registration);
    }).catch((error) => {
        console.error('Service Worker échec :', error);
    });
}

let deferredPrompt;

window.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault(); // Empêche l'invite automatique
    deferredPrompt = event; // Stocke l'événement pour une utilisation ultérieure

    // Crée un bouton pour l'installation
    const installButton = document.createElement('button');
    installButton.textContent = "Installer l'application";
    installButton.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 10px 20px;
        background-color: #fdd835;
        color: #000;
        border: none;
        border-radius: 5px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        font-size: 16px;
        cursor: pointer;
        z-index: 1000;
    `;

    document.body.appendChild(installButton);

    installButton.addEventListener('click', () => {
        deferredPrompt.prompt(); // Affiche l'invite d'installation
        deferredPrompt.userChoice.then((choiceResult) => {
            if (choiceResult.outcome === 'accepted') {
                console.log('L\'application a été installée.');
            } else {
                console.log('L\'installation a été refusée.');
            }
            deferredPrompt = null; // Réinitialise l'invite
            installButton.remove(); // Supprime le bouton après l'action
        });
    });
});
