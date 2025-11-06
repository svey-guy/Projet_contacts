<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE, POST');
header('Access-Control-Allow-Headers: Content-Type');

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "DELETE" || $_SERVER["REQUEST_METHOD"] === "POST") {
    // Support pour DELETE et POST (pour compatibilité)
    if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = isset($input['id']) ? $input['id'] : null;
    } else {
        // POST avec paramètre id
        $input = json_decode(file_get_contents('php://input'), true);
        $id = isset($input['id']) ? $input['id'] : (isset($_POST['id']) ? $_POST['id'] : null);
    }
    
    if (!$id || !is_numeric($id)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID du contact requis et doit être un nombre'
        ]);
        exit();
    }
    
    try {
        // Vérifier que le contact existe
        $checkStmt = $pdo->prepare("SELECT id FROM contacts WHERE id = ?");
        $checkStmt->execute([$id]);
        
        if ($checkStmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Contact non trouvé'
            ]);
            exit();
        }
        
        // Supprimer le contact
        $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Contact supprimé avec succès',
            'data' => ['id' => $id]
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erreur lors de la suppression du contact : ' . $e->getMessage()
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