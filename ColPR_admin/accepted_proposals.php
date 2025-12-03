<?php
session_start();
include 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Check if is_read column exists, if not add it
$query_check = "SHOW COLUMNS FROM proposals LIKE 'is_read'";
$column_exists = $conn->query($query_check)->num_rows > 0;
if (!$column_exists) {
    $conn->query("ALTER TABLE proposals ADD COLUMN is_read TINYINT(1) DEFAULT 0");
}

// Handle search by user_id
$user_id_filter = isset($_GET['user_id']) ? intval($_GET['user_id']) : '';
$query = $user_id_filter ? 
    "SELECT p.*, m.name as assigned_member_name, COALESCE(p.is_read, 0) as is_read
     FROM proposals p 
     LEFT JOIN marketing_members m ON p.assigned_member_id = m.id 
     WHERE p.proposal_type = 'ACCEPTED' AND p.user_id = ? 
     ORDER BY p.created_at DESC" : 
    "SELECT p.*, m.name as assigned_member_name, COALESCE(p.is_read, 0) as is_read
     FROM proposals p 
     LEFT JOIN marketing_members m ON p.assigned_member_id = m.id 
     WHERE p.proposal_type = 'ACCEPTED' 
     ORDER BY p.created_at DESC";

$stmt = $conn->prepare($query);
if ($user_id_filter) {
    $stmt->bind_param("i", $user_id_filter);
}
$stmt->execute();
$proposals_result = $stmt->get_result();

// Get chat logs for accepted proposals
$chat_logs_query = $user_id_filter ? 
    "SELECT * FROM chat_logs WHERE user_id = ? ORDER BY timestamp DESC" : 
    "SELECT * FROM chat_logs ORDER BY timestamp DESC";
$stmt_chat = $conn->prepare($chat_logs_query);
if ($user_id_filter) {
    $stmt_chat->bind_param("i", $user_id_filter);
}
$stmt_chat->execute();
$chat_logs_result = $stmt_chat->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accepted Proposals - ColPR Admin</title>
    <link rel="icon" type="image/png" href="colpr-logo.png">
    <link rel="shortcut icon" type="image/png" href="colpr-logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .proposal-row.accepted {
            border-left: 4px solid #10b981;
            background: linear-gradient(90deg, #f0fdf4 0%, #ffffff 100%);
        }
        .proposal-row.accepted.unread-proposal {
            background: linear-gradient(90deg, #d1fae5 0%, #f0fdf4 100%);
            border-left-width: 6px;
        }
        .unread-proposal {
            animation: pulse-border 2s infinite;
        }
        @keyframes pulse-border {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
            }
            50% {
                box-shadow: 0 0 0 4px rgba(16, 185, 129, 0);
            }
        }
        .status-badge.accepted {
            background: #10b981;
            color: white;
        }
        
        /* Main content styles */
        .main-content {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 100vh;
            margin-left: 18rem; /* Default with sidebar open */
            width: calc(100vw - 18rem);
        }
        
        .main-content.full-width {
            margin-left: 0;
            width: 100vw;
        }
        
        body {
            overflow-x: hidden;
        }
        
        /* Mobile responsive */
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
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Accepted Proposals</h1>
                    <p class="text-gray-600 mt-2">Successfully accepted proposals and their details</p>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm font-medium bg-green-100 text-green-800 px-3 py-1 rounded-full">
                        <?php echo $proposals_result->num_rows; ?> Accepted Proposals
                    </span>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="bg-white p-6 rounded-xl shadow-sm mb-8">
                <form method="GET" class="flex items-center space-x-4">
                    <div class="flex-1">
                        <input type="number" name="user_id" placeholder="Search by User ID" 
                               value="<?php echo htmlspecialchars($user_id_filter); ?>" 
                               class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                    <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition duration-200">
                        Search
                    </button>
                    <?php if ($user_id_filter): ?>
                        <a href="accepted_proposals.php" class="text-gray-600 hover:text-gray-800 px-4 py-3 transition duration-200">
                            Clear
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Proposals Table -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Accepted Proposals</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requirements</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timeline</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php while ($row = $proposals_result->fetch_assoc()): ?>
                            <?php $isUnread = (!isset($row['is_read']) || $row['is_read'] == 0); ?>
                            <tr class="proposal-row accepted hover:bg-green-50 transition duration-200 <?php echo $isUnread ? 'bg-green-50 unread-proposal' : ''; ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <?php if ($isUnread): ?>
                                        <span class="inline-block w-2 h-2 bg-green-500 rounded-full animate-pulse" title="Unread"></span>
                                        <?php endif; ?>
                                        <span class="text-sm font-medium <?php echo $isUnread ? 'text-gray-900 font-bold' : 'text-gray-900'; ?>"><?php echo $row['id']; ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm <?php echo $isUnread ? 'text-gray-900 font-semibold' : 'text-gray-900'; ?>"><?php echo $row['user_id']; ?></td>
                                <td class="px-6 py-4 text-sm <?php echo $isUnread ? 'text-gray-900 font-semibold' : 'text-gray-900'; ?> max-w-xs truncate"><?php echo htmlspecialchars($row['requirements']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm <?php echo $isUnread ? 'text-gray-900 font-semibold' : 'text-gray-900'; ?>">$<?php echo $row['estimated_price']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm <?php echo $isUnread ? 'text-gray-900 font-semibold' : 'text-gray-900'; ?>"><?php echo $row['estimated_timeline']; ?> days</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm <?php echo $isUnread ? 'text-gray-900 font-semibold' : 'text-gray-900'; ?>">
                                    <?php echo $row['assigned_member_name'] ?: 'Not assigned'; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm <?php echo $isUnread ? 'text-gray-900 font-semibold' : 'text-gray-900'; ?>"><?php echo date('M j, Y', strtotime($row['created_at'])); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="viewProposalAndMarkRead(<?php echo $row['id']; ?>)" 
                                            class="text-green-600 hover:text-green-900 mr-3 transition duration-200">
                                        View
                                    </button>
                                    <?php if (!$row['assigned_member_name'] || $row['status'] == 'Pending'): ?>
                                        <button onclick="assignProposal(<?php echo $row['id']; ?>)" 
                                                class="text-blue-600 hover:text-blue-900 transition duration-200">
                                            Assign
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Chat Logs Section -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Related Chat Logs</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User Message</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bot Response</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php while ($row = $chat_logs_result->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50 transition duration-200">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $row['id']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $row['user_id']; ?></td>
                                <td class="px-6 py-4 text-sm text-gray-900 max-w-md truncate"><?php echo htmlspecialchars($row['user_message']); ?></td>
                                <td class="px-6 py-4 text-sm text-gray-900 max-w-md truncate"><?php echo htmlspecialchars($row['bot_response']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo date('M j, Y H:i', strtotime($row['timestamp'])); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- View Proposal Modal -->
    <div id="proposalModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl shadow-2xl max-w-6xl w-full mx-4 max-h-[90vh] overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Proposal Details</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto" id="modalContent">
                <!-- Content will be loaded here via AJAX -->
            </div>
        </div>
    </div>

    <!-- Assign Proposal Modal -->
    <div id="assignModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Assign Proposal</h3>
                <button onclick="closeAssignModal()" class="text-gray-400 hover:text-gray-600 transition duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <form id="assignForm" method="POST" action="assign_proposal.php">
                    <input type="hidden" name="proposal_id" id="assignProposalId">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Marketing Member</label>
                        <select name="member_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select a member</option>
                            <!-- Options will be loaded via AJAX -->
                        </select>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeAssignModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition duration-200">Cancel</button>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200">Assign</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function viewProposal(proposalId) {
            // Mark as read first
            markProposalRead(proposalId);
            
            // Load proposal details via AJAX
            fetch(`view_proposal.php?id=${proposalId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('modalContent').innerHTML = data;
                    document.getElementById('proposalModal').classList.remove('hidden');
                    
                    // Update notification badges after viewing
                    if (window.checkNotifications) {
                        setTimeout(checkNotifications, 500);
                    }
                })
                .catch(error => {
                    console.error('Error loading proposal:', error);
                    document.getElementById('modalContent').innerHTML = `
                        <div class="text-center py-8">
                            <p class="text-red-600">Error loading proposal details. Please try again.</p>
                        </div>
                    `;
                    document.getElementById('proposalModal').classList.remove('hidden');
                });
        }
        
        function viewProposalAndMarkRead(proposalId) {
            viewProposal(proposalId);
        }
        
        function markProposalRead(proposalId) {
            const formData = new FormData();
            formData.append('proposal_id', proposalId);
            
            fetch('mark_proposal_read.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove unread styling
                    const row = document.querySelector(`tr[onclick*="${proposalId}"]`) || 
                               document.querySelector(`button[onclick*="${proposalId}"]`)?.closest('tr');
                    if (row) {
                        row.classList.remove('unread-proposal');
                        row.classList.remove('bg-green-50');
                    }
                }
            })
            .catch(error => {
                console.error('Error marking proposal as read:', error);
            });
        }

        function assignProposal(proposalId) {
            document.getElementById("assignProposalId").value = proposalId;
            
            // Load marketing members via AJAX
            fetch("get_marketing_members.php")
                .then(response => response.json())
                .then(members => {
                    const select = document.querySelector("select[name='member_id']");
                    select.innerHTML = '<option value="">Select a member</option>';
                    members.forEach(member => {
                        const option = document.createElement("option");
                        option.value = member.id;
                        option.textContent = member.name + " (" + member.email + ")";
                        select.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading members:', error);
                    Swal.fire('Error', 'Failed to load marketing members', 'error');
                });
            
            document.getElementById("assignModal").classList.remove("hidden");
        }

        function closeModal() {
            document.getElementById('proposalModal').classList.add('hidden');
        }

        function closeAssignModal() {
            document.getElementById("assignModal").classList.add("hidden");
        }

        // Close modal when clicking outside
        document.getElementById('proposalModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        document.getElementById('assignModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAssignModal();
            }
        });

        // Handle assign form submission
        document.getElementById('assignForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('assign_proposal.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                try {
                    const result = JSON.parse(data);
                    if (result.success) {
                        Swal.fire('Success', result.message, 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', result.message, 'error');
                    }
                } catch (e) {
                    Swal.fire('Error', 'Unexpected response from server', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Failed to assign proposal', 'error');
            });
        });
    </script>
</body>
</html>

<?php 
$stmt->close();
$stmt_chat->close();
$conn->close(); 
?>