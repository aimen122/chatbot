<?php
session_start();
include 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Get proposal statistics
$total_proposals = $conn->query("SELECT COUNT(*) as count FROM proposals")->fetch_assoc()['count'];
$accepted_proposals = $conn->query("SELECT COUNT(*) as count FROM proposals WHERE proposal_type = 'ACCEPTED'")->fetch_assoc()['count'];
$rejected_proposals = $conn->query("SELECT COUNT(*) as count FROM proposals WHERE proposal_type = 'REJECTED'")->fetch_assoc()['count'];
$escalated_proposals = $conn->query("SELECT COUNT(*) as count FROM proposals WHERE proposal_type = 'ESCALATED'")->fetch_assoc()['count'];

// Calculate rates
$approval_rate = $total_proposals > 0 ? round(($accepted_proposals / $total_proposals) * 100) : 0;
$rejection_rate = $total_proposals > 0 ? round(($rejected_proposals / $total_proposals) * 100) : 0;

// Get recent proposals
$recent_proposals = $conn->query("SELECT * FROM proposals ORDER BY created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

// Get top contributors
$top_contributors = $conn->query("
    SELECT user_id, COUNT(*) as proposal_count 
    FROM proposals 
    GROUP BY user_id 
    ORDER BY proposal_count DESC 
    LIMIT 3
")->fetch_all(MYSQLI_ASSOC);

// Get monthly proposal trends starting from the first proposal month
$monthly_trends = [];
$months = [];
$monthly_total = [];
$monthly_approved = [];
$monthly_rejected = [];
$monthly_escalated = [];

// Get the earliest proposal date
$earliest_result = $conn->query("SELECT MIN(created_at) as earliest_date FROM proposals");
$earliest_date = null;
if ($earliest_result && $row = $earliest_result->fetch_assoc()) {
    $earliest_date = $row['earliest_date'];
}

if ($earliest_date) {
    // Parse the earliest date
    $earliest = new DateTime($earliest_date);
    $earliest_month = $earliest->format('Y-m');
    $current = new DateTime();
    $current_month = $current->format('Y-m');
    
    // Create a date period from earliest month to current month
    $start = new DateTime($earliest_month . '-01');
    $end = new DateTime($current_month . '-01');
    $end->modify('+1 month'); // Include current month
    
    $interval = new DateInterval('P1M'); // 1 month interval
    $period = new DatePeriod($start, $interval, $end);
    
    foreach ($period as $date) {
        $date_str = $date->format('Y-m');
        $month_name = $date->format('M Y');
        
        $total = $conn->query("SELECT COUNT(*) as count FROM proposals WHERE DATE_FORMAT(created_at, '%Y-%m') = '$date_str'")->fetch_assoc()['count'];
        $approved = $conn->query("SELECT COUNT(*) as count FROM proposals WHERE proposal_type = 'ACCEPTED' AND DATE_FORMAT(created_at, '%Y-%m') = '$date_str'")->fetch_assoc()['count'];
        $rejected = $conn->query("SELECT COUNT(*) as count FROM proposals WHERE proposal_type = 'REJECTED' AND DATE_FORMAT(created_at, '%Y-%m') = '$date_str'")->fetch_assoc()['count'];
        $escalated = $conn->query("SELECT COUNT(*) as count FROM proposals WHERE proposal_type = 'ESCALATED' AND DATE_FORMAT(created_at, '%Y-%m') = '$date_str'")->fetch_assoc()['count'];
        
        $months[] = $month_name;
        $monthly_total[] = (int)$total;
        $monthly_approved[] = (int)$approved;
        $monthly_rejected[] = (int)$rejected;
        $monthly_escalated[] = (int)$escalated;
    }
} else {
    // Fallback: if no proposals exist, show last 6 months
    for ($i = 5; $i >= 0; $i--) {
        $date = date('Y-m', strtotime("-$i months"));
        $month_name = date('M Y', strtotime("-$i months"));
        
        $months[] = $month_name;
        $monthly_total[] = 0;
        $monthly_approved[] = 0;
        $monthly_rejected[] = 0;
        $monthly_escalated[] = 0;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ColPR Admin Dashboard</title>
    <link rel="icon" type="image/png" href="colpr-logo.png">
    <link rel="shortcut icon" type="image/png" href="colpr-logo.png">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #3b82f6;
            --accepted-color: #10b981;
            --rejected-color: #ef4444;
            --escalated-color: #f59e0b;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #fef3c7 0%, #dbeafe 100%);
            min-height: 100vh;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, #fbbf24 0%, #3b82f6 100%);
            color: white;
            padding: 1.5rem 2rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 15px rgba(251, 191, 36, 0.3);
        }
        
        .stat-card {
            background-color: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-left: 4px solid;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card.accepted { border-left-color: var(--accepted-color); }
        .stat-card.rejected { border-left-color: var(--rejected-color); }
        
        .proposal-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            color: white;
        }
        
        .proposal-badge.accepted { background-color: var(--accepted-color); }
        .proposal-badge.rejected { background-color: var(--rejected-color); }
        .proposal-badge.escalated { background-color: var(--escalated-color); }
        
        .contributor-card {
            background-color: white;
            border-radius: 0.75rem;
            padding: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
            transition: transform 0.2s ease;
        }
        
        .contributor-card:hover {
            transform: translateX(4px);
        }
        
        .quick-action-card {
            background-color: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            border: 1px solid #e2e8f0;
        }
        
        .quick-action-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-color: var(--primary-color);
        }
        
        .trends-section {
            background-color: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }
        
        .activity-item {
            padding: 0.75rem 0;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        #proposalTrendsChart {
            max-height: 300px;
        }
        
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
        
        @media (max-width: 1024px) {
            .main-content {
                margin-left: 0;
                width: 100vw;
            }
        }
    </style>
</head>
<body class="bg-gray-50 overflow-x-hidden">
    <!-- Include Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content p-8 min-h-screen transition-all duration-300 full-width" id="mainContent">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="dashboard-header">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold">Dashboard Overview</h1>
                        <p class="mt-2 opacity-90">Monitor proposal submissions and approval metrics</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <img src="colpr-logo.png" alt="ColPR Logo" class="h-12 max-w-full object-contain opacity-90">
                        <div class="text-right">
                            <p class="text-lg font-semibold">ColPR Admin</p>
                            <p class="text-sm opacity-90">ColPR Software</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analytics Section -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4">Analytics</h2>
                <p class="text-gray-600 mb-6">Track proposal trends and performance metrics</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="stat-card accepted">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm text-gray-500">Approval Rate</p>
                                <p class="text-2xl font-bold mt-1"><?php echo $approval_rate; ?>%</p>
                                <p class="text-xs text-gray-500 mt-1">0% from last month</p>
                            </div>
                            <div class="p-2 bg-yellow-50 rounded-lg">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card accepted">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm text-gray-500">Approved</p>
                                <p class="text-2xl font-bold mt-1"><?php echo $accepted_proposals; ?></p>
                                <p class="text-xs text-gray-500 mt-1">0% Success rate <?php echo $approval_rate; ?>%</p>
                            </div>
                            <div class="p-2 bg-yellow-50 rounded-lg">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card rejected">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm text-gray-500">Rejected</p>
                                <p class="text-2xl font-bold mt-1"><?php echo $rejected_proposals; ?></p>
                                <p class="text-xs text-gray-500 mt-1">0% From last month</p>
                            </div>
                            <div class="p-2 bg-blue-50 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card rejected">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm text-gray-500">Rejection Rate</p>
                                <p class="text-2xl font-bold mt-1"><?php echo $rejection_rate; ?>%</p>
                                <p class="text-xs text-blue-500 font-medium mt-1">Monitor closely</p>
                            </div>
                            <div class="p-2 bg-blue-50 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Proposal Trends and Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Proposal Trends -->
                <div class="trends-section">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-lg font-semibold">Proposal Trends</h3>
                            <p class="text-gray-600 text-sm mt-1">Monthly submission and approval patterns</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button id="chartTypeBtn" class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                <span id="chartTypeText">Line Chart</span>
                            </button>
                        </div>
                    </div>
                    
                    <div class="relative" style="height: 300px;">
                        <canvas id="proposalTrendsChart"></canvas>
                    </div>
                    
                    <div class="flex justify-center mt-4 space-x-6 flex-wrap gap-2">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full mr-2" style="background: linear-gradient(135deg, #fbbf24 0%, #3b82f6 100%);"></div>
                            <span class="text-xs text-gray-600">Total</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                            <span class="text-xs text-gray-600">Approved</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                            <span class="text-xs text-gray-600">Rejected</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                            <span class="text-xs text-gray-600">Escalated</span>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="trends-section">
                    <h3 class="text-lg font-semibold mb-4">Recent Activity</h3>
                    <p class="text-gray-600 mb-4">Latest proposal actions and updates</p>
                    
                    <div class="space-y-2">
                        <?php foreach($recent_proposals as $proposal): ?>
                        <div class="activity-item">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="proposal-badge <?php echo strtolower($proposal['proposal_type']); ?>">
                                        <?php echo strtolower($proposal['proposal_type']); ?>
                                    </span>
                                    <p class="text-sm font-medium mt-1"><?php echo substr($proposal['id'], 0, 8); ?>...</p>
                                    <p class="text-xs text-gray-500">by <?php echo isset($proposal['user_id']) ? substr($proposal['user_id'], 0, 8) : 'unknown'; ?></p>
                                </div>
                                <span class="text-xs text-gray-500"><?php echo date('M j, H:i', strtotime($proposal['created_at'])); ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Top Contributors and Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Top Contributors -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Top Contributors</h3>
                    <p class="text-gray-600 mb-4">Most active users and their approval rates</p>
                    
                    <div class="space-y-4">
                        <?php
                        $rank = 1;
                        foreach($top_contributors as $contributor):
                        ?>
                        <div class="contributor-card">
                            <div class="flex items-center">
                                <span class="bg-yellow-100 text-yellow-800 rounded-full w-8 h-8 flex items-center justify-center mr-3 font-semibold">
                                    #<?php echo $rank; ?>
                                </span>
                                <div>
                                    <p class="font-medium">User <?php echo substr($contributor['user_id'], 0, 8); ?></p>
                                    <p class="text-sm text-gray-500"><?php echo $contributor['proposal_count']; ?> proposal<?php echo $contributor['proposal_count'] > 1 ? 's' : ''; ?></p>
                                </div>
                            </div>
                        </div>
                        <?php 
                        $rank++;
                        endforeach; 
                        ?>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                    <p class="text-gray-600 mb-4">Common dashboard tasks</p>
                    
                    <div class="space-y-4">
                        <div class="quick-action-card" onclick="window.location.href='export.php'">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium">Export Monthly Report</h4>
                                    <p class="text-sm text-gray-500 mt-1">Generate comprehensive proposal analytics</p>
                                </div>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                        
                        <div class="quick-action-card" onclick="window.location.href='marketing_members.php'">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium">Manage Marketing Members</h4>
                                    <p class="text-sm text-gray-500 mt-1">Update user access and permissions</p>
                                </div>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                        </div>
                        
                        <div class="quick-action-card" onclick="window.location.href='allproposals.php'">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium">View All Proposals</h4>
                                    <p class="text-sm text-gray-500 mt-1">Review accessible and relocated proposals</p>
                                </div>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Chart data from PHP
            const months = <?php echo json_encode($months); ?>;
            const monthlyTotal = <?php echo json_encode($monthly_total); ?>;
            const monthlyApproved = <?php echo json_encode($monthly_approved); ?>;
            const monthlyRejected = <?php echo json_encode($monthly_rejected); ?>;
            const monthlyEscalated = <?php echo json_encode($monthly_escalated); ?>;
            
            // Overall totals for pie chart
            const totalApproved = <?php echo $accepted_proposals; ?>;
            const totalRejected = <?php echo $rejected_proposals; ?>;
            const totalEscalated = <?php echo $escalated_proposals; ?>;
            
            const ctx = document.getElementById('proposalTrendsChart').getContext('2d');
            let chartType = 'line'; // 'line', 'bar', or 'pie'
            let proposalChart;
            
            // Create gradient for total line (yellow-blue theme)
            const gradientTotal = ctx.createLinearGradient(0, 0, 0, 300);
            gradientTotal.addColorStop(0, 'rgba(251, 191, 36, 0.3)');
            gradientTotal.addColorStop(1, 'rgba(251, 191, 36, 0.05)');
            
            const gradientApproved = ctx.createLinearGradient(0, 0, 0, 300);
            gradientApproved.addColorStop(0, 'rgba(59, 130, 246, 0.3)');
            gradientApproved.addColorStop(1, 'rgba(59, 130, 246, 0.05)');
            
            function createChart(type) {
                if (proposalChart) {
                    proposalChart.destroy();
                }
                
                let config;
                
                if (type === 'pie') {
                    // Pie chart configuration
                    config = {
                        type: 'pie',
                        data: {
                            labels: ['Approved', 'Rejected', 'Escalated'],
                            datasets: [{
                                data: [totalApproved, totalRejected, totalEscalated],
                                backgroundColor: [
                                    'rgba(59, 130, 246, 0.8)',
                                    'rgba(239, 68, 68, 0.8)',
                                    'rgba(251, 191, 36, 0.8)'
                                ],
                                borderColor: [
                                    'rgb(59, 130, 246)',
                                    'rgb(239, 68, 68)',
                                    'rgb(251, 191, 36)'
                                ],
                                borderWidth: 3,
                                hoverOffset: 8
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'bottom',
                                    labels: {
                                        padding: 15,
                                        font: {
                                            size: 12,
                                            weight: '500'
                                        },
                                        usePointStyle: true,
                                        pointStyle: 'circle'
                                    }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    padding: 12,
                                    titleFont: {
                                        size: 14,
                                        weight: 'bold'
                                    },
                                    bodyFont: {
                                        size: 13
                                    },
                                    borderColor: 'rgba(255, 255, 255, 0.1)',
                                    borderWidth: 1,
                                    cornerRadius: 8,
                                    displayColors: true,
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.parsed || 0;
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                            return label + ': ' + value + ' (' + percentage + '%)';
                                        }
                                    }
                                }
                            },
                            animation: {
                                animateRotate: true,
                                animateScale: true,
                                duration: 1500,
                                easing: 'easeInOutQuart'
                            }
                        }
                    };
                } else {
                    // Line/Bar chart configuration
                    config = {
                        type: type,
                        data: {
                            labels: months,
                            datasets: [
                                {
                                    label: 'Total Proposals',
                                    data: monthlyTotal,
                                    borderColor: 'rgb(251, 191, 36)',
                                    backgroundColor: type === 'line' ? gradientTotal : 'rgba(251, 191, 36, 0.6)',
                                    borderWidth: 3,
                                    fill: type === 'line' ? true : false,
                                    tension: type === 'line' ? 0.4 : 0,
                                    pointRadius: 5,
                                    pointHoverRadius: 7,
                                    pointBackgroundColor: '#fff',
                                    pointBorderColor: 'rgb(251, 191, 36)',
                                    pointBorderWidth: 2,
                                },
                                {
                                    label: 'Approved',
                                    data: monthlyApproved,
                                    borderColor: 'rgb(59, 130, 246)',
                                    backgroundColor: type === 'line' ? gradientApproved : 'rgba(59, 130, 246, 0.6)',
                                    borderWidth: 3,
                                    fill: type === 'line' ? true : false,
                                    tension: type === 'line' ? 0.4 : 0,
                                    pointRadius: 5,
                                    pointHoverRadius: 7,
                                    pointBackgroundColor: '#fff',
                                    pointBorderColor: 'rgb(59, 130, 246)',
                                    pointBorderWidth: 2,
                                },
                                {
                                    label: 'Rejected',
                                    data: monthlyRejected,
                                    borderColor: 'rgb(239, 68, 68)',
                                    backgroundColor: 'rgba(239, 68, 68, 0.6)',
                                    borderWidth: 3,
                                    fill: type === 'line' ? false : false,
                                    tension: type === 'line' ? 0.4 : 0,
                                    pointRadius: 5,
                                    pointHoverRadius: 7,
                                    pointBackgroundColor: '#fff',
                                    pointBorderColor: 'rgb(239, 68, 68)',
                                    pointBorderWidth: 2,
                                },
                                {
                                    label: 'Escalated',
                                    data: monthlyEscalated,
                                    borderColor: 'rgb(245, 158, 11)',
                                    backgroundColor: 'rgba(245, 158, 11, 0.6)',
                                    borderWidth: 3,
                                    fill: type === 'line' ? false : false,
                                    tension: type === 'line' ? 0.4 : 0,
                                    pointRadius: 5,
                                    pointHoverRadius: 7,
                                    pointBackgroundColor: '#fff',
                                    pointBorderColor: 'rgb(245, 158, 11)',
                                    pointBorderWidth: 2,
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    padding: 12,
                                    titleFont: {
                                        size: 14,
                                        weight: 'bold'
                                    },
                                    bodyFont: {
                                        size: 13
                                    },
                                    borderColor: 'rgba(255, 255, 255, 0.1)',
                                    borderWidth: 1,
                                    cornerRadius: 8,
                                    displayColors: true,
                                    callbacks: {
                                        label: function(context) {
                                            return context.dataset.label + ': ' + context.parsed.y + ' proposals';
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.05)',
                                        drawBorder: false
                                    },
                                    ticks: {
                                        precision: 0,
                                        font: {
                                            size: 11
                                        },
                                        color: '#6b7280'
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false,
                                        drawBorder: false
                                    },
                                    ticks: {
                                        font: {
                                            size: 11
                                        },
                                        color: '#6b7280'
                                    }
                                }
                            },
                            animation: {
                                duration: 1500,
                                easing: 'easeInOutQuart'
                            },
                            interaction: {
                                intersect: false,
                                mode: 'index'
                            }
                        }
                    };
                }
                
                proposalChart = new Chart(ctx, config);
            }
            
            // Initialize with line chart
            createChart('line');
            
            // Toggle chart type (line -> bar -> pie -> line)
            document.getElementById('chartTypeBtn').addEventListener('click', function() {
                if (chartType === 'line') {
                    chartType = 'bar';
                    document.getElementById('chartTypeText').textContent = 'Bar Chart';
                } else if (chartType === 'bar') {
                    chartType = 'pie';
                    document.getElementById('chartTypeText').textContent = 'Pie Chart';
                } else {
                    chartType = 'line';
                    document.getElementById('chartTypeText').textContent = 'Line Chart';
                }
                createChart(chartType);
            });
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>