<?php
/**
 * Password Hash Generator & Tester
 * Use this to generate password hashes and test password verification
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Hash Generator - TrendsOne</title>
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
        .section {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }
        input[type="text"],
        input[type="password"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }
        textarea {
            font-family: monospace;
            font-size: 12px;
            resize: vertical;
        }
        button {
            background: #4e73df;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
        }
        button:hover {
            background: #2e59d9;
        }
        .result {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 15px;
            border-radius: 4px;
            margin-top: 15px;
        }
        .error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 15px;
            border-radius: 4px;
            margin-top: 15px;
        }
        .info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            padding: 15px;
            border-radius: 4px;
            margin-top: 15px;
        }
        code {
            background: #f0f0f0;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
            color: #e83e8c;
        }
        .hash-output {
            word-break: break-all;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
            font-family: monospace;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <h1>🔐 Password Hash Generator & Tester</h1>
    
    <!-- Generate Hash Section -->
    <div class="section">
        <h2>1. Generate Password Hash</h2>
        <p>Enter a password to generate its bcrypt hash (used for storing in database)</p>
        
        <form method="POST">
            <div class="form-group">
                <label>Password to Hash:</label>
                <input type="text" name="password_to_hash" placeholder="Enter password (e.g., admin123)" required>
            </div>
            
            <div class="form-group">
                <label>Bcrypt Cost (10-12 recommended):</label>
                <input type="number" name="cost" value="12" min="10" max="15">
                <small style="color: #666;">Higher cost = more secure but slower (12 is good balance)</small>
            </div>
            
            <button type="submit" name="generate_hash">Generate Hash</button>
        </form>
        
        <?php
        if (isset($_POST['generate_hash'])) {
            $password = $_POST['password_to_hash'];
            $cost = (int)$_POST['cost'];
            
            $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => $cost]);
            
            echo '<div class="result">';
            echo '<h3>✅ Hash Generated Successfully!</h3>';
            echo '<p><strong>Original Password:</strong> <code>' . htmlspecialchars($password) . '</code></p>';
            echo '<p><strong>Generated Hash:</strong></p>';
            echo '<div class="hash-output">' . $hash . '</div>';
            echo '<p style="margin-top: 15px;"><strong>How to use this hash:</strong></p>';
            echo '<ol>';
            echo '<li>Copy the hash above</li>';
            echo '<li>Update user password in database with this hash</li>';
            echo '<li>User can now login with the original password</li>';
            echo '</ol>';
            echo '</div>';
        }
        ?>
    </div>
    
    <!-- Verify Password Section -->
    <div class="section">
        <h2>2. Verify Password Against Hash</h2>
        <p>Test if a password matches a hash (like what happens during login)</p>
        
        <form method="POST">
            <div class="form-group">
                <label>Password to Test:</label>
                <input type="text" name="test_password" placeholder="Enter password" required>
            </div>
            
            <div class="form-group">
                <label>Hash to Compare:</label>
                <textarea name="test_hash" rows="3" placeholder="Paste the password hash here" required></textarea>
            </div>
            
            <button type="submit" name="verify_password">Verify Password</button>
        </form>
        
        <?php
        if (isset($_POST['verify_password'])) {
            $test_password = $_POST['test_password'];
            $test_hash = trim($_POST['test_hash']);
            
            if (password_verify($test_password, $test_hash)) {
                echo '<div class="result">';
                echo '<h3>✅ Password Matches!</h3>';
                echo '<p>The password <code>' . htmlspecialchars($test_password) . '</code> is correct for this hash.</p>';
                echo '<p>This means login would succeed with this password.</p>';
                echo '</div>';
            } else {
                echo '<div class="error">';
                echo '<h3>❌ Password Does Not Match!</h3>';
                echo '<p>The password <code>' . htmlspecialchars($test_password) . '</code> is incorrect for this hash.</p>';
                echo '<p>This means login would fail with this password.</p>';
                echo '</div>';
            }
        }
        ?>
    </div>
    
    <!-- Update Admin Password Section -->
    <div class="section">
        <h2>3. Update Admin Password in Database</h2>
        <p>Directly update the admin password in the database</p>
        
        <form method="POST">
            <div class="form-group">
                <label>Admin Email:</label>
                <input type="email" name="admin_email" value="admin@trendsone.com" required>
            </div>
            
            <div class="form-group">
                <label>New Password:</label>
                <input type="text" name="new_password" placeholder="Enter new password" required>
            </div>
            
            <button type="submit" name="update_admin_password">Update Admin Password</button>
        </form>
        
        <?php
        if (isset($_POST['update_admin_password'])) {
            require_once 'config/config.php';
            
            $admin_email = $_POST['admin_email'];
            $new_password = $_POST['new_password'];
            
            try {
                // Generate new hash
                $new_hash = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);
                
                // Update in database
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ? AND role = 'admin'");
                $stmt->execute([$new_hash, $admin_email]);
                
                if ($stmt->rowCount() > 0) {
                    echo '<div class="result">';
                    echo '<h3>✅ Password Updated Successfully!</h3>';
                    echo '<p>Admin password has been updated in the database.</p>';
                    echo '<p><strong>Login Credentials:</strong></p>';
                    echo '<ul>';
                    echo '<li>Email: <code>' . htmlspecialchars($admin_email) . '</code></li>';
                    echo '<li>Password: <code>' . htmlspecialchars($new_password) . '</code></li>';
                    echo '</ul>';
                    echo '<p><a href="http://localhost/trendsone/admin/" style="display: inline-block; background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Go to Admin Login</a></p>';
                    echo '</div>';
                } else {
                    echo '<div class="error">';
                    echo '<h3>❌ Update Failed!</h3>';
                    echo '<p>No admin user found with email: ' . htmlspecialchars($admin_email) . '</p>';
                    echo '</div>';
                }
            } catch (PDOException $e) {
                echo '<div class="error">';
                echo '<h3>❌ Database Error!</h3>';
                echo '<p>' . $e->getMessage() . '</p>';
                echo '</div>';
            }
        }
        ?>
    </div>
    
    <!-- Information Section -->
    <div class="section">
        <h2>📚 How Password Hashing Works</h2>
        
        <div class="info">
            <h3>What is Password Hashing?</h3>
            <p>Password hashing is a one-way encryption method that converts a plain text password into a scrambled string (hash). This hash is stored in the database instead of the actual password.</p>
            
            <h4>Key Points:</h4>
            <ul>
                <li><strong>One-way:</strong> You cannot reverse a hash back to the original password</li>
                <li><strong>Unique:</strong> Same password always generates same hash (with same salt)</li>
                <li><strong>Secure:</strong> Even if database is compromised, passwords are safe</li>
                <li><strong>Verification:</strong> Use <code>password_verify()</code> to check if password matches hash</li>
            </ul>
            
            <h4>PHP Functions Used:</h4>
            <ul>
                <li><code>password_hash($password, PASSWORD_BCRYPT, ['cost' => 12])</code> - Generate hash</li>
                <li><code>password_verify($password, $hash)</code> - Verify password against hash</li>
            </ul>
            
            <h4>Example:</h4>
            <p><strong>Password:</strong> <code>admin123</code></p>
            <p><strong>Hash:</strong> <code>$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5GyYL0xvJXjYy</code></p>
            <p>When user logs in with "admin123", PHP uses <code>password_verify()</code> to check if it matches the stored hash.</p>
        </div>
    </div>
    
    <div class="section">
        <h2>🔒 Security Best Practices</h2>
        <ul>
            <li>✅ Never store plain text passwords in database</li>
            <li>✅ Always use <code>password_hash()</code> with bcrypt</li>
            <li>✅ Use cost factor of 12 (good balance of security and speed)</li>
            <li>✅ Never display passwords in logs or error messages</li>
            <li>✅ Require strong passwords (8+ characters, mixed case, numbers)</li>
            <li>✅ Implement password reset via email (never show old password)</li>
            <li>⚠️ Delete this file after use (contains sensitive functionality)</li>
        </ul>
    </div>
    
    <hr>
    <p style="text-align: center; color: #666;">
        <em>After fixing your password issue, delete this file: hash-password.php</em>
    </p>
</body>
</html>
