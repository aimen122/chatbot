<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $proposal_id = isset($_POST['proposal_id']) ? intval($_POST['proposal_id']) : null;
    $mark_all = isset($_POST['mark_all']) && $_POST['mark_all'] === 'true';
    
    if ($mark_all) {
        // Mark all proposals as read
        // First check if column exists
        $query_check = "SHOW COLUMNS FROM proposals LIKE 'is_read'";
        $column_exists = $conn->query($query_check)->num_rows > 0;
        
        if (!$column_exists) {
            // Add is_read column if it doesn't exist
            $conn->query("ALTER TABLE proposals ADD COLUMN is_read TINYINT(1) DEFAULT 0");
        }
        
        $stmt = $conn->prepare("UPDATE proposals SET is_read = 1 WHERE is_read = 0 OR is_read IS NULL");
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'All proposals marked as read']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to mark all as read']);
        }
        $stmt->close();
    } else if ($proposal_id) {
        // Mark single proposal as read
        // First check if column exists
        $query_check = "SHOW COLUMNS FROM proposals LIKE 'is_read'";
        $column_exists = $conn->query($query_check)->num_rows > 0;
        
        if (!$column_exists) {
            // Add is_read column if it doesn't exist
            $conn->query("ALTER TABLE proposals ADD COLUMN is_read TINYINT(1) DEFAULT 0");
        }
        
        $stmt = $conn->prepare("UPDATE proposals SET is_read = 1 WHERE id = ?");
        $stmt->bind_param("i", $proposal_id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Proposal marked as read']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to mark proposal as read']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>

