<?php
/**
 * Test Admin Login
 * This will show you if the password verification is working
 */

require_once 'config/config.php';

echo "<h1>Admin Login Test</h1>";
echo "<hr>";

$test_email = 'admin@trendsone.com';
$test_password = 'admin123';

echo "<h2>Testing Login for:</h2>";
echo "<p>Email: <strong>$test_email</strong></p>";
echo "<p>Password: <strong>$test_password</strong></p>";
echo "<hr>";

try {
    // Get admin user from database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin' AND status = 'active'");
    $stmt->execute([$test_email]);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "<h3>✅ User Found in Database</h3>";
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Value</th></tr>";
        echo "<tr><td>ID</td><td>" . $user['id'] . "</td></tr>";
        echo "<tr><td>Name</td><td>" . $user['name'] . "</td></tr>";
        echo "<tr><td>Email</td><td>" . $user['email'] . "</td></tr>";
        echo "<tr><td>Role</td><td>" . $user['role'] . "</td></tr>";
        echo "<tr><td>Status</td><td>" . $user['status'] . "</td></tr>";
        echo "<tr><td>Password Hash</td><td style='font-size: 10px; word-break: break-all;'>" . substr($user['password'], 0, 50) . "...</td></tr>";
        echo "</table>";
        
        echo "<hr>";
        echo "<h3>Testing Password Verification...</h3>";
        
        // Test password verification
        if (password_verify($test_password, $user['password'])) {
            echo "<div style='background: #d4edda; padding: 20px; border: 2px solid #28a745; border-radius: 5px;'>";
            echo "<h2 style='color: #28a745; margin-top: 0;'>✅ PASSWORD VERIFICATION: SUCCESS!</h2>";
            echo "<p><strong>The password is correct and should work for login.</strong></p>";
            echo "<p>If you still can't login, try these steps:</p>";
            echo "<ol>";
            echo "<li>Clear your browser cache and cookies</li>";
            echo "<li>Try a different browser (Chrome, Firefox, Edge)</li>";
            echo "<li>Make sure you're typing the password exactly: <code>admin123</code></li>";
            echo "<li>Check that Caps Lock is OFF</li>";
            echo "</ol>";
            echo "<p><a href='http://localhost/trendsone/admin/' style='display: inline-block; background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Try Login Again</a></p>";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; padding: 20px; border: 2px solid #dc3545; border-radius: 5px;'>";
            echo "<h2 style='color: #dc3545; margin-top: 0;'>❌ PASSWORD VERIFICATION: FAILED!</h2>";
            echo "<p><strong>The password hash doesn't match.</strong></p>";
            echo "<p>Let me fix this by resetting the password...</p>";
            echo "</div>";
            
            // Reset password
            $new_password_hash = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12]);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$new_password_hash, $user['id']]);
            
            echo "<div style='background: #d4edda; padding: 20px; border: 2px solid #28a745; border-radius: 5px; margin-top: 20px;'>";
            echo "<h2 style='color: #28a745; margin-top: 0;'>✅ PASSWORD RESET SUCCESSFUL!</h2>";
            echo "<p>The password has been reset. You can now login with:</p>";
            echo "<ul>";
            echo "<li>Email: <code>admin@trendsone.com</code></li>";
            echo "<li>Password: <code>admin123</code></li>";
            echo "</ul>";
            echo "<p><a href='http://localhost/trendsone/admin/' style='display: inline-block; background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Admin Login</a></p>";
            echo "</div>";
        }
        
    } else {
        echo "<div style='background: #f8d7da; padding: 20px; border: 2px solid #dc3545; border-radius: 5px;'>";
        echo "<h2 style='color: #dc3545; margin-top: 0;'>❌ USER NOT FOUND!</h2>";
        echo "<p>No admin user found with email: <strong>$test_email</strong></p>";
        echo "<p>Creating admin user now...</p>";
        echo "</div>";
        
        // Create admin user
        $password_hash = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, role, email_verified, status, created_at) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute(['Admin User', 'admin@trendsone.com', $password_hash, '+91 9876543210', 'admin', 1, 'active']);
        
        echo "<div style='background: #d4edda; padding: 20px; border: 2px solid #28a745; border-radius: 5px; margin-top: 20px;'>";
        echo "<h2 style='color: #28a745; margin-top: 0;'>✅ ADMIN USER CREATED!</h2>";
        echo "<p>You can now login with:</p>";
        echo "<ul>";
        echo "<li>Email: <code>admin@trendsone.com</code></li>";
        echo "<li>Password: <code>admin123</code></li>";
        echo "</ul>";
        echo "<p><a href='http://localhost/trendsone/admin/' style='display: inline-block; background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Admin Login</a></p>";
        echo "</div>";
    }
    
} catch (PDOException $e) {
    echo "<div style='background: #f8d7da; padding: 20px; border: 2px solid #dc3545; border-radius: 5px;'>";
    echo "<h2 style='color: #dc3545;'>❌ DATABASE ERROR</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "<hr>";
echo "<p><em>After successful login, you can delete this file: test-login.php</em></p>";
?>

<style>
    body {
        font-family: Arial, sans-serif;
        max-width: 900px;
        margin: 50px auto;
        padding: 20px;
        background: #f5f5f5;
    }
    h1 {
        color: #333;
        border-bottom: 3px solid #4e73df;
        padding-bottom: 10px;
    }
    code {
        background: #f0f0f0;
        padding: 2px 6px;
        border-radius: 3px;
        font-family: monospace;
        color: #e83e8c;
    }
</style>
