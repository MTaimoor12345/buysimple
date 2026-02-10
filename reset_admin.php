<?php
// Admin Password Reset Script
// Run this file once to reset admin password
// Then delete this file for security

// Start session first
session_start();

// Autoload classes
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/core/' . $class . '.php',
        __DIR__ . '/models/' . $class . '.php',
        __DIR__ . '/controllers/' . $class . '.php',
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require $path;
            return;
        }
    }
});

require __DIR__ . '/config/database.php';

try {
    $db = Database::getInstance();
    
    // New admin credentials
    $adminEmail = 'admin@example.com';
    $adminPassword = 'admin123'; // Change this to your desired password
    $adminName = 'Admin';
    
    // Hash the password
    $hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);
    
    // Check if admin exists
    $existingAdmin = $db->fetch("SELECT * FROM users WHERE email = ?", [$adminEmail]);
    
    if ($existingAdmin) {
        // Update existing admin
        $db->query(
            "UPDATE users SET password = ?, name = ?, role = 'admin' WHERE email = ?",
            [$hashedPassword, $adminName, $adminEmail]
        );
        echo "<h2 style='color: green;'>✅ Admin password updated successfully!</h2>";
    } else {
        // Create new admin
        $db->query(
            "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')",
            [$adminName, $adminEmail, $hashedPassword]
        );
        echo "<h2 style='color: green;'>✅ Admin user created successfully!</h2>";
    }
    
    // Verify the password works
    $testUser = $db->fetch("SELECT * FROM users WHERE email = ?", [$adminEmail]);
    if ($testUser && password_verify($adminPassword, $testUser['password'])) {
        echo "<p style='color: green;'>✅ Password verification successful!</p>";
    } else {
        echo "<p style='color: red;'>❌ Password verification failed!</p>";
    }
    
    echo "<hr>";
    echo "<h2>Admin Login Credentials:</h2>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; margin: 20px 0;'>";
    echo "<tr><th>Email</th><td>" . htmlspecialchars($adminEmail) . "</td></tr>";
    echo "<tr><th>Password</th><td><strong>" . htmlspecialchars($adminPassword) . "</strong></td></tr>";
    echo "</table>";
    echo "<br>";
    echo "<a href='login' style='padding: 10px 20px; background: #2563eb; color: white; text-decoration: none; border-radius: 5px;'>Go to Login Page</a>";
    echo "<br><br>";
    echo "<strong style='color: red;'>⚠️ IMPORTANT: Delete this file (reset_admin.php) after use for security!</strong>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Make sure your database is configured correctly in config/database.php</p>";
}

