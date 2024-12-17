<?php
session_start();

// Remplace ces valeurs par celles que tu as obtenues sur le portail Discord Developer
$client_id = '1280665297417932850';
$client_secret = 'FwjuT5fM3HBQ66Y8-O3kUNRICVO4QYNA';
$redirect_uri = 'https://sysmiproject.000.pe/traitements/Auth/disc-login.php'; 

// Vérifie si le code est dans l'URL
if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Échange du code d'autorisation contre un jeton d'accès
    $token_url = 'https://discord.com/api/oauth2/token';
    $data = [
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => $redirect_uri,
        'scope' => 'identify guilds',
    ];

    // Utilise file_get_contents pour envoyer la requête POST
    $options = [
        'http' => [
            'method'  => 'POST',
            'header'  => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query($data),
        ],
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($token_url, false, $context);

    if ($response === FALSE) {
        echo 'Erreur lors de la récupération du jeton d\'accès.';
    } else {
        $json = json_decode($response, true);

        // Vérifie si le jeton a été récupéré
        if (isset($json['access_token'])) {
            $access_token = $json['access_token'];

            // Utilise le jeton pour récupérer les informations de l'utilisateur
            $user_url = 'https://discord.com/api/v10/users/@me';
            $user_options = [
                'http' => [
                    'header' => "Authorization: Bearer $access_token\r\n",
                ],
            ];
            $user_context = stream_context_create($user_options);
            $user_response = file_get_contents($user_url, false, $user_context);
            $user_data = json_decode($user_response, true);

            if ($user_data) {
                // Sauvegarde les informations de l'utilisateur dans la session ou dans la base de données
                $_SESSION['user'] = $user_data;

                // Exemple : Affichage des informations de l'utilisateur
                echo 'Bienvenue, ' . $user_data['username'] . ' (' . $user_data['id'] . ')';
                echo '<br><img src="https://cdn.discordapp.com/avatars/' . $user_data['id'] . '/' . $user_data['avatar'] . '.png">';

                // Optionnel : redirection vers une page protégée ou d'accueil
                // header('Location: /index.php');
            } else {
                echo 'Erreur lors de la récupération des données utilisateur.';
            }
        } else {
            echo 'Erreur d\'authentification. Le jeton d\'accès est manquant.';
        }
    }
} else {
    echo 'Code d\'authentification manquant.';
}
?>
