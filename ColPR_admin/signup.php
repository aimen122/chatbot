<?php
session_start();
include 'config.php';

if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Username already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashed_password);
            if ($stmt->execute()) {
                $success = "Account created successfully. Please <a href='login.php' class='font-medium'>login</a>.";
            } else {
                $error = "Error creating account. Please try again.";
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Signup</title>
    <link rel="icon" type="image/png" href="colpr-logo.png">
    <link rel="shortcut icon" type="image/png" href="colpr-logo.png">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fadeInGlow {
            from { opacity: 0; transform: translateY(-40px); box-shadow: 0 0 0 rgba(0, 0, 0, 0); }
            to { opacity: 1; transform: translateY(0); box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05); }
        }
        @keyframes slideInBounce {
            0% { opacity: 0; transform: translateX(-40px) scale(0.9); }
            60% { transform: translateX(-10px) scale(1.05); }
            100% { opacity: 1; transform: translateX(0) scale(1); }
        }
        @keyframes hoverGlow {
            0% { transform: scale(1); box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05); }
            50% { transform: scale(1.05); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1); }
            100% { transform: scale(1); box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05); }
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        body {
            background: #f5f5f5;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .auth-container {
            background: #ffffff;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 400px;
            transition: all 0.5s ease;
            animation: fadeInGlow 0.8s ease-in-out;
        }
        .auth-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            animation: hoverGlow 0.5s ease-in-out;
        }
        form {
            animation: slideInBounce 0.7s ease-in-out;
        }
        input {
            background: #ffffff;
            color: #000000;
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            padding: 10px;
            transition: all 0.3s ease;
            box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        input:hover {
            transform: scale(1.02);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1), inset 0 2px 10px rgba(0, 0, 0, 0.05);
            animation: hoverGlow 0.5s ease-in-out;
        }
        input:focus {
            border-color: rgba(0, 0, 0, 0.1);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            transform: scale(1.02);
        }
        button {
            background: #ffffff;
            color: #000000;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            animation: slideInBounce 0.7s ease-in-out;
        }
        button:hover {
            background: #e5e5e5;
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            animation: hoverGlow 0.5s ease-in-out;
        }
        a {
            color: #000000;
            transition: all 0.3s ease;
        }
        a:hover {
            background: #e5e5e5;
            transform: scale(1.05);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            animation: hoverGlow 0.5s ease-in-out;
        }
        .error, .success {
            background: #e5e5e5;
            color: #000000;
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 1rem;
            text-align: center;
            animation: pulse 1.5s infinite;
        }
        .success a {
            color: #000000;
            font-weight: 600;
        }
        .success a:hover {
            background: #e5e5e5;
            animation: hoverGlow 0.5s ease-in-out;
        }
        h1 {
            color: #000000;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            font-size: 2rem;
            font-weight: 700;
            animation: fadeInGlow 0.8s ease-in-out;
        }
        label {
            color: #000000;
            font-weight: 500;
            text-shadow: 0 1px 5px rgba(0, 0, 0, 0.05);
        }
        p {
            color: #000000;
        }
        .dark-mode {
            background: #e5e5e5;
        }
        .dark-mode .auth-container {
            background: #e5e5e5;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-color: rgba(0, 0, 0, 0.05);
        }
        .dark-mode .auth-container:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            animation: hoverGlow 0.5s ease-in-out;
        }
        .dark-mode input {
            background: #e5e5e5;
            color: #000000;
            border-color: rgba(0, 0, 0, 0.05);
        }
        .dark-mode input:focus {
            border-color: rgba(0, 0, 0, 0.1);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .dark-mode button {
            background: #e5e5e5;
            color: #000000;
        }
        .dark-mode button:hover {
            background: #f5f5f5;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            animation: hoverGlow 0.5s ease-in-out;
        }
        .dark-mode a {
            color: #000000;
        }
        .dark-mode a:hover {
            background: #f5f5f5;
            animation: hoverGlow 0.5s ease-in-out;
        }
        .dark-mode .error, .dark-mode .success {
            background: #e5e5e5;
            color: #000000;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        .dark-mode .success a {
            color: #000000;
        }
        .dark-mode .success a:hover {
            background: #f5f5f5;
            animation: hoverGlow 0.5s ease-in-out;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="flex flex-col items-center mb-6">
            <img src="colpr-logo.png" alt="ColPR Logo" class="h-16 max-w-full object-contain mb-3">
            <h1 class="text-2xl font-bold text-center">ColPR Admin</h1>
            <p class="text-sm text-gray-600 text-center">ColPR Software</p>
        </div>
        <h2 class="text-xl font-semibold text-center mb-6">Signup</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>
        <form method="POST" action="signup.php" class="space-y-4">
            <div>
                <label for="username" class="block text-sm font-medium">Username</label>
                <input type="text" name="username" id="username" required class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none transition duration-200">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium">Password</label>
                <input type="password" name="password" id="password" required class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none transition duration-200">
            </div>
            <div>
                <label for="confirm_password" class="block text-sm font-medium">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" required class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none transition duration-200">
            </div>
            <button type="submit" class="w-full py-2 px-4 rounded-md focus:outline-none transition duration-200">Sign Up</button>
        </form>
        <p class="mt-4 text-center text-sm">Already have an account? <a href="login.php" class="mt-6 inline-block font-medium">Login</a></p>
    </div>

    <script>
        // Reapply animations on page load
        document.addEventListener('DOMContentLoaded', () => {
            const authContainer = document.querySelector('.auth-container');
            const form = document.querySelector('form');
            const inputs = document.querySelectorAll('input');
            const button = document.querySelector('button');
            const link = document.querySelector('p > a');
            const error = document.querySelector('.error');
            const success = document.querySelector('.success');
            const p = document.querySelectorAll('p');

            // Remove and reapply animations for refresh effect
            authContainer.classList.remove('animate-fadeInGlow');
            form.classList.remove('animate-slideInBounce');
            inputs.forEach(input => input.classList.remove('animate-slideInBounce'));
            button.classList.remove('animate-slideInBounce');
            link.classList.remove('animate-slideInBounce');
            p.forEach(p => p.classList.remove('animate-slideInBounce'));
            if (error) error.classList.remove('animate-pulse');
            if (success) success.classList.remove('animate-pulse');

            void authContainer.offsetWidth; // Trigger reflow
            authContainer.classList.add('animate-fadeInGlow');
            form.classList.add('animate-slideInBounce');
            inputs.forEach(input => input.classList.add('animate-slideInBounce'));
            button.classList.add('animate-slideInBounce');
            link.classList.add('animate-slideInBounce');
            p.forEach(p => p.classList.add('animate-slideInBounce'));
            if (error) error.classList.add('animate-pulse');
            if (success) success.classList.add('animate-pulse');
        });

        // Apply dark mode if enabled
        const body = document.body;
        if (localStorage.getItem('dark-mode') === 'enabled') {
            body.classList.add('dark-mode');
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>