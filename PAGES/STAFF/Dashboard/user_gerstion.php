<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="../../../Logo.png" type="image/png">
    <title>Liste des comptes utilisateurs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

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

        #idCount {
            text-align: center;
            margin-bottom: 20px;
        }

        .count-container {
            background-color: #c5c5c5;
            border-radius: 5px;
            width: 129px;
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
    </style>
</head>
<body>
    <h2>Liste des comptes utilisateurs</h2>

    <!-- Search Bar -->
    <div>
        <input type="text" id="searchInput" placeholder="Rechercher un utilisateur..." onkeyup="searchAccounts()">
    </div>

    <!-- ID Count -->
    <div class="count-container">
        <?php
        require_once('../../../request/DB.php');

        try {
            $stmt = $conn->query("SELECT COUNT(id) AS total FROM utilisateur");
            $row = $stmt->fetch();
            echo "<p>Total IDs créés : " . $row['total'] . "</p>";
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
        ?>
    </div>

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
        try {
            $query = "SELECT id, prenom, nom FROM utilisateur";
            $stmt = $conn->query($query);
            while ($row = $stmt->fetch()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['prenom'] . " " . $row['nom'] . "</td>";
                echo "<td class='action-buttons'>";
                echo "<form method='post' action='supprimer_compte.php'>";
                echo "<input type='hidden' name='id_utilisateur' value='" . $row['id'] . "'>";
                echo "<button type='submit' onclick='return confirmerSuppression(" . $row['id'] . ")'>Supprimer</button>";
                echo "</form>";
                echo "<form method='post' action='bannir_compte.php'>";
                echo "<input type='hidden' name='id_utilisateur' value='" . $row['id'] . "'>";
                echo "<button type='submit' onclick='return confirmerBannissement(" . $row['id'] . ")'>Bannir</button>";
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
        function confirmerSuppression(id) {
            if (confirm("Êtes-vous sûr de vouloir supprimer ce compte ? Cette action est irréversible.")) {
                window.location.href = "../../../traitements/STAFF/delete_account.php?id=" + id;
            }
            return false; // Empêcher le lien de déclencher sa fonctionnalité par défaut
        }

        function confirmerBannissement(id) {
            if (confirm("Êtes-vous sûr de vouloir bannir ce compte ?")) {
                window.location.href = "../../../traitements/STAFF/banissement.php?id=" + id;
            }
            return false; // Empêcher le lien de déclencher sa fonctionnalité par défaut
        }

        function searchAccounts() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.querySelector("table");
            tr = table.getElementsByTagName("tr");

            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[1]; // Index 1 corresponds to the "Nom d'utilisateur" column
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    </script>
</body>
</html>