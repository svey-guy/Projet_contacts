<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Récupérer les données JSON
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Données invalides'
        ]);
        exit();
    }
    
    $nom = isset($input['nom']) ? trim($input['nom']) : '';
    $email = isset($input['email']) ? trim($input['email']) : '';
    $telephone = isset($input['telephone']) ? trim($input['telephone']) : '';
    
    // Validation
    if (empty($nom) || empty($email)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Le nom et l\'email sont requis'
        ]);
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Format d\'email invalide'
        ]);
        exit();
    }
    
    try {
        $sql = "INSERT INTO contacts (nom, email, telephone) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nom, $email, $telephone]);
        
        $contactId = $pdo->lastInsertId();
        
        echo json_encode([
            'success' => true,
            'message' => 'Contact ajouté avec succès',
            'data' => [
                'id' => $contactId,
                'nom' => $nom,
                'email' => $email,
                'telephone' => $telephone
            ]
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erreur lors de l\'ajout du contact : ' . $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée'
    ]);
}
?>