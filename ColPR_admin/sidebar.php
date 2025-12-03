<!-- Sidebar Component -->
<div id="sidebar" class="w-72 bg-white h-screen p-6 fixed top-0 left-0 z-40 shadow-xl transition-all duration-300 hidden">
    <!-- Header with Close Button -->
    <div class="flex items-center justify-between mb-10">
        <div class="flex items-center space-x-3">
            <img src="colpr-logo.png" alt="ColPR Logo" class="h-10 max-w-full object-contain">
            <div>
                <h2 class="text-lg font-bold text-gray-800">ColPR Admin</h2>
                <p class="text-xs text-gray-500">ColPR Software</p>
            </div>
        </div>
        <!-- Close Icon - Always Visible -->
        <button id="sidebarClose" class="p-2 rounded-lg hover:bg-gray-100 transition duration-200">
            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <nav class="space-y-3">
        <a href="index.php" class="nav-link flex items-center space-x-3 p-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-all duration-300 <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
            <div class="p-2 bg-blue-100 rounded-lg">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
            </div>
            <span class="font-medium">Dashboard Overview</span>
        </a>
        
        <a href="accepted_proposals.php" id="acceptedProposalsLink" class="nav-link flex items-center justify-between p-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-all duration-300 <?php echo basename($_SERVER['PHP_SELF']) == 'accepted_proposals.php' ? 'active' : ''; ?>">
            <div class="flex items-center space-x-3 flex-1">
                <div class="relative">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <!-- Unread Icon Badge -->
                    <span id="acceptedNotificationIcon" class="absolute -top-1 -right-1 bg-green-500 text-white text-[10px] font-bold rounded-full h-4 w-4 flex items-center justify-center hidden shadow-lg animate-pulse"></span>
                </div>
                <span class="font-medium flex-1">Accepted Proposals</span>
            </div>
            <!-- Unread Count Badge -->
            <span id="acceptedNotificationBadge" class="bg-green-500 text-white text-xs font-bold rounded-full h-6 w-6 min-w-[24px] flex items-center justify-center hidden shadow-md ml-2">0</span>
        </a>
        
        <a href="rejected_escalated.php" id="rejectedProposalsLink" class="nav-link flex items-center justify-between p-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-all duration-300 <?php echo basename($_SERVER['PHP_SELF']) == 'rejected_escalated.php' ? 'active' : ''; ?>">
            <div class="flex items-center space-x-3 flex-1">
                <div class="relative">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <!-- Unread Icon Badge -->
                    <span id="rejectedNotificationIcon" class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold rounded-full h-4 w-4 flex items-center justify-center hidden shadow-lg animate-pulse"></span>
                </div>
                <span class="font-medium flex-1">Rejected & Escalated</span>
            </div>
            <!-- Unread Count Badge -->
            <span id="rejectedNotificationBadge" class="bg-red-500 text-white text-xs font-bold rounded-full h-6 w-6 min-w-[24px] flex items-center justify-center hidden shadow-md ml-2">0</span>
        </a>

        <a href="PricingEngine.php" class="nav-link flex items-center space-x-3 p-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-all duration-300 <?php echo basename($_SERVER['PHP_SELF']) == 'PricingEngine.php' ? 'active' : ''; ?>">
            <div class="p-2 bg-orange-100 rounded-lg">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <span class="font-medium">Pricing Engine</span>
        </a>
        
        <a href="marketing_members.php" class="nav-link flex items-center space-x-3 p-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-all duration-300 <?php echo basename($_SERVER['PHP_SELF']) == 'marketing_members.php' ? 'active' : ''; ?>">
            <div class="p-2 bg-purple-100 rounded-lg">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
            <span class="font-medium">Marketing Members</span>
        </a>
        
        <!-- Notification Bell Icon - Moved to bottom -->
        <div class="mt-6 p-3 bg-gray-50 rounded-xl">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        <button id="notificationBell" class="p-2 bg-white rounded-lg hover:bg-gray-100 transition-all duration-200 relative">
                            <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <span id="notificationBadge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                        </button>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-medium text-gray-700">Notifications</span>
                        <span id="newNotificationText" class="text-xs text-red-600 font-semibold hidden">New unread notifications</span>
                    </div>
                </div>
                <button id="markAllReadBtn" class="text-xs text-blue-600 hover:text-blue-800 transition duration-200" title="Mark all as read">Mark all read</button>
            </div>
            <!-- Notification Dropdown -->
            <div id="notificationDropdown" class="hidden mt-3 bg-white rounded-lg shadow-lg border border-gray-200 max-h-64 overflow-y-auto">
                <div id="notificationList" class="p-2">
                    <div class="text-center text-gray-500 text-sm py-4">Loading notifications...</div>
                </div>
            </div>
        </div>
        
        <a href="logout.php" class="nav-link flex items-center space-x-3 p-3 rounded-xl text-gray-600 hover:bg-gray-100 transition-all duration-300">
            <div class="p-2 bg-gray-100 rounded-lg">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
            </div>
            <span class="font-medium">Logout</span>
        </a>
    </nav>
</div>

<!-- View Sidebar Button - Always Visible when sidebar is closed -->
<button id="viewSidebarBtn" class="fixed top-4 left-4 z-50 p-3 bg-white rounded-lg shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-300">
    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
    </svg>
</button>

<style>
#sidebar {
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

#sidebar.hidden {
    transform: translateX(-100%);
}

#sidebar:not(.hidden) {
    transform: translateX(0);
}

.nav-link.active {
    background: linear-gradient(135deg, #fbbf24 0%, #3b82f6 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(251, 191, 36, 0.3);
}

.nav-link.active .bg-blue-100,
.nav-link.active .bg-green-100,
.nav-link.active .bg-red-100,
.nav-link.active .bg-purple-100,
.nav-link.active .bg-orange-100,
.nav-link.active .bg-gray-100 {
    background: rgba(255, 255, 255, 0.2) !important;
}

.nav-link.active .text-blue-600,
.nav-link.active .text-green-600,
.nav-link.active .text-red-600,
.nav-link.active .text-purple-600,
.nav-link.active .text-orange-600,
.nav-link.active .text-gray-600 {
    color: white !important;
}

/* Unread notification highlight */
.nav-link.has-unread {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(239, 68, 68, 0.1) 100%);
    border-left: 3px solid #3b82f6;
    animation: pulse-highlight 2s ease-in-out infinite;
}

.nav-link.has-unread.accepted-unread {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(16, 185, 129, 0.05) 100%);
    border-left: 3px solid #10b981;
}

.nav-link.has-unread.rejected-unread {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(239, 68, 68, 0.05) 100%);
    border-left: 3px solid #ef4444;
}

@keyframes pulse-highlight {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4);
    }
    50% {
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0);
    }
}

/* Notification badge styles */
.notification-badge {
    animation: bounce-in 0.3s ease-out;
}

@keyframes bounce-in {
    0% {
        transform: scale(0);
    }
    50% {
        transform: scale(1.2);
    }
    100% {
        transform: scale(1);
    }
}

/* View Sidebar Button Animation */
#viewSidebarBtn {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

#viewSidebarBtn:hover {
    transform: scale(1.05);
    background-color: #f8fafc;
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

/* Mobile responsive */
@media (max-width: 1024px) {
    .main-content {
        margin-left: 0;
        width: 100vw;
    }
    
    #viewSidebarBtn {
        display: block;
    }
}

@media (min-width: 1025px) {
    #viewSidebarBtn {
        display: block;
    }
}

/* Proposal Popup Animation */
@keyframes slide-in {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slide-out {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

.animate-slide-in {
    animation: slide-in 0.3s ease-out;
}
</style>

<script>
// Global sidebar toggle function
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const viewSidebarBtn = document.getElementById('viewSidebarBtn');
    
    if (sidebar.classList.contains('hidden')) {
        // Show sidebar
        sidebar.classList.remove('hidden');
        if (mainContent) {
            mainContent.classList.remove('full-width');
        }
        viewSidebarBtn.style.display = 'none';
        localStorage.setItem('sidebarVisible', 'true');
    } else {
        // Hide sidebar
        sidebar.classList.add('hidden');
        if (mainContent) {
            mainContent.classList.add('full-width');
        }
        viewSidebarBtn.style.display = 'block';
        localStorage.setItem('sidebarVisible', 'false');
    }
}

// Initialize sidebar state
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const viewSidebarBtn = document.getElementById('viewSidebarBtn');
    
    // Set initial sidebar state - CLOSED by default
    const sidebarVisible = localStorage.getItem('sidebarVisible') === 'true';
    
    if (sidebarVisible) {
        sidebar.classList.remove('hidden');
        if (mainContent) {
            mainContent.classList.remove('full-width');
        }
        viewSidebarBtn.style.display = 'none';
    } else {
        sidebar.classList.add('hidden');
        if (mainContent) {
            mainContent.classList.add('full-width');
        }
        viewSidebarBtn.style.display = 'block';
    }

    // Add event listeners
    document.getElementById('sidebarClose').addEventListener('click', toggleSidebar);
    document.getElementById('viewSidebarBtn').addEventListener('click', toggleSidebar);
    
    // Initialize notification system
    initNotificationSystem();
});

// Notification System
let notificationCheckInterval;
let lastCheckedTime = new Date().toISOString();
let previousNotificationCount = 0;
let previousNotificationIds = new Set();
let notifiedProposalIds = new Set(); // Track proposals that have already been notified about
let previousNotificationCounts = { accepted: 0, rejected: 0, total: 0 };
let browserNotificationPermission = false;

function initNotificationSystem() {
    // Load notified proposal IDs from localStorage
    const savedNotifiedIds = localStorage.getItem('notifiedProposalIds');
    if (savedNotifiedIds) {
        try {
            const idsArray = JSON.parse(savedNotifiedIds);
            notifiedProposalIds = new Set(idsArray.map(id => String(id))); // Convert to strings for consistent comparison
        } catch (e) {
            console.warn('Error loading notified proposal IDs from localStorage:', e);
            notifiedProposalIds = new Set();
        }
    }
    
    // Request browser notification permission
    requestNotificationPermission();
    
    // Check for notifications on page load
    checkNotifications();
    
    // Poll for new notifications every 5 seconds (more frequent)
    notificationCheckInterval = setInterval(checkNotifications, 5000);
    
    // Notification bell click handler
    const notificationBell = document.getElementById('notificationBell');
    const notificationDropdown = document.getElementById('notificationDropdown');
    
    if (notificationBell) {
        notificationBell.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationDropdown.classList.toggle('hidden');
            checkNotifications(); // Refresh when opened
        });
    }
    
    // Mark all as read button
    const markAllReadBtn = document.getElementById('markAllReadBtn');
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            markAllProposalsRead();
        });
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (notificationDropdown && !notificationBell.contains(e.target) && !notificationDropdown.contains(e.target)) {
            notificationDropdown.classList.add('hidden');
        }
    });
    
    // Create audio context for sound notifications
    window.notificationAudio = createNotificationSound();
    
    // Listen for visibility changes to check notifications when tab becomes visible
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            checkNotifications();
        }
    });
}

function requestNotificationPermission() {
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission().then(function(permission) {
            browserNotificationPermission = permission === 'granted';
        });
    } else if ('Notification' in window && Notification.permission === 'granted') {
        browserNotificationPermission = true;
    }
}

function checkNotifications() {
    fetch('get_notifications.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const currentNotificationIds = new Set((data.recent_unread || []).map(p => String(p.id)));
                
                // Filter for truly NEW proposals that haven't been notified about yet
                // Convert IDs to strings for consistent comparison
                const newNotifications = (data.recent_unread || []).filter(p => {
                    const proposalId = String(p.id);
                    return !notifiedProposalIds.has(proposalId);
                });
                
                // Check if counts changed (for increment/decrement detection)
                const countChanged = data.total_unread !== previousNotificationCount;
                const acceptedChanged = data.unread_accepted !== (previousNotificationCounts?.accepted || 0);
                const rejectedChanged = (data.unread_rejected + data.unread_escalated) !== (previousNotificationCounts?.rejected || 0);
                
                updateNotificationBadges(data);
                updateNotificationList(data.recent_unread || []);
                
                // Play sound and show browser notification for NEW proposals (only once per proposal)
                if (newNotifications.length > 0) {
                    // Play sound only once for all new notifications
                    playNotificationSound();
                    
                    // Show browser notification and popup for each new proposal
                    newNotifications.forEach(proposal => {
                        const proposalId = String(proposal.id);
                        
                        // Mark as notified BEFORE showing notifications to prevent duplicates
                        notifiedProposalIds.add(proposalId);
                        
                        showBrowserNotification(proposal);
                        showProposalPopup(proposal);
                    });
                    
                    // Save notified IDs to localStorage to persist across page refreshes
                    try {
                        localStorage.setItem('notifiedProposalIds', JSON.stringify(Array.from(notifiedProposalIds)));
                    } catch (e) {
                        console.warn('Error saving notified proposal IDs to localStorage:', e);
                    }
                }
                
                // Update previous counts and IDs
                previousNotificationCount = data.total_unread;
                previousNotificationCounts = {
                    accepted: data.unread_accepted,
                    rejected: data.unread_rejected + data.unread_escalated,
                    total: data.total_unread
                };
                previousNotificationIds = currentNotificationIds;
            }
        })
        .catch(error => {
            console.error('Error checking notifications:', error);
        });
}

function updateNotificationBadges(data) {
    // Update main notification badge
    const notificationBadge = document.getElementById('notificationBadge');
    const newNotificationText = document.getElementById('newNotificationText');
    
    if (notificationBadge) {
        if (data.total_unread > 0) {
            notificationBadge.textContent = data.total_unread > 99 ? '99+' : data.total_unread;
            notificationBadge.classList.remove('hidden');
            if (newNotificationText) {
                newNotificationText.classList.remove('hidden');
            }
        } else {
            notificationBadge.classList.add('hidden');
            if (newNotificationText) {
                newNotificationText.classList.add('hidden');
            }
        }
    }
    
    // Update accepted proposals badge and highlight
    const acceptedBadge = document.getElementById('acceptedNotificationBadge');
    const acceptedIcon = document.getElementById('acceptedNotificationIcon');
    const acceptedLink = document.getElementById('acceptedProposalsLink');
    
    if (acceptedBadge && acceptedIcon && acceptedLink) {
        if (data.unread_accepted > 0) {
            const count = data.unread_accepted > 99 ? '99+' : data.unread_accepted;
            acceptedBadge.textContent = count;
            acceptedBadge.classList.remove('hidden');
            acceptedBadge.classList.add('notification-badge');
            
            // Show icon badge
            acceptedIcon.classList.remove('hidden');
            
            // Add highlight to sidebar item
            acceptedLink.classList.add('has-unread', 'accepted-unread');
            
            // Adjust badge size for larger numbers
            if (data.unread_accepted > 9) {
                acceptedBadge.classList.remove('h-6', 'w-6');
                acceptedBadge.classList.add('h-7', 'px-2', 'min-w-[28px]');
            } else {
                acceptedBadge.classList.remove('h-7', 'px-2', 'min-w-[28px]');
                acceptedBadge.classList.add('h-6', 'w-6');
            }
        } else {
            acceptedBadge.classList.add('hidden');
            acceptedIcon.classList.add('hidden');
            acceptedLink.classList.remove('has-unread', 'accepted-unread');
        }
    }
    
    // Update rejected/escalated proposals badge and highlight
    const rejectedBadge = document.getElementById('rejectedNotificationBadge');
    const rejectedIcon = document.getElementById('rejectedNotificationIcon');
    const rejectedLink = document.getElementById('rejectedProposalsLink');
    
    if (rejectedBadge && rejectedIcon && rejectedLink) {
        const totalRejected = data.unread_rejected + data.unread_escalated;
        if (totalRejected > 0) {
            const count = totalRejected > 99 ? '99+' : totalRejected;
            rejectedBadge.textContent = count;
            rejectedBadge.classList.remove('hidden');
            rejectedBadge.classList.add('notification-badge');
            
            // Show icon badge
            rejectedIcon.classList.remove('hidden');
            
            // Add highlight to sidebar item
            rejectedLink.classList.add('has-unread', 'rejected-unread');
            
            // Adjust badge size for larger numbers
            if (totalRejected > 9) {
                rejectedBadge.classList.remove('h-6', 'w-6');
                rejectedBadge.classList.add('h-7', 'px-2', 'min-w-[28px]');
            } else {
                rejectedBadge.classList.remove('h-7', 'px-2', 'min-w-[28px]');
                rejectedBadge.classList.add('h-6', 'w-6');
            }
        } else {
            rejectedBadge.classList.add('hidden');
            rejectedIcon.classList.add('hidden');
            rejectedLink.classList.remove('has-unread', 'rejected-unread');
        }
    }
}

function updateNotificationList(recentUnread) {
    const notificationList = document.getElementById('notificationList');
    if (!notificationList) return;
    
    if (recentUnread.length === 0) {
        notificationList.innerHTML = '<div class="text-center text-gray-500 text-sm py-4">No new notifications</div>';
        return;
    }
    
    let html = '<div class="px-2 py-1 text-xs font-semibold text-gray-700 bg-blue-50 rounded mb-2">New Unread Notifications</div>';
    recentUnread.forEach(proposal => {
        const typeColors = {
            'ACCEPTED': 'bg-green-100 text-green-800',
            'REJECTED': 'bg-red-100 text-red-800',
            'ESCALATED': 'bg-yellow-100 text-yellow-800'
        };
        const colorClass = typeColors[proposal.proposal_type] || 'bg-gray-100 text-gray-800';
        const date = new Date(proposal.created_at);
        const timeAgo = getTimeAgo(date);
        const proposalTypeLower = proposal.proposal_type.toLowerCase();
        const isNew = !notifiedProposalIds.has(String(proposal.id));
        
        html += '<div class="p-3 border-b border-gray-200 hover:bg-gray-50 transition duration-150 cursor-pointer ' + (isNew ? 'bg-blue-50' : '') + '" onclick="viewProposalAndMarkRead(' + proposal.id + ')">';
        html += '<div class="flex items-start justify-between">';
        html += '<div class="flex-1">';
        html += '<div class="flex items-center space-x-2 mb-1">';
        html += '<span class="px-2 py-1 text-xs font-semibold rounded ' + colorClass + '">' + proposal.proposal_type + '</span>';
        html += '<span class="text-xs text-gray-500">Proposal #' + proposal.id + '</span>';
        if (isNew) {
            html += '<span class="px-1.5 py-0.5 text-xs font-bold text-white bg-red-500 rounded-full">NEW</span>';
        }
        html += '</div>';
        html += '<p class="text-sm text-gray-600 font-medium">New ' + proposalTypeLower + ' proposal</p>';
        html += '<p class="text-xs text-gray-400 mt-1">' + timeAgo + '</p>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
    });
    
    notificationList.innerHTML = html;
}

function getTimeAgo(date) {
    const now = new Date();
    const diff = Math.floor((now - date) / 1000); // seconds
    
    if (diff < 60) return 'Just now';
    if (diff < 3600) return Math.floor(diff / 60) + ' minutes ago';
    if (diff < 86400) return Math.floor(diff / 3600) + ' hours ago';
    return Math.floor(diff / 86400) + ' days ago';
}

function viewProposalAndMarkRead(proposalId) {
    // Mark as read
    markProposalRead(proposalId);
    
    // Redirect to appropriate page based on proposal type
    window.location.href = `allproposals.php?proposal_id=${proposalId}`;
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
            // Immediately refresh notifications to update counts
            checkNotifications();
            
            // Also remove from previous notification IDs set (convert to string for consistency)
            previousNotificationIds.delete(String(proposalId));
        }
    })
    .catch(error => {
        console.error('Error marking proposal as read:', error);
    });
}

function markAllProposalsRead() {
    const formData = new FormData();
    formData.append('mark_all', 'true');
    
    fetch('mark_proposal_read.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Refresh notifications
            checkNotifications();
            
            // Show success message
            const notificationDropdown = document.getElementById('notificationDropdown');
            const notificationList = document.getElementById('notificationList');
            if (notificationList) {
                notificationList.innerHTML = '<div class="text-center text-green-600 text-sm py-4">All notifications marked as read</div>';
            }
        }
    })
    .catch(error => {
        console.error('Error marking all as read:', error);
    });
}

function createNotificationSound() {
    try {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        
        return function playSound() {
            try {
                // Resume audio context if suspended (browser autoplay policy)
                if (audioContext.state === 'suspended') {
                    audioContext.resume();
                }
                
                // Create a pleasant notification sound with multiple tones
                const oscillator1 = audioContext.createOscillator();
                const oscillator2 = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                
                oscillator1.connect(gainNode);
                oscillator2.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                // Three-tone notification sound (more noticeable)
                oscillator1.frequency.setValueAtTime(800, audioContext.currentTime);
                oscillator1.frequency.setValueAtTime(1000, audioContext.currentTime + 0.1);
                oscillator1.frequency.setValueAtTime(1200, audioContext.currentTime + 0.2);
                
                oscillator2.frequency.setValueAtTime(600, audioContext.currentTime);
                oscillator2.frequency.setValueAtTime(800, audioContext.currentTime + 0.1);
                oscillator2.frequency.setValueAtTime(1000, audioContext.currentTime + 0.2);
                
                gainNode.gain.setValueAtTime(0.4, audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
                
                oscillator1.start(audioContext.currentTime);
                oscillator2.start(audioContext.currentTime);
                oscillator1.stop(audioContext.currentTime + 0.3);
                oscillator2.stop(audioContext.currentTime + 0.3);
            } catch (e) {
                console.warn('Error playing sound:', e);
            }
        };
    } catch (e) {
        // Fallback: use Web Audio API or silent
        console.warn('Audio context not supported, notifications will be silent');
        return function() {}; // Silent fallback
    }
}

function playNotificationSound() {
    if (window.notificationAudio) {
        try {
            window.notificationAudio();
        } catch (e) {
            console.warn('Could not play notification sound:', e);
        }
    }
}

function showBrowserNotification(proposal) {
    if ('Notification' in window && Notification.permission === 'granted') {
        const proposalType = proposal.proposal_type.toLowerCase();
        const notification = new Notification('New ' + proposalType.charAt(0).toUpperCase() + proposalType.slice(1) + ' Proposal', {
            body: 'Proposal #' + proposal.id + ' has been received',
            icon: 'colpr-logo.png',
            badge: 'colpr-logo.png',
            tag: 'proposal-' + proposal.id,
            requireInteraction: false
        });
        
        notification.onclick = function() {
            window.focus();
            viewProposalAndMarkRead(proposal.id);
            notification.close();
        };
        
        // Auto close after 5 seconds
        setTimeout(() => notification.close(), 5000);
    }
}

function showProposalPopup(proposal) {
    // Remove existing popup if any
    const existingPopup = document.getElementById('proposalPopup');
    if (existingPopup) {
        existingPopup.remove();
    }
    
    const proposalType = proposal.proposal_type.toLowerCase();
    const typeColors = {
        'ACCEPTED': { bg: 'bg-green-500', text: 'text-green-800', border: 'border-green-300' },
        'REJECTED': { bg: 'bg-red-500', text: 'text-red-800', border: 'border-red-300' },
        'ESCALATED': { bg: 'bg-yellow-500', text: 'text-yellow-800', border: 'border-yellow-300' }
    };
    const colors = typeColors[proposal.proposal_type] || { bg: 'bg-blue-500', text: 'text-blue-800', border: 'border-blue-300' };
    
    const popup = document.createElement('div');
    popup.id = 'proposalPopup';
    popup.className = 'fixed top-4 right-4 z-50 bg-white rounded-lg shadow-2xl border-2 ' + colors.border + ' p-4 max-w-sm animate-slide-in';
    popup.innerHTML = `
        <div class="flex items-start justify-between mb-3">
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 rounded-full ${colors.bg} animate-pulse"></div>
                <h3 class="font-bold ${colors.text}">New ${proposalType} Proposal</h3>
            </div>
            <button onclick="closeProposalPopup()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <p class="text-sm text-gray-600 mb-3">Proposal #${proposal.id} has been received</p>
        <div class="flex space-x-2">
            <button onclick="viewProposalAndMarkRead(${proposal.id}); closeProposalPopup();" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200 font-medium">
                View Proposal
            </button>
            <button onclick="closeProposalPopup()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition duration-200">
                Dismiss
            </button>
        </div>
    `;
    
    document.body.appendChild(popup);
    
    // Auto close after 10 seconds
    setTimeout(() => {
        if (document.getElementById('proposalPopup')) {
            closeProposalPopup();
        }
    }, 10000);
}

function closeProposalPopup() {
    const popup = document.getElementById('proposalPopup');
    if (popup) {
        popup.style.animation = 'slide-out 0.3s ease-out';
        setTimeout(() => popup.remove(), 300);
    }
}

// Make functions globally accessible
window.viewProposalAndMarkRead = viewProposalAndMarkRead;
window.closeProposalPopup = closeProposalPopup;
window.checkNotifications = checkNotifications;

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (notificationCheckInterval) {
        clearInterval(notificationCheckInterval);
    }
});
</script>