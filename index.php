<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des contacts</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Liste des contacts</h2>
    
    <!-- Formulaire d'ajout de contact -->
    <div class="add-form-container">
        <button id="toggleFormBtn" class="toggle-btn">➕ Ajouter un contact</button>
        <form id="addContactForm" class="add-form" style="display: none;">
            <div class="form-group">
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" required>
            </div>
            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="telephone">Téléphone :</label>
                <input type="text" id="telephone" name="telephone">
            </div>
            <div class="form-actions">
                <button type="submit" id="submitBtn">
                    <span class="btn-text">Ajouter</span>
                    <span class="loading" style="display: none;">⟳ Ajout en cours...</span>
                </button>
                <button type="button" id="cancelBtn">Annuler</button>
            </div>
        </form>
    </div>
    
    <!-- Messages de statut -->
    <div id="messageContainer" class="message-container"></div>
    
    <hr>

    <!-- Indicateur de chargement -->
    <div id="loadingIndicator" class="loading-indicator">
        ⟳ Chargement des contacts...
    </div>

    <table border="1" cellpadding="8" id="contactsTable">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="contactsTableBody">
            <!-- Les contacts seront chargés ici via JavaScript -->
        </tbody>
    </table>

    <script src="script.js"></script>
</body>
</html>

