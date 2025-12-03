<?php
session_start();
include 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Get current month and year
$current_month = date('F Y');
$month_start = date('Y-m-01');
$month_end = date('Y-m-t');

// Get monthly statistics
$monthly_total = $conn->query("SELECT COUNT(*) as count FROM proposals WHERE created_at BETWEEN '$month_start' AND '$month_end'")->fetch_assoc()['count'];
$monthly_accepted = $conn->query("SELECT COUNT(*) as count FROM proposals WHERE proposal_type = 'ACCEPTED' AND created_at BETWEEN '$month_start' AND '$month_end'")->fetch_assoc()['count'];
$monthly_rejected = $conn->query("SELECT COUNT(*) as count FROM proposals WHERE proposal_type = 'REJECTED' AND created_at BETWEEN '$month_start' AND '$month_end'")->fetch_assoc()['count'];
$monthly_escalated = $conn->query("SELECT COUNT(*) as count FROM proposals WHERE proposal_type = 'ESCALATED' AND created_at BETWEEN '$month_start' AND '$month_end'")->fetch_assoc()['count'];

// Get top contributors for the month
$monthly_contributors = $conn->query("
    SELECT user_id, COUNT(*) as proposal_count 
    FROM proposals 
    WHERE created_at BETWEEN '$month_start' AND '$month_end'
    GROUP BY user_id 
    ORDER BY proposal_count DESC 
    LIMIT 5
")->fetch_all(MYSQLI_ASSOC);

// Get all proposals for the month
$monthly_proposals = $conn->query("
    SELECT * FROM proposals 
    WHERE created_at BETWEEN '$month_start' AND '$month_end'
    ORDER BY created_at DESC
")->fetch_all(MYSQLI_ASSOC);

// Handle TXT export
if (isset($_POST['export_txt'])) {
    $filename = "monthly_report_" . date('Y_m') . ".txt";
    
    // Set headers for download
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    // Generate TXT content
    $content = "ColPR - MONTHLY PROPOSAL REPORT\n";
    $content .= "================================\n";
    $content .= "Report Period: " . $current_month . "\n";
    $content .= "Generated on: " . date('Y-m-d H:i:s') . "\n\n";
    
    $content .= "MONTHLY STATISTICS\n";
    $content .= "==================\n";
    $content .= "Total Proposals: " . $monthly_total . "\n";
    $content .= "Accepted: " . $monthly_accepted . "\n";
    $content .= "Rejected: " . $monthly_rejected . "\n";
    $content .= "Escalated: " . $monthly_escalated . "\n";
    
    $approval_rate = $monthly_total > 0 ? round(($monthly_accepted / $monthly_total) * 100, 2) : 0;
    $rejection_rate = $monthly_total > 0 ? round(($monthly_rejected / $monthly_total) * 100, 2) : 0;
    
    $content .= "Approval Rate: " . $approval_rate . "%\n";
    $content .= "Rejection Rate: " . $rejection_rate . "%\n\n";
    
    $content .= "TOP CONTRIBUTORS\n";
    $content .= "================\n";
    $rank = 1;
    foreach($monthly_contributors as $contributor) {
        $content .= $rank . ". User " . $contributor['user_id'] . " - " . $contributor['proposal_count'] . " proposals\n";
        $rank++;
    }
    
    $content .= "\nDETAILED PROPOSALS\n";
    $content .= "==================\n";
    foreach($monthly_proposals as $proposal) {
        $content .= "ID: " . $proposal['id'] . "\n";
        $content .= "User: " . ($proposal['user_id'] ?? 'N/A') . "\n";
        $content .= "Status: " . $proposal['proposal_type'] . "\n";
        $content .= "Created: " . $proposal['created_at'] . "\n";
        $content .= "----------------------------------------\n";
    }
    
    $content .= "\nREPORT SUMMARY\n";
    $content .= "==============\n";
    $content .= "Total proposals this month: " . $monthly_total . "\n";
    $content .= "Success rate: " . $approval_rate . "%\n";
    $content .= "Areas needing attention: " . ($monthly_rejected + $monthly_escalated) . " proposals\n";
    
    echo $content;
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Monthly Report - ColPR Admin</title>
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
        
        .stat-box {
            background: white;
            border-radius: 0.5rem;
            padding: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .stat-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
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
        
        .contributor-item, .proposal-item {
            transition: all 0.3s ease;
        }
        
        .contributor-item:hover, .proposal-item:hover {
            transform: translateX(4px);
            background-color: #f8fafc;
        }
        
        .export-button {
            transition: all 0.3s ease;
        }
        
        .export-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.4);
        }
        
        .status-badge {
            transition: all 0.3s ease;
        }
        
        .status-badge:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body class="bg-gray-50 overflow-x-hidden">
    <?php include 'sidebar.php'; ?>

    <div class="main-content p-8 min-h-screen transition-all duration-300" id="mainContent">
        <div class="max-w-6xl mx-auto content-wrapper">
            <!-- Header -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-8 transition-all duration-300 hover:shadow-md">
                <h1 class="text-2xl font-bold text-gray-800 transition-colors duration-200">Export Monthly Report</h1>
                <p class="text-gray-600 mt-2 transition-colors duration-200">Generate comprehensive proposal analytics for <?php echo $current_month; ?></p>
            </div>

            <!-- Report Summary -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-8 transition-all duration-300 hover:shadow-md">
                <h2 class="text-xl font-semibold mb-6 transition-colors duration-200">Monthly Summary - <?php echo $current_month; ?></h2>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                    <div class="stat-box border-l-4 border-blue-500">
                        <p class="text-sm text-blue-600 font-medium transition-colors duration-200">Total Proposals</p>
                        <p class="text-2xl font-bold text-blue-800 mt-1 transition-colors duration-200"><?php echo $monthly_total; ?></p>
                    </div>
                    <div class="stat-box border-l-4 border-green-500">
                        <p class="text-sm text-green-600 font-medium transition-colors duration-200">Accepted</p>
                        <p class="text-2xl font-bold text-green-800 mt-1 transition-colors duration-200"><?php echo $monthly_accepted; ?></p>
                    </div>
                    <div class="stat-box border-l-4 border-red-500">
                        <p class="text-sm text-red-600 font-medium transition-colors duration-200">Rejected</p>
                        <p class="text-2xl font-bold text-red-800 mt-1 transition-colors duration-200"><?php echo $monthly_rejected; ?></p>
                    </div>
                    <div class="stat-box border-l-4 border-yellow-500">
                        <p class="text-sm text-yellow-600 font-medium transition-colors duration-200">Escalated</p>
                        <p class="text-2xl font-bold text-yellow-800 mt-1 transition-colors duration-200"><?php echo $monthly_escalated; ?></p>
                    </div>
                </div>

                <!-- Performance Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="bg-blue-50 p-4 rounded-lg transition-all duration-300 hover:shadow-md">
                        <h3 class="text-lg font-semibold text-blue-800 mb-2 transition-colors duration-200">Approval Rate</h3>
                        <p class="text-3xl font-bold text-blue-900 transition-colors duration-200">
                            <?php echo $monthly_total > 0 ? round(($monthly_accepted / $monthly_total) * 100, 2) : 0; ?>%
                        </p>
                    </div>
                    <div class="bg-red-50 p-4 rounded-lg transition-all duration-300 hover:shadow-md">
                        <h3 class="text-lg font-semibold text-red-800 mb-2 transition-colors duration-200">Issues Requiring Attention</h3>
                        <p class="text-3xl font-bold text-red-900 transition-colors duration-200"><?php echo $monthly_rejected + $monthly_escalated; ?></p>
                    </div>
                </div>

                <!-- Top Contributors -->
                <h3 class="text-lg font-semibold mb-4 transition-colors duration-200">Top Contributors</h3>
                <div class="space-y-3 mb-8">
                    <?php 
                    $rank = 1;
                    foreach($monthly_contributors as $contributor): 
                    ?>
                    <div class="contributor-item flex items-center justify-between p-3 bg-gray-50 rounded-lg transition-all duration-300">
                        <div class="flex items-center">
                            <span class="bg-purple-100 text-purple-800 rounded-full w-8 h-8 flex items-center justify-center text-sm font-semibold mr-3 transition-all duration-300 hover:scale-110">
                                <?php echo $rank; ?>
                            </span>
                            <span class="font-medium transition-colors duration-200">User <?php echo $contributor['user_id']; ?></span>
                        </div>
                        <span class="text-sm text-gray-600 bg-white px-3 py-1 rounded-full transition-colors duration-200">
                            <?php echo $contributor['proposal_count']; ?> proposals
                        </span>
                    </div>
                    <?php 
                    $rank++;
                    endforeach; 
                    ?>
                </div>

                <!-- Recent Proposals -->
                <h3 class="text-lg font-semibold mb-4 transition-colors duration-200">Recent Proposals This Month</h3>
                <div class="space-y-2 max-h-60 overflow-y-auto">
                    <?php 
                    $recent_monthly = array_slice($monthly_proposals, 0, 10);
                    foreach($recent_monthly as $proposal): 
                    ?>
                    <div class="proposal-item flex items-center justify-between p-3 bg-gray-50 rounded-lg transition-all duration-300">
                        <div class="flex items-center space-x-3">
                            <span class="status-badge <?php 
                                if($proposal['proposal_type'] == 'ACCEPTED') echo 'bg-green-100 text-green-800';
                                elseif($proposal['proposal_type'] == 'REJECTED') echo 'bg-red-100 text-red-800';
                                else echo 'bg-yellow-100 text-yellow-800';
                            ?> text-xs px-2 py-1 rounded-full font-medium transition-all duration-300">
                                <?php echo $proposal['proposal_type']; ?>
                            </span>
                            <span class="text-sm font-medium transition-colors duration-200"><?php echo substr($proposal['id'], 0, 12); ?>...</span>
                        </div>
                        <span class="text-xs text-gray-500 transition-colors duration-200"><?php echo date('M j', strtotime($proposal['created_at'])); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Export Options -->
            <div class="bg-white rounded-xl shadow-sm p-6 transition-all duration-300 hover:shadow-md">
                <h2 class="text-xl font-semibold mb-4 transition-colors duration-200">Export Report</h2>
                <form method="POST">
                    <button type="submit" name="export_txt" class="export-button bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2 transition-transform duration-300 hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download TXT Report
                    </button>
                </form>
                
                <div class="mt-4 p-4 bg-blue-50 rounded-lg transition-all duration-300 hover:shadow-md">
                    <h4 class="font-medium text-blue-800 mb-2 transition-colors duration-200">What's included in the report:</h4>
                    <ul class="text-sm text-blue-700 list-disc list-inside space-y-1">
                        <li class="transition-colors duration-200">Monthly statistics and performance metrics</li>
                        <li class="transition-colors duration-200">Top contributors ranking</li>
                        <li class="transition-colors duration-200">Detailed list of all proposals</li>
                        <li class="transition-colors duration-200">Approval and rejection rates</li>
                        <li class="transition-colors duration-200">Executive summary and recommendations</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sidebar toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mainContent = document.getElementById('mainContent');
            
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
            
            // Add animation to elements on load
            const animateElements = () => {
                const statBoxes = document.querySelectorAll('.stat-box');
                const contributorItems = document.querySelectorAll('.contributor-item');
                const proposalItems = document.querySelectorAll('.proposal-item');
                
                // Animate stat boxes
                statBoxes.forEach((box, index) => {
                    box.style.opacity = '0';
                    box.style.transform = 'translateY(20px)';
                    
                    setTimeout(() => {
                        box.style.transition = 'all 0.6s ease';
                        box.style.opacity = '1';
                        box.style.transform = 'translateY(0)';
                    }, index * 100);
                });
                
                // Animate contributor items
                contributorItems.forEach((item, index) => {
                    item.style.opacity = '0';
                    item.style.transform = 'translateX(-20px)';
                    
                    setTimeout(() => {
                        item.style.transition = 'all 0.5s ease';
                        item.style.opacity = '1';
                        item.style.transform = 'translateX(0)';
                    }, (statBoxes.length * 100) + (index * 50));
                });
                
                // Animate proposal items
                proposalItems.forEach((item, index) => {
                    item.style.opacity = '0';
                    item.style.transform = 'translateX(-20px)';
                    
                    setTimeout(() => {
                        item.style.transition = 'all 0.5s ease';
                        item.style.opacity = '1';
                        item.style.transform = 'translateX(0)';
                    }, (statBoxes.length * 100) + (contributorItems.length * 50) + (index * 30));
                });
            };
            
            // Initialize animations
            setTimeout(animateElements, 100);
            
            // Add hover effects to export button
            const exportButton = document.querySelector('.export-button');
            if (exportButton) {
                exportButton.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px) scale(1.02)';
                });
                
                exportButton.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            }
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