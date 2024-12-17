<?php
session_start();

// Remplace ces valeurs par celles que tu as obtenues sur le portail Discord Developer
$client_id = '1280665297417932850'; 
$redirect_uri = 'https://sysmiproject.000.pe/traitements/Auth/disc-login.php'; 
$scope = 'identify guilds';  // Tu peux ajouter d'autres permissions si nécessaire

// Génère l'URL d'authentification OAuth2
$discord_oauth_url = 'https://discord.com/oauth2/authorize?client_id=' . $client_id . '&redirect_uri=' . urlencode($redirect_uri) . '&response_type=code&scope=' . $scope;

// Redirection vers Discord
header('Location: ' . $discord_oauth_url);
exit;
?>
