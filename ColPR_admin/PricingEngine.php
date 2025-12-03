<?php
session_start();
include 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle form submission for adding/updating service
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category = trim($_POST['category']);
    $service_name = trim($_POST['service_name']);
    $description = trim($_POST['description']);
    $min_price = floatval($_POST['min_price']);
    $max_price = floatval($_POST['max_price']);
    $min_timeline = intval($_POST['min_timeline']);
    $max_timeline = intval($_POST['max_timeline']);
    $action = $_POST['action'];
    $service_id = isset($_POST['service_id']) ? intval($_POST['service_id']) : 0;

    // Validation
    $errors = [];
    
    if (empty($category)) $errors[] = "Category is required";
    if (empty($service_name)) $errors[] = "Service name is required";
    if ($min_price <= 0 || $max_price <= 0) $errors[] = "Price ranges must be greater than 0";
    if ($min_price > $max_price) $errors[] = "Minimum price cannot be greater than maximum price";
    if ($min_timeline <= 0 || $max_timeline <= 0) $errors[] = "Timeline ranges must be greater than 0";
    if ($min_timeline > $max_timeline) $errors[] = "Minimum timeline cannot be greater than maximum timeline";

    if (empty($errors)) {
        if ($action == 'add') {
            // Add new service to database
            $stmt = $conn->prepare("INSERT INTO services (category, service_name, description, min_price, max_price, min_timeline, max_timeline) VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            if ($stmt) {
                $stmt->bind_param("sssdddd", $category, $service_name, $description, $min_price, $max_price, $min_timeline, $max_timeline);
                
                if ($stmt->execute()) {
                    header("Location: PricingEngine.php?success=Service added successfully");
                    exit();
                } else {
                    header("Location: PricingEngine.php?error=Failed to add service: " . $stmt->error);
                    exit();
                }
                $stmt->close();
            } else {
                header("Location: PricingEngine.php?error=Database error: " . $conn->error);
                exit();
            }
            
        } elseif ($action == 'update' && $service_id > 0) {
            // Update existing service
            $stmt = $conn->prepare("UPDATE services SET category=?, service_name=?, description=?, min_price=?, max_price=?, min_timeline=?, max_timeline=? WHERE id=?");
            
            if ($stmt) {
                $stmt->bind_param("sssddddi", $category, $service_name, $description, $min_price, $max_price, $min_timeline, $max_timeline, $service_id);
                
                if ($stmt->execute()) {
                    header("Location: PricingEngine.php?success=Service updated successfully");
                    exit();
                } else {
                    header("Location: PricingEngine.php?error=Failed to update service: " . $stmt->error);
                    exit();
                }
                $stmt->close();
            }
        }
    } else {
        header("Location: PricingEngine.php?error=" . urlencode(implode(", ", $errors)));
        exit();
    }
}

// Handle delete action
if (isset($_GET['delete'])) {
    $service_id = intval($_GET['delete']);
    
    $check_sql = "SELECT service_name FROM services WHERE id = $service_id";
    $check_result = $conn->query($check_sql);
    
    if ($check_result && $check_result->num_rows > 0) {
        $service = $check_result->fetch_assoc();
        $sql = "DELETE FROM services WHERE id = $service_id";
        
        if ($conn->query($sql) === TRUE) {
            header("Location: PricingEngine.php?success=Service deleted successfully");
        } else {
            header("Location: PricingEngine.php?error=Failed to delete service");
        }
    } else {
        header("Location: PricingEngine.php?error=Service not found");
    }
    exit();
}

// Fetch all services for display
$services_query = "SELECT * FROM services ORDER BY category, service_name";
$services_result = $conn->query($services_query);

// Group services by category
$services_by_category = [];
if ($services_result && $services_result->num_rows > 0) {
    while ($service = $services_result->fetch_assoc()) {
        $services_by_category[$service['category']][] = $service;
    }
}

// Get existing categories for dropdown
$categories_result = $conn->query("SELECT DISTINCT category FROM services ORDER BY category");
$existing_categories = [];
if ($categories_result && $categories_result->num_rows > 0) {
    while ($row = $categories_result->fetch_assoc()) {
        $existing_categories[] = $row['category'];
    }
}

// Handle messages
$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';

// Check if editing or adding
$editing_service = null;
$show_form = false;

if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM services WHERE id = $edit_id");
    if ($result && $result->num_rows > 0) {
        $editing_service = $result->fetch_assoc();
        $show_form = true;
    }
}

if (isset($_GET['add'])) {
    $show_form = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing Engine - ColPR Admin</title>
    <link rel="icon" type="image/png" href="colpr-logo.png">
    <link rel="shortcut icon" type="image/png" href="colpr-logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .main-content { margin-left: 18rem; width: calc(100vw - 18rem); }
        .main-content.full-width { margin-left: 0; width: 100vw; }
        @media (max-width: 1024px) { .main-content { margin-left: 0; width: 100vw; } }
        .price-badge { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .timeline-badge { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }
        .service-card { transition: all 0.3s ease; border-left: 4px solid #667eea; }
        .service-card:hover { transform: translateY(-2px); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1); }
        .category-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .floating-btn { position: fixed; bottom: 2rem; right: 2rem; z-index: 50; transition: all 0.3s ease; }
        .floating-btn:hover { transform: scale(1.05); }
    </style>
</head>
<body class="font-inter bg-gray-50 overflow-x-hidden">
    <?php include 'sidebar.php'; ?>

    <div class="main-content p-8 min-h-screen transition-all duration-300 full-width" id="mainContent">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Pricing Engine</h1>
                    <p class="text-gray-600 mt-2">Manage your services, pricing, and timelines</p>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm font-medium bg-blue-100 text-blue-800 px-3 py-1 rounded-full">
                        <?php 
                        $total_services = 0;
                        foreach ($services_by_category as $services) $total_services += count($services);
                        echo $total_services; 
                        ?> Services
                    </span>
                    <span class="text-sm font-medium bg-green-100 text-green-800 px-3 py-1 rounded-full">
                        <?php echo count($services_by_category); ?> Categories
                    </span>
                </div>
            </div>

            <!-- Notifications -->
            <?php if ($success): ?>
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-green-800"><?php echo htmlspecialchars($success); ?></p>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-red-800"><?php echo htmlspecialchars($error); ?></p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Add/Edit Service Form -->
            <?php if ($show_form): ?>
            <div class="bg-white rounded-xl shadow-sm p-6 mb-8" id="serviceForm">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-800">
                        <?php echo $editing_service ? 'Edit Service' : 'Add New Service'; ?>
                    </h2>
                    <button type="button" onclick="hideServiceForm()" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form method="POST" action="PricingEngine.php" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <input type="hidden" name="action" value="<?php echo $editing_service ? 'update' : 'add'; ?>">
                    <?php if ($editing_service): ?>
                        <input type="hidden" name="service_id" value="<?php echo $editing_service['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="md:col-span-2 lg:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                        <input type="text" name="category" required value="<?php echo $editing_service ? htmlspecialchars($editing_service['category']) : ''; ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="e.g., Web Design & Development" list="categories">
                        <datalist id="categories">
                            <?php foreach ($existing_categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat); ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    
                    <div class="md:col-span-2 lg:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Service Name *</label>
                        <input type="text" name="service_name" required value="<?php echo $editing_service ? htmlspecialchars($editing_service['service_name']) : ''; ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="e.g., Basic Sites, E-Commerce">
                    </div>
                    
                    <div class="md:col-span-2 lg:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Describe the service..."><?php echo $editing_service ? htmlspecialchars($editing_service['description']) : ''; ?></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Price ($) *</label>
                        <input type="number" name="min_price" step="0.01" min="0" required value="<?php echo $editing_service ? $editing_service['min_price'] : ''; ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="0.00">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Price ($) *</label>
                        <input type="number" name="max_price" step="0.01" min="0" required value="<?php echo $editing_service ? $editing_service['max_price'] : ''; ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="0.00">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Timeline (Days) *</label>
                        <input type="number" name="min_timeline" min="1" required value="<?php echo $editing_service ? $editing_service['min_timeline'] : ''; ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="e.g., 7">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Timeline (Days) *</label>
                        <input type="number" name="max_timeline" min="1" required value="<?php echo $editing_service ? $editing_service['max_timeline'] : ''; ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="e.g., 30">
                    </div>
                    
                    <div class="md:col-span-2 lg:col-span-3 flex items-end space-x-3">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                            <?php echo $editing_service ? 'Update Service' : 'Add Service'; ?>
                        </button>
                        <?php if ($editing_service): ?>
                            <a href="PricingEngine.php" class="text-gray-600 hover:text-gray-800 px-4 py-2">Cancel</a>
                        <?php else: ?>
                            <button type="button" onclick="hideServiceForm()" class="text-gray-600 hover:text-gray-800 px-4 py-2">Cancel</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            <?php endif; ?>

            <!-- Services Display -->
            <div class="space-y-8">
                <?php if (!empty($services_by_category)): ?>
                    <?php foreach ($services_by_category as $category => $services): ?>
                        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                            <div class="category-header px-6 py-4">
                                <h2 class="text-lg font-semibold"><?php echo htmlspecialchars($category); ?></h2>
                                <p class="text-blue-100 text-sm mt-1"><?php echo count($services); ?> services</p>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                                <?php foreach ($services as $service): ?>
                                <div class="service-card bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($service['service_name']); ?></h3>
                                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded"><?php echo htmlspecialchars($service['category']); ?></span>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="PricingEngine.php?edit=<?php echo $service['id']; ?>" class="text-blue-600 hover:text-blue-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            <button onclick="confirmDelete(<?php echo $service['id']; ?>, '<?php echo htmlspecialchars($service['service_name']); ?>')" class="text-red-600 hover:text-red-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <?php if (!empty($service['description'])): ?>
                                        <p class="text-gray-600 text-sm mb-4"><?php echo htmlspecialchars($service['description']); ?></p>
                                    <?php endif; ?>
                                    
                                    <div class="space-y-3">
                                        <div>
                                            <div class="flex justify-between items-center mb-1">
                                                <span class="text-sm font-medium text-gray-700">Price Range:</span>
                                                <span class="text-sm font-semibold price-badge px-2 py-1 rounded">$<?php echo number_format($service['min_price']); ?> - $<?php echo number_format($service['max_price']); ?></span>
                                            </div>
                                            <div class="flex justify-between text-xs text-gray-500">
                                                <span>Min: $<?php echo number_format($service['min_price']); ?></span>
                                                <span>Max: $<?php echo number_format($service['max_price']); ?></span>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <div class="flex justify-between items-center mb-1">
                                                <span class="text-sm font-medium text-gray-700">Timeline:</span>
                                                <span class="text-sm font-semibold timeline-badge px-2 py-1 rounded"><?php echo $service['min_timeline']; ?>-<?php echo $service['max_timeline']; ?> days</span>
                                            </div>
                                            <div class="flex justify-between text-xs text-gray-500">
                                                <span>Min: <?php echo $service['min_timeline']; ?> days</span>
                                                <span>Max: <?php echo $service['max_timeline']; ?> days</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No services found</h3>
                        <p class="text-gray-500">Get started by adding your first service using the button below.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Floating Add Button -->
    <?php if (!$show_form): ?>
    <button class="floating-btn bg-blue-600 text-white p-4 rounded-full shadow-lg hover:bg-blue-700" onclick="showServiceForm()">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
    </button>
    <?php endif; ?>

    <script>
    function confirmDelete(serviceId, serviceName) {
        Swal.fire({
            title: 'Are you sure?',
            text: `Delete "${serviceName}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `PricingEngine.php?delete=${serviceId}`;
            }
        });
    }

    function showServiceForm() {
        window.location.href = 'PricingEngine.php?add=true';
    }

    function hideServiceForm() {
        window.location.href = 'PricingEngine.php';
    }

    // Auto-hide alerts
    setTimeout(() => {
        document.querySelectorAll('.bg-green-50, .bg-red-50').forEach(alert => {
            alert.style.display = 'none';
        });
    }, 5000);
    </script>
</body>
</html>

<?php $conn->close(); ?>