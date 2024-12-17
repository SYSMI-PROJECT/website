<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="../../../Logo.png" type="image/png">
    <title>Liste des comptes utilisateurs bannis</title>
    <style>
        /* Styles généraux pour le corps de la page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }

        /* Styles pour le titre */
        h2 {
            text-align: center;
            color: #333;
            animation: changeColor 5s infinite;
        }

        /* Animation pour changer la couleur du texte */
        @keyframes changeColor {
            0% { color: red; }
            25% { color: green; }
            50% { color: blue; }
            75% { color: yellow; }
            100% { color: red; }
        }

        /* Styles pour la table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #333;
            text-transform: uppercase;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        /* Styles pour les boutons d'action */
        .action-buttons {
            text-align: center;
            display: flex;
            justify-content: space-evenly;
        }    

        .action-buttons button {
            padding: 8px 12px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-right: 5px;
            font-size: 14px;
        }

        .action-buttons button:last-child {
            margin-right: 0;
        }

        .action-buttons button:hover {
            background-color: #0056b3;
        }

        button.confirmation-no {
            background-color: #dc3545;
        }

        button.confirmation-no:hover {
            background-color: #c82333;
        }

        .redirection-button {
            padding: 10px 20px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            transition: background-color 0.3s;
        }

        .redirection-button:hover {
            background-color: #218838;
        }

        /* Media queries pour la réactivité */
        @media (max-width: 768px) {
            th, td {
                padding: 8px 10px;
                font-size: 12px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .action-buttons button {
                margin-bottom: 5px;
                font-size: 12px;
                padding: 6px 10px;
            }

            .action-buttons button:last-child {
                margin-bottom: 0;
            }
        }

        @media (max-width: 480px) {
            th, td {
                display: block;
                text-align: right;
            }

            th {
                text-align: left;
                background-color: transparent;
                border-bottom: none;
            }

            td {
                border: none;
                position: relative;
                padding-left: 50%;
                text-align: left;
            }

            td::before {
                content: attr(data-label);
                position: absolute;
                left: 10px;
                font-weight: bold;
                text-transform: uppercase;
            }

            .action-buttons {
                flex-direction: column;
            }

            .action-buttons button {
                margin-bottom: 5px;
                font-size: 12px;
                padding: 6px 10px;
            }

            .action-buttons button:last-child {
                margin-bottom: 0;
            }
        }
    </style>
</head>
<body>
    <h2>Liste des comptes utilisateurs bannis</h2>

    <div style="text-align: center; margin-top: 20px;">
        <button class="redirection-button" onclick="window.location.href = '../Dashboard.php';">Retour au menu</button>
    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Nom d'utilisateur</th>
            <th>Action</th>
        </tr>
        <?php
        // Inclure le fichier de connexion à la base de données
        require_once('../../../request/DB.php');

        try {
            // Requête pour sélectionner les utilisateurs bannis
            $query = "SELECT id, prenom, nom FROM utilisateur WHERE banni = 1";
            $stmt = $conn->query($query);
            while ($row = $stmt->fetch()) {
                echo "<tr>";
                echo "<td data-label='ID'>" . $row['id'] . "</td>";
                echo "<td data-label=\"Nom d'utilisateur\">" . $row['prenom'] . " " . $row['nom'] . "</td>";
                echo "<td data-label='Action' class='action-buttons'>";
                echo "<form method='post' action='debannir_compte.php'>";
                echo "<input type='hidden' name='id_utilisateur' value='" . $row['id'] . "'>";
                echo "<button type='submit' onclick='return confirmerDebannissement(" . $row['id'] . ")'>Débannir</button>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
        ?>
    </table>

    <script>
        function confirmerDebannissement(id) {
            if (confirm("Êtes-vous sûr de vouloir débannir ce compte ?")) {
                window.location.href = "../../../traitements/STAFF/unban.php?id=" + id;
            }
            return false; // Empêcher le formulaire de déclencher sa fonctionnalité par défaut
        }
    </script>
</body>
</html>
