<?php
session_start();
include 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit();
}

// Fetch all marketing members
$sql = "SELECT id, name, email FROM marketing_members ORDER BY name ASC";
$result = $conn->query($sql);

$members = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $members[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'email' => $row['email']
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($members);

$conn->close();
?>