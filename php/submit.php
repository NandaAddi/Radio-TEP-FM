<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
include 'config.php';

try {
    // Validasi lebih ketat
    $required = ['name', 'email', 'message'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Field $field harus diisi!");
        }
    }
    
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    if (strlen($name) < 2 || strlen($name) > 100) {
        throw new Exception("Nama harus 2-100 karakter");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Email tidak valid");
    }

    if (strlen($message) < 10 || strlen($message) > 1000) {
        throw new Exception("Pesan harus 10-1000 karakter");
    }

    // Gunakan transaction untuk keamanan
    $conn->begin_transaction();
    
    $stmt = $conn->prepare("INSERT INTO comments (name, email, message) VALUES (?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("sss", $name, $email, $message);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $newId = $conn->insert_id;
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Komentar berhasil dikirim!',
        'id' => $newId
    ]);
    
} catch (Exception $e) {
    if (isset($conn) && $conn->begin_transaction()) {
        $conn->rollback();
    }
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

if (isset($stmt)) $stmt->close();
if (isset($conn)) $conn->close();
?>