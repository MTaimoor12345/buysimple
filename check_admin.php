<?php
// Check Admin Credentials Script
// This will show you the current admin user details

require __DIR__ . '/core/Database.php';

$db = Database::getInstance();

// Get admin user
$admin = $db->fetch("SELECT id, name, email, role, created_at FROM users WHERE role = 'admin'");

if ($admin) {
    echo "<h2>Current Admin User:</h2>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Created</th></tr>";
    echo "<tr>";
    echo "<td>" . htmlspecialchars($admin['id']) . "</td>";
    echo "<td>" . htmlspecialchars($admin['name']) . "</td>";
    echo "<td>" . htmlspecialchars($admin['email']) . "</td>";
    echo "<td>" . htmlspecialchars($admin['role']) . "</td>";
    echo "<td>" . htmlspecialchars($admin['created_at']) . "</td>";
    echo "</tr>";
    echo "</table>";
    echo "<br><br>";
    echo "<strong>Default Password:</strong> admin123<br>";
    echo "<a href='reset_admin.php'>Reset Admin Password</a> | <a href='login'>Go to Login</a>";
} else {
    echo "<h2 style='color: red;'>No admin user found!</h2>";
    echo "<a href='reset_admin.php'>Create Admin User</a>";
}

