<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include 'config.php';

try {
    // Query yang lebih robust
    $query = "SELECT name, message, created_at FROM comments ORDER BY created_at DESC";
    
    if (!($result = $conn->query($query))) {
        throw new Exception("Query error: " . $conn->error);
    }
    
    $comments = [];
    while ($row = $result->fetch_assoc()) {
        $comments[] = [
            'name' => htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'),
            'message' => nl2br(htmlspecialchars($row['message'], ENT_QUOTES, 'UTF-8')),
            'created_at' => $row['created_at']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $comments,
        'count' => count($comments)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'query' => isset($query) ? $query : null
    ]);
}

$conn->close();
?>