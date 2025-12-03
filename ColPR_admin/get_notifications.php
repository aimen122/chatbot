<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Get unread proposal counts
// Check if is_read column exists, if not, assume all proposals are unread if created in last 30 days
$query_check = "SHOW COLUMNS FROM proposals LIKE 'is_read'";
$column_exists = $conn->query($query_check)->num_rows > 0;

if ($column_exists) {
    // Get unread counts by type
    $unread_accepted = $conn->query("SELECT COUNT(*) as count FROM proposals WHERE proposal_type = 'ACCEPTED' AND (is_read = 0 OR is_read IS NULL)")->fetch_assoc()['count'];
    $unread_rejected = $conn->query("SELECT COUNT(*) as count FROM proposals WHERE proposal_type = 'REJECTED' AND (is_read = 0 OR is_read IS NULL)")->fetch_assoc()['count'];
    $unread_escalated = $conn->query("SELECT COUNT(*) as count FROM proposals WHERE proposal_type = 'ESCALATED' AND (is_read = 0 OR is_read IS NULL)")->fetch_assoc()['count'];
    $total_unread = $unread_accepted + $unread_rejected + $unread_escalated;
    
    // Get recent unread proposals (last 10)
    $recent_unread = $conn->query("SELECT id, proposal_type, created_at FROM proposals WHERE (is_read = 0 OR is_read IS NULL) ORDER BY created_at DESC LIMIT 10")->fetch_all(MYSQLI_ASSOC);
} else {
    // If column doesn't exist, consider all recent proposals (last 24 hours) as unread
    $unread_accepted = $conn->query("SELECT COUNT(*) as count FROM proposals WHERE proposal_type = 'ACCEPTED' AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)")->fetch_assoc()['count'];
    $unread_rejected = $conn->query("SELECT COUNT(*) as count FROM proposals WHERE proposal_type = 'REJECTED' AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)")->fetch_assoc()['count'];
    $unread_escalated = $conn->query("SELECT COUNT(*) as count FROM proposals WHERE proposal_type = 'ESCALATED' AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)")->fetch_assoc()['count'];
    $total_unread = $unread_accepted + $unread_rejected + $unread_escalated;
    
    // Get recent proposals
    $recent_unread = $conn->query("SELECT id, proposal_type, created_at FROM proposals WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR) ORDER BY created_at DESC LIMIT 10")->fetch_all(MYSQLI_ASSOC);
}

echo json_encode([
    'success' => true,
    'unread_accepted' => (int)$unread_accepted,
    'unread_rejected' => (int)$unread_rejected,
    'unread_escalated' => (int)$unread_escalated,
    'total_unread' => (int)$total_unread,
    'recent_unread' => $recent_unread
]);

$conn->close();
?>

