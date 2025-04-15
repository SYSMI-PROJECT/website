<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="/Import/icons/Logo.png" type="image/png">
    <title>Gestion des Utilisateurs</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/public/import/css/dashboard/user_dashboard.css">
</head>
<body>
    <header>Gestion des Utilisateurs</header>

    <!-- Statistiques -->
    <div class="count-container">
        <?php
        require_once('../../../../requiments/database.php');

        try {
            $totalStmt = $conn->query("SELECT COUNT(id) AS total FROM utilisateur");
            $bannedStmt = $conn->query("SELECT COUNT(id) AS banned FROM utilisateur WHERE statut = 'banni'");
            $activeStmt = $conn->query("SELECT COUNT(id) AS active FROM utilisateur WHERE statut = 'actif'");

            $total = $totalStmt->fetch()['total'];
            $banned = $bannedStmt->fetch()['banned'];
            $active = $activeStmt->fetch()['active'];

            echo '<div class="count-container">';
            echo "<div>Total d'utilisateurs : $total</div>";
            echo "<div>Utilisateurs actifs : $active</div>";
            echo "<div>Utilisateurs bannis : $banned</div>";
            echo '</div>';
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
        ?>
    </div>

    <!-- Barre de recherche -->
    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Rechercher un utilisateur par prénom ou nom..." onkeyup="searchAccounts()">
    </div>

    <!-- Filtre par rôle -->
    <div class="filter-bar">
        <select id="filterStatus" onchange="filterUsersByStatus()">
            <option value="">Tous les utilisateurs</option>
            <option value="actif">Actifs</option>
            <option value="banni">Bannis</option>
        </select>
    </div>

    <!-- Bouton redirection -->
    <div style="text-align: center;">
        <button class="redirection-button" onclick="window.location.href = '../../miscellaneous/dashboard.php';">Retour au menu</button>
    </div>

    <!-- Tableau des utilisateurs -->
    <table id="userTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom d'utilisateur</th>
                <th>Statut</th>
                <th>Rôle</th> <!-- Nouvelle colonne -->
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            try {
                $query = "SELECT id, prenom, nom, statut, role FROM utilisateur"; // Ajout de la colonne 'role'
                $stmt = $conn->query($query);
                while ($row = $stmt->fetch()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['prenom'] . " " . $row['nom'] . "</td>";
                    echo "<td>" . ucfirst($row['statut']) . "</td>";
                    echo "<td>" . ucfirst($row['role']) . "</td>"; // Afficher le rôle
                    echo "<td class='action-buttons'>";
                    echo "<form method='post' action='/traitements/STAFF/delete_account.php'>";
                    echo "<input type='hidden' name='id_utilisateur' value='" . $row['id'] . "'>";
                    echo "<button type='submit' class='confirmation-no' onclick='return confirmerSuppression(" . $row['id'] . ")'>Supprimer</button>";
                    echo "</form>";

                    if ($row['statut'] === 'banni') {
                        echo "<form method='post' action='/public/import/php/script/staff/unban.php'>";
                        echo "<input type='hidden' name='id_utilisateur' value='" . $row['id'] . "'>";
                        echo "<button type='submit' class='confirmation-no'>Débannir</button>";
                        echo "</form>";
                    } else {
                        echo "<form method='post' action='/public/import/php/script/staff/banissement.php'>";
                        echo "<input type='hidden' name='id_utilisateur' value='" . $row['id'] . "'>";
                        echo "<button type='submit' onclick='return confirmerBannissement(" . $row['id'] . ")'>Bannir</button>";
                        echo "</form>";
                    }

                    echo "</td>";
                    echo "</tr>";
                }
            } catch (PDOException $e) {
                echo "Erreur : " . $e->getMessage();
            }
            ?>
        </tbody>
    </table>

    <!-- Formulaire d'ajout d'utilisateur -->
    <form action="add_user.php" method="POST">
        <input type="text" name="prenom" placeholder="Prénom" required>
        <input type="text" name="nom" placeholder="Nom" required>
        <input type="email" name="email" placeholder="Email" required>
        <button type="submit">Ajouter un utilisateur</button>
    </form>

    <script>
        function confirmerSuppression(id) {
            return confirm("Êtes-vous sûr de vouloir supprimer cet utilisateur ?");
        }

        function confirmerBannissement(id) {
            return confirm("Êtes-vous sûr de vouloir bannir cet utilisateur ?");
        }

        function searchAccounts() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("userTable");
            tr = table.getElementsByTagName("tr");

            for (i = 1; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[1];
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

        function filterUsersByStatus() {
            var filter = document.getElementById("filterStatus").value.toUpperCase();
            var table = document.getElementById("userTable");
            var tr = table.getElementsByTagName("tr");

            for (var i = 1; i < tr.length; i++) {
                var statusCell = tr[i].getElementsByTagName("td")[2];
                if (statusCell) {
                    var statusValue = statusCell.textContent || statusCell.innerText;
                    if (filter === "" || statusValue.toUpperCase() === filter) {
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
