<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des contacts</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'db.php'; ?>

    <h2>Liste des contacts</h2>
    <a href="add.php">➕ Ajouter un contact</a>
    <hr>

    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            try {
                $stmt = $pdo->query("SELECT * FROM contacts ORDER BY id DESC");
                $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($contacts) > 0) {
                    foreach ($contacts as $row) {
                        echo "<tr>
                            <td>" . htmlspecialchars($row['nom']) . "</td>
                            <td>" . htmlspecialchars($row['email']) . "</td>
                            <td>" . htmlspecialchars($row['telephone']) . "</td>
                            <td>
                                <a href='delete.php?id=" . urlencode($row['id']) . "' onclick=\"return confirm('Voulez-vous vraiment supprimer ce contact ?');\">
                                    🗑️ Supprimer
                                </a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Aucun contact trouvé</td></tr>";
                }
            } catch (PDOException $e) {
                echo "<tr><td colspan='4'>Erreur : " . htmlspecialchars($e->getMessage()) . "</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>

