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
    $latitude = isset($input['latitude']) && $input['latitude'] !== '' ? floatval($input['latitude']) : null;
    $longitude = isset($input['longitude']) && $input['longitude'] !== '' ? floatval($input['longitude']) : null;
    
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
    
    // Validation des coordonnées GPS
    if (($latitude !== null && $longitude === null) || ($latitude === null && $longitude !== null)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'La latitude et la longitude doivent être fournies ensemble'
        ]);
        exit();
    }
    
    if ($latitude !== null && ($latitude < -90 || $latitude > 90)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'La latitude doit être entre -90 et 90'
        ]);
        exit();
    }
    
    if ($longitude !== null && ($longitude < -180 || $longitude > 180)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'La longitude doit être entre -180 et 180'
        ]);
        exit();
    }
    
    try {
        $sql = "INSERT INTO contacts (nom, email, telephone, latitude, longitude) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nom, $email, $telephone, $latitude, $longitude]);
        
        $contactId = $pdo->lastInsertId();
        
        echo json_encode([
            'success' => true,
            'message' => 'Contact ajouté avec succès',
            'data' => [
                'id' => $contactId,
                'nom' => $nom,
                'email' => $email,
                'telephone' => $telephone,
                'latitude' => $latitude,
                'longitude' => $longitude
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