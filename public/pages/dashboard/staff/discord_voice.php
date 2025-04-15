<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/import/css/dashboard/discord_voice.css">
    <title>Gestion d'Appels Discord</title>
</head>
<body>
    <h1>Créer un Appel Discord</h1>
    
    <label for="server">Choisissez le Serveur :</label>
    <select id="server" required>
        <!-- Liste des serveurs que le bot peut gérer -->
        <!-- Remplir dynamiquement avec JavaScript -->
    </select>
    
    <label for="channel">Choisissez le Canal :</label>
    <select id="channel" required>
        <!-- Liste des canaux vocaux du serveur sélectionné -->
        <!-- Remplir dynamiquement avec JavaScript -->
    </select>
    
    <button onclick="startCall()">Démarrer l'Appel</button>

    <script>
        async function fetchServers() {
            // Remplacez '/api/servers' par l'URL de l'API pour obtenir les serveurs
            const response = await fetch('/api/servers');
            const servers = await response.json();
            const serverSelect = document.getElementById('server');
            
            servers.forEach(server => {
                const option = document.createElement('option');
                option.value = server.id;
                option.text = server.name;
                serverSelect.appendChild(option);
            });
        }

        async function fetchChannels(serverId) {
            // Remplacez '/api/channels/' par l'URL de l'API pour obtenir les canaux
            const response = await fetch(`/api/channels/${serverId}`);
            const channels = await response.json();
            const channelSelect = document.getElementById('channel');
            
            // Vider la liste des canaux avant d'en ajouter de nouveaux
            channelSelect.innerHTML = '';
            
            channels.forEach(channel => {
                const option = document.createElement('option');
                option.value = channel.id;
                option.text = channel.name;
                channelSelect.appendChild(option);
            });
        }

        document.getElementById('server').addEventListener('change', (event) => {
            const serverId = event.target.value;
            fetchChannels(serverId);
        });

        async function startCall() {
            const serverId = document.getElementById('server').value;
            const channelId = document.getElementById('channel').value;

            // Requête pour démarrer l'appel via votre bot
            const response = await fetch('/api/start_call', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ serverId, channelId })
            });

            if (response.ok) {
                alert("Appel démarré avec succès !");
            } else {
                alert("Erreur lors du démarrage de l'appel.");
            }
        }

        // Chargement initial des serveurs
        fetchServers();
    </script>
</body>
</html>
