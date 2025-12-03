<?php
session_start();
include 'config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo "Unauthorized access.";
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Mark proposal as read when viewed
    // First check if is_read column exists
    $query_check = "SHOW COLUMNS FROM proposals LIKE 'is_read'";
    $column_exists = $conn->query($query_check)->num_rows > 0;
    
    if (!$column_exists) {
        // Add is_read column if it doesn't exist
        $conn->query("ALTER TABLE proposals ADD COLUMN is_read TINYINT(1) DEFAULT 0");
    }
    
    // Mark as read
    $update_stmt = $conn->prepare("UPDATE proposals SET is_read = 1 WHERE id = ?");
    $update_stmt->bind_param("i", $id);
    $update_stmt->execute();
    $update_stmt->close();
    
    $query = "SELECT proposal_content, proposal_type, created_at, user_id FROM proposals WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Output the full HTML page with proper styling and scroll
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Proposal Details - ColPR Admin</title>
            <link rel="icon" type="image/png" href="colpr-logo.png">
            <link rel="shortcut icon" type="image/png" href="colpr-logo.png">
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
            <script src="https://cdn.tailwindcss.com"></script>
            <style>
                body {
                    font-family: 'Inter', sans-serif;
                    background-color: #f8fafc;
                }
                
                .proposal-content {
                    max-height: 70vh;
                    overflow-y: auto;
                    background: white;
                    border: 1px solid #e2e8f0;
                    border-radius: 0.5rem;
                    padding: 1.5rem;
                    margin: 1rem 0;
                    line-height: 1.6;
                }
                
                .proposal-content::-webkit-scrollbar {
                    width: 8px;
                }
                
                .proposal-content::-webkit-scrollbar-track {
                    background: #f1f5f9;
                    border-radius: 4px;
                }
                
                .proposal-content::-webkit-scrollbar-thumb {
                    background: #cbd5e1;
                    border-radius: 4px;
                }
                
                .proposal-content::-webkit-scrollbar-thumb:hover {
                    background: #94a3b8;
                }
                
                .proposal-header {
                    background: white;
                    border-radius: 0.5rem;
                    padding: 1.5rem;
                    margin-bottom: 1rem;
                    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                }
                
                .status-badge {
                    padding: 0.25rem 0.75rem;
                    border-radius: 9999px;
                    font-size: 0.75rem;
                    font-weight: 600;
                }
                
                .status-badge.accepted { background-color: #86efac; color: #166534; }
                .status-badge.rejected { background-color: #fca5a5; color: #991b1b; }
                .status-badge.escalated { background-color: #fcd34d; color: #92400e; }
                
                .close-button {
                    transition: all 0.3s ease;
                }
                
                .close-button:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                }
            </style>
        </head>
        <body class="bg-gray-50 p-4">
            <div class="max-w-4xl mx-auto">
                <!-- Header -->
                <div class="proposal-header">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">Proposal Details</h1>
                            <p class="text-gray-600 mt-1">Proposal ID: <?php echo $id; ?></p>
                        </div>
                        <span class="status-badge <?php echo strtolower($row['proposal_type']); ?>">
                            <?php echo $row['proposal_type']; ?>
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                        <div>
                            <span class="font-medium">Submitted by:</span>
                            <p class="mt-1">User <?php echo htmlspecialchars($row['user_id'] ?? 'Unknown'); ?></p>
                        </div>
                        <div>
                            <span class="font-medium">Submitted on:</span>
                            <p class="mt-1"><?php echo date('F j, Y', strtotime($row['created_at'])); ?></p>
                        </div>
                        <div>
                            <span class="font-medium">Submitted at:</span>
                            <p class="mt-1"><?php echo date('g:i A', strtotime($row['created_at'])); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Proposal Content with Scroll -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Proposal Content</h2>
                    <div class="proposal-content">
                        <?php 
                        $content = htmlspecialchars($row['proposal_content']);
                        echo nl2br($content); 
                        ?>
                    </div>
                    
                    <!-- Scroll indicator -->
                    <div class="mt-3 text-center">
                        <p class="text-sm text-gray-500 flex items-center justify-center">
                            <svg class="w-4 h-4 mr-1 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                            </svg>
                            Scroll to read full proposal content
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 flex justify-end space-x-3">
                    <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print
                    </button>
                    <button onclick="window.close()" class="close-button bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Close
                    </button>
                </div>
            </div>

            <script>
                // Add smooth scrolling behavior
                document.addEventListener('DOMContentLoaded', function() {
                    const proposalContent = document.querySelector('.proposal-content');
                    
                    // Add scroll event listener to hide scroll indicator when user starts scrolling
                    proposalContent.addEventListener('scroll', function() {
                        const scrollIndicator = document.querySelector('.text-center');
                        if (this.scrollTop > 10) {
                            scrollIndicator.style.opacity = '0.5';
                        } else {
                            scrollIndicator.style.opacity = '1';
                        }
                    });

                    // Add keyboard navigation
                    document.addEventListener('keydown', function(e) {
                        if (e.key === 'Escape') {
                            window.close();
                        }
                    });

                    // Auto-focus on content for better accessibility
                    proposalContent.focus();
                });
            </script>
        </body>
        </html>
        <?php
    } else {
        // Proposal not found - show error page
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Proposal Not Found - ColPR Admin</title>
            <link rel="icon" type="image/png" href="colpr-logo.png">
            <link rel="shortcut icon" type="image/png" href="colpr-logo.png">
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
            <script src="https://cdn.tailwindcss.com"></script>
        </head>
        <body class="bg-gray-50 p-4">
            <div class="max-w-md mx-auto text-center">
                <div class="bg-white rounded-lg shadow-sm p-8">
                    <svg class="mx-auto h-16 w-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <h2 class="mt-4 text-xl font-bold text-gray-800">Proposal Not Found</h2>
                    <p class="mt-2 text-gray-600">The requested proposal could not be found.</p>
                    <button onclick="window.close()" class="mt-6 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-200">
                        Close Window
                    </button>
                </div>
            </div>
        </body>
        </html>
        <?php
    }
    
    $stmt->close();
} else {
    // Invalid ID - show error page
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Invalid Proposal - ColPR Admin</title>
        <link rel="icon" type="image/png" href="colpr-logo.png">
        <link rel="shortcut icon" type="image/png" href="colpr-logo.png">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-50 p-4">
        <div class="max-w-md mx-auto text-center">
            <div class="bg-white rounded-lg shadow-sm p-8">
                <svg class="mx-auto h-16 w-16 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <h2 class="mt-4 text-xl font-bold text-gray-800">Invalid Proposal ID</h2>
                <p class="mt-2 text-gray-600">Please provide a valid proposal ID.</p>
                <button onclick="window.close()" class="mt-6 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    Close Window
                </button>
            </div>
        </div>
    </body>
    </html>
    <?php
}

$conn->close();
?>