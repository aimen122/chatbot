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

// Handle search
$search_term = isset($_GET['search']) ? $_GET['search'] : '';

// Build query with search
$where_conditions = [];
$query_params = [];

if (!empty($search_term)) {
    $where_conditions[] = "(id LIKE ? OR user_id LIKE ?)";
    $query_params[] = "%$search_term%";
    $query_params[] = "%$search_term%";
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = "WHERE " . implode(' AND ', $where_conditions);
}

// Get total count for pagination
$count_query = "SELECT COUNT(*) as count FROM proposals $where_clause";
$count_stmt = $conn->prepare($count_query);
if (!empty($query_params)) {
    $count_stmt->bind_param(str_repeat('s', count($query_params)), ...$query_params);
}
$count_stmt->execute();
$total_proposals = $count_stmt->get_result()->fetch_assoc()['count'];
$count_stmt->close();

// Pagination
$limit = 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$total_pages = ceil($total_proposals / $limit);

// Get proposals with search and pagination
$query = "SELECT *, COALESCE(is_read, 0) as is_read FROM proposals $where_clause ORDER BY created_at DESC LIMIT ? OFFSET ?";
$query_params[] = $limit;
$query_params[] = $offset;

$stmt = $conn->prepare($query);
if (!empty($query_params)) {
    $stmt->bind_param(str_repeat('s', count($query_params)), ...$query_params);
}
$stmt->execute();
$proposals = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Proposals - ColPR Admin</title>
    <link rel="icon" type="image/png" href="colpr-logo.png">
    <link rel="shortcut icon" type="image/png" href="colpr-logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .main-content {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 100vh;
            margin-left: 18rem;
            width: calc(100vw - 18rem);
        }
        
        .main-content.sidebar-collapsed {
            margin-left: 5rem;
            width: calc(100vw - 5rem);
        }
        
        .main-content.full-width {
            margin-left: 0;
            width: 100vw;
        }
        
        @media (max-width: 1024px) {
            .main-content {
                margin-left: 0;
                width: 100vw;
            }
            
            .main-content.sidebar-collapsed {
                margin-left: 0;
                width: 100vw;
            }
        }
        
        .proposal-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
        }
        
        .proposal-badge.accepted { background-color: #86efac; color: #166534; }
        .proposal-badge.rejected { background-color: #fca5a5; color: #991b1b; }
        .proposal-badge.escalated { background-color: #fcd34d; color: #92400e; }
        
        .table-row:hover {
            background-color: #f8fafc;
            transform: translateX(4px);
            transition: all 0.2s ease;
        }
        
        .content-wrapper {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .sidebar-open .content-wrapper {
            transform: translateX(0);
        }
        
        .sidebar-collapsed .content-wrapper {
            transform: translateX(-13rem);
        }
        
        .stat-card {
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .unread-proposal-row {
            border-left: 4px solid #3b82f6;
            animation: pulse-blue 2s infinite;
        }
        
        @keyframes pulse-blue {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4);
            }
            50% {
                box-shadow: 0 0 0 4px rgba(59, 130, 246, 0);
            }
        }
    </style>
</head>
<body class="bg-gray-50 overflow-x-hidden">
    <!-- Include Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content p-8 min-h-screen transition-all duration-300" id="mainContent">
        <div class="max-w-7xl mx-auto content-wrapper">
            <!-- Header -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-8 transition-all duration-300 hover:shadow-md">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">All Proposals</h1>
                        <p class="text-gray-600 mt-2">View all submitted proposals in the system</p>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-800"><?php echo $total_proposals; ?></div>
                        <div class="text-sm text-gray-500">Total Proposals</div>
                    </div>
                </div>
            </div>

            <!-- Search -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6 transition-all duration-300 hover:shadow-md">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex-1">
                        <form method="GET" class="flex gap-2">
                            <input type="text" 
                                   name="search" 
                                   value="<?php echo htmlspecialchars($search_term); ?>" 
                                   placeholder="Search by Proposal ID or User ID..." 
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition duration-200 transform hover:-translate-y-0.5">
                                Search
                            </button>
                            <?php if (!empty($search_term)): ?>
                                <a href="allproposals.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition duration-200 transform hover:-translate-y-0.5 flex items-center">
                                    Clear
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Proposals Table -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden transition-all duration-300 hover:shadow-md">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider transition-colors duration-200">Proposal ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider transition-colors duration-200">User ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider transition-colors duration-200">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider transition-colors duration-200">Submitted Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider transition-colors duration-200">Submitted Time</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($proposals)): ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 transition-all duration-300">
                                        <svg class="mx-auto h-12 w-12 text-gray-400 transition-transform duration-300 hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900 transition-colors duration-200">No proposals found</h3>
                                        <p class="mt-1 text-sm text-gray-500 transition-colors duration-200">
                                            <?php echo empty($search_term) ? 'No proposals have been submitted yet.' : 'No proposals found matching your search criteria.'; ?>
                                        </p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($proposals as $proposal): ?>
                                <?php $isUnread = (!isset($proposal['is_read']) || $proposal['is_read'] == 0); ?>
                                <tr class="table-row transition-all duration-150 <?php echo $isUnread ? 'bg-blue-50 unread-proposal-row' : ''; ?>">
                                    <td class="px-6 py-4 whitespace-nowrap transition-colors duration-200">
                                        <div class="flex items-center space-x-2">
                                            <?php if ($isUnread): ?>
                                            <span class="inline-block w-2 h-2 bg-blue-500 rounded-full animate-pulse" title="Unread"></span>
                                            <?php endif; ?>
                                            <div class="text-sm font-medium <?php echo $isUnread ? 'text-gray-900 font-bold' : 'text-gray-900'; ?> font-mono">
                                                <?php echo $proposal['id']; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap transition-colors duration-200">
                                        <div class="text-sm <?php echo $isUnread ? 'text-gray-900 font-semibold' : 'text-gray-900'; ?> font-medium font-mono">
                                            <?php echo isset($proposal['user_id']) ? $proposal['user_id'] : 'N/A'; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap transition-colors duration-200">
                                        <span class="proposal-badge <?php echo strtolower($proposal['proposal_type']); ?> transition-all duration-300 hover:scale-105">
                                            <?php echo $proposal['proposal_type']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap transition-colors duration-200">
                                        <div class="text-sm <?php echo $isUnread ? 'text-gray-900 font-semibold' : 'text-gray-900'; ?> font-medium">
                                            <?php echo date('F j, Y', strtotime($proposal['created_at'])); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap transition-colors duration-200">
                                        <div class="text-sm <?php echo $isUnread ? 'text-gray-600 font-semibold' : 'text-gray-500'; ?>">
                                            <?php echo date('g:i A', strtotime($proposal['created_at'])); ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 transition-all duration-300">
                    <div class="flex flex-1 justify-between sm:hidden">
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => max(1, $page-1)])); ?>" 
                           class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition duration-200">
                            Previous
                        </a>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => min($total_pages, $page+1)])); ?>" 
                           class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition duration-200">
                            Next
                        </a>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700 transition-colors duration-200">
                                Showing <span class="font-medium"><?php echo $offset + 1; ?></span> to 
                                <span class="font-medium"><?php echo min($offset + $limit, $total_proposals); ?></span> of 
                                <span class="font-medium"><?php echo $total_proposals; ?></span> results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => max(1, $page-1)])); ?>" 
                                   class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition duration-200">
                                    <span class="sr-only">Previous</span>
                                    <svg class="h-5 w-5 transition-transform duration-200 hover:scale-110" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
                                       class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium <?php echo $i == $page ? 'text-blue-600 bg-blue-50 border-blue-500' : 'text-gray-500 hover:bg-gray-50'; ?> transition duration-200">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endfor; ?>
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => min($total_pages, $page+1)])); ?>" 
                                   class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition duration-200">
                                    <span class="sr-only">Next</span>
                                    <svg class="h-5 w-5 transition-transform duration-200 hover:scale-110" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </nav>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Summary Stats -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="stat-card bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500 transition-all duration-300 hover:shadow-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-500 transition-transform duration-300 hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 transition-colors duration-200">Total Proposals</p>
                            <p class="text-2xl font-bold text-gray-900 transition-colors duration-200"><?php echo $total_proposals; ?></p>
                        </div>
                    </div>
                </div>
                
                <?php
                // Get status counts for summary
                $accepted_count = $conn->query("SELECT COUNT(*) as count FROM proposals WHERE proposal_type = 'ACCEPTED'")->fetch_assoc()['count'];
                $rejected_count = $conn->query("SELECT COUNT(*) as count FROM proposals WHERE proposal_type = 'REJECTED'")->fetch_assoc()['count'];
                $escalated_count = $conn->query("SELECT COUNT(*) as count FROM proposals WHERE proposal_type = 'ESCALATED'")->fetch_assoc()['count'];
                ?>
                
                <div class="stat-card bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500 transition-all duration-300 hover:shadow-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-500 transition-transform duration-300 hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 transition-colors duration-200">Accepted</p>
                            <p class="text-2xl font-bold text-gray-900 transition-colors duration-200"><?php echo $accepted_count; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="stat-card bg-white rounded-lg shadow-sm p-4 border-l-4 border-red-500 transition-all duration-300 hover:shadow-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-500 transition-transform duration-300 hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 transition-colors duration-200">Rejected + Escalated</p>
                            <p class="text-2xl font-bold text-gray-900 transition-colors duration-200"><?php echo $rejected_count + $escalated_count; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sidebar toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mainContent = document.getElementById('mainContent');
            const sidebarToggle = document.getElementById('sidebarToggle');
            
            // Check if sidebar state is stored in localStorage
            const isSidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            
            if (isSidebarCollapsed) {
                mainContent.classList.add('sidebar-collapsed');
                document.body.classList.add('sidebar-collapsed');
            } else {
                mainContent.classList.remove('sidebar-collapsed');
                document.body.classList.remove('sidebar-collapsed');
            }
            
            // Listen for sidebar toggle events (from sidebar.php)
            document.addEventListener('sidebarToggled', function() {
                const isCollapsed = mainContent.classList.contains('sidebar-collapsed');
                
                if (isCollapsed) {
                    mainContent.classList.remove('sidebar-collapsed');
                    document.body.classList.remove('sidebar-collapsed');
                    localStorage.setItem('sidebarCollapsed', 'false');
                } else {
                    mainContent.classList.add('sidebar-collapsed');
                    document.body.classList.add('sidebar-collapsed');
                    localStorage.setItem('sidebarCollapsed', 'true');
                }
            });
            
            // Add animation to table rows on load
            const tableRows = document.querySelectorAll('.table-row');
            tableRows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateX(-20px)';
                
                setTimeout(() => {
                    row.style.transition = 'all 0.5s ease';
                    row.style.opacity = '1';
                    row.style.transform = 'translateX(0)';
                }, index * 100);
            });
            
            // Add hover effects to pagination buttons
            const paginationButtons = document.querySelectorAll('nav a');
            paginationButtons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                });
                
                button.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
        
        // Handle window resize
        window.addEventListener('resize', function() {
            const mainContent = document.getElementById('mainContent');
            if (window.innerWidth <= 1024) {
                mainContent.classList.remove('sidebar-collapsed');
                mainContent.classList.add('full-width');
            } else {
                mainContent.classList.remove('full-width');
                // Restore previous sidebar state
                const isSidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                if (isSidebarCollapsed) {
                    mainContent.classList.add('sidebar-collapsed');
                } else {
                    mainContent.classList.remove('sidebar-collapsed');
                }
            }
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>