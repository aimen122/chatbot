<?php
session_start();
include 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle form submission for adding/updating member
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $action = $_POST['action'];
    $member_id = isset($_POST['member_id']) ? intval($_POST['member_id']) : 0;

    if (!empty($name) && !empty($email)) {
        if ($action == 'add') {
            // Check if email already exists
            $check_sql = "SELECT id FROM marketing_members WHERE email = '$email'";
            $check_result = $conn->query($check_sql);
            
            if ($check_result && $check_result->num_rows > 0) {
                header("Location: marketing_members.php?error=Email already exists");
                exit();
            }

            // Add new member
            $sql = "INSERT INTO marketing_members (name, email) VALUES ('$name', '$email')";
            
            if ($conn->query($sql) === TRUE) {
                header("Location: marketing_members.php?success=Member added successfully");
            } else {
                header("Location: marketing_members.php?error=Failed to add member: " . $conn->error);
            }
            
        } elseif ($action == 'update' && $member_id > 0) {
            // Check if email exists for other users
            $check_sql = "SELECT id FROM marketing_members WHERE email = '$email' AND id != $member_id";
            $check_result = $conn->query($check_sql);
            
            if ($check_result && $check_result->num_rows > 0) {
                header("Location: marketing_members.php?error=Email already exists for another member");
                exit();
            }

            // Update existing member
            $sql = "UPDATE marketing_members SET name = '$name', email = '$email' WHERE id = $member_id";
            
            if ($conn->query($sql) === TRUE) {
                header("Location: marketing_members.php?success=Member updated successfully");
            } else {
                header("Location: marketing_members.php?error=Failed to update member: " . $conn->error);
            }
        }
    } else {
        header("Location: marketing_members.php?error=Name and email are required");
    }
    exit();
}

// Handle delete action
if (isset($_GET['delete'])) {
    $member_id = intval($_GET['delete']);
    
    // First check if member exists
    $check_sql = "SELECT id, name FROM marketing_members WHERE id = $member_id";
    $check_result = $conn->query($check_sql);
    
    if ($check_result && $check_result->num_rows > 0) {
        $member = $check_result->fetch_assoc();
        
        // Now delete the member
        $sql = "DELETE FROM marketing_members WHERE id = $member_id";
        
        if ($conn->query($sql) === TRUE) {
            header("Location: marketing_members.php?success=Member '".urlencode($member['name'])."' deleted successfully");
        } else {
            header("Location: marketing_members.php?error=Failed to delete member: " . $conn->error);
        }
    } else {
        header("Location: marketing_members.php?error=Member not found");
    }
    exit();
}

// Fetch all marketing members
$members_query = "SELECT * FROM marketing_members ORDER BY created_at DESC";
$members_result = $conn->query($members_query);

// Handle success/error feedback
$success = isset($_GET['success']) ? $_GET['success'] : '';
$error = isset($_GET['error']) ? $_GET['error'] : '';

// Check if editing
$editing_member = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $sql = "SELECT * FROM marketing_members WHERE id = $edit_id";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $editing_member = $result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketing Members - ColPR Admin</title>
    <link rel="icon" type="image/png" href="colpr-logo.png">
    <link rel="shortcut icon" type="image/png" href="colpr-logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .main-content {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 100vh;
            margin-left: 18rem;
            width: calc(100vw - 18rem);
        }
        .main-content.full-width {
            margin-left: 0;
            width: 100vw;
        }
        body {
            overflow-x: hidden;
        }
        @media (max-width: 1024px) {
            .main-content {
                margin-left: 0;
                width: 100vw;
            }
        }
    </style>
</head>
<body class="font-inter bg-gray-50 overflow-x-hidden">
    <!-- Include Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content p-8 min-h-screen transition-all duration-300 full-width" id="mainContent">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Marketing Members</h1>
                    <p class="text-gray-600 mt-2">Manage your marketing team members</p>
                </div>
            </div>

            <!-- Notifications -->
            <?php if ($success): ?>
                <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-green-800"><?php echo htmlspecialchars(urldecode($success)); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-red-800"><?php echo htmlspecialchars(urldecode($error)); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Add/Edit Member Form -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    <?php echo $editing_member ? 'Edit Member' : 'Add New Member'; ?>
                </h2>
                <form method="POST" action="marketing_members.php" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <input type="hidden" name="action" value="<?php echo $editing_member ? 'update' : 'add'; ?>">
                    <?php if ($editing_member): ?>
                        <input type="hidden" name="member_id" value="<?php echo $editing_member['id']; ?>">
                    <?php endif; ?>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                        <input type="text" name="name" required 
                               value="<?php echo $editing_member ? htmlspecialchars($editing_member['name']) : ''; ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" required
                               value="<?php echo $editing_member ? htmlspecialchars($editing_member['email']) : ''; ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div class="flex items-end space-x-3">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                            <?php echo $editing_member ? 'Update Member' : 'Add Member'; ?>
                        </button>
                        <?php if ($editing_member): ?>
                            <a href="marketing_members.php" class="text-gray-600 hover:text-gray-800 px-4 py-2 transition duration-200">
                                Cancel
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Members Table -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Existing Members</h2>
                    <?php if ($members_result): ?>
                        <p class="text-sm text-gray-600 mt-1">Total: <?php echo $members_result->num_rows; ?> members</p>
                    <?php endif; ?>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if ($members_result && $members_result->num_rows > 0): ?>
                                <?php while ($row = $members_result->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50 transition duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $row['id']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo date('M j, Y', strtotime($row['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="marketing_members.php?edit=<?php echo $row['id']; ?>" 
                                           class="text-blue-600 hover:text-blue-900 mr-3 transition duration-200">
                                            Edit
                                        </a>
                                        <button onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['name']); ?>')" 
                                                class="text-red-600 hover:text-red-900 transition duration-200">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No marketing members found. Add your first member above.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
    function confirmDelete(memberId, memberName) {
        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to delete "${memberName}". This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `marketing_members.php?delete=${memberId}`;
            }
        });
    }

    // Auto-hide alerts after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.bg-green-50, .bg-red-50');
        alerts.forEach(alert => {
            alert.style.display = 'none';
        });
    }, 5000);
    </script>
</body>
</html>

<?php 
$conn->close();
?>