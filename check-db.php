<?php
/**
 * Database Connection & Admin User Check
 * Access: http://localhost/trendsone/check-db.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>TrendsOne - Database Check</h1>";
echo "<hr>";

// Database credentials
$host = 'localhost';
$dbname = 'trendsone_db';
$username = 'root';
$password = '';

echo "<h2>1. Testing MySQL Connection...</h2>";

try {
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ <strong style='color: green;'>MySQL Connection: SUCCESS</strong><br>";
} catch (PDOException $e) {
    echo "❌ <strong style='color: red;'>MySQL Connection: FAILED</strong><br>";
    echo "Error: " . $e->getMessage() . "<br>";
    echo "<p>Solution: Make sure MySQL is running in XAMPP Control Panel</p>";
    exit;
}

echo "<hr>";
echo "<h2>2. Checking if Database Exists...</h2>";

try {
    $stmt = $pdo->query("SHOW DATABASES LIKE 'trendsone_db'");
    $db_exists = $stmt->rowCount() > 0;
    
    if ($db_exists) {
        echo "✅ <strong style='color: green;'>Database 'trendsone_db': EXISTS</strong><br>";
    } else {
        echo "❌ <strong style='color: red;'>Database 'trendsone_db': NOT FOUND</strong><br>";
        echo "<p><strong>Solution:</strong></p>";
        echo "<ol>";
        echo "<li>Open <a href='http://localhost/phpmyadmin' target='_blank'>phpMyAdmin</a></li>";
        echo "<li>Click 'New' in left sidebar</li>";
        echo "<li>Database name: <code>trendsone_db</code></li>";
        echo "<li>Click 'Create'</li>";
        echo "<li>Go to 'Import' tab</li>";
        echo "<li>Choose file: <code>C:\\xampp\\htdocs\\trendsone\\database\\schema.sql</code></li>";
        echo "<li>Click 'Go'</li>";
        echo "</ol>";
        exit;
    }
} catch (PDOException $e) {
    echo "❌ Error checking database: " . $e->getMessage() . "<br>";
    exit;
}

echo "<hr>";
echo "<h2>3. Connecting to Database...</h2>";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ <strong style='color: green;'>Database Connection: SUCCESS</strong><br>";
} catch (PDOException $e) {
    echo "❌ <strong style='color: red;'>Database Connection: FAILED</strong><br>";
    echo "Error: " . $e->getMessage() . "<br>";
    exit;
}

echo "<hr>";
echo "<h2>4. Checking if 'users' Table Exists...</h2>";

try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    $table_exists = $stmt->rowCount() > 0;
    
    if ($table_exists) {
        echo "✅ <strong style='color: green;'>Table 'users': EXISTS</strong><br>";
    } else {
        echo "❌ <strong style='color: red;'>Table 'users': NOT FOUND</strong><br>";
        echo "<p><strong>Solution:</strong> You need to import the database schema.</p>";
        echo "<ol>";
        echo "<li>Open <a href='http://localhost/phpmyadmin' target='_blank'>phpMyAdmin</a></li>";
        echo "<li>Click on 'trendsone_db' database</li>";
        echo "<li>Go to 'Import' tab</li>";
        echo "<li>Choose file: <code>C:\\xampp\\htdocs\\trendsone\\database\\schema.sql</code></li>";
        echo "<li>Click 'Go'</li>";
        echo "</ol>";
        exit;
    }
} catch (PDOException $e) {
    echo "❌ Error checking table: " . $e->getMessage() . "<br>";
    exit;
}

echo "<hr>";
echo "<h2>5. Checking for Admin User...</h2>";

try {
    $stmt = $pdo->query("SELECT * FROM users WHERE role = 'admin'");
    $admin_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($admin_users) > 0) {
        echo "✅ <strong style='color: green;'>Admin User: FOUND</strong><br><br>";
        
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Created</th>";
        echo "</tr>";
        
        foreach ($admin_users as $user) {
            echo "<tr>";
            echo "<td>" . $user['id'] . "</td>";
            echo "<td>" . $user['name'] . "</td>";
            echo "<td><strong>" . $user['email'] . "</strong></td>";
            echo "<td>" . $user['role'] . "</td>";
            echo "<td>" . $user['status'] . "</td>";
            echo "<td>" . $user['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<br>";
        echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px;'>";
        echo "<h3 style='color: #155724; margin-top: 0;'>✅ Everything is Ready!</h3>";
        echo "<p><strong>You can now login to admin panel:</strong></p>";
        echo "<p>URL: <a href='http://localhost/trendsone/admin/' target='_blank'>http://localhost/trendsone/admin/</a></p>";
        echo "<p><strong>Login Credentials:</strong></p>";
        echo "<ul>";
        echo "<li>Email: <code>admin@trendsone.com</code></li>";
        echo "<li>Password: <code>admin123</code></li>";
        echo "</ul>";
        echo "<p><em>Note: Change password after first login!</em></p>";
        echo "</div>";
        
    } else {
        echo "❌ <strong style='color: red;'>Admin User: NOT FOUND</strong><br>";
        echo "<p><strong>Solution:</strong> The database was imported but admin user is missing.</p>";
        echo "<p>Let me create one for you...</p>";
        
        // Create admin user
        $password_hash = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, role, email_verified, status, created_at) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute(['Admin User', 'admin@trendsone.com', $password_hash, '+91 9876543210', 'admin', 1, 'active']);
        
        echo "✅ <strong style='color: green;'>Admin user created successfully!</strong><br>";
        echo "<p>You can now login with:</p>";
        echo "<ul>";
        echo "<li>Email: <code>admin@trendsone.com</code></li>";
        echo "<li>Password: <code>admin123</code></li>";
        echo "</ul>";
        echo "<p><a href='http://localhost/trendsone/admin/' target='_blank'>Go to Admin Panel</a></p>";
    }
} catch (PDOException $e) {
    echo "❌ Error checking admin user: " . $e->getMessage() . "<br>";
    exit;
}

echo "<hr>";
echo "<h2>6. Checking All Tables...</h2>";

try {
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<p>Total tables found: <strong>" . count($tables) . "</strong></p>";
    
    if (count($tables) >= 18) {
        echo "✅ <strong style='color: green;'>All tables imported successfully!</strong><br>";
    } else {
        echo "⚠️ <strong style='color: orange;'>Expected 18 tables, found " . count($tables) . "</strong><br>";
    }
    
    echo "<details>";
    echo "<summary>Click to see all tables</summary>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>$table</li>";
    }
    echo "</ul>";
    echo "</details>";
    
} catch (PDOException $e) {
    echo "❌ Error listing tables: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<p><em>After successful login, you can delete this file: check-db.php</em></p>";
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
    h2 {
        color: #555;
        margin-top: 20px;
    }
    code {
        background: #f0f0f0;
        padding: 2px 6px;
        border-radius: 3px;
        font-family: monospace;
    }
    a {
        color: #4e73df;
        text-decoration: none;
    }
    a:hover {
        text-decoration: underline;
    }
    details {
        margin-top: 10px;
    }
    summary {
        cursor: pointer;
        color: #4e73df;
        font-weight: bold;
    }
</style>
