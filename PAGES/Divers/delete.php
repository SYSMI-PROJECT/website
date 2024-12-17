<?php
// Vérifie si l'indice du message à supprimer a été fourni
if (isset($_GET['index'])) {
    // Récupère l'indice du message à supprimer depuis la requête GET
    $index = $_GET['index'];

    // Lit tous les messages depuis le fichier
    $messages = file('messages.txt');

    // Vérifie si l'indice est valide
    if ($index >= 0 && $index < count($messages)) {
        // Supprime le message correspondant de la liste des messages
        unset($messages[$index]);

        // Réécrit le fichier avec les messages mis à jour
        file_put_contents('messages.txt', implode('', $messages));

        // Renvoie une réponse 200 OK
        http_response_code(200);
    } else {
        // Renvoie une réponse 400 Bad Request si l'indice est invalide
        http_response_code(400);
    }
} else {
    // Renvoie une réponse 400 Bad Request si l'indice n'est pas fourni
    http_response_code(400);
}
?>
